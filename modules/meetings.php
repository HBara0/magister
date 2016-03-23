<?php
$module['name'] = 'meetings';
$module['title'] = $lang->meetings;
$module['homepage'] = 'list';
$module['globalpermission'] = 'canUseMeetings';
$module['menu'] = array('file' => array('create', 'list', 'minutesmeeting'),
        'title' => array('createmeeting', 'listmeeting', 'minutesmeeting'),
        'permission' => array('meetings_canCreateMeeting', 'meetings_canCreateMeeting', 'meetings_canCreateMeeting')
);
?>