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

//if($_REQUEST['authkey'] == 'etc') {
$lang = new Language('english');
$lang->load('messages');
$date['to'] = date(TIME_NOW);
$uids = $db->query("SELECT gpf.createdBy FROM ".Tprefix."grouppurchase_forecast AS gpf INNER JOIN ".Tprefix."budgeting_budgets_lines AS bbl ON (gpf.createdBy=bbl.createdBy)");
if($db->num_rows($uids) > 0) {
    while($uid = $db->fetch_assoc($uids)) {
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
                    $user = new Users($gpforecasts_obj->createdBy);
                    $usergroup = new UserGroups($user->gid);
                    if($usergroup->canUseGroupPurchase == 0) {
                        //     continue;
                    }
                    $affiliate = new Affiliates($gpforecasts_obj->affid);
                    $ent = new Entities($gpforecasts_obj->spid);
                    $email = $user->email;
                    $url = $core->settings['rootdir']."ocos/index.php?module=grouppurchase/createforecast";
                    $table = "<table><thead><td>Affiliate</td><td>Year</td><td>Supplier</td><td>Last Modified On</td></thead>";
                    $table.="<tbody><tr><td>".$affiliate->get_displayname()."</td><td>".$ent->get_displayname()."</td><td>".$gpforecasts_obj->year."</td></tr></tbody></table>";
                    $email_message = "<h2>Kindly check that your forecasts are valid, otherwise please update them</h2>";
                    $email_message.='Check: '.$table;
                    $email_message.='<form><input type="button" value="Update Forecast" onclick="window.location.href='.$url.'"/></form>';
                    $email_data = array(
                            'from_email' => 'admin@ocos.orkila.com',
                            'from' => 'Orkila Reminder System',
                            'to' => $email,
                            'subject' => 'Update Group Purchase Forcast Reminder',
                            'message' => $email_message,
                    );
                    $mail = new Mailer($email_data, 'php');
                    if($mail->get_status() === true) {

                    }
                    else {
                        continue;
                    }
                }
            }
        }
    }
}