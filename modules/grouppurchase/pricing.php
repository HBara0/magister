<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Price products
 * $module: grouppurchase
 * $id: pricing.php
 * Created:	   	 @najwa.kassem	November 2, 2010 | 10:00 AM
 * Last Update: 	@zaher.reda 	February 11, 2011 | 11:11 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['grouppurchase_canPrice'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    $rowid = 2;
    $methods = array('1' => 'CNF', '2' => 'FOB', '3' => 'Ex Works', '4' => 'CIP', '5' => 'CIF');
    $units = array('1' => 'MT', '2' => 'Unit', '3' => 'Item', '4' => 'Gram', '5' => 'Mg', '6' => 'Kg');

    $pricingmethods_mainlist = parse_selectlist('pricingMethod[1]', 1, $methods, 0, 0);
    $units_mainlist = parse_selectlist('unit[1]', 1, $units, 0, 0);

    $pricingmethods_list = parse_selectlist("pricingMethod[2]", 1, $methods, 0, 0);
    $units_list = parse_selectlist("unit[2]", 1, $units, 0, 0);

    $query = $db->query("SELECT a.affid, aem.eid, a.name as affname 
						FROM ".Tprefix."affiliates a JOIN ".Tprefix."affiliatedentities ae ON (a.affid=ae.affid) JOIN ".Tprefix."assignedemployees aem ON (ae.eid=aem.eid)
						WHERE aem.uid={$core->user[uid]}"); // AND ae.eid=".$db->escape_string($core->input['spid'])."
    $affiliates[0] = '';
    while($affiliate = $db->fetch_array($query)) {
        $affiliates[$affiliate['affid']] = $affiliate['affname'];
    }
    $affiliates_list = parse_selectlist("affiliate[2]", 1, $affiliates, 0, 0);

    eval("\$pricing = \"".$template->get('grouppurchase_pricing')."\";");
    output_page($pricing);
}
else {
    if($core->input['action'] == 'do_perform_pricing') {
        if(is_empty($core->input['pid'], $core->input['spid'])) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }

        $newprice = array(
                'pid' => $core->input['pid'],
                'setBy' => $core->user['uid'],
                'setTime' => TIME_NOW,
                'notes' => $core->input['notes']);

        $main_pricing_query = $db->insert_query('grouppurchase_pricing', $newprice);

        if($main_pricing_query) {
            $gpid = $db->last_id();
            $log->record($core->input['pid']);

            if(is_array($core->input['affiliate'])) {
                $processed_affiliates = array();
                foreach($core->input['affiliate'] as $key => $affiliate) {
                    if(in_array($core->input['affiliate'][$key], $processed_affiliates)) {
                        continue;
                    }

                    if($core->input['affiliate'][$key] == '') {
                        continue;
                    }

                    $valid_date = explode('-', $core->input['validThrough'][$key]);

                    $newprice_detail = array(
                            'gppid' => $gpid,
                            'affid' => $core->input['affiliate'][$key],
                            'pricingMethod' => $core->input['pricingMethod'][$key],
                            'price' => $core->input['price'][$key],
                            'unit' => $core->input['unit'][$key],
                            'validThrough' => mktime(0, 0, 0, $valid_date[1], $valid_date[0], $valid_date[2]),
                            'remark' => $core->input['remark'][$key]);
                    $detailed_pricing_query = $db->insert_query('grouppurchase_pricingdetails', $newprice_detail);
                    $processed_affiliates[] = $core->input['affiliate'][$key];

                    $log->record($core->input['pid'], $core->input['affiliate'][$key]);
                }
            }
        }

        if($detailed_pricing_query) {
            $lang->load('messages');
            $query = $db->query("SELECT u.uid, u.email, afe.affid 
								FROM ".Tprefix."users u JOIN ".Tprefix."assignedemployees ae ON (ae.uid=u.uid) JOIN ".Tprefix."affiliatedemployees afe ON (u.uid=afe.uid) JOIN ".Tprefix."affiliatedentities aen ON (aen.affid=afe.affid) 
								WHERE ae.eid='".$db->escape_string($core->input['spid'])."'");
            $inform_employees = array();
            while($employee = $db->fetch_array($query)) {
                if($core->user['uid'] == $employee['uid']) {
                    $email_data_cc = $employee['email'];
                }

                if(!array_key_exists($employee['uid'], $inform_employees)) {
                    $inform_employees[$employee['uid']]['email'] = $employee['email'];
                }

                $inform_employees[$employee['uid']]['affid'][] = $employee['affid'];

                if(!empty($employee['assistant'])) {
                    //$inform_employees[] = $employee['assistant']['email'];
                }
            }

            if(is_array($inform_employees)) {
                $methods = array('1' => 'CNF', '2' => 'FOB', '3' => 'Ex Works', '4' => 'CIP', '5' => 'CIF');
                $units = array('1' => 'MT', '2' => 'Unit', '3' => 'Item', '4' => 'Gram', '5' => 'Mg', '6' => 'Kg');

                foreach($inform_employees as $uid => $employee) {
                    if($core->user['uid'] == $uid) {
                        continue;
                    }

                    $affiliate_prices = '';
                    $email_data = array(
                            'from_email' => 'no-reply@ocos.orkila.com',
                            'from' => 'OCOS Mailer',
                            'to' => $employee['email'],
                            'cc' => $email_data_cc,
                            'subject' => $lang->sprint($lang->grouppurchase_pricingsubject, trim($core->input['product_name']))
                    );

                    $add_global_price = false;
                    foreach($employee['affid'] as $affid) {
                        $index = array_search($affid, $core->input['affiliate']);
                        if(!empty($index)) {
                            if(!in_array($affid, $affiliate_names)) {
                                $affiliate_names[$affid] = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."affiliates WHERE affid='{$affid}'"), 'name');
                            }
                            $affiliate_prices = '<li>'.$affiliate_names[$affid].': '.$core->input['price'][$index].'/'.$units[$core->input['unit'][$index]].' - '.$methods[$core->input['pricingMethod'][$index]].' ('.$core->input['remark'][$index].')</li>';
                        }
                        else {
                            $add_global_price = true;
                        }
                    }

                    if($add_global_price == true) {
                        $affiliate_prices .= '<li>'.$lang->globalprice.': '.$core->input['price'][1].'/'.$units[$core->input['unit'][1]].' - '.$methods[$core->input['pricingMethod'][1]].' ('.$core->input['remark'][1].')</li>';
                    }

                    if(!empty($affiliate_prices)) {
                        $affiliate_prices = '<ul>'.$affiliate_prices.'</ul>';
                        $email_data['message'] = $lang->sprint($lang->grouppurchase_pricingmessage, $core->input['product_name'], $affiliate_prices, $core->input['notes']);
                        $mail = new Mailer($email_data, 'php');
                    }
                }

                if($mail) {
                    output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                }
                else {
                    output_xml("<status>false</status><message>{$lang->errorsendingemail}</message>");
                }
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
        }
    }
}
?>