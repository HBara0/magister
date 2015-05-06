<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: basicingredients.php
 * Created:        @rasha.aboushakra    May 5, 2015 | 3:39:31 PM
 * Last Update:    @rasha.aboushakra    May 5, 2015 | 3:39:31 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canManageapllicationsProducts'] == 0) {
    //error($lang->sectionnopermission);
    //exit;
}

if(!$core->input['action']) {
    $sort_url = sort_url();


    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('title')
            ),
            'process' => array(
                    'filterKey' => 'mibdid',
                    'mainTable' => array(
                            'name' => 'basic_ingredients',
                            'filters' => array('title' => array('operatorType' => 'equal', 'name' => 'title'), 'description' => '')
                    ),
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();

    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
    if(!empty($filter_where)) {
        $basicingredients_objs = BasicIngredients::get_data($filter_where, array('returnarray' => true, 'simple' => false));
    }

    $basicingredients_objs = BasicIngredients::get_data(array('name is not null'), array('returnarray' => true));
    if(is_array($basicingredients_objs)) {
        foreach($basicingredients_objs as $basicingredients_obj) {
            $basicingredient = $basicingredients_obj->get();
            eval("\$basicingredients_list .= \"".$template->get('admin_basicingredients_rows')."\";");
        }
    }
    else {
        $basicingredients_list = '<tr><td colspan="3">'.$lang->na.'</td></tr>';
    }

    $module = 'products';
    $modulefile = 'basicingredients';
    eval("\$popup_createbasicingredient = \"".$template->get('popup_createbasicingredient')."\";");
    eval("\$basicingredientspage = \"".$template->get('admin_basicingredients')."\";");
    output_page($basicingredientspage);
}
else {
    if($core->input['action'] == 'do_addbasicingredient') {
        if(!isset($core->input['basicingredient']['biid']) && empty($core->input['basicingredient']['biid'])) {
            $basicingredients_obj = BasicIngredients::get_data(array('title' => $core->input['basicingredient']['title']));
            if(is_object($basicingredients_obj)) {
                output_xml('<status>false</status><message>'.$lang->itemalreadyexist.'</message>');
                exit;
            }
        }
        $basicing_obj = new BasicIngredients();
        $basicing_obj->set($core->input['basicingredient']);
        $basicing_obj->save();
        switch($basicing_obj->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                break;
        }
    }
}
?>
