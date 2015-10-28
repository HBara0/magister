<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * CMS News Class
 * $id: CmsNews_class.php
 * Created:			@tony.assaad	August 13, 2012 | 10:53 PM
 * Last Update: 	@zaher.reda		August 29, 2012 | 12:12 PM
 */

class CmsNews extends Cms {
    private $status = 0;
    protected $data = array();

    const PRIMARY_KEY = 'cmsnid';
    const TABLE_NAME = 'cms_news';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;
    const SIMPLEQ_ATTRS = 'cmsnid, createdBy, isPublished';

    public function __construct($id = '', $simple = true) {
        if(isset($id) && !empty($id)) {
            $this->data = $this->read($id, $simple);
        }
        return null;
    }

    public function add($data, array $options = array()) {
        global $db, $log, $core, $errorhandler, $lang;

        $this->data = $data;
        $this->categories = $this->data['categories'];

        if(is_empty($this->data['title'])) {
            $this->status = 1;
            return false;
        }
        if(empty($this->data['alias'])) {
            $this->data['alias'] = generate_alias($this->data['title']);
        }
        $this->settings = parent::read_settings_db(false);


        /* Check if news with same title created by anyone */
        if($options['operationtype'] != 'updateversion') {
            if(value_exists('cms_news', 'title', $this->data['title'])) {
                $this->status = 2;
                return false;
            }
        }

        unset($this->data['categories']);

        if(isset($this->data['publishDate']) && !empty($this->data['publishDate'])) {
            $this->data['publishDate'] = strtotime($this->data['publishDate']);
        }
        else {
            $this->data['publishDate'] = TIME_NOW;
        }

        // $this->data['alias'] = parent::generate_alias($this->data['alias']);
        $this->data['title'] = $core->sanitize_inputs($this->data['title'], array('removetags' => true));
        $this->data['bodyText'] = $core->sanitize_inputs($this->data['bodyText'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true));
        $this->data['createdBy'] = $core->user['uid'];
        $this->data['createDate'] = TIME_NOW;
        /* Double check publishing permissions */
        if($core->usergroup['cms_canPublishNews'] == 0) {
            $this->data['isPublished'] = 0;
        }

        if($options['operationtype'] == 'updateversion') {
            $cmsnid['cmsnid'] = $db->fetch_field($db->query("SELECT cmsnid as cmsnid  FROM ".Tprefix.self::TABLE_NAME), 'cmsnid');
            $this->data['modifyDate'] = TIME_NOW;
            $this->data['modifiedBy'] = $core->user['uid'];
            $this->prevversion = $this->get_lastversion_news($this->data['alias'], array('exclude' => array('cmsnid' => $cmsnid['cmsnid'])));
        }

        /* Set appropriate version - START */
        if(isset($this->prevversion)) {
            if(similar_text($this->data['bodyText'], $this->prevversion['bodyText']) > 70) {
                $this->data['version'] = $this->prevversion['version'] + 0.1;
            }
            else {
                $this->data['version'] = $this->prevversion['version'] + 1;
            }
        }
        else {
            $this->data['version'] = 1.0;
        }
        /* Set appropriate version - END */

        $newsimages = $this->data['uploadedImages'];
        $this->prepare_newsimages($newsimages);
        $this->data['bodyText'] = $this->replace_imageslinks($this->data['bodyText'], $newsimages);

        if(isset($this->data['attachments']) && is_array($this->data['attachments'])) {
            $attachments = $this->data['attachments'];
            unset($this->data['attachments']);
        }

        /* Insert news - START */
        if(is_array($this->data)) {
            $this->data['token'] = md5(uniqid(microtime(), true));
            $query = $db->insert_query('cms_news', $this->data);
            if($query) {
                $this->status = 0;
                $cmsnid = $this->data['cmsnid'] = $db->last_id();
                $log->record($this->data['cmsnid']);

                /* Insert relatedcategories */
                $related_categories = array(
                        'cmsnid' => $cmsnid,
                        'cmsccid' => $this->categories
                );
                $categoyquery = $db->insert_query('cms_news_relatedcategories', $related_categories);

                /* Inform audits about the change, and request approval - START */
                if($core->usergroup['cms_canPublishNews'] == 0) {
                    if(!is_array($this->settings['websiteaudits'])) {
                        $this->settings['websiteaudits'] = explode(';', $this->settings['websiteaudits']);
                    }

                    $news_approvers = unserialize($this->settings['websiteaudits']['value']);
                    if(is_array($news_approvers)) {
                        foreach($news_approvers as $approver) {
                            $user_object = new Users($approver);
                            $email_data['to'][] = $user_object->email;
                        }
                        $mailer = new Mailer();
                        $mailer = $mailer->get_mailerobj();
                        $mailer->set_type();
                        $mailer->set_from(array('name' => 'OCOS Mailer', 'email' => $core->settings['maileremail']));
                        $mailer->set_to($email_data['to']);


                        if($options['operationtype'] == 'updateversion') {
                            $mailer->set_subject($lang->sprint($lang->modifynotification_subject, $this->prevversion['title']));
                            $emailmessage = $lang->sprint($lang->modifynotification_body, $this->prevversion['title'], //1
                                    similar_text($this->prevversion['title'], $this->data['title']), //2
                                    $this->data['title'], //3
                                    similar_text($this->prevversion['summary'], $this->data['summary']), //4
                                    $this->data['summary'], //5
                                    similar_text($this->prevversion['bodyText'], $this->data['bodyText']), //6
                                    get_stringdiff($this->oldnews['bodyText'], $this->data['bodyText'])); //7

                            $mailer->set_message($emailmessage);
                        }
                        else {
                            $mailer->set_subject($lang->sprint($lang->newnotification_subject, $this->data['title']));
                            $emailmessage = $lang->sprint($lang->newnotification_body, $this->data['title'], $this->data['summary'], $this->data['bodyText']);
                            $mailer->set_message($emailmessage);
                        }
                        /* Attach new attachments to the message and indicate that in the message body */
//HERE
                        $mailer->send();
                    }
                }
                /* Inform audits about the change, and request approval - END */

                /* Upload related files - START */

                $ftp_settings = array('server' => $this->settings['ftpserver']['value'], 'username' => $this->settings['ftpusername']['value'], 'password' => $this->settings['ftppassword']['value']);
//                $upload = new Uploader('', array(), array(), 'constructonly');
//                $upload->establish_ftp($ftp_settings);

                /* Upload news images - Start */
//                $upload->set_upload_path($this->settings['newsimagespath']);
//                $allowed_types_newsimages = array('image/jpeg', 'image/gif', 'image/png');

                foreach($newsimages as $key_link => $link) {
                    $link = parse_url($link);
                    $path_info = pathinfo($link['path']);
                    $file_info = finfo_open(FILEINFO_MIME_TYPE);

//  $upload->set_options('newsimage', array('newsimage' => array('type' => finfo_file($file_info, $link['path']), 'name' => $path_info['basename'], 'tmp_name' => $link['path'], 'size' => filesize($link['path']))), $allowed_types_newsimages, 'ftp', 5242880, 0, 0); //5242880 bytes = 5 MB (1024)
                    finfo_close($file_info);

//  $upload->process_file();
                    @unlink($link['path']);
                }
                /* Upload news images - End */
//  $upload->close_ftp();

                /* Upload attachments - Start */
                $upload_param['allowed_types'] = array('image/jpeg', 'image/gif', 'image/png', 'application/zip', 'application/pdf', 'application/x-pdf', 'application/msword', 'application/vnd.ms-powerpoint', 'text/plain', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation');
                if(isset($options['upload_allowed_types'])) {
                    $upload_param['allowed_types'] = $options['upload_allowed_types'];
                }

                $upload_param['maxsize'] = 300000;
                if(isset($options['upload_maxsize'])) {
                    $upload_param['maxsize'] = $options['upload_maxsize'];
                }

                $upload = new Uploader('attachments', $attachments, $upload_param['allowed_types'], 'ftp', $upload_param['maxsize'], 1, 1);

                $upload->establish_ftp($ftp_settings);
                $upload->set_upload_path($this->settings['newsattachmentspath']);
                $upload->process_file();
                /* Upload attachments - Start */
                $upload->close_ftp();
                foreach($upload->get_status_array() as $key => $val) {
                    if($val == 4) {
                        $fileinfo = $upload->get_fileinfo($key);
                        $db->insert_query('cms_news_attachments', array('cmsnid' => $cmsnid, 'title' => $fileinfo['originalname'], 'filename' => $fileinfo['name'], 'type' => $fileinfo['extension'], 'size' => $fileinfo['size'], 'dateAdded' => TIME_NOW, 'addedBy' => $core->user['uid']));
                    }
                    else {
                        $errorhandler->record('fileuploaderrors', array($key => $val));
                    }
                }
                /* Upload related files - END */

                return true;
            }
        }
    }

    private function get_lastversion_news($alias, array $options = array()) {
        global $db;

        if(isset($options['exclude'])) {
            $exclude_querystring = ' AND cmsnid NOT IN ('.implode(",", $options['exclude']).')';
        }
        return $db->fetch_assoc($db->query("SELECT title, alias, summary, bodyText, version FROM ".Tprefix."cms_news WHERE alias='".$db->escape_string($alias)."'{$exclude_querystring} ORDER BY version DESC"));
    }

    public function edit() {

    }

    protected function read($id, $simple = false) {
        global $db;

        if(empty($id)) {
            return false;
        }

        $query_select = 'cn.*';
        if($simple == true) {
            $query_select = 'cn.cmsnid, cn.title, cn.summary';
        }

        return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."cms_news cn LEFT JOIN ".Tprefix."cms_news_relatedcategories cnrc ON (cnrc.cmsnid=cn.cmsnid) LEFT JOIN ".Tprefix."cms_contentcategories cnc ON(cnc.cmsccid=cnrc.cmsccid) WHERE cn.cmsnid=".$db->escape_string($id)));
    }

    public function get() {
        return $this->data;
    }

    private function prepare_newsimages(&$newsimages) {
        $newsimages = explode(';', $newsimages);
        foreach($newsimages as $key_link => $link) {
            if(empty($link)) {
                unset($newsimages[$key_link]);
                continue;
            }

            if(!strstr($this->data['bodyText'], $link)) {
                $link = parse_url($link);
                unset($newsimages[$key_link]);
                @unlink($link['path']);
            }
        }
    }

    private function replace_imageslinks($string, $newsimages) {
        foreach($newsimages as $key_link => $link) {
            $link_info = parse_url($link);
            $path_info = pathinfo($link_info['path']);
            $string = str_replace($link, '{$cms->settings[newsimagespath]}/'.$path_info['basename'], $string);
        }
        return $string;
    }

    /* Will retun an associative array of all news that match the selected options */
    public function get_multiplenews($filter_where) {
        global $db, $core;

        $sort_query = 'ORDER BY publishDate DESC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = 'ORDER BY '.$core->input['sortby'].' '.$core->input['order'];
        }

        if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
            $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
        }

        $limit_start = 0;
        if(isset($core->input['start'])) {
            $limit_start = $db->escape_string($core->input['start']);
        }

//        if(isset($core->input['filterby'], $core->input['filtervalue'])) {
//            $attributes_filter_options['title'] = array('title' => 'cn.');
//
//            if($attributes_filter_options['title'][$core->input['filterby']] == 'int') {
//                $filter_value = ' = "'.$db->escape_string($core->input['filtervalue']).'"';
//            }
//            else {
//                $filter_value = ' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
//            }
//              $filter_where = ' WHERE '.$db->escape_string($attributes_filter_options['title'][$core->input['filterby']].$core->input['filterby']).$filter_value;
//        }


        $news_query = $db->query("SELECT cn.cmsnid, cn.version, cn.createDate,cn.isPublished, cn.isFeatured, cn.lang, cn.hits, cn.title, cn.summary, cn.createDate AS date, u.displayname as creator
								FROM ".Tprefix."cms_news cn
								JOIN ".Tprefix."users u ON(u.uid=cn.createdBy)
								LEFT JOIN ".Tprefix."cms_news_relatedcategories cnrc ON (cnrc.cmsnid=cn.cmsnid)
								LEFT JOIN ".Tprefix."cms_contentcategories cnc ON (cnc.cmsccid=cnrc.cmsccid)
								{$filter_where}
								{$sort_query}
								LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

        if($db->num_rows($news_query) > 0) {
            while($news = $db->fetch_assoc($news_query)) {
                $listnews[$news['cmsnid']] = $news;
            }
            return $listnews;
        }
        return false;
    }

    public function get_status() {
        return $this->status;
    }

    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

}
?>