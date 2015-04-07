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
    $uids = $db->query("SELECT DISTINCT gpf.createdBy AS GPfor,bbl.createdBy AS BBline FROM ".Tprefix."grouppurchase_forecast AS gpf,".Tprefix."budgeting_budgets_lines AS bbl WHERE (DATEDIFF(CURDATE(),FROM_UNIXTIME(gpf.createdOn))>30 AND DATEDIFF(CURDATE(),FROM_UNIXTIME(gpf.modifiedOn))>30)");
    if($db->num_rows($uids) > 0) {
        while($uid = $db->fetch_assoc($uids)) {
            foreach($uid as $key => $value) {
                if($value != 0) {
                    $userids[] = $value;
                }
            }
        }
    }
    $userid = array_unique($userids);
    foreach($userid as $uid) {
        $gpforecasts_objs = GroupPurchaseForecast::get_data(array('createdBy' => $uid), array('returnarray' => true, 'simple' => false));
        if(!empty($gpforecasts_objs)) {
            foreach($gpforecasts_objs as $gpforecasts_obj) {
                if($gpforecasts_obj->modifiedOn == 0) {
                    $date['from'] = $gpforecasts_obj->createdOn;
                }
                else {
                    $date['from'] = $gpforecasts_obj->modifiedOn;
                }
                $dif_month = (date('Y', $date['to']) - date('Y', $date['from'])) * 12 + (date('m', $date['to']) - date('m', $date['from']));
                if($dif_month >= 1) {
                    $user = new Users($uid);
                    $usergroup = new UserGroups($user->gid);
                    if($usergroup->canUseGroupPurchase == 0) {
                        //  continue;
                    }
//
//                $affiliate = new Affiliates($gpforecasts_obj->affid);
//                $ent = new Entities($gpforecasts_obj->spid);
//                $email = $user->email;
//                $url = $core->settings['rootdir']."ocos/index.php?module=grouppurchase/createforecast";
//                $table = "<table><thead><td>Affiliate</td><td>Year</td><td>Supplier</td><td>Last Modified On</td></thead>";
//                $table.="<tbody><tr><td>".$affiliate->get_displayname()."</td><td>".$ent->get_displayname()."</td><td>".$gpforecasts_obj->year."</td></tr></tbody></table>";
//                $email_message = "<h2>Kindly check that your forecasts are valid, otherwise please update them</h2>";
//                $email_message.='Check: '.$table;
//                $email_message.='<form><input type="button" value="Update Forecast" onclick="window.location.href='.$url.'"/></form>';


                    $data_array[$uid][$gpforecasts_obj->gpfid]['affid'] = $gpforecasts_obj->affid;
                    $data_array[$uid][$gpforecasts_obj->gpfid]['supplier'] = $gpforecasts_obj->spid;
                    $data_array[$uid][$gpforecasts_obj->gpfid]['year'] = $gpforecasts_obj->year;
                }
            }
        }
        else {
            $budgetlines_objs = BudgetLines::get_data(array('createdBy' => $uid), array('returnarray' => true, 'simple' => false));
            if(!empty($budgetlines_objs)) {
                foreach($budgetlines_objs as $budgetlines_obj) {
                    $user = new Users($uid);
                    $usergroup = new UserGroups($user->gid);
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
    send_mail($data_array);
//$email_content should be a three dimensional array:1st uid 2cnd:grouppurchsse forecast primary 3rd: the data(affid,spid,year)
    function send_mail($email_content) {
        global $lang;
        foreach($email_content as $uid => $rest) {
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
            $url = DOMAIN."/index.php?module=grouppurchase/previewforecast&".$sent_query;
            $url2 = DOMAIN."/index.php?module=grouppurchase/generateforecast";
            $email_message = "<h3>".$lang->checkforecast."</h3>";
            $email_message.= '<a target="_blank" href='.$url.'>'.$lang->check.'</a><br>';
            $email_message.= '<a target="_blank" href='.$url2.'>'.$lang->update.'</a>';
            $email_data = array(
                    'from_email' => 'admin@ocos.orkila.com',
                    'from' => 'Orkila Reminder System',
                    'to' => $email,
                    'subject' => $lang->gpreminder,
                    'message' => $email_message,
            );
            $mail = new Mailer($email_data, 'php');
            echo($email_message);
            if($mail->get_status() === true) {

            }
            else {
                continue;
            }
        }
    }

}