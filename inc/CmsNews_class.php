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
    private $news = array();

    public function __construct($id = '', $simple = false) {
        $this->read_settings_db();

        if(isset($id) && !empty($id)) {
            $this->news = $this->read(intval($id), $simple);
        }
    }

    public function add($data, array $options = array()) {
        global $db, $log, $core, $errorhandler, $lang;

        $this->news = $data;
        $this->categories = $this->news['categories'];

        if(is_empty($this->news['title'], $this->news['alias'], $this->news['bodyText'])) {
            $this->status = 1;
            return false;
        }

        /* Check if news with same title created by anyone */
        if($options['operationtype'] == 'updateversion') {
            if(value_exists('cms_news', 'title', $this->news['title'])) {
                $this->status = 2;
                return false;
            }
        }

        unset($this->news['categories']);

        /* Closing date can be empty, means news doesn't expire */
        if(isset($this->news['publishDate']) && !empty($this->news['publishDate'])) {
            $this->news['publishDate'] = strtotime($this->news['publishDate']);
        }

        if(empty($this->news['alias'])) {
            $this->news['alias'] = $this->news['title'];
        }

        $this->news['alias'] = parent::generate_alias($this->news['alias']);
        $this->news['title'] = $core->sanitize_inputs($this->news['title'], array('removetags' => true));
        $this->news['bodyText'] = $core->sanitize_inputs($this->news['bodyText'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true));
        $this->news['createdBy'] = $core->user['uid'];
        $this->news['createDate'] = TIME_NOW;
        /* Double check publishing permissions */
        if($core->usergroup['crm_canPublishNews'] == 0) {
            $this->news['isPublished'] = 0;
        }

        if($options['operationtype'] == 'updateversion') {
            $this->prevversion = $this->get_lastversion_news($this->news['alias'], array('exclude' => array('cmsnid' => $cmsnid)));
        }

        /* Set appropriate version - START */
        if(isset($this->prevversion)) {
            if(similar_text($this->news['bodyText'], $this->prevversion['bodyText']) > 70) {
                $this->news['version'] = $this->prevversion['version'] + 0.1;
            }
            else {
                $this->news['version'] = $this->prevversion['version'] + 1;
            }
        }
        else {
            $this->news['version'] = 1.0;
        }
        /* Set appropriate version - END */

        $newsimages = $this->news['uploadedImages'];
        $this->prepare_newsimages($newsimages);
        $this->news['bodyText'] = $this->replace_imageslinks($this->news['bodyText'], $newsimages);

        if(isset($this->news['attachments']) && is_array($this->news['attachments'])) {
            $attachments = $this->news['attachments'];
            unset($this->news['attachments']);
        }

        /* Insert news - START */
        if(is_array($this->news)) {
            $query = $db->insert_query('cms_news', $this->news);
            if($query) {
                $this->status = 0;
                $cmsnid = $db->last_id();
                $log->record($this->news['cmsnid']);

                /* Insert relatedcategories */
                $related_categories = array(
                        'cmsnid' => $cmsnid,
                        'cmsccid' => $this->categories
                );
                $categoyquery = $db->insert_query('cms_news_relatedcategories', $related_categories);

                /* Inform audits about the change, and request approval - START */
                if($core->usergroup['crm_canPublishNews'] == 0) {
                    if(!is_array($this->settings['websiteaudits'])) {
                        $this->settings['websiteaudits'] = explode(';', $this->settings['websiteaudits']);
                    }

                    $email_data = array(
                            'to' => $this->settings['websiteaudits'],
                            'from_email' => $core->settings['maileremail'],
                            'from' => 'OCOS Mailer'
                    );

                    if($options['operationtype'] == 'updateversion') {
                        $email_data['subject'] = $lang->sprint($lang->modifynotification_subject, $this->prevversion['title']);
                        $email_data['message'] = $lang->sprint($lang->modifynotification_body, $this->prevversion['title'], //1
                                similar_text($this->prevversion['title'], $this->news['title']), //2
                                $this->news['title'], //3
                                similar_text($this->prevversion['summary'], $this->news['summary']), //4
                                $this->news['summary'], //5
                                similar_text($this->prevversion['bodyText'], $this->news['bodyText']), //6
                                get_stringdiff($this->oldnews['bodyText'], $this->news['bodyText'])//7
                        );
                    }
                    else {
                        $email_data['subject'] = $lang->sprint($lang->newnotification_subject, $this->news['title']);
                        $email_data['message'] = $lang->sprint($lang->newnotification_body, $this->news['title'], $this->news['summary'], $this->news['bodyText']);
                    }
                    /* Attach new attachments to the message and indicate that in the message body */
                    //HERE

                    $mail = new Mailer($email_data, 'php');
                }
                /* Inform audits about the change, and request approval - END */

                /* Upload related files - START */
                $ftp_settings = array('server' => $this->settings['ftpserver'], 'username' => $this->settings['ftpusername'], 'password' => $this->settings['ftppassword']);
                $upload = new Uploader('', array(), array(), 'constructonly');
                $upload->establish_ftp($ftp_settings);

                /* Upload news images - Start */
                $upload->set_upload_path($this->settings['newsimagespath']);
                $allowed_types_newsimages = array('image/jpeg', 'image/gif', 'image/png');

                foreach($newsimages as $key_link => $link) {
                    $link = parse_url($link);
                    $path_info = pathinfo($link['path']);
                    $file_info = finfo_open(FILEINFO_MIME_TYPE);

                    $upload->set_options('newsimage', array('newsimage' => array('type' => finfo_file($file_info, $link['path']), 'name' => $path_info['basename'], 'tmp_name' => $link['path'], 'size' => filesize($link['path']))), $allowed_types_newsimages, 'ftp', 5242880, 0, 0); //5242880 bytes = 5 MB (1024)
                    finfo_close($file_info);

                    $upload->process_file();
                    @unlink($link['path']);
                }
                /* Upload news images - End */
                $upload->close_ftp();

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

    private function read($id, $simple = false) {
        global $db;

        if(empty($id)) {
            return false;
        }

        $query_select = 'cn.*';
        if($simple == true) {
            $query_select = 'cmsnid, title, summary';
        }

        return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."cms_news cn JOIN ".Tprefix."cms_news_relatedcategories cnrc ON (cnrc.cmsnid=cn.cmsnid) JOIN ".Tprefix."cms_contentcategories cnc ON(cnc.cmsccid=cnrc.cmsccid) WHERE cn.cmsnid=".$db->escape_string($id)));
    }

    public function get() {
        
    }

    private function prepare_newsimages(&$newsimages) {
        $newsimages = explode(';', $newsimages);
        foreach($newsimages as $key_link => $link) {
            if(empty($link)) {
                unset($newsimages[$key_link]);
                continue;
            }

            if(!strstr($this->news['bodyText'], $link)) {
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
    public function get_multiplenews() {
        global $db, $core;

        $sort_query = 'ORDER BY cn.title ASC, cn.version DESC';
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

        if(isset($core->input['filterby'], $core->input['filtervalue'])) {
            $attributes_filter_options['title'] = array('title' => 'cn.');

            if($attributes_filter_options['title'][$core->input['filterby']] == 'int') {
                $filter_value = ' = "'.$db->escape_string($core->input['filtervalue']).'"';
            }
            else {
                $filter_value = ' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
            }
            $filter_where = ' WHERE '.$db->escape_string($attributes_filter_options['title'][$core->input['filterby']].$core->input['filterby']).$filter_value;
        }


        $news_query = $db->query("SELECT cn.cmsnid, cn.version, cn.isPublished, cn.isFeatured, cn.lang, cn.hits, cn.title, cn.summary, cn.createDate AS date, u.displayname as creator 
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

}
?>