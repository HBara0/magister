<?php
$module['name'] = 'calendar';
$module['title'] = $lang->calendar;
$module['homepage'] = 'home';
$module['globalpermission'] = 'canAccessSystem';
$module['menu'] = array('file' => array('home', 'tasksboard'),
        'title' => array('home', 'tasksboard'),
        'permission' => array('canAccessSystem', 'canAccessSystem')
);
?>