<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: remindergrouppurchaseupdate.php
 * Created:        @hussein.barakat    Mar 26, 2015 | 1:29:46 PM
 * Last Update:    @hussein.barakat    Mar 26, 2015 | 1:29:46 PM
 */

require_once '../inc/init.php';

if($_REQUEST['authkey'] == 'odsfaddkjj!hre23jh4k2_3h49g3jh') {
    $lang = new Language('english');
    $lang->load('grouppurchase_meta');
    $date['to'] = date(TIME_NOW);
    $uids = $db->query("SELECT DISTINCT gpf.businessMgr AS GPfor, bbl.businessMgr AS BBline FROM ".Tprefix."grouppurchase_forecastlines AS gpf,".Tprefix."budgeting_budgets_lines AS bbl WHERE (gpf.inputChecksum <> '' AND bbl.inputChecksum <> '' )AND (DATEDIFF(CURDATE(),FROM_UNIXTIME(gpf.createdOn))>30 AND (gpf.modifiedOn > 0 AND DATEDIFF(CURDATE(),FROM_UNIXTIME(gpf.modifiedOn))>30))");
    if($db->num_rows($uids) > 0) {
        while($uid = $db->fetch_assoc($uids)) {
            foreach($uid as $key => $value) {
                if($value != 0) {
                    $userids[] = $value;
                }
            }
        }
    }
    if(!is_array($userids)) {
        exit;
    }
    $userid = array_unique($userids);
    foreach($userid as $uid) {
        $gpforecastslines_objs = GroupPurchaseForecastLines::get_data(array('businessMgr' => $uid), array('returnarray' => true, 'simple' => false));
        if(!empty($gpforecastslines_objs)) {
            foreach($gpforecastslines_objs as $gpforecastline_obj) {
                if($gpforecastline_obj->modifiedOn == 0) {
                    $date['from'] = $gpforecastline_obj->createdOn;
                }
                else {
                    $date['from'] = $gpforecastline_obj->modifiedOn;
                }
                $dif_month = (date('Y', $date['to']) - date('Y', $date['from'])) * 12 + (date('m', $date['to']) - date('m', $date['from']));
                if($dif_month >= 1) {
                    $user = new Users($uid);
                    $usergroup = new UserGroups($user->gid, false);
                    if($usergroup->canUseGroupPurchase == 0) {
                        continue;
                    }

                    $forecast = $gpforecastline_obj->get_gpforecast();
                    $data_array[$uid][$gpforecastline_obj->gpfid]['affid'] = $forecast->affid;
                    $supp = new Entities($forecast->spid);
                    if(is_object($supp) && !empty($supp->eid)) {
                        if($supp->isActive == 1) {
                            $data_array[$uid][$gpforecastline_obj->gpfid]['supplier'] = $forecast->spid;
                        }
                    }
                    $data_array[$uid][$gpforecastline_obj->gpfid]['year'] = $forecast->year;
                }
            }
        }
        else {
            $budgetlines_objs = BudgetLines::get_data(array('businessMgr' => $uid), array('returnarray' => true, 'simple' => false));
            if(!empty($budgetlines_objs)) {
                foreach($budgetlines_objs as $budgetlines_obj) {
                    $user = new Users($uid);
                    $usergroup = new UserGroups($user->gid, false);
                    if($usergroup->canUseGroupPurchase == 0) {
                        continue;
                    }

                    $data_array[$uid][0]['affid'] = 0;
                    $data_array[$uid][0]['supplier'] = 0;
                    $data_array[$uid][0]['year'] = 0;
                }
            }
        }
    }

    if(is_null($data_array) || is_empty($data_array)) {
        exit;
    }

    foreach($data_array as $uid => $rest) {
        $user_obj = new Users($uid);
        $email = $user_obj->email;
        if($email == false) {
            continue;
        }
        $stuffings['reporttype'] = 'basic';
        foreach($rest as $gpfid => $values) {
            foreach($values as $key => $value) {
                if($key == 'affid') {
                    $stuffings['affiliates'][] = $value;
                }
                elseif($key == 'supplier') {
                    $stuffings['suppliers'][] = $value;
                }
                elseif($key == 'year') {
                    $stuffings['years'][] = $value;
                }
            }
        }
        $stuffings['affiliates'] = array_unique($stuffings['affiliates']);
        $stuffings['suppliers'] = array_unique($stuffings['suppliers']);
        $stuffings['years'] = array_unique($stuffings['years']);
        $forecast = base64_encode(serialize($stuffings));
        if($stuffings['affiliates'][0] == 0) {
            $forecast = 0;
        }
        foreach($stuffings['affiliates'] as $affid) {
            $affil_obj = new Affiliates($affid);
            $message['affiliates'][] = $affil_obj->get_displayname();
        }
        foreach($stuffings['suppliers'] as $supid) {
            $suppliers_obj = new Entities($supid);
            $message['suppliers'][] = $suppliers_obj->get_displayname();
        }
        $message['affiliates'] = implode(',', $message['affiliates']);
        $message['suppliers'] = implode(',', $message['suppliers']);
        $message['years'] = implode(',', $stuffings['years']);
        unset($stuffings);
//        foreach($stuffings as $key => $value) {
//            if(!is_array($value)) {
//                $key = base64_encode($key);
//                $forecast[$key] = base64_encode($value);
//            }
//            else {
//                $key = base64_encode($key);
//                foreach($value as $key_2 => $value_2) {
//                    $key_2 = base64_encode($key_2);
//                    $forecast[$key][$key_2] = base64_encode($value_2);
//                }
//            }
//        }
        $sent_query = http_build_query(array('stuffings' => $forecast));
        $buttonstyle = 'style="font: bold 11px Arial;text-decoration: none; background-color: #EEEEEE;color: #333333;padding: 2px 6px 2px 6px; border-top: 1px solid #CCCCCC;border-right: 1px solid #333333;border-bottom: 1px solid #333333;border-left: 1px solid #CCCCCC;"';
        $url = DOMAIN."/index.php?module=grouppurchase/previewforecast&".$sent_query;
        $url2 = DOMAIN."/index.php?module=grouppurchase/createforecast";
        $check_link = '<a '.$buttonstyle.' target="_blank" href='.$url.'>'.$lang->check.'</a>';
        if($forecast == 0 && !is_string($forecast)) {
            $check_link = '';
        }
        $email_message = 'Dear '.$user_obj->displayName.',<br /> ';
        $lang->checkforecast = $lang->sprint($lang->checkforecast, $message['affiliates'], $message['suppliers'], $message['years']);
        $email_message .= $lang->checkforecast;
        $email_message .= '<br /><br />'.$check_link;
        $email_message .= '<br /><br /><a '.$buttonstyle.' target="_blank" href='.$url2.'>'.$lang->update.'</a>';
        $email_data = array(
                'from_email' => $core->settings['maileremail'],
                'from' => 'Orkila Mailer',
                'to' => $email,
                'cc' => 'rima.saad@orkila.com',
                'subject' => $lang->gpreminder,
                'message' => $email_message,
        );

        $mail = new Mailer($email_data, 'php');
        unset($email_message);
    }
}
?>