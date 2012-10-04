<?php
$module['name'] = 'profiles';
$module['title'] = 'Profiles';
$module['homepage'] = 'affiliateslist';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' 		  => array('affiliateslist', 'supplierslist', 'customerslist'),
						'title'		 => array('affiliateslist', 'supplierslist', 'customerslist'),
						'permission'	=> array('canAccessSystem', 'canAccessSystem', 'canAccessSystem')
						);
?>