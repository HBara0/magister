<?php

$module['name'] = 'users';
$module['title'] = $lang->users;
$module['homepage'] = 'view';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' => array('add', 'view'),
    'title' => array('add', 'view'),
    'permission' => array('canAdminCP', 'canAdminCP')
);
?>