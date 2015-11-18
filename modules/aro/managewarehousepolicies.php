<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managewarehousepolicies.php
 * Created:        @tony.assaad    Feb 3, 2015 | 11:07:51 AM
 * Last Update:    @tony.assaad    Feb 3, 2015 | 11:07:51 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['aro_canManageWarehousePolicies'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {

    if(isset($core->input['id']) && !empty($core->input['id'])) {
        $aroobj = new AroManageWarehousesPolicies($core->input['id'], false);
        $warehouse = $aroobj->get();
        $warehouse[effectiveFrom_output] = date($core->settings['dateformat'], $warehouse['effectiveFrom']);
        $warehouse[effectiveTo_output] = date($core->settings['dateformat'], $warehouse['effectiveTo']);

        $warehouse['effectiveFrom_formatted'] = date('d-m-Y', $warehouse['effectiveFrom']);
        $warehouse['effectiveTo_formatted'] = date('d-m-Y', $warehouse['effectiveTo']);

        $audittrailfields = array('createdOn', 'createdBy', 'modifiedOn', 'modifiedBy');
        foreach($audittrailfields as $field) {
            if(!empty($warehouse[$field])) {
                switch($field) {
                    case 'createdOn':
                    case 'modifiedOn':
                        $warehouse[$field.'_output'] = date($core->settings['dateformat'], $warehouse[$field]);
                        break;
                    default:
                        $user = new Users($warehouse[$field]);
                        if(is_object($user)) {
                            $warehouse[$field.'_output'] = $user->get_displayname();
                        }
                        break;
                }
                $field_strtolower = strtolower($field);
                $audittrail .= '<tr><td>'.$lang->$field_strtolower.'</td><td>'.$warehouse[$field.'_output'].'</td></tr>';
            }
        }
    }
    /* get warehouses of the affiliates users */
    $dal_config = array(
            'operators' => array('affid' => 'in'),
            'simple' => false,
            'returnarray' => true
    );
    $warehouse_objs = Warehouses::get_data(array('affid' => $core->user['affiliates'], 'isActive' => 1), $dal_config);
    $warehouse_list = parse_selectlist('warehousepolicy[warehouse]', 1, $warehouse_objs, $warehouse['warehouse'], '', '', array('width' => '50%'));

    /* parse select list of covered countries currencies */

    $currency['filter']['numCode'] = 'SELECT mainCurrency FROM countries where affid IS NOT NULL';
    $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
    $currencies['840'] = 'USD';
    $currencies['978'] = 'EURO';
    $currencies['826'] = 'GBP';
    if(is_array($curr_objs)) {
        foreach($curr_objs as $curr) {
            $currencies[$curr->numCode] = $curr->alphaCode;
        }
    }
    $currencies_list = parse_selectlist('warehousepolicy[currency]', '', $currencies, $warehouse['currency'], '', '', array('width' => '50%'));
    $uoms = Uom::get_data(array('isWeight' => 1));

    $reateuom = parse_selectlist('warehousepolicy[rate_uom]', '', $uoms, $warehouse['rate_uom'], '', '', array('width' => '50%'));
    eval("\$aro_managewarehousespolicies = \"".$template->get('aro_managewarehouses_policies')."\";");
    output_page($aro_managewarehousespolicies);
}
else if($core->input['action'] == 'do_perform_managewarehousepolicies') {

    unset($core->input['identifier'], $core->input['module'], $core->input['action']);
    $arowarepolicy = new AroManageWarehousesPolicies();
    $core->input['warehousepolicy']['effectiveFrom'] = strtotime($core->input['warehousepolicy']['effectiveFrom'].' 00:00:00');
    $core->input['warehousepolicy']['effectiveTo'] = strtotime($core->input['warehousepolicy']['effectiveTo'].' 23:59:59');
    if($core->input['warehousepolicy']['effectiveFrom'] > $core->input['warehousepolicy']['effectiveTo']) {
        output_xml('<status>false</status><message>'.$lang->errordate.'</message>');
        exit;
    }
    $arowarepolicy->set($core->input['warehousepolicy']);
    $arowarepolicy = $arowarepolicy->save();
    switch($arowarepolicy->get_errorcode()) {
        case 0:
        case 1:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            $error_output = $errorhandler->get_errors_inline();
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}<![CDATA[<br/>{$error_output}]]></message>");
            exit;
        case 3:
            output_xml('<status>false</status><message>'.$lang->warehousepoliciescoexist.'</message>');
            break;
    }
}