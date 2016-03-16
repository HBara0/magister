<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: notifyfinance.php
 * Created:        @hussein.barakat    Oct 20, 2015 | 5:08:36 PM
 * Last Update:    @hussein.barakat    Oct 20, 2015 | 5:08:36 PM
 */
require '../inc/init.php';
$userslist = array();
$affiliates = Affiliates::get_affiliates(array('isActive' => 1), array('returnarray' => true));
if(is_array($affiliates)) {
    foreach($affiliates as $affiliate) {
        if(!empty($affiliate->finManager)) {
            $finman = new Users($affiliate->finManager);
            if($finman->gid != 7 && !in_array($finman->uid, $userslist)) {
                $userslist = $finman->uid;
                $relaffilateslist = Affiliates::get_affiliates(array('finManager' => $finman->uid), array('returnarray' => true));
                if(is_array($relaffilateslist)) {
                    foreach($relaffilateslist as $aff) {
                        $relaffnames = $aff->get_displayname();
                    }
                    $email_message = 'Dear '.$finman->get_displayname().', please fill the Budget FX Rates for these following Affiliates : '.implode('\n', $relaffnames).'\n The link is here: https://ocos.orkila.com/index.php?module=budgeting/listfxrates';
                }
                $email_data = array(
                        'to' => $finman->email,
                        'from_email' => $core->settings['maileremail'],
                        'from' => 'OCOS Mailer',
                        'subject' => 'URGENT : Update budgeting FX rates',
                        'message' => $email_message
                );
                $mail = new Mailer($email_data, 'php');
                unset($email_message, $relaffilateslist);
            }
        }
    }
}
