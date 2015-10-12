<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 *
 * Manage Segments
 * $module: admin/products
 * $id: segments.php
 * Last Update: @zaher.reda 	Mar 18, 2009 | 03:32 PM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canManageSegments'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('products_segments');
if(!$core->input['action']) {
    $filters_config = array(
            'parse' => array('filters' => array('id', 'segment', 'description'),
                    'overwriteField' => array(
                            'id' => '',
                            'description' => '',
                    )
            ),
            'process' => array(
                    'filterKey' => 'psid',
                    'mainTable' => array(
                            'name' => 'productsegments',
                            'filters' => array('segment' => array('operatorType' => 'multiple', 'name' => 'psid')),
                    ),
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filter_where = null;
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' WHERE '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));

    /* Perform inline filtering - END */
    $query = $db->query("SELECT * FROM ".Tprefix."productsegments ".$filter_where." ORDER BY title ASC");
    if($db->num_rows($query) > 0) {
        while($segment = $db->fetch_array($query)) {
            $segments_list .= "<tr><td>".$segment['psid']."</td><td><a href=../index.php?module=profiles/segmentprofile&id=".$segment['psid']." target='_blank'>".$segment['title']."</a></td><td>".$segment['description']."</td>";
            $segments_list .='<td><a style="cursor: pointer;" title="'.$lang->update.'" id="updatesegmentdtls_'.$segment['psid'].'_'.$core->input['module'].'_loadpopupbyid" rel="segmentdetail_'.$coid.'"><img src="'.$core->settings[rootdir].'/images/icons/update.png"/></a></td></tr>';
        }
    }
    else {
        $segments_list = "<tr><td colspan='3' style='text-align: center;'>{$lang->nosegementsavailable}</td></tr>";
    }
    $segmentcats = SegmentCategories::get_data(array(), array('simple' => false, 'returnarray' => true));
    $category_selectlist = parse_selectlist("segment[category]", '', $segmentcats, '', '', '');
    eval("\$addsegment = \"".$template->get('popup_admin_product_addsegment')."\";");
    eval("\$segmentspage = \"".$template->get('admin_products_segments')."\";");
    output_page($segmentspage);
}
else {
    if($core->input['action'] == 'do_add_segments') {
        $segment_obj = new ProductsSegments();
        $core->input['segment']['alias'] = generate_alias($core->input['segment']['title']);
        $segment_obj->set($core->input['segment']);
        $segment_obj = $segment_obj->save();
        switch($segment_obj->get_errorcode()) {
            case 0:
                if(is_array($core->input['lang'])) {
                    $trans_data['tableKey'] = intval($segment_obj->{ProductsSegments::PRIMARY_KEY});
                    $trans_data['tableName'] = ProductsSegments::TABLE_NAME;
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
                }
                output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                exit;
            case 1:
                output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;
            case 2:
            default:
                output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
        }
        exit;
//        if(empty($core->input['title'])) {
//            output_xml("<status>false</status><message>{$lang->pleasefillintitle}</message>");
//            exit;
//        }
//
//        if(value_exists('productsegments', 'title', $core->input['title'])) {
//            output_xml("<status>false</status><message>{$lang->segmentalreadyexists}</message>");
//            exit;
//        }
//        $new_segment = array(
//                'title' => $core->input['title'],
//                'description' => $core->input['description']
//        );
//
//        $query = $db->insert_query('productsegments', $new_segment);
//        if($query) {
//            $psid = $db->last_id();
//            foreach($core->input['coordinators'] as $coordinators) {
//                if(empty($coordinators)) {
//                    continue;
//                }
//
//                $coordinators['fromDate'] = strtotime($coordinators['fromDate']);
//                $segment_coordinators = array(
//                        'psid' => $psid,
//                        'uid' => $coordinators['uid'],
//                        'fromDate' => $coordinators['fromDate'],
//                        'createdBy' => $core->user['uid'],
//                        'createdOn' => TIME_NOW
//                );
//
//                $query = $db->insert_query('productsegmentcoordinators', $segment_coordinators);
//            }
//
//            output_xml("<status>true</status><message>{$lang->segmentadded}</message>");
//            $log->record($core->input['title']);
//        }
//        else {
//            output_xml("<status>false</status><message>{$lang->segmentadderror}</message>");
//        }
    }
    elseif($core->input['action'] == 'get_updatesegmentdtls') {
        $segment_obj = new ProductsSegments($core->input['id'], false);
        $languages = SystemLanguages::get_data('slid !=1', array('returnarray' => true));
        if(is_array($languages)) {
            foreach($languages as $language) {
                $lid = $language->htmllang;
                $trans_fields = array('description', 'title', 'shortDescription', 'slogan');
                foreach($trans_fields as $field) {
                    $translation = Translations::get_translation($field, ProductsSegments::TABLE_NAME, $lid, $segment_obj->{ProductsSegments::PRIMARY_KEY});
                    if(is_object($translation)) {
                        $trans[$lid][$field] = $translation->text;
                    }
                }
                eval("\$translationtabs_content=\"".$template->get('admin_products_translatesegment_content')."\";");
                $lang_output .= $translationtabs_content;
                $lid = '';
            }
        }
        eval("\$translationsprodseg = \"".$template->get('admin_translationsprodfunc')."\";");
        $segment = $segment_obj->get();
        if($segment['publishOnWebsite'] == '1') {
            $checked = 'checked="checked"';
        }
        $segmentcats = SegmentCategories::get_data(array(), array('simple' => false, 'returnarray' => true));
        $category_selectlist = parse_selectlist("segment[category]", '', $segmentcats, $segment_obj->category, '', '');
//        $segment['description'] = $segment_obj->description;
//        $segment['title'] = $segment_obj->title;
//        $segment['psid'] = $segment_obj->psid;
        eval("\$addsegment = \"".$template->get('popup_admin_product_addsegment')."\";");
        output_page($addsegment);
    }
    elseif($core->input['action'] == 'ajaxaddmore_coordrows') {
        echo(334);
    }
}
?>