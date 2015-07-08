<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * CMS News Class
 * $id: CmsPages_class.php
 * Created:			@tony.assaad	August 24, 2012 | 10:53 PM
 * Last Update: 	@zaher.reda		October 02, 2012 | 10:59  AM
 */

class CmsPages extends Cms {
    protected $status = 0;
    private $page = array();

    public function __construct($id = '', $simple = false) {
        if(isset($id) && !empty($id)) {
            $this->page = $this->read($id, $simple);
        }
    }

    public function create($data, array $options = array()) {
        global $db, $log, $core, $errorhandler, $lang;
        $this->page = $data;

        $this->category = $this->page['category'];

        /* if the action is edit here we call the add and pass the [options type] function if the action type is do_editpage */
        if(empty($this->page['title'])) {
            $this->status = 1;
            return false;
        }

        /* Check if news with same title created by anyone */
        if($options['operationtype'] != 'updateversion') {
            if(value_exists('cms_pages', 'title', $this->page['title'])) {
                $this->status = 2;
                return false;
            }
        }

        /* Closing date can be empty, means page doesn't expire */
        if(isset($this->page['publishDate']) && !empty($this->page['publishDate'])) {
            $this->page['publishDate'] = strtotime($this->page['publishDate']);
        }
        $this->page['dateCreated'] = TIME_NOW;

        if(empty($this->page['alias'])) {
            $this->page['alias'] = $this->page['title'];
        }

        $this->page['alias'] = parent::generate_alias($this->page['alias']);
        $this->page['title'] = $core->sanitize_inputs($this->page['title'], array('removetags' => true));
        $this->page['bodyText'] = $core->sanitize_inputs($this->page['bodyText'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true));
        $this->page['createdBy'] = $core->user['uid'];
        $this->page['isPublished'] = 1;
        if($core->usergroup['cms_canPublishNews'] == 0) {
            $this->page['isPublished'] = 0;
        }

        /* Set appropriate version - START */
        if($options['operationtype'] == 'updateversion') {
            //$cmspid['cmspid'] = $db->fetch_field($db->query("SELECT MAX(cmspid) as cmspid FROM ".Tprefix."cms_pages"), 'cmspid');
            $this->page['dateModified'] = TIME_NOW;
            $this->page['modifiedBy'] = $core->user['uid'];
            $this->prevversion = $this->get_lastversion_page($this->page['alias']);
        }

        if(is_array($this->prevversion)) {
            if(similar_text($this->page['bodyText'], $this->prevversion['bodyText']) > 70) {
                $this->page['version'] = $this->prevversion['version'] + 0.1;
            }
            else {
                $this->page['version'] = $this->prevversion['version'] + 1;
            }
        }
        else {
            $this->page['version'] = 1.0;
        }
        /* Set appropriate version - END */

        $pageimages = $this->page['uploadedImages']; /* Get the from the hidden field */
        $this->prepare_pageimages($pageimages);
        $this->page['bodyText'] = $this->replace_imageslinks($this->page['bodyText'], $pageimages);

        /* Insert page - START */
        if(is_array($this->page)) {
            $query = $db->insert_query('cms_pages', $this->page);
            if($query) {
                $this->status = 0;
                $cmspid = $this->page['cmspid'] = $db->last_id();
                $log->record($this->page['cmspid']);

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

                    $mail = new Mailer($email_data, 'php');
                }
                /* Inform audits about the change, and request approval - END */

                /* Upload related files - START */
//                $ftp_settings = array('server' => $this->settings['ftpserver'], 'username' => $this->settings['ftpusername'], 'password' => $this->settings['ftppassword']);
//                $upload = new Uploader('', array(), array(), 'constructonly');
//                $upload->establish_ftp($ftp_settings);
//
//                /* Upload page images - Start */
//                $upload->set_upload_path($this->settings['pageimagespath']);
//                $allowed_types_pageimages = array('image/jpeg', 'image/gif', 'image/png');
//                if(is_array($pageimages)) {
//                    foreach($pageimages as $key_link => $link) {
//
//                        $link = parse_url($link);
//                        $path_info = pathinfo($link['path']);
//                        $file_info = finfo_open(FILEINFO_MIME_TYPE);
//
//                        $upload->set_options('pageimage', array('pageimage' => array('type' => finfo_file($file_info, $link['path']), 'name' => $path_info['basename'], 'tmp_name' => $link['path'], 'size' => filesize($link['path']))), $allowed_types_pageimages, 'ftp', 5242880, 0, 0); //5242880 bytes = 5 MB (1024)
//                        finfo_close($file_info);
//
//                        $upload->process_file();
//                        @unlink($link['path']);
//                    }
//
//                    /* Upload page images - End */
//                    $upload->close_ftp();
//                }
                /* Upload related files - END */
                return true;
            }
        }
    }

    private function get_lastversion_page($alias, array $options = array()) {
        global $db;
        if(isset($options['exclude'])) {
            $exclude_querystring = ' AND cmspid NOT IN ('.implode(',', $options['exclude']).')';
        }
        return $db->fetch_assoc($db->query("SELECT title, alias, bodyText,version,metaDesc FROM ".Tprefix."cms_pages WHERE alias='".$db->escape_string($alias)."'{$exclude_querystring} ORDER BY version DESC"));
    }

    private function read($id, $simple = false) {
        global $db;

        if(empty($id)) {
            return false;
        }

        $query_select = '*';
        if($simple == true) {
            $query_select = 'cmspid, title, alias';
        }

        return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."cms_pages WHERE cmspid=".$db->escape_string($id)));
    }

    public function get() {
        return $this->page;
    }

    private function prepare_pageimages(&$pageimages) {
        $pageimages = explode(';', $pageimages);
        foreach($pageimages as $key_link => $link) {
            if(empty($link)) {
                unset($pageimages[$key_link]);
                continue;
            }

            if(!strstr($this->page['bodyText'], $link)) {
                $link = parse_url($link);
                unset($pageimages[$key_link]);
                @unlink($link['path']);
            }
        }
    }

    private function replace_imageslinks($string, $pageimages) {
        foreach($pageimages as $key_link => $link) {
            $link_info = parse_url($link);
            $path_info = pathinfo($link_info['path']);
            $string = str_replace($link, '{$cms->settings[pageimagespath]}/'.$path_info['basename'], $string);
        }
        return $string;
    }

    public function get_multiplepages($filter_where) {
        global $db, $core;

        $sort_query = 'ORDER BY cp.title ASC, cp.version DESC';
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
//            $attributes_filter_options['title'] = array('title' => 'cp.');
//
//            if($attributes_filter_options['title'][$core->input['filterby']] == 'int') {
//                $filter_value = ' = "'.$db->escape_string($core->input['filtervalue']).'"';
//            }
//            else {
//                $filter_value = ' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
//            }
//
//            $filter_where = ' WHERE '.$db->escape_string($attributes_filter_options['title'][$core->input['filterby']].$core->input['filterby']).$filter_value;
//        }
        $pages_query = $db->query("SELECT cp.cmspid, cp.version, cp.alias, cp.isPublished, cp.lang, cp.hits, cp.title, cp.dateCreated, u.displayname as creator
								FROM  ".Tprefix."cms_pages cp
								JOIN ".Tprefix."users u ON (u.uid=cp.createdBy)
								{$filter_where}
								{$sort_query}
								LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

        if($db->num_rows($pages_query) > 0) {
            while($page = $db->fetch_assoc($pages_query)) {
                $pages[$page['cmspid']] = $page;
            }
            return $pages;
        }
        return false;
    }

    public function get_status() {
        return $this->status;
    }

    public function __get($name) {
        if(isset($this->page[$name])) {
            return $this->page[$name];
        }
        return false;
    }

    public function get_column($column, $filters = '', array $configs = array()) {
        $data = new DataAccessLayer('CmsPages', 'cms_pages', 'cmspid');
        return $data->get_column($column, $filters, $configs);
    }

    public function get_latest_pages() {
        $aliases = CmsPages::get_column('alias');
        if(!is_array($aliases)) {
            return null;
        }
        foreach($aliases as $alias) {
            $pages[] = CmsPages::get_lastversion_page($alias);
        }
        if(is_array($pages)) {
            return $pages;
        }
        return null;
    }

}
?>