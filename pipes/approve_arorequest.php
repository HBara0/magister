#!/usr/bin/php -q
<?php
$dir = dirname(dirname(__FILE__)).'/';
if(!$dir) {
    $dir = '..';
}
require_once $dir.'/inc/init.php';

$pipe = new Pipe();
$data = $pipe->get_data();

$lang = new Language('english');
$lang->load('aro_meta');

if(preg_match("/\[([a-zA-Z0-9]+)\]$/", $data['subject'], $subject)) {
    /* Check if reply ia possiblity auto-responder */
    if(strstr(strtolower($data['subject']), 'auto')) {
        exit;
    }
    $request_key = $db->escape_string($subject[1]); //check aro with inputchecksum including "_"
    $arorequest = AroRequests::get_data(array('inputChecksum' => $request_key));
    $user = Users::get_data(array('email' => $data['from']));  //Check from email

    if(strstr(strtolower($data['subject']), 'message')) {
        $data['message'] = strstr($data['message'], '__ARO NOTIFICATION__', true);
        $arorequestmessage_obj = new AroRequestsMessages();
        $arorequestmessage_obj = $arorequestmessage_obj->create_message(array('message' => $data['message']), $arorequest->aorid, array('source' => 'emaillink'));
        if($arorequestmessage_obj->get_errorcode() == 0) {
            $arorequestmessage_obj = $arorequestmessage_obj->send_message();
        }
    }
    else {
        $aproval = AroRequestsApprovals::get_data(array('aorid' => $arorequest->aorid, 'uid' => $user->uid), array('simple' => false));
        if(is_object($aproval)) {
            $timesapproved = $aproval->timesApproved + 1;
        }
        $approve = $arorequest->approve($user, $timesapproved);
        if($approve) {
            $arorequest->inform_nextapprover();
        }
        if($arorequest->is_approved()) {
            $arorequest->update_arorequeststatus();
            $arorequest->notifyapprove();
        }
    }
}
?>