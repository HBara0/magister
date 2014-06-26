<?php
$module['name'] = 'development';
$module['title'] = $lang->development;
$module['homepage'] = 'requirementslist';
$module['globalpermission'] = 'canUseDevelopment';
$module['menu'] = array('file' => array('requirementslist', 'createrequirement', 'bugslist'),
        'title' => array('requirementslist', 'createrequirement', 'bugslist'),
        'permission' => array('canUseDevelopment', 'development_canCreateReq', 'canUseDevelopment')
);
?>