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
    $content_categories = CmsContentCategories::get_data('title IS NOT NULL', array('returnarray' => true, 'simple' => false));
    if($core->input['type'] == 'edit') {
        $actiontype = $lang->edit;
        $lang->createwebpage = $lang->editwebpage;
        $pageid = $db->escape_string($core->input['id']);
        $cms_page = new CmsPages($pageid);  /* call the page object and the pageid to the constructor to read the single page */
        $url = 'http://'.$core->settings['websitedir'].'/general/'.$cms_page->alias.'/'.$cms_page->cmspid.'/'.$cms_page->token.'/preview';
        $preview_display = 'display:inline-block';
        $page = $cms_page->get();
        $pagecategories_list = parse_selectlist('page[category]', 5, $content_categories, $page['category']);
        $page['publishDate_output'] = date($core->settings['dateformat'], $page['publishDate']);
        $existing_pagehighlights = CmsPagesHighlights::get_data(array('cmspid' => $cms_page->cmspid), array('returnarray' => true));
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
        $base_page = new CmsPages($page['baseVersionId'], false);
        if(is_object($base_page)) {
            $base_page = $base_page->get();
            if(!empty($base_page['version'])) {
                $baseversion['display'] = 'display:block';
                $page['baseVersion_outpt'] = '<a href="index.php?module=cms/managewebpage&type=edit&id='.$base_page[cmspid].'" target="_blank">'.$base_page['title'].', Version '.$base_page['version'].'</a>';
            }
        }
        $page['version_output'] = $page[title].', '.$lang->version.' '.$page[version];
    }
    else {
        $baseversion['display'] = $preview_display = 'display:none';
        $actiontype = 'Add';
        $url = '';
        $pagecategories_list = parse_selectlist('page[category]', 5, $content_categories, $page['category']);
        $highlights = CmsHighlights::get_data(array('isEnabled' => '1'), array('returnarray' => true));
        if(is_array($highlights)) {
            foreach($highlights as $highlight) {
                $highlights_list .= ' <tr class="'.$rowclass.'">';
                $highlights_list .= '<td><input id="highlightsfilter_check_'.$highlight->cmshid.'" type="checkbox" value="'.$highlight->cmshid.'" name="page[highlights]['.$highlight->cmshid.']">'.$highlight->title.' - '.$highlight->type;
            }
        }
    }
    eval("\$higlightsbox =\"".$template->get('cms_manage_pagehighlight')."\";");
    eval("\$createpage =\"".$template->get('cms_page_create')."\";");
    output_page($createpage);
}
else {
    if($core->input['action'] == 'do_Addpage' || $core->input['action'] == 'do_Editpage') {
        $core->input['pages']['attachments'] = $_FILES;
        $cms_page = new CmsPages();
        $options = array();
        if($core->input['action'] == 'do_Editpage') {
            $options['operationtype'] = 'updateversion';
        }

        $cms_page->create($core->input['page'], $options);

        switch($cms_page->get_status()) {
            case 0:
                if(isset($core->input['highlights']) && !empty($core->input['highlights'])) {
                    $higlights = $core->input['highlights'];
                    $higlight_ar['cmspid'] = $cms_page->cmspid;
                    $existing_highlightsids = CmsPagesHighlights::get_column('cmshid', array('cmspid' => $cms_page->cmspid), array('returnarray' => true));
                    foreach($higlights as $higlight) {
                        $higlight_ar['cmshid'] = $higlight;
                        if(is_array($existing_highlightsids)) {
                            if(in_array($higlight_ar['cmshid'], $existing_highlightsids)) {
                                $existingticked = $higlight_ar['cmshid'];
                                continue;
                            }
                        }
                        $pagehighlight = new CmsPagesHighlights();
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
                            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                            exit;
                        }
                        $preview_output = '<script>$(\'div[id="preview"]\').show();$(\'div[id="preview"]\').css(\'display\',\'inline-block\');$(\'a[id="preview_link"]\').attr("href", "http://'.$core->settings['websitedir'].'/general/'.$cms_page->alias.'/'.$cms_page->cmspid.'/'.$cms_page->token.'/preview");</script>';
                        output_xml("<status>true</status><message>{$lang->successfullysaved}<![CDATA[{$preview_output}]]></message>");
                        exit;
                    }
                }
                $preview_output = '<script>$(\'div[id="preview"]\').show();$(\'div[id="preview"]\').css(\'display\',\'inline-block\');$(\'a[id="preview_link"]\').attr("href", "http://'.$core->settings['websitedir'].'/general/'.$cms_page->alias.'/'.$cms_page->cmspid.'/'.$cms_page->token.'/preview");</script>';
                output_xml("<status>true</status><message>{$lang->successfullysaved}<![CDATA[{$preview_output}]]></message>");
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
    elseif($core->input['action'] == 'togglepublish') {
        if($core->usergroup['cms_canPublishNews'] == 1 && !empty($core->input['id'])) {
            $page = new CmsPages($core->input['id']);
            $db->update_query('cms_pages', array('isPublished' => !$page->isPublished), 'cmspid='.intval($core->input['id']));
        }
        redirect('index.php?module=cms/listwebpages');
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