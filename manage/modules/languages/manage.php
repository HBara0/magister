<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Modify Language File
 * $module: Admin/Languages
 * $id: edit.php
 * Created: 	@zaher.reda 	July 25, 2012 | 11:43:00 AM	
 * Last Update: @zaher.reda 	August 25, 2012 | 11:27:00 AM	
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if(!$core->input['action']) {
    $languages = $lang->get_languages_db();

    if($core->input['type'] == 'edit') {
        if($core->usergroup['admin_canModifyLangFiles'] == 0) {
            error($lang->sectionnopermission);
            exit;
        }

        $actiontype = 'edit';
        $arrayname = 'values';
        $filename_inputtype = 'hidden';

        $query = $db->query('SELECT *
							FROM '.Tprefix.'system_langvariables
							WHERE fileName="'.$db->escape_string($core->input['filename']).'"
							ORDER BY name ASC');
        if($db->num_rows($query) > 0) {
            while($langvar = $db->fetch_assoc($query)) {
                $varvalues_query = $db->query('SELECT *
												FROM '.Tprefix.'system_languages_varvalues
												WHERE variable='.$langvar['slvid']);
                $values = '';
                while($varvalue = $db->fetch_assoc($varvalues_query)) {
                    $varvalues[$varvalue['variable']][$varvalue['lang']] = $varvalue;
                }

                foreach($languages as $slid => $language) {
                    if(isset($varvalues[$langvar['slvid']][$slid])) {
                        $varvalue = $varvalues[$langvar['slvid']][$slid];
                        $varvalue['langName'] = $language['name'];
                    }
                    else {
                        $varvalue = array('lang' => $slid, 'variable' => $langvar['slvid'], 'langName' => $language['name']);
                    }

                    $varvalue['value'] = str_replace("<br />", "", $varvalue['value']);
                    eval("\$values .= \"".$template->get('admin_languages_addedit_varvals')."\";");
                }
                eval("\$edit_langvariables .= \"".$template->get('admin_languages_edit_var')."\";");
            }
        }
        else {
            redirect('index.php?module=languages/list');
        }
        /* Parse add new variables - Start */
        foreach($languages as $slid => $language) {
            $var_rowid = 1;

            $arrayname = 'newvars';
            $groupby = '[values]';

            $varvalue = array('lang' => $slid, 'variable' => $var_rowid, 'langName' => $language['name']);
            eval("\$language_textareas .= \"".$template->get('admin_languages_addedit_varvals')."\";");
        }

        eval("\$add_langvariables = \"".$template->get('admin_languages_add_var')."\";");
        /* Parse add new variables - End */

        eval("\$managelanguages_page = \"".$template->get('admin_languages_addedit')."\";");
        output_page($managelanguages_page);
    }
    else {
        if($core->usergroup['admin_canCreateLangFiles'] == 0) {
            error($lang->sectionnopermission);
            exit;
        }
        $actiontype = 'add';
        $filename_inputtype = 'text';
        foreach($languages as $slid => $language) {
            $var_rowid = 1;

            $arrayname = 'newvars';
            $groupby = '[values]';

            $varvalue = array('lang' => $slid, 'variable' => $var_rowid, 'langName' => $language['name']);
            eval("\$language_textareas .= \"".$template->get('admin_languages_addedit_varvals')."\";");
        }

        eval("\$add_langvariables = \"".$template->get('admin_languages_add_var')."\";");

        eval("\$managelanguages_page = \"".$template->get('admin_languages_addedit')."\";");
        output_page($managelanguages_page);
    }
}
else {
    if($core->input['action'] == 'do_editlanguages' || $core->input['action'] == 'do_addlanguages') {
        $languages = $lang->get_languages_db();

        if(isset($core->input['values'])) {
            if($core->usergroup['admin_canModifyLangFiles'] == 1) {
                foreach($core->input['values'] as $slvid => $values) {
                    foreach($values as $slid => $val) {
                        if($val == '') {
                            continue;
                        }

                        $val = nl2br($val);

                        if(value_exists('system_languages_varvalues', 'variable', $slvid, 'lang="'.$slid.'"')) {
                            $db->update_query('system_languages_varvalues', array('lang' => $slid, 'value' => $val, 'timeModified' => TIME_NOW, 'modifiedBy' => $core->user['uid']), 'variable='.$slvid.' AND lang="'.$slid.'"');
                        }
                        else {
                            $db->insert_query('system_languages_varvalues', array('lang' => $slid, 'variable' => $slvid, 'value' => $val, 'timeCreated' => TIME_NOW, 'createdBy' => $core->user['uid']));
                        }
                    }
                }
            }
        }

        /* Process new lang variables - START */

        if(isset($core->input['newvars']) && !empty($core->input['filename'])) {
            if($core->usergroup['admin_canCreateLangFiles'] == 1) {
                foreach($core->input['newvars'] as $key => $newvar) {
                    if(!empty($newvar['name'])) {
                        if(!value_exists('system_langvariables', 'name', $newvar['name'], 'fileName="'.$db->escape_string($core->input['filename']).'"')) {

                            $newvar['name'] = str_replace(' ', '', $newvar['name']);
                            $newvar['name'] = trim(strtolower($core->sanitize_inputs($newvar['name'], array('removetags' => true))));

                            $core->input['filename'] = str_replace(' ', '', $core->input['filename']);
                            $core->input['filename'] = strtolower($core->sanitize_inputs($core->input['filename'], array('removetags' => true)));

                            $db->insert_query('system_langvariables', array('name' => $newvar['name'], 'fileName' => $core->input['filename'], 'isFrontEnd' => 1));
                            $slvid = $db->last_id();

                            foreach($newvar['values'] as $langid => $newvarval) {
                                $db->insert_query('system_languages_varvalues', array('lang' => $langid, 'variable' => $slvid, 'value' => $newvarval, 'timeCreated' => TIME_NOW, 'createdBy' => $core->user['uid']));
                            }
                        }
                    }
                }
            }
        }
        /* Process new lang variables - END */

        if($core->usergroup['admin_canCreateLangFiles'] == 1 || $core->usergroup['admin_canModifyLangFiles'] == 1) {
            $operation = $lang->rebuild_langfile($core->input['filename'], array(1 => 'english', 2 => 'french'));
            if($operation == true) {
                output_xml("<status>true</status><message></message>");
            }
            else {
                output_xml("<status>true</status><message>{$lang->errorsaving}</message>");
            }
        }
    }
}