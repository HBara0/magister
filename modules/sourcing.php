<?php
$module['name'] = 'sourcing';
$module['title'] = 'Sourcing';
$module['homepage'] = 'managesupplier';
$module['globalpermission'] = 'canUseSourcing';
$module['menu'] = array('file' 		  => array('managesupplier','listpotentialsupplier','supplierprofile'),
						'title'		 => array('managesupplier','listpotentialsupplier','supplierprofile'),
						'permission'	=> array('canAccessSystem','canAccessSystem','canAccesSystem')
						);
?>