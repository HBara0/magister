<?php
$module['name'] = 'sourcing';
$module['title'] = 'SOURCING';
$module['homepage'] = 'managesupplier';
$module['globalpermission'] = 'canUseSourcing';
$module['menu'] = array('file' 		  => array('managesupplier','listpotentialsupplier','listchemcialsrequests'),
						'title'		 => array('managesupplier','listpotentialsupplier','listchemcialsrequests'),
						'permission'	=> array('canAccessSystem','canAccessSystem','canAccessSystem')
						);
?>