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
    /* Check if reply is possibly auto-responder */
    if(strstr(strtolower($data['subject']), 'auto')) {
        exit;
    }
    $request_key = $db->escape_string($subject[1]);
    //    $attendanceadddays = new AttendanceAddDays(array('identifier' => $request_key));
    $arorequest = new AroRequests();
    $approve=$arorequest->approve($data['from'],$core->user['uid']);
    if($approve){
    $arorequest->inform_nextapprover();
    }
    if($arorequest->is_approved()){
         $arorequest->update_arorequeststatus();
         $arorequest->notifyapprove();
    }

}
?>