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
    if($core->usergroup['cms_canPublishNews'] == 1) {
        $publish_news = '<div style="display:inline-block">'.$lang->ispublish.'</div><div style="display: table-cell; padding:10px;"><input name="news[isPublished]" type="checkbox" value="1"></div>';
    }
    $content_categories = CmsContentCategories::get_data('title IS NOT NULL');

    if(isset($core->input['newsid']) && !empty($core->input['newsid'])) {
        $actiontype = 'edit';
        $cms_news = new CmsNews(intval($core->input['newsid']), false);
        $url = 'http://'.$core->settings['websitedir'].'/news/'.$cms_news->alias.'/'.base64_encode($cms_news->cmsnid).'/'.$cms_news->token.'/preview';
        $preview_display = 'display:inline-block';
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

        // $newscategories = get_specificdata('cms_contentcategories', array('cmsccid', 'name'), 'cmsccid', 'name', 'name');
        $newscategories_list = parse_selectlist('news[categories]', 5, $content_categories, $news['category']);
        $existing_pagehighlights = CmsNewsHighlights::get_data(array('cmsnid' => $cms_news->cmsnid), array('returnarray' => true));
        if(is_array($existing_pagehighlights)) {
            foreach($existing_pagehighlights as $pagehighlight) {
                $highlight = $pagehighlight->get_CmsHighlights();
                if(is_object($highlight)) {
                    $existing_highid[] = $highlight->cmshid;
                    $highlights_list .= ' <tr class="'.$rowclass.'">';
                    $highlights_list .= '<td><input id="highlightsfilter_check_'.$highlight->cmshid.'" type="checkbox" checked="checked" value="'.$highlight->cmshid.'" name="highlights['.$highlight->cmshid.']">'.$highlight->title.' - '.$highlight->type;
                }
            }
        }

        $highlights = CmsHighlights::get_data(array('isEnabled' => '1'), array('returnarray' => true));
        if(is_array($highlights)) {
            foreach($highlights as $highlight) {
                if(is_array($existing_highid)) {
                    if(in_array($highlight->cmshid, $existing_highid)) {
                        continue;
                    }
                }
                $highlights_list .= ' <tr class="'.$rowclass.'">';
                $highlights_list .= '<td><input id="highlightsfilter_check_'.$highlight->cmshid.'" type="checkbox" value="'.$highlight->cmshid.'" name="highlights['.$highlight->cmshid.']">'.$highlight->title.' - '.$highlight->type;
            }
        }

        $baseversion['display'] = 'display:none';
        $base_news = new CmsNews($news['baseVersionId'], false);
        if(is_object($base_news)) {
            $base_news = $base_news->get();
            if(!empty($base_news['version'])) {
                $baseversion['display'] = 'display:block';
                $news['baseVersion_outpt'] = '<a href="index.php?module=cms/managenews&type=edit&newsid='.$base_news[cmsnid].'" target="_blank">'.$base_news['title'].', Version '.$base_news['version'].'</a>';
            }
        }
        $news['version_output'] = $lang->version.' '.$news[version];
    }
    else {
        $baseversion['display'] = $preview_display = 'display:none';
        $actiontype = 'add';
        $url = 'none';
        $newscategories_list = parse_selectlist('news[categories]', 5, $content_categories, '');
        $highlights = CmsHighlights::get_data(array('isEnabled' => '1'), array('returnarray' => true));
        if(is_array($highlights)) {
            foreach($highlights as $highlight) {
                $highlights_list .= ' <tr class="'.$rowclass.'">';
                $highlights_list .= '<td><input id="highlightsfilter_check_'.$highlight->cmshid.'" type="checkbox" value="'.$highlight->cmshid.'" name="page[highlights]['.$highlight->cmshid.']">'.$highlight->title.' - '.$highlight->type;
            }
        }
    }

    if($core->usergroup['cms_canPublishNews'] == 1) {
        $publish_news = '<div style="display:table-cell;">'.$lang->ispublish.'</div><div style="display: table-cell; padding:10px;"><input name="news[isPublished]" type="checkbox" value=1" '.$checkedboxes[isPublished].'"></div>';
    }

    $robots_list = parse_selectlist('news[robotsRule]', 1, array("INDEX,FOLLOW" => "INDEX,FOLLOW", "NOINDEX,FOLLOW" => "NOINDEX,FOLLOW", "INDEX,NOFOLLOW" => "INDEX,NOFOLLOW", "NOINDEX,NOFOLLOW" => "NOINDEX,NOFOLLOW"), 0);
    eval("\$higlightsbox =\"".$template->get('cms_manage_pagehighlight')."\";");
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
                if(isset($core->input['highlights']) && !empty($core->input['highlights'])) {
                    $higlights = $core->input['highlights'];
                    $higlight_ar['cmsnid'] = $cms_news->cmsnid;
                    $existing_highlightsids = CmsNewsHighlights::get_column('cmshid', array('cmsnid' => $cms_news->cmsnid), array('returnarray' => true));
                    foreach($higlights as $higlight) {
                        $higlight_ar['cmshid'] = $higlight;
                        if(is_array($existing_highlightsids)) {
                            if(in_array($higlight_ar['cmshid'], $existing_highlightsids)) {
                                $existingticked = $higlight_ar['cmshid'];
                                continue;
                            }
                        }
                        $pagehighlight = new CmsNewsHighlights();
                        $pagehighlight->set($higlight_ar);
                        $pagehighlight = $pagehighlight->save();
                        $errorcodes[] = $pagehighlight->get_errorcode();
                    }
                    if(is_array($existingticked)) {
                        $deleted_highlights = array_diff($higlights, $existingticked);
                        if(is_array($deleted_highlights)) {
                            foreach($deleted_highlights as $cmshid) {
                                $query = $db->delete_query(CmsPagesHighlights::TABLE_NAME, 'cmshid ='.$cmshid.' AND cmspid ='.$cms_page->cmspid);
                                if($query) {
                                    continue;
                                }
                                $errorcodes[] = 2;
                            }
                        }
                    }
                    if(is_array($errorcodes)) {
                        $errorcodes = array_filter(array_unique($errorcodes));
                        if(in_array('1', $errorcodes) || in_array('2', $errorcodes)) {
                            $output_class = 'red_text';
                            $output_message = $lang->errorsaving;
                            exit;
                        }
                    }
                }
                $output_class = 'green_text';
                $output_message = $lang->successfullysaved;
                $preview_output = '';
                $url = 'http://'.$core->settings['websitedir'].'/news/'.$cms_news->alias.'/'.base64_encode($cms_news->cmsnid).'/'.$cms_news->token.'/1';
                ?>
                <script language = "javascript" type = "text/javascript">
                    $(function () {
                        top.$('div[id="preview"]').show();
                        top.$('div[id="preview"]').css('display', 'inline-block');
                        top.$('a[id="preview_link"]').attr("href", "<?php echo $url;?>");
                        top.$("#upload_Result").html("<span class='<?php echo $output_class;?>'><?php echo $output_message;?></span>");
                    });
                </script>
                <?php
                exit;
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
            $(function () {
                top.$("#upload_Result").html("<span class='<?php echo $output_class;?>'><?php echo $output_message;?></span>");
            });
        </script>
        <?php
    }
    elseif($core->input['action'] == 'togglepublish') {
        if($core->usergroup['cms_canPublishNews'] == 1 && !empty($core->input['id'])) {
            $news = new CmsNews($core->input['id'], false);
            $db->update_query(CmsNews::TABLE_NAME, array('isPublished' => !$news->isPublished), CmsNews::PRIMARY_KEY.'='.intval($core->input['id']));
        }
        redirect('index.php?module=cms/listnews');
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
