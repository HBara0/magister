<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: manageapplication.php
 * Created:        @tony.assaad    Dec 5, 2013 | 11:50:21 AM
 * Last Update:    @tony.assaad    Dec 5, 2013 | 11:50:21 AM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageSegments'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('products_applications');
$lang->load('global');
if(!$core->input['action']) {
    $sort_url = sort_url();
    $filters_config = array(
            'parse' => array('filters' => array('name', 'title', 'segment', 'sequence', 'description'),
                    'overwriteField' => array(
                            'description' => '',
                            'name' => '',
                            'sequence' => '',
                    )
            ),
            'process' => array(
                    'filterKey' => 'psaid',
                    'mainTable' => array(
                            'name' => 'segmentapplications',
                            'filters' => array('title' => array('name' => 'title'), 'segment' => array('operatorType' => 'multiple', 'name' => 'psid')),
                    ),
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filter_where = null;
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));

    /* Perform inline filtering - END */
    $applications_obj = SegmentApplications::get_segmentsapplications($filter_where, array('order' => 'name'));
    if(is_array($applications_obj)) {
        /* loop over the returned objects and get their related data */
        foreach($applications_obj as $application_obj) {
            $altrow_class = alt_row($altrow_class);
            $application = $application_obj->get();
            $application['segment'] = $application_obj->get_segment()->get()['title'];
            eval("\$productsapplications_list .= \"".$template->get('admin_products_applications_row')."\";");
        }
    }

    $segments = ProductsSegments::get_segments();
    $segments_list = parse_selectlist('segmentapplications[psid]', 2, $segments, $application->psid);

    $chemicals_obj = ChemicalFunctions::get_functions();
    if(is_array($chemicals_obj)) {
        /* for best preformance loop over the returned ChemicalFunctions objects and get their related data */
        $checmicalfunctions_list = '<option value="" selected="selected"> </option>';
        foreach($chemicals_obj as $chemical_obj) {
            $chemical = $chemical_obj->get();
            $checmicalfunctions_list .= '<option value='.$chemical['cfid'].'>'.$chemical['title'].'</option>';
        }
    }
    $multipages = new Multipages('segmentapplications', $core->settings['itemsperlist']);
    $productsapplications_list .= "<tr><td colspan='5'>".$multipages->parse_multipages()."</td></tr>";
    $publishonwebcheckbox = '<input type="checkbox" value="1" name="segmentapplications[publishOnWebsite]">';
    $display_trans = 'none';
    eval("\$dialog_managerapplication = \"".$template->get("admin_popup_manageapplication")."\";");
    eval("\$applicationpage = \"".$template->get("admin_products_applications")."\";");
    output_page($applicationpage);
}
else {
    if($core->input['action'] == 'do_create') {
        $segmentapplications_obj = new SegmentApplications($core->input['segmentapplications'][SegmentApplications::PRIMARY_KEY]);
        $segmentapplications_obj->save($core->input['segmentapplications']);
        switch($segmentapplications_obj->get_errorcode()) {
            case 0:
                if(is_array($core->input['lang'])) {
                    $trans_data['tableKey'] = intval($segmentapplications_obj->{SegmentApplications::PRIMARY_KEY});
                    $trans_data['tableName'] = SegmentApplications::TABLE_NAME;
                    foreach($core->input['lang'] as $slid => $language) {
                        $trans_data['language'] = $slid;
                        if(is_array($language)) {
                            foreach($language as $field => $text) {
                                $trans_data['language'] = $slid;
                                $trans_data['field'] = $field;
                                $trans_data['text'] = $text;
                                $translation_obj = new Translations();
                                $translation_obj->set($trans_data);
                                $translation_obj->save();
                                if($translation_obj->get_errorcode() != 0) {
                                    output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                                    exit;
                                }
                            }
                        }
                    }
                    output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                    exit;
                }
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 1:
                output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>'.$lang->entryexist.'</message>');
                break;
        }
    }
    elseif($core->input['action'] == 'get_editbox') {
        $apid = intval($core->input['id']);
        $application = new SegmentApplications($apid, false);
        if($application->publishOnWebsite == 1) {
            $checked = 'checked="checked"';
        }
        $segments = ProductsSegments::get_segments();
        $segments_list = parse_selectlist('segmentapplications[psid]', 2, $segments, $application->psid);
        $publishonwebcheckbox = '<input type="checkbox" value="1" name="segmentapplications[publishOnWebsite]" '.$checked.'>';
        //$dialog_managerapplication = $headerinc;
        $languages = SystemLanguages::get_data('slid !=1', array('returnarray' => true));
        if(is_array($languages)) {
            foreach($languages as $language) {
                $lid = $language->htmllang;
                $trans_fields = array('description', 'title');
                foreach($trans_fields as $field) {
                    $translation = Translations::get_translation($field, SegmentApplications::TABLE_NAME, $lid, $application->{SegmentApplications::PRIMARY_KEY});
                    if(is_object($translation)) {
                        $trans[$lid][$field] = $translation->text;
                    }
                }
                eval("\$translationtabs_content=\"".$template->get('admin_products_translateapplication_content')."\";");
                $lang_output .= $translationtabs_content;
                $lid = '';
            }
        }
        eval("\$translationsprodapp = \"".$template->get('admin_translationsprodapp')."\";");
        eval("\$dialog_managerapplication = \"".$template->get('admin_popup_manageapplication')."\";");
        output($dialog_managerapplication);
    }
    elseif($core->input['action'] == 'get_deletapp') {
        $psaid = intval($core->input['id']);
        eval("\$delete_app = \"".$template->get('popup_admin_deleteapplications')."\";");
        output($delete_app);
    }
    elseif($core->input['action'] == 'do_delete') {
        if($core->usergroup['canManageSegments'] == 0) {
            output_xml("<status>false</status><message>{$lang->errordeleting}</message>");
            exit;
        }
        $todeleteid = intval($core->input['psaid']);
        $app_cols = array('psaid');
        foreach($app_cols as $column) {
            $tables[$column] = $db->get_tables_havingcolumn($column);
        }
        if(is_array($tables)) {
            foreach($tables as $key => $columntables) {
                if(is_array($columntables)) {
                    $app_tables[$key] = array_fill_keys(array_values($columntables), $key);
                }
            }
        }
        $exclude_tables = array('segmentapplications');

        foreach($app_tables as $tables) {
            if(is_array($tables)) {
                foreach($tables as $table => $attr) {
                    if(in_array($table, $exclude_tables)) {
                        continue;
                    }
                    $query = $db->query("SELECT * FROM ".Tprefix.$table." WHERE ".$attr." = ".$todeleteid);
                    if($db->num_rows($query) > 0) {
                        $usedin_tables[] = $table;
                    }
                }
            }
        }
        if(is_array($usedin_tables)) {
            // $result = implode(", ", $usedin_tables);
            output_xml("<status>false</status><message>{$lang->deleteerror}</message>");
            exit;
        }
        /* Delete Entity */
        $deletequery = $db->delete_query('segmentapplications', "psaid = '{$todeleteid}'");

        /* Delete "deleted entity" data from excluded tabes if found ? */
        if($deletequery) {
            output_xml("<status>true</status><message>{$lang->successdelete}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errordeleting}</message>");
        }
    }
    elseif($core->input['action'] == 'do_addtranslatedaff') {

    }
}
?>