<?php
$module['name'] = 'sourcing';
$module['title'] = $lang->sourcing;
$module['homepage'] = 'listpotentialsupplier';
$module['globalpermission'] = 'canUseSourcing';
$module['menu'] = array('file' 		  => array('managesupplier', 'listpotentialsupplier', 'listchemcialsrequests'),
						'title'		 => array('managesuppliers', 'listpotentialsupplier', 'listchemcialsrequests'),
						'permission'	=> array('sourcing_canManageEntries', 'sourcing_canListSuppliers', 'sourcing_canListSuppliers')
						);
?>