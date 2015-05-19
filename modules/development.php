<?php
$module['name'] = 'development';
$module['title'] = $lang->development;
$module['homepage'] = 'requirementslist';
$module['globalpermission'] = 'canUseDevelopment';
$module['menu'] = array('file' => array('requirementslist', 'createrequirement', 'requirementchangeslist', 'bugslist'),
        'title' => array('requirementslist', 'createrequirement', 'requirementchange', 'bugslist'),
        'permission' => array('canUseDevelopment', 'development_canCreateReq', 'canUseDevelopment', 'canUseDevelopment')
);
?>