<?php

$module['name'] = 'events';
$module['title'] = $lang->events;
$module['homepage'] = 'eventslist';
$module['globalpermission'] = 'canAccessSystem';
$module['menu'] = array('file' => array('eventslist'),
    'title' => array('eventslist'),
    'permission' => array('canAccessSystem')
);
?>