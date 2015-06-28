<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: productcharacteristicprofile.php
 * Created:        @hussein.barakat    Jun 3, 2015 | 10:06:06 AM
 * Last Update:    @hussein.barakat    Jun 3, 2015 | 10:06:06 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if(!$core->input['action']) {
    if($core->input['pcid'] && !empty($core->input['pcid'])) {
        $pcid = $db->escape_string($core->input['pcid']);
        $productchar = new ProductCharacteristics($pcid);
        $characteristic = $productchar->get();
        $charvalues = ProductCharacteristicValues::get_data(array('pcid' => $productchar->pcid), array('returnarray' => true));
        if(is_array($charvalues)) {
            $itemscount['value'] = 0;
            foreach($charvalues as $charvalue) {
                $itemscount['value'] ++;
                $valuename = $charvalue->get_displayname();
                eval("\$value_rows .= \"".$template->get('profiles_productcharacteristic_values_rows')."\";");
            }
        }
        else {
            $valuename = 'NA';
            $itemscount['value'] = 0;
            eval("\$value_rows .= \"".$template->get('profiles_productcharacteristic_values_rows')."\";");
        }
        eval("\$values_list = \"".$template->get('profiles_productcharacteristic_values')."\";");
        eval("\$profile_char = \"".$template->get('profiles_productcharacteristic')."\";");
        output_page($profile_char);
    }
}