<?php
$module['name'] = 'sourcing';
$module['title'] = 'Sourcing';
$module['homepage'] = 'managesupplier';
$module['globalpermission'] = 'canUseSourcing';
$module['menu'] = array('file' 		  => array('managesupplier','listpotentialsupplier'),
						'title'		 => array('managesupplier','listpotentialsupplier'),
						'permission'	=> array('canAccessSystem','canAccessSystem')
						);
?>