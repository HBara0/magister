<?php

$module['name'] = 'users';
$module['title'] = $lang->users;
$module['homepage'] = 'view';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' => array('add', 'edit', 'view'),
    'title' => array('add', 'edit', 'view'),
    'permission' => array('canAdminCP', 'canAdminCP', 'canAdminCP')
);
?>