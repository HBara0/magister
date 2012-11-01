<?php
$module['name'] = 'sourcing';
$module['title'] = $lang->sourcing;
$module['homepage'] = 'listpotentialsupplier';
$module['globalpermission'] = 'canUseSourcing';
$module['menu'] = array('file' 		  => array('managesupplier', 'listpotentialsupplier', 'supplierprofile'),
						'title'		 => array('managesupplier', 'listpotentialsupplier', 'supplierprofile'),
						'permission'	=> array('canAccessSystem', 'canAccessSystem', 'canAccesSystem')
						);
?>