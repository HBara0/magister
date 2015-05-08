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
            'parse' => array('filters' => array('title', ''),
                    'overwriteField' => array('title' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->title.'"/>',
                            '' => ''
                    )),
    );

    $filter = new Inlinefilters($filters_config);
    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));

    $basicingredients_objs = BasicIngredients::get_data(array('name is not null'), array('returnarray' => true));
    if(is_array($basicingredients_objs)) {
        foreach($basicingredients_objs as $basicingredients_obj) {
            $basicingredient = $basicingredients_obj->get();
            eval("\$basicingredients_list .= \"".$template->get('admin_basicingredients_rows')."\";");
            unset($basicingredient);
        }
    }
    else {
        $basicingredients_list = '<tr><td colspan="3">'.$lang->na.'</td></tr>';
    }
    $title = $lang->createbasicingredient;
    $module = 'products';
    $modulefile = 'basicingredients';
    eval("\$popup_createbasicingredient = \"".$template->get('popup_createbasicingredient')."\";");
    eval("\$basicingredientspage = \"".$template->get('admin_basicingredients')."\";");
    output_page($basicingredientspage);
}
else {
    if($core->input['action'] == 'do_addbasicingredient') {
        if(empty($core->input['basicingredient']['biid'])) {
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
            case 3:
                output_xml('<status>false</status><message>'.$lang->itemalreadyexist.'</message>');
        }
    }
    else if($core->input['action'] == 'get_updatebasicingredient') {
        $title = $lang->updatebasicingredient;
        $module = 'products';
        $modulefile = 'basicingredients';
        $biid = $core->input['id'];
        $basicingredient_obj = BasicIngredients::get_data(array('biid' => $biid));
        if(is_object($basicingredient_obj)) {
            $basicingredient = $basicingredient_obj->get();
            eval("\$popup_createbasicingredient = \"".$template->get('popup_createbasicingredient')."\";");
            unset($basicingredient);
            output($popup_createbasicingredient);
        }
    }
}
?>
