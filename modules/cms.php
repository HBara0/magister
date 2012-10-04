<?php
$module['name'] = 'cms';
$module['title'] = 'CMS';
$module['homepage'] = 'listpages';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' 		  => array('listpages', 'settings', 'managenews'),
						'title'		 => array('listpages', 'cmssettings', 'managenews'),
						'permission'	=> array('canAccessSystem', 'canAccessSystem', 'canAccessSystem')
						);
?>