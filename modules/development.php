<?php
$module['name'] = 'development';
$module['title'] = $lang->development;
$module['homepage'] = 'requirementslist';
$module['globalpermission'] = 'canUseDevelopment';
$module['menu'] = array( 'file'		=> array('requirementslist', 'createrequirement'),
						 'title'	=> array('requirementslist', 'createrequirement'),
						 'permission' => array('canUseDevelopment', 'development_canCreateReq')
);  
?>