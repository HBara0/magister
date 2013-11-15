<?php
$module['name'] = 'meetings';
$module['title'] = $lang->meetings;
$module['homepage'] = 'create';
$module['globalpermission'] = 'canUseMeetings';
$module['menu'] = array('file' => array('create', 'list', 'minutesmeeting'),
		'title' => array('create', 'listmeeting', 'mof'),
		'permission' => array('meetings_canCreateMeeting', 'meetings_canCreateMeeting', 'meetings_canCreateMeeting')
);
?>