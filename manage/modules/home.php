<?php

$module['name'] = 'home';
$module['title'] = $lang->home;
$module['homepage'] = 'stats';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' => array('stats'),
    'title' => array('home'),
    'permission' => array('canAdminCP')
);
?>