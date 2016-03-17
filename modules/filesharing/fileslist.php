<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Upload a Shared File
 * $module: filesharing
 * $id: uploadfile.php
 * Created:	   	 	@najwa.kassem   Jan 31, 2010 | 10:10 AM
 * Last Update: 	@zaher.reda 	April 27, 2012 | 04:49 PM
 */

if(!defined("DIRECT_ACCESS")) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['filesharing_canViewSharedfiles'] == 0) {
    error($lang->sectionnopermission);
    exit;
}
if(!$core->input['action']) {
    if(value_exists('filesfolder_viewrestriction', 'ffid', $core->input['ffid'], 'uid="'.$core->user['uid'].'" AND noRead="1"')) {
        redirect('index.php?module=filesharing/fileslist');
    }

    $sort_query = 'filetitle ASC';
    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $core->input['sortby'].' '.$core->input['order'];
    }
    $sort_url = sort_url();

    $limit_start = 0;
    $multipage_where = ' f.isShared = 1';

    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }

    if($core->usergroup['filesharing_canViewAllFilesfolder'] == 0) {
        $folder_query_where = " AND ffid NOT IN (SELECT ffid FROM ".Tprefix."filesfolder_viewrestriction WHERE uid={$core->user[uid]} AND noRead=1)";
        $file_query_where = " AND f.fid NOT IN (SELECT fid FROM ".Tprefix."files_viewrestriction WHERE uid={$core->user[uid]})";
        $multipage_where .= $file_query_where;
    }
    else {
        $folder_query_where = '';
    }

    $url_fcid = '';
    if(isset($core->input['fcid'])) {
        $fcid = $db->escape_string($core->input['fcid']);

        $filter_where .= " AND category ={$fcid}";
        $url_fcid = "&amp;fcid={$fcid}";
    }

    if(isset($core->input['view'])) {
        $url_view = '&amp;view='.$core->input['view'];
    }
    if(isset($core->input['ffid']) && !empty($core->input['ffid'])) {
        $ffid = $db->escape_string($core->input['ffid']);
        $url_ffid = "&amp;ffid={$ffid}";
        $directory_details = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."filesfolder WHERE ffid='{$ffid}'{$folder_query_where}"));
        $files_list .= '<tr><td colspan="8" style="padding: 0px;"><a href="index.php?module=filesharing/fileslist"><img src="./images/icons/home.gif" alt="'.$lang->home.'" border="0" style="margin: 0px; vertical-align:bottom;"/></a> / <a href="index.php?module=filesharing/fileslist'.$url_fcid.'&amp;ffid='.$directory_details['parent'].$url_view.'"><img src="./images/icons/upfolder.gif" alt="'.$lang->updir.'" border="0" style="margin: 0px; vertical-align:bottom;"/></a> / <span style="font-weight: bold;">'.$directory_details['name'].'</span></td></tr>';

        if(!empty($directory_details['description'])) {
            $files_list .= '<tr><td colspan="8" style="font-style:italic; color: #666;">'.$directory_details['description'].'</td></tr>';
        }
    }
    else {
        $ffid = 0;
        $url_ffid = '';
    }

    $categories_query = get_specificdata('filescategories', array('fcid', 'title'), 'fcid', 'title', array('by' => 'title', 'sort' => 'ASC'), 1, 'isPublic=1');
    $categories_list = parse_selectlist('category', 1, $categories_query, 'fcid', 0, 'goToURL("index.php?module=filesharing/fileslist'.$url_ffid.'&amp;fcid="+$(this).val())').'';

    $multipage_where .= $filter_where.' AND f.ffid = '.$ffid;

    $folder_query = $db->query("SELECT * FROM ".Tprefix."filesfolder WHERE parent='{$ffid}'{$folder_query_where}");
    while($folder = $db->fetch_array($folder_query)) {
        $rowclass = alt_row($rowclass);
        if(strlen($folder['description']) > 40) {
            $description = '<a href="#description" id="showmore_folderdescription_'.$folder['ffid'].'">'.substr($folder['description'], 0, 40).'</a><span style="display:none;" id="folderdescription_'.$folder['ffid'].'">'.substr($folder['description'], 40).'</span>';
        }
        else {
            $description = $folder['description'];
        }

        if($core->user['uid'] == $folder['uid'] || $core->usergroup['filesharing_canSendAllFiles'] == 1) {
            $foldermail_icon = '<a href="#" id="sharefolderbymail_'.$folder['ffid'].'_filesharing/fileslist_loadpopupbyid"><img src="images/icons/send.gif"  alt="'.$lang->sharebyemail.'" border="0"></a>';
        }
        else {
            $foldermail_icon = '';
        }

        $files_list .= '<tr class="'.$rowclass.'"><td><img src="./images/icons/folder.gif" alt="'.$lang->folder.'" border="0" /></td><td><a href="index.php?module=filesharing/fileslist'.$url_fcid.'&amp;ffid='.$folder['ffid'].$url_view.'">'.$folder['name'].'</a><td>'.$description.'</td><td style="text-align:center;" colspan="3">&mdash;</td><td style="text-align:right;">'.$foldermail_icon.'</td></tr>';
    }
    if(isset($core->input['fid']) && !empty($core->input['fid'])) {
        if(!empty($file_query_where)) {
            unset($file_query_where);
        }
        $file_extra_where = ' AND f.fid='.$core->input['fid'].' ';
    }
    else {
        $versions_extra_where = ' AND fv.timeLine=(SELECT timeLine FROM fileversions fv2 WHERE fv2.fid=f.fid ORDER BY timeLine DESC LIMIT 0,1)';
    }
    $query = $db->query("SELECT *, c.title as category, f.title as filetitle,f.alias as filealias, fv.*
						FROM ".Tprefix."files f JOIN ".Tprefix."fileversions fv ON (f.fid=fv.fid)
						JOIN ".Tprefix."filescategories c ON (c.fcid=f.category)
						WHERE f.isShared = 1".$file_extra_where.$versions_extra_where."
                                                AND f.ffid = {$ffid}{$filter_where}{$file_query_where}
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

    if($core->input['view'] == 'thumbnails') {
        $files_list .= '<tr><td colspan="6" style="vertical-align:top;" id="gallery">';
        $headerinc .= '<script type="text/javascript" src="'.$core->settings['rootdir'].'/js/jquery.lightbox-0.5.pack.js"></script>';
        $headerinc .= '<link rel="stylesheet" type="text/css" href="'.$core->settings['rootdir'].'/css/jquery.lightbox-0.5.css" media="screen" />';
        $headerinc .= '<script type="text/javascript">$(function() {$("#gallery div a[rel=\'lightbox\']").lightBox();});</script>';
    }

    if($db->num_rows($query) > 0) {
        while($file = $db->fetch_assoc($query)) {
            if($core->input['view'] == 'thumbnails') {
                if(in_array(strtolower($file['type']), array('jpg', 'jpeg', 'png', 'gif'))) {
                    $thumbnail_link = 'index.php?module=filesharing/fileslist&amp;action=thumbnail&amp;file='.base64_encode(serialize(array('name' => $file['name'], 'type' => $file['type']))).'';
                    $rel_value = 'lightbox';
                }
                else {
                    if(file_exists('./images/filetypes/'.$file['type'].'.gif')) {
                        $thumbnail_link = './images/filetypes/'.$file['type'].'.gif';
                    }
                    else {
                        $thumbnail_link = './images/filetypes/txt.gif';
                    }
                    $rel_value = '';
                }
                eval("\$files_list .= \"".$template->get('filesharing_fileslist_thumbnails')."\";");
            }
            else {
                $rowclass = alt_row($rowclass);
                $mail_icon = $delete_icon = '';

                $file['date_output'] = date($core->settings['dateformat'], $file['timeLine']);

                if($core->user['uid'] == $file['uid'] || $core->usergroup['filesharing_canSendAllFiles'] == 1) {
                    $tools = '<a href="#" id="sharebymail_'.$file['fvid'].'_filesharing/fileslist_loadpopupbyid"><img src="images/icons/send.gif"  alt="'.$lang->sharebyemail.'" border="0"></a>';
                }
                if($core->user['uid'] == $file['uid']) {
                    $delete_icon = '<a href="#" id="deletefilebox_'.$file['fid'].'_filesharing/fileslist_loadpopupbyid"><img src="images/invalid.gif"  alt="'.$lang->deletefile.'" border="0">&nbsp'.$lang->deletefile.'</a>';
                    $edit_icon = '<a href="index.php?module=filesharing/uploadfile&fid='.$file['fid'].'" title="'.$lang->edit.'"><img src="./images/icons/edit.gif" border=0 alt="'.$lang->edit.'"/>&nbsp'.$lang->edit.'</a>';
                    $view_versions = '<a href="index.php?module=filesharing/fileslist&ffid='.$file['ffid'].'&fid='.$file['fid'].'&referrer=viewversion" title="'.$lang->viewfileversions.'"><span class="glyphicon glyphicon-eye-open">'.$lang->viewfileversions.'</span></a>';
                    $mail_icon = '<a href="#" id="sharebymail_'.$file['fvid'].'_filesharing/fileslist_loadpopupbyid"><img src="images/icons/send.gif"  alt="'.$lang->sharebyemail.'" border="0">&nbsp'.$lang->sharebyemail.'</a>';

                    $tools = '<div class="btn-group" style="display:inline-block;">
                             <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width:40px;">
                                    <span class=" glyphicon glyphicon-cog"></span> <span class="caret"></span>
                             </button>
                                <ul class="dropdown-menu">
                                    <li>'.$edit_icon.'</li>
                                    <li>'.$delete_icon.'</li>
                                    <li>'.$view_versions.'</li>
                                    <li>'.$mail_icon.'</li>
                                </ul>
                             </div>';
                }

                if(isset($core->input['fid']) && !empty($core->input['fid'])) {
                    unset($tools);
                }
                $file['size'] = format_size($file['size']);
                if(file_exists('./images/filetypes/'.$file['type'].'.gif')) {
                    $type_icon = '<img src = "./images/filetypes/'.$file['type'].'.gif" alt = "'.$file['type'].'" border = "0" />';
                }
                else {
                    $type_icon = '<img src = "./images/filetypes/txt.gif" alt = "'.$lang->unknowntype.'" border = "0" />';
                }

                if(strlen($file['description']) > 40) {
                    $description = '<a href = "#description" id = "showmore_description_'.$file['fid'].'">'.substr($file['description'], 0, 40).'</a><span style = "display:none;" id = "description_'.$file['fid'].'">'.substr($file['description'], 40).'</span>';
                }
                else {
                    $description = $file['description'];
                }
                if(isset($core->input['referrer']) && $core->input['referrer'] == 'viewversion') {
                    $url_fileversion = '&amp;fvid='.$file['fvid'];
                }
                eval("\$files_list .= \"".$template->get('filesharing_fileslist_filerow')."\";");
            }
        }

        if($core->input['view'] == 'thumbnails') {
            echo '</td></tr>';
        }

        $multipages = new Multipages('files f JOIN '.Tprefix.'fileversions fv ON (f.fid = fv.fid) JOIN '.Tprefix.'filescategories c ON (c.fcid = f.category)', $core->settings['itemsperlist'], $multipage_where);
        $files_list .= '<tr><td colspan = "8">'.$multipages->parse_multipages().'</td></tr>';
    }
    else {
        $files_list .= '<tr><td colspan = "8" style = "text-align:center;">'.$lang->nomatchfound.'</td></tr>';
    }

    if($core->input['view'] == 'thumbnails') {
        $change_view_icon = 'list_view.gif';
        $change_view_url = preg_replace("/&view=[A-Za-z]+/i", '&view = list', $sort_url);
    }
    else {
        $change_view_icon = 'thumbnail_view.gif';
        if(isset($core->input['view'])) {
            $change_view_url = preg_replace("/&view=[A-Za-z]+/i", '&view = thumbnails', $sort_url);
        }
        else {
            $change_view_url = $sort_url.'&view = thumbnails';
        }
    }

    eval("\$viewfiles_page = \"".$template->get('filesharing_fileslist')."\";");
    output_page($viewfiles_page);
}
else {
    if($core->input['action'] == 'download') {
        if(!isset($core->input['alias']) || empty($core->input['alias'])) {
            redirect($_SERVER['HTTP_REFERER']);
        }
        $file = Files::get_data(array('alias' => $core->input['alias']));

        $fids = get_specificdata('files_viewrestriction', 'fid', 'fid', 'fid', '', 0, "uid={$core->user[uid]}");
        if(is_array($fids)) {
            if(in_array($file->fid, $fids)) {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        if(isset($core->input['fvid'])) {
            $fvid = $core->input['fvid'];
        }
        else {
            $query = $db->query("SELECT * FROM fileversions WHERE fid=".$file->fid." ORDER BY timeLine DESC LIMIT 0,1");
            if($db->num_rows($query) > 0) {
                while($fileversion = $db->fetch_assoc($query)) {
                    $fvid = $fileversion['fvid'];
                }
            }
        }
        $path = ROOT.'/uploads/sharedfiles';
        $download = new Download('fileversions', 'name', array('fvid' => $fvid), $path);
        $download->stream_file();
        $log->record($file->fid);
    }
    elseif($core->input['action'] == 'thumbnail') {
        if(!isset($core->input['file']) || empty($core->input['file'])) {
            redirect($_SERVER['HTTP_REFERER']);
        }

        $file = unserialize(base64_decode($core->input['file']));

        if(!file_exists('./uploads/sharedfiles/thumbnails/th_'.$file['name'])) {
            switch(strtolower($file['type'])) {
                case 'gif': $imagecreatefunc = 'imagecreatefromgif';
                    $imagefunc = 'imagegif';
                    $quality = 85;
                    break;
                case 'jpg': $imagecreatefunc = 'imagecreatefromjpeg';
                    $imagefunc = 'imagejpeg';
                    $quality = 85;
                    break;
                case 'png': $imagecreatefunc = 'imagecreatefrompng';
                    $imagefunc = 'imagepng';
                    $quality = 8;
                    break;
                case 'bmp': $imagecreatefunc = 'imagecreatefromwbmp';
                    $imagefunc = 'imagewbmp';
                    $quality = '';
                    break;
                default: break;
            }

            if(!empty($imagecreatefunc)) {
                $source = $imagecreatefunc('./uploads/sharedfiles/'.$file['name']);

                list($width, $height) = getimagesize($core->settings['rootdir'].'/uploads/sharedfiles/'.$file['name']);

                $new_height = ($height / $width) * 100;
                $thumbnail = imagecreatetruecolor(100, $new_height);
                imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, 100, $new_height, $width, $height);

                $imagefunc($thumbnail, './uploads/sharedfiles/thumbnails/th_'.$file['name'], $quality);
                imagedestroy($thumbnail);
            }
        }

        header("Content-type: application/x-msdownload");
        header("Pragma: no-cache");
        header("Expires: 0");
        header('location:'.$core->settings['rootdir'].'/uploads/sharedfiles/thumbnails/th_'.$file['name']);
    }
    elseif($core->input['action'] == 'get_sharefolderbymail') {
        $fid = $db->escape_string($core->input['id']);
        $creator = $db->fetch_field($db->query("SELECT uid as creator FROM ".Tprefix."filesfolder WHERE ffid=' {
                        $fid
                    }'"), 'creator');
        if($core->user['uid'] == $creator || $core->usergroup['filesharing_canSendAllFiles'] == 1) {
            $lang->load('messages');

            /* $fids = get_specificdata('filesfolder_viewrestriction', 'ffid', 'ffid', 'ffid', '', 0, "uid={$core->user[uid]}");
              if(in_array($fid, $fids)) {
              exit;
              } */

            $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'));
            $affiliates_list = parse_selectlist("affids[]", 1, $affiliates, '', 1);

            $folder = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."filesfolder WHERE ffid=' {
                        $fid
                    }'"));
            $folder_path = $core->settings['rootdir'].'/index.php?module = filesharing/fileslist&ffid = '.$fid;

            $lang->filesharing_sharesubject = $lang->sprint($lang->filesharing_sharesubject, $folder['name']);
            $lang->filesharing_sharefoldermessage = $lang->sprint($lang->filesharing_sharefoldermessage, $folder['name'], $folder['description']);

            eval("\$sharefolder = \"".$template->get('popup_fileslist_sharefolderbymail')."\";");
            output_page($sharefolder);
        }
    }
    elseif($core->input['action'] == 'get_sharebymail') {
        $creator = $db->fetch_field($db->query("SELECT fv.uid as creator FROM ".Tprefix."files f JOIN ".Tprefix."fileversions fv ON (f.fid=fv.fid) WHERE fv.fvid= '".$db->escape_string($core->input['id'])."'"), 'creator');
        if($core->user['uid'] == $creator || $core->usergroup['filesharing_canSendAllFiles'] == 1) {
            $lang->load('messages');

            /*
              $fids = array();
              $fids = get_specificdata('files_viewrestriction', 'fid', 'fid', 'fid', '', 0, "uid = {$core->user[uid]}");

              if(in_array($core->input['id'], $fids)) {
              redirect($_SERVER['HTTP_REFERER']);
              }
             */
            $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'));
            $affiliates_list = parse_selectlist("affids[]", 1, $affiliates, '', 1);

            $file = $db->fetch_assoc($db->query("SELECT fv.*, f.* FROM ".Tprefix."fileversions fv JOIN ".Tprefix."files f ON (fv.fid=f.fid) WHERE fv.fvid='".$db->escape_string($core->input['id'])."'"));
            $attachment_path = './uploads/sharedfiles/'.$file['name'];

            $lang->filesharing_sharesubject = $lang->sprint($lang->filesharing_sharesubject, $file['title']);
            $lang->filesharing_sharemessage = $lang->sprint($lang->filesharing_sharemessage, $file['description']);

            $file['size'] = format_size($file['size']);
            eval("\$sharebyemailbox = \"".$template->get('popup_fileslist_sharebymail')."\";");
            output_page($sharebyemailbox);
        }
    }
    elseif($core->input['action'] == 'do_sendbymail') {
        $lang->load('messages');
        if(empty($core->input['affids'])) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }

        if($core->input['sendingobject'] == 'file') {
            $creator = $db->fetch_field($db->query("SELECT fv.uid as creator FROM ".Tprefix."files f JOIN ".Tprefix."fileversions fv ON (f.fid=fv.fid) WHERE fv.fvid= '".$db->escape_string($core->input['id'])."'"), 'creator');
        }
        else {
            $creator = $db->fetch_field($db->query("SELECT uid as creator FROM ".Tprefix."filesfolder WHERE ffid= '".$db->escape_string($core->input['id'])."'"), 'creator');
            $core->input['message'] .= $lang->sprint($lang->filesharing_sharemessage_clicktoview, $core->input['path']);
        }

        if($core->user['uid'] == $creator || $core->usergroup['filesharing_canSendAllFiles'] == 1) {

            /* $fids = array();
              $fids = get_specificdata('files_viewrestriction', 'fid', 'fid', 'fid', '', 0, "uid = {$core->user[uid]}");

              if(in_array($core->input['attachment'], $fids)) {
              redirect($_SERVER['HTTP_REFERER']);
              }
             */

            $mails = get_specificdata('affiliates', array('affid', 'mailingList'), 'affid', 'mailingList', '', 0, 'mailingList != "" AND affid IN('.implode(', ', $core->input['affids']).')');
            $core->input['message'] = str_replace("\n", '<br />', $core->input['message']);

            $email_data = array(
                    'from_email' => $core->user['email'], //$core->settings['adminemail'],
                    'from' => $core->user['displayName'], //'OCOS Mailer',
                    'to' => $mails,
                    'subject' => $core->input['subject'],
                    'message' => $core->input['message']
            );

            if($core->input['sendingobject'] == 'file') {
                $email_data['attachments'] = array($core->input['attachment']);
            }

            $mail = new Mailer($email_data, 'php');

            if($mail->get_status() === true) {
                $log->record($mails);
                output_xml("<status>true</status><message>{$lang->filesuccessfullyshared}</message>");
            }
            else {
                output_xml("<status>false</status><message>{$lang->errorsendingemail}</message>");
            }
        }
    }
    elseif($core->input['action'] == 'get_deletefilebox') {
        $file = $db->fetch_assoc($db->query("SELECT fv.uid as creator, f.fid, f.title FROM ".Tprefix."files f JOIN ".Tprefix."fileversions fv ON (f.fid=fv.fid) WHERE f.fid= '".$db->escape_string($core->input['id'])."'"), 'creator');

        if($core->user['uid'] == $file['creator'] || $core->usergroup['filesharing_canDeleteAllFiles'] == 1) {
            eval("\$deletefilebox = \"".$template->get('popup_filesharing_fileslist_deletefilebox')."\";");
            output_page($deletefilebox);
        }
        /* else
          {
          error($lang->sectionnopermission, 'index.php?module = filesharing/fileslist');
          } */
    }
    elseif($core->input['action'] == 'do_deletefile') {
        $fid = $db->escape_string($core->input['fid']);
        //THERE MiGHT BE SEVERAL VERSIONS
        $creator = $db->fetch_field($db->query("SELECT fv.uid as creator FROM ".Tprefix."files f JOIN ".Tprefix."fileversions fv ON (f.fid=fv.fid) WHERE f.fid=' {
                        $fid
                    }'"), 'creator');
        if($core->user['uid'] == $creator || $core->usergroup['filesharing_canDeleteAllFiles'] == 1) {
            $query = $db->delete_query('files', "fid=' {
                        $fid
                    }'");

            if($query) {
                $log->record($fid);

                $versions = $db->query("SELECT fvid, name FROM fileversions WHERE fid=' {
                        $fid
                    }'");
                while($version = $db->fetch_assoc($versions)) {
                    $filepath = '';
                    $db->delete_query('fileversions', "fvid=' {
                        $version[fvid]
                    }'");
                    $filepath = './uploads/sharedfiles/'.$version['name'];

                    unlink($filepath);
                }

                /* if(!$versions_query) {
                  $error['version'] = true;//'error in deleting versions for'.$fid;
                  } */

                //if(value_exists('files_viewrestriction', 'fid', $fid)) {
                $restriction_query = $db->delete_query('files_viewrestriction', "fid=' {
                        $fid
                    }'");
                if(!$restriction_query) {
                    $error['restriction'] = true; //'error in deleting restriction for'.$fid;
                }
                //}
                //Add errors
                output_xml("<status>true</status><message>{$lang->successdelete}</message>");
            }
            else {
                output_xml("<status>false</status><message>{$lang->errordeleting}</message>");
            }
        }
        /* else
          {
          error($lang->sectionnopermission, 'index.php?module = filesharing/fileslist');
          } */
    }
    elseif($core->input['action'] == 'createfolder') {
        if(empty($core->input['name'])) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }

        $parentrestriction_query = $db->query("SELECT * FROM ".Tprefix."filesfolder_viewrestriction WHERE ffid='".$db->escape_string($core->input['folder'])."'");
        $parentrestriction_array = array();
        if($db->num_rows($parentrestriction_query) > 0) {
            while($parentrestriction = $db->fetch_assoc($parentrestriction_query)) {
                if($parentrestriction['noWrite'] == 1) {
                    $parentrestriction_array['write'][] = $parentrestriction['uid'];
                }

                if($parentrestriction['noRead'] == 1) {
                    $parentrestriction_array['read'][] = $parentrestriction['uid'];
                }
            }
        }

        $newrestriction['read'] = $core->input['read'];
        $newrestriction['write'] = $core->input['write'];
        //$restriction['write'] = array();

        $restriction = array_merge_recursive($parentrestriction_array, $newrestriction);
        if(is_array($restriction['write'])) {
            $restriction['write'] = array_unique($restriction['write']);
        }

        if(is_array($restriction['read'])) {
            $restriction['read'] = array_unique($restriction['read']);
        }

        $newfolder = array(
                'parent' => $core->input['folder'],
                'name' => $core->input['name'],
                'uid' => $core->user['uid'],
                'description' => $core->input['description'],
                'noReadPermissionsLater' => $core->input['noReadPermissionsLater'],
                'noWritePermissionsLater' => $core->input['noWritePermissionsLater']
        );

        $query = $db->insert_query('filesfolder', $newfolder);
        if($query) {
            $ffid = $db->last_id();
            $log->record($ffid);
            if(is_array($restriction['read'])) {
                foreach($restriction['read'] as $key => $uid) {
                    if(is_array($restriction['write'])) {
                        if(in_array($uid, $restriction['write'])) {
                            unset($restriction['write'][array_search($uid, $restriction['write'])]);
                        }
                    }
                    $newfolderrestriction = array(
                            'ffid' => $ffid,
                            'uid' => $uid,
                            'noRead' => 1,
                            'noWrite' => 1
                    );
                    $restriction_query = $db->insert_query('filesfolder_viewrestriction', $newfolderrestriction);
                    /* if(!$restriction_query) {
                      $error .= "{$lang->errorsaving}restriction";
                      }
                      else
                      {
                      $log->record($db->last_id());
                      } */
                }
            }
            if(is_array($restriction['write'])) {
                if(!empty($restriction['write'])) {
                    foreach($restriction['write'] as $key => $uid) {
                        $newfolderrestriction = array(
                                'ffid' => $ffid,
                                'uid' => $uid,
                                'noRead' => 0,
                                'noWrite' => 1
                        );
                        $restriction_query = $db->insert_query('filesfolder_viewrestriction', $newfolderrestriction);
                        /* if(!$restriction_query)
                          {
                          $error .= "{$lang->errorsaving}restriction";
                          }
                          else
                          {
                          $log->record($db->last_id());
                          } */
                    }
                }
            }
            output_xml("<status>true</status><message>{$lang->successfullysaved}<![CDATA[<script language='javascript' type='text/javascript'>window.top.$('#folder > option[value={$core->input[folder]}]').after('<option style=\'font-weight:bold;\' value={$ffid}>&raquo; {$core->input[name]}</option>');</script>]]></message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
        }
    }
    elseif($core->input['action'] == '') {
        $url = 'index.php?module=filesharing/filelist&ffid='.$core->input['ffid'].'&fid='.$core->input['fid'].'&referrer=viewversion';
        output_xml("<status>true</status><message>Successfully<![CDATA[<script>goToURL('$url');</script>]]></message>");
    }
}
?>