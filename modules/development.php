<?php
$module['name'] = 'development';
$module['title'] = $lang->development;
$module['homepage'] = 'requirementslist';
$module['globalpermission'] = 'canUseDevelopment';
$module['menu'] = array( 'file'		=> array('requirementslist','createrequirment'),
						 'title'	=> array('requirementslist','createrequirment'),
						 'permission' => array('canUseDevelopment','canUseDevelopment')
);  
?>