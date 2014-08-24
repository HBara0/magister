<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Manage News
 * $module: CMS
 * $id: managenews.php
 * Created By: 		@tony.assaad		Augusst 13, 2012 | 3:30 PM
 * Last Update: 	@zaher.reda			August 29, 2012 | 12:13 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['cms_canAddNews'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    if($core->usergroup['crm_canPublishNews'] == 1) {
        $publish_news = '<div style="display:inline-block">'.$lang->ispublish.'</div><div style="display: table-cell; padding:10px;"><input name="news[isPublished]" type="checkbox" value="1"></div>';
    }

    if(isset($core->input['newsid']) && !empty($core->input['newsid'])) {
        $actiontype = 'edit';
        $cms_news = new CmsNews(intval($core->input['newsid']), false);
        $news = $cms_news->get();

        if($news['isFeatured'] == 1) {
//$checkedboxes['isFeatured'] = "checked='checked'";
        }
        $news['publishDate_output'] = date($core->settings['dateformat'], $news['publishDate']);

        $lang_array = array('en', 'fr');
        foreach($lang_array as $key) {
            if($news['lang'] == $key) {
                $seletcted[$key] = "selected='selected'";
            }
        }

        $checkboxes_index = array('isFeatured', 'isPublished');
        foreach($checkboxes_index as $key) {
            if($news[$key] == 1) {
                $checkedboxes[$key] = "checked='checked'";
            }
        }

        $newscategories = get_specificdata('cms_contentcategories', array('cmsccid', 'name'), 'cmsccid', 'name', 'name');
        $newscategories_list = parse_selectlist('news[categories]', 5, $newscategories, $news['category']);
    }
    else {
        $actiontype = 'add';
        $newscategories_list = parse_selectlist('news[categories]', 5, array('1' => 'featured news', '2' => 'afilliates related news'), '');
    }

    if($core->usergroup['crm_canPublishNews'] == 1) {
        $publish_news = '<div style="display:table-cell;">'.$lang->publish.'</div><div style="display: table-cell; padding:10px;"><input name="news[isPublished]" type="checkbox" value=1"{$checkboxes[isPublished]}"></div>';
    }

    eval("\$addnews =\"".$template->get('cms_news_add')."\";");
    output_page($addnews);
}
else {
    if($core->input['action'] === 'do_addnews' || $core->input['action'] === 'do_editnews') {
        $upload_param['upload_allowed_types'] = array('image/jpeg', 'image/gif', 'image/png', 'application/zip', 'application/pdf', 'application/x-pdf', 'application/msword', 'application/vnd.ms-powerpoint', 'text/plain', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation');
        /* Verify before passing to object - to be done */

        $core->input['news']['attachments'] = $_FILES;
        $cms_news = new CmsNews();
        $options = array();
        if($core->input['action'] == 'do_editnews') {
            $options['operationtype'] = 'updateversion';
        }

        $cms_news->add($core->input['news'], $options);
        echo $headerinc;

        switch($cms_news->get_status()) {
            case 0:
                $output_class = 'green_text';
                $output_message = $lang->successfullysaved;
                break;
            case 1:
                $output_class = 'red_text';
                $output_message = $lang->fillallrequiredfields;
                break;
            case 2:
                $output_class = 'red_text';
                $output_message = $lang->newsexists;
                break;
            case 3:
                $output_class = 'red_text';
                $output_message = $lang->errorsaving;
                break;
        }
        ?>
        <script language="javascript" type="text/javascript">
            $(function() {
                top.$("#upload_Result").html("<span class='<?php echo $output_class;?>'><?php echo $output_message;?></span>");
            });
        </script>
        <?php
    }
    elseif($core->input['action'] = 'do_uploadtmpimage') {
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
