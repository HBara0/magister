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
$lang->load('attendance_messages');

if(preg_match("/\[([a-zA-Z0-9]+)\]$/", $data['subject'], $subject)) {
    /* Check if reply is possibly auto-responder */
    if(strstr(strtolower($data['subject']), 'auto')) {
        exit;
    }

    $request_key = $db->escape_string($subject[1]);
    $data['from'] = $core->user['uid'];
    
    $arorequest = new AroRequests();

    $arorequest->approve($data['from']);

    //notify approve user
    $arorequest->update_requeststatus();
    $arorequest->inform_nextapprover();
    
    ///////if ()
    $arorequest->notifyapprove();
}
?>