<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Create Web Page
 * $id:createwebpage.php
 * Created:			@tony.assaad	August 24, 2012 | 10:53 PM
 * Last Update: 	@tony.assaad	August 24, 2012 | 02:55  PM
 */

if($core->usergroup['cms_canAddPage'] == 0) {
    //error($lang->sectionnopermission);
    //exit;
}

if(!$core->input['action']) {
    if($core->usergroup['crm_canPublishNews'] == 1) {
        $publish_page = '<div style="display:inline-block">'.$lang->ispublish.'</div><div style="display: table-cell; padding:10px;"><input name="page[isPublished]" type="checkbox" value="1"></div>';
    }
    $robots_list = parse_selectlist('page[robotsRule]', 1, array("INDEX,FOLLOW" => "INDEX,FOLLOW", "NOINDEX,FOLLOW" => "NOINDEX,FOLLOW", "INDEX,NOFOLLOW" => "INDEX,NOFOLLOW", "NOINDEX,NOFOLLOW" => "NOINDEX,NOFOLLOW"), 0);
    $content_categories = CmsContentCategories::get_data('title IS NOT NULL', array('returnarray' => true));
    if($core->input['type'] == 'edit') {
        $actiontype = 'edit';
        $lang->createwebpage = $lang->editwebpage;
        $pageid = $db->escape_string($core->input['id']);
        $cms_page = new CmsPages($pageid);  /* call the page object and the pageid to the constructor to read the single page */
        $page = $cms_page->get();
        if(isset($page['publishDate']) && !empty($page['publishDate'])) {
            $page['publishDate_output'] = date($core->settings['dateformat'], $page['publishDate']);
        }
        else {
            $page['publishDate_output'] = date($core->settings['dateformat'], TIME_NOW);
        }
        $pagecategories_list = parse_selectlist('page[category]', 5, $content_categories, $page['category']);
    }
    else {
        $actiontype = 'add';
        $pagecategories_list = parse_selectlist('page[category]', 5, $content_categories, $page['category']);
        $page['publishDate_output'] = date($core->settings['dateformat'], TIME_NOW);
    }

    eval("\$createpage =\"".$template->get('cms_page_create')."\";");
    output_page($createpage);
}
else {
    if($core->input['action'] == 'do_addpage' || $core->input['action'] == 'do_editpage') {
        $core->input['pages']['attachments'] = $_FILES;
        $cms_page = new CmsPages();
        $options = array();
        if($core->input['action'] == 'do_editpage') {
            $options['operationtype'] = 'updateversion';
        }

        $cms_page->create($core->input['page'], $options);

        switch($cms_page->get_status()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            case 2:
                output_xml("<status>false</status><message>{$lang->pageexists}</message>");
                break;
            case 3:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                break;
        }
    }
    elseif($core->input['action'] == 'do_uploadtmpimage') {
        $filepath = './tmp/';

        $allowed_types = array('image/jpeg', 'image/gif', 'image/png');
        $upload = new Uploader('file', $_FILES, $allowed_types, 'putfile', 5242880, 0, 1); //5242880 bytes = 5 MB (1024)

        $upload->set_upload_path($filepath);
        $upload->process_file();

        $fileinfo = $upload->get_fileinfo();

        echo stripslashes(json_encode(array('filelink' => DOMAIN.'/tmp/'.$fileinfo['name'])));
    }
}
?>