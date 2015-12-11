<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: aro_approvalreminder.php
 * Created:        @rasha.aboushakra    Dec 3, 2015 | 11:37:05 AM
 * Last Update:    @rasha.aboushakra    Dec 3, 2015 | 11:37:05 AM
 */

require_once '../inc/init.php';
$lang = new Language('english');
$lang->load('aro_meta');

$arorequests = AroRequests::get_data(array('isFinalized' => 1, 'isApproved' => 0), array('returnarray' => true));
if(is_array($arorequests)) {
    foreach($arorequests as $arorequest) {
        $sequence = 1;
        $lastapproval = $arorequest->get_lastapproval();
        if(is_object($lastapproval)) {
            $sequence = $lastapproval->sequence + 1;
        }
        $nextapproval = AroRequestsApprovals::get_data(array('sequence' => $sequence, 'aorid' => $arorequest->aorid));
        if(is_object($nextapproval) && TIME_NOW >= strtotime('+1 day', $nextapproval->emailRecievedDate)) {
            $reminders[$nextapproval->uid][] = $arorequest;
        }
    }
}

if(is_array($reminders)) {
    foreach($reminders as $uid => $userreminders) {
        $nextapprover = Users::get_data(array('uid' => $uid));

        if(is_array($userreminders)) {
            $message = "Some AROs are pending your approval. Click on each link Go to the approval process of each following the below links: <br/>";
            foreach($userreminders as $arorequest) {
                if(!is_object($arorequest)) {
                    continue;
                }
                $nextapproval = AroRequestsApprovals::get_data(array('aorid' => $arorequest->aorid, 'uid' => $uid));
                $aroaffiliate_obj = Affiliates::get_affiliates(array('affid' => $arorequest->affid));
                $purchasteype_obj = PurchaseTypes::get_data(array('ptid' => $arorequest->orderType));
                $approve_link = $core->settings['rootdir']."/index.php?module=aro/managearodouments&requestKey=".base64_encode($arorequest->data['identifier'])."&id=".$arorequest->aorid."&referrer=toapprove";
                $approve_link = '<a href="'.$approve_link.'">Aro Request ['.$arorequest->orderReference.'] '.$aroaffiliate_obj->get_displayname().' '.$purchasteype_obj->get_displayname().'</a>';
                $message .= $approve_link;
                if(!empty($nextapproval->emailRecievedDate)) {
                    $message .= " recieved on ".date($core->settings['dateformat'], $nextapproval->emailRecievedDate);
                }
                $message .= '<br/>';
            }
            $email_data = array(
                    'from' => 'ocos@orkila.com',
                    'to' => $nextapprover->email,
                    'subject' => "ARO Requests pending your approval",
                    'message' => $message,
            );

            $mailer = new Mailer();
            $mailer = $mailer->get_mailerobj();
            $mailer->set_type();
            $mailer->set_from(array('name' => 'ARO Approval Reminder', 'email' => $email_data['from']));
            $mailer->set_subject($email_data['subject']);
            $mailer->set_message($email_data['message']);
            $mailer->set_to($email_data['to']);
            $mailer->send();
            //  $x = $mailer->debug_info();
            //  print_R($x);
            // exit;
        }
    }
}
?>