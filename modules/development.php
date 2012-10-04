<?php
$module['name'] = 'development';
$module['title'] = $lang->development;
$module['homepage'] = 'requirementslist';
$module['globalpermission'] = 'canUseDevelopment';
$module['menu'] = array( 'file'		=> array('requirementslist'),
						 'title'	=> array('requirementslist'),
						 'permission' => array('canUseDevelopment')
);  
?>