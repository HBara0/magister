<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Upload a Shared File
 * $module: filesharing
 * $id: uploadfile.php
 * Created:	   	 	@najwa.kassem	January 31, 2010 | 10:10 AM
 * Last Update: 	@zaher.reda 	August 23, 2011 | 01:00 AM
 */

if(!defined("DIRECT_ACCESS")) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['filesharing_canUploadFile'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {

    $categories_query = get_specificdata('filescategories', array('fcid', 'title'), 'fcid', 'title', array('by' => 'title', 'sort' => 'ASC'), 0, 'isPublic=1');
    $categories_list = parse_selectlist('category', 1, $categories_query, '', '', '', array('required' => 'required', 'blankstart' => true));

    if($core->usergroup['filesharing_canViewAllFilesfolder'] == 0) {
        $folder_query_where = " WHERE ffid NOT IN(SELECT ffid FROM ".Tprefix."filesfolder_viewrestriction WHERE uid={$core->user[uid]})";
    }
    else {
        $folder_query_where = '';
    }

    /* $query = $db->query("SELECT * FROM ".Tprefix."filesfolder{$folder_query_where} ORDER BY name ASC");
      while($folder = $db->fetch_array($query))  {
      $folders[$folder['ffid']]['name'] = $folder['name'];
      if($folder['parent'] != '0') {
      $folders[$folder['parent']]['sub'][] = $folder['ffid'];
      }
      }
     */
    $query = $db->query("SELECT ffid, name, parent FROM ".Tprefix."filesfolder{$folder_query_where} ORDER BY name ASC");
    while($folder = $db->fetch_assoc($query)) {
        $folders[$folder['ffid']] = $folder;
    }

    $folders = get_folderstruture($folders, 0);
    $folders_list = parse_selectlist('folder', 3, array(0 => '') + get_folderslist(), 0, '', '', array('required' => 'required'));

    $employees_query = $db->query("SELECT uid, displayName as fullname FROM ".Tprefix."users WHERE gid!=7 AND uid!={$core->user[uid]} ORDER by fullname ASC");
    while($employee = $db->fetch_array($employees_query)) {
        $employees[$employee['uid']] = $employee['fullname'];
    }

    $employees_preventaccess_list = parse_selectlist('uid[]', 1, $employees, '', 1);
    $employees_preventread_list = parse_selectlist('read[]', 1, $employees, '', 1);
    $employees_preventwrite_list = parse_selectlist('write[]', 1, $employees, '', 1);
    if(isset($core->input['fid']) && !empty($core->input['fid'])) {
        $file_obj = Files::get_data(array('fid' => $core->input['fid']));
        if(is_object($file_obj)) {
            $file_title = $file_obj->title;
            $readonly['filetitle'] = 'readonly="readonly"';
        }
    }

    eval("\$uploadfiles_page = \"".$template->get('filesharing_uploadfile')."\";");
    output_page($uploadfiles_page);
}
elseif($core->input['action'] == 'do_uploadfile') {
    set_time_limit(0);
    echo $headerinc;
    if(is_empty($core->input['title']) || empty($core->input['category'])) {
        ?>
        <script language="javascript" type="text/javascript">
            $(function() {
                window.top.$("#upload_Result").html("<?php echo $lang->fillallrequiredfields;?>");
            });
        </script>
        <?php
        exit;
    }

    $allowed_types = array('application/excel', 'application/kset', 'application/kswps', 'application/octet-stream', 'application/x-excel', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'image/jpeg', 'image/gif', 'image/png', 'application/zip', 'application/pdf', 'application/x-pdf', 'application/msword', 'application/vnd.ms-powerpoint', 'text/plain', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation');

    $upload = new Uploader('uploadfile', $_FILES, $allowed_types, 'putfile', 5242880, 1, 1); //5242880 bytes = 5 MB (1024)

    $path = "./uploads/sharedfiles/";
    $upload->set_upload_path($path);
    $upload->process_file();
    //$upload->resize(200);

    $file_data = $upload->get_filesinfo();
    $multiple_files = false;
    if(count($file_data) > 1) {
        $multiple_files = true;
        $file_number = 1;
    }

    foreach($file_data as $key => $val) {
        if($upload->get_status($key) != 4) {
            continue;
        }

        if(!empty($core->input['title'])) {
            $filetitle = $core->input['title'];
        }
        else {
            $original_name = explode('.', $val['originalname']);
            $filetitle = $original_name[0];
        }
        if(!isset($core->input['fid']) || empty($core->input['fid'])) {
            if(value_exists('files', 'title', $filetitle)) {
                ?>
                <script language="javascript" type="text/javascript">
                    $(function() {
                        window.top.$("#upload_Result").html("<?php echo $lang->filetitleexists;?>");
                    });
                </script>
                <?php
                exit;
            }
        }
        if($multiple_files == true) {
            $filetitle = $core->input['title'].' ('.$file_number.')';
            $file_number++;
        }
        else {
            $filetitle = $core->input['title'];
        }
        $alias = generate_alias($filetitle).'_'.$core->input['category'].'_'.$core->input['folder'];
        $newfile = array(
                'title' => $filetitle,
                'alias' => $alias,
                'category' => $core->input['category'],
                'description' => $core->input['description'],
                'ffid' => $core->input['folder'],
                'reference' => '0',
                'referenceId' => 0,
                'isShared' => 1
        );
        if(isset($core->input['fid']) && !empty($core->input['fid'])) {
            $query = $db->update_query('files', $newfile, 'fid='.intval($core->input['fid']));
            $operation = 'update';
        }
        else {
            $query = $db->insert_query('files', $newfile);
            $operation = 'insert';
        }
        if($query) {
            if($operation == 'insert') {
                $fid = $db->last_id();
            }
            else {
                $fid = intval($core->input['fid']);
            }
            $newfileversion = array(
                    'fid' => $fid,
                    'name' => $val['name'],
                    'type' => $val['extension'],
                    'size' => $val['size'],
                    'timeLine' => TIME_NOW,
                    'uid' => $core->user['uid']
            );

            $insert = $db->insert_query('fileversions', $newfileversion);

            if($insert) {
                $log->record($fid);
            }

            $parentrestriction_query = $db->query("SELECT uid FROM ".Tprefix."filesfolder_viewrestriction WHERE ffid='".$db->escape_string($core->input['folder'])."' AND noRead=1");
            $parentrestriction_array = array();
            while($parentrestriction = $db->fetch_assoc($parentrestriction_query)) {
                $parentrestriction_array[] = $parentrestriction['uid'];
            }

            if(is_array($core->input['uid'])) {
                $restriction = array_unique(array_merge($parentrestriction_array, $core->input['uid']));
            }
            else {
                $restriction = $parentrestriction_array;
            }

            if(is_array($restriction)) {
                foreach($restriction as $key => $uid) {
                    $newfilerestriction = array('fid' => $fid, 'uid' => $uid);
                    $db->insert_query('files_viewrestriction', $newfilerestriction);
                }
            }
        }
    }
    ?>
    <script language="javascript" type="text/javascript">
        $(function() {
            window.top.$("#upload_Result").html("<?php echo $upload->parse_status($upload->get_status());?>");
        });
    </script>
    <?php
}
function get_folderslist() {
    global $folders;
    $folders_list = array();

    if(is_array($folders)) {
        foreach($folders as $id => $folder_details) {
            if(empty($folders[$id]['name'])) {
                continue;
            }

            $folders_list[$id] = $folders[$id]['name'];

            if(is_array($folder_details['sub'])) {

                $folders_list += get_subfolders($folder_details['sub'], 1);

                /* foreach($folder_details['sub'] as $key => $val){
                  $folders_list[$val] = '&hellip; '.$folders[$val]['name'];

                  if(is_array($folders[$val]['sub'])) {
                  $folders_list = array_merge($folders_list, get_subfolders($folders[$val]['sub'], $depth));
                  }
                  unset($folders[$val]);
                  } */
            }
            //unset($folders[$id]);
        }
    }
    return $folders_list;
}

function get_subfolders($subfolders, $depth = '') {
    global $folders;
    if(!empty($depth)) {
        $depth++;
    }

    foreach($subfolders as $key => $subfolder) {
        $folders_list[$subfolder['id']] = str_repeat('&hellip;', $depth).' '.$subfolder['name'];

        if(is_array($subfolder['sub'])) {
            $folders_list += get_subfolders($subfolder['sub'], $depth);
        }
    }
    return $folders_list;
}

function get_folderstruture($folders, $parent = 0) {
    global $db;

    foreach($folders as $ffid => $folder) {
        if($folder['parent'] == $parent) {
            $folders_structure[$folder['ffid']]['id'] = $folder['ffid'];
            $folders_structure[$folder['ffid']]['name'] = $folder['name'];

            $subfolders = get_folderstruture($folders, $folder['ffid']);
            if(!empty($subfolders)) {
                $folders_structure[$folder['ffid']]['sub'] = $subfolders;
                unset($subfolders);
            }
        }
    }
    if(empty($folders_structure)) {
        return;
    }
    return $folders_structure;
}
?>