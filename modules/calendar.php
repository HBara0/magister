<?php
$module['name'] = 'calendar';
$module['title'] = $lang->calendar;
$module['homepage'] = 'home';
$module['globalpermission'] = 'canAccessSystem';
$module['menu'] = array('file' => array('home', 'taskboard'),
        'title' => array('home', 'taskboard'),
        'permission' => array('canAccessSystem', 'canAccessSystem')
);
?>