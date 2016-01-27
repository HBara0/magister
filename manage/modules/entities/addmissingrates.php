<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: addmissingrates.php
 * Created:        @hussein.barakat    27-Jan-2016 | 08:55:33
 * Last Update:    @hussein.barakat    27-Jan-2016 | 08:55:33
 */

if(!defined("DIRECT_ACCESS")) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canManageSuppliers'] == 0) {
//    error($lang->sectionnopermission);
//    exit;
}
if(!$core->input['action']) {

    $currencies_obj = Currencies::get_data('', array('returnarray' => true));
    $sourcecurrencies_list = parse_selectlist('rate[sourcecurrency][]', 1, $currencies_obj, '', 1);
    $tocurrencies_list = parse_selectlist('rate[tocurrency][]', 1, $currencies_obj, '', 1);

    eval("\$page = \"".$template->get('admin_entities_addmissingrates')."\";");
    output_page($page);
}
else {
    if($core->input['action'] == 'do_perform_addmissingrates') {
        if(!is_array($core->input['rate'])) {
            output_xml("<status>false</status><message>{$lang->fillallfields}</message>");
            exit;
        }
        foreach($core->input['rate'] as $field => $vals) {
            if(empty($vals)) {
                output_xml("<status>false</status><message>{$lang->fillallfields} : {$field}</message>");
                exit;
            }
        }
        $fromdate = strtotime($core->input['rate']['fromDate']);
        $tomdate = strtotime($core->input['rate']['toDate']);
        $changedrates = Currencies::get_missingfxrates($fromdate, $tomdate, $core->input['rate']['sourcecurrency'], $core->input['rate']['tocurrency']);
        if($changedrates) {
            output_xml("<status>true</status><message>{$lang->success}</message>");
            exit;
        }
        else {
            output_xml("<status>false</status><message>{$lang->erroroccured}</message>");
            exit;
        }
    }
}