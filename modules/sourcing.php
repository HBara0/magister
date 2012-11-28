<?php
$module['name'] = 'sourcing';
$module['title'] = $lang->sourcing;
$module['homepage'] = 'managesupplier';
$module['globalpermission'] = 'canUseSourcing';
$module['menu'] = array('file' 		  => array('managesupplier', 'listpotentialsupplier', 'listchemcialsrequests'),
						'title'		 => array('managesupplier', 'listpotentialsupplier', 'listchemcialsrequests'),
						'permission'	=> array('sourcing_canManageEntries', 'sourcing_canListSuppliers', 'sourcing_canListSuppliers')
						);
?>