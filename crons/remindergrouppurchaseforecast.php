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
    $lang->load('messages');
    $date['to'] = date(TIME_NOW);
    $uids = $db->query("SELECT gpf.createdBy AS GPfor,bbl.createdBy AS BBline FROM ".Tprefix."grouppurchase_forecast AS gpf,".Tprefix."budgeting_budgets_lines AS bbl");
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
                        continue;
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
    }
    send_mail($data_array);
//$email_content should be a three dimensional array:1st uid 2cnd:grouppurchsse forecast primary 3rd: the data(affid,spid,year)
    function send_mail($email_content) {
        foreach($email_content as $uid => $rest) {
            $user_obj = new Users($uid);
            $email = $user_obj->email;
            $forecast['reporttype'] = 'basic';
            foreach($rest as $gpfid => $values) {
                foreach($values as $key => $value) {
                    if($key == 'affid') {
                        $forecast['affiliates'][] = $value;
                    }
                    elseif($key == 'supplier') {
                        $forecast['suppliers'][] = $value;
                    }
                    elseif($key == 'year') {
                        $forecast['years'][] = $value;
                    }
                }
            }
            $forecast['affiliates'] = array_unique($forecast['affiliates']);
            $forecast['suppliers'] = array_unique($forecast['suppliers']);
            $forecast['years'] = array_unique($forecast['years']);
            $sent_query = http_build_query(array('forecast' => $forecast));
            $url = "http://127.0.0.1/ocos/index.php?module=grouppurchase/previewforecast&".$sent_query;
            $url2 = "http://127.0.0.1/ocos/index.php?module=grouppurchase/generateforecast";
            $email_message = "<h3>Kindly check that your forecasts are valid, otherwise please update them</h3>";
            $email_message.= '<a href='.$url.'>Check</a><br>';
            $email_message.= '<a href='.$url2.'>Update</a>';
            $email_data = array(
                    'from_email' => 'admin@ocos.orkila.com',
                    'from' => 'Orkila Reminder System',
                    'to' => $email,
                    'subject' => 'Update Group Purchase Forcast Reminder',
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