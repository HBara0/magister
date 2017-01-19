<?php

$module['name'] = 'config';
$module['title'] = $lang->config;
$module['homepage'] = 'settings';
$module['globalpermission'] = 'canChangeSettings';
$module['menu'] = array('file' => array('settings'),
    'title' => array('settings'),
    'permission' => array('canChangeSettings')
);
?>