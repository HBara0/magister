<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: charactersticslist.php
 * Created:        @hussein.barakat    Jun 2, 2015 | 3:25:12 PM
 * Last Update:    @hussein.barakat    Jun 2, 2015 | 3:25:12 PM
 */


if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
//Put related permission here
//if($core->usergroup['canManageapllicationsProducts'] == 0) {
//    //error($lang->sectionnopermission);
//    //exit;
//}

if(!$core->input['action']) {
    $filters_config = array(
            'parse' => array('filters' => array('name', ''),
                    'overwriteField' => array('name' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->name.'"/>',
                            '' => ''
                    )),
    );

    $filter = new Inlinefilters($filters_config);
    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
    $characteristics = ProductCharacteristics::get_data('', array('returnarray' => true));
    if(is_array($characteristics)) {
        foreach($characteristics as $characteristic) {
            $chars = $characteristic->get();
            $values = ProductCharacteristicValues::get_data(array('pcid' => $chars['pcid']), array('returnarray' => true));
            if(is_array($values)) {
                $totalval = count($values);
            }
            else {
                $totalval = 0;
            }
            eval("\$char_rows .= \"".$template->get('admin_characteristiclists_rows')."\";");
            unset($values, $totalval, $chars);
        }
    }
    else {
        $char_rows = '<tr><td>NA</td><td></td></tr>';
    }
    eval("\$popup_addchar = \"".$template->get('popup_createproductcharcteristic')."\";");
    eval("\$characteristic_list = \"".$template->get('admin_characteristiclists')."\";");
    output_page($characteristic_list);
}
else {
    if($core->input['action'] == 'do_addcharacteristic') {
        if(isset($core->input['characteristic']['title']) && !empty($core->input['characteristic']['title'])) {
            $characteristic = new ProductCharacteristics();
            $characteristic->set($core->input['characteristic']);
            $characteristic = $characteristic->save();
            switch($characteristic->get_errorcode()) {
                case 0:
                    output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                    break;
                case 2:
                    output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                    break;
                case 3:
                    output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
            }
        }
        else {
            output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
            exit;
        }
    }
}
