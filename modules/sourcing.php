<?php
$module['name'] = 'sourcing';
$module['title'] = $lang->sourcing;
$module['homepage'] = 'listpotentialsupplier';
$module['globalpermission'] = 'canUseSourcing';
$module['menu'] = array('file' => array('workspace', 'managesupplier', 'listpotentialsupplier', 'listchemcialsrequests'),
        'title' => array('workspace', 'managesuppliers', 'listpotentialsupplier', 'listchemcialsrequests'),
        'permission' => array('sourcing_canViewKPIs', 'sourcing_canManageEntries', 'sourcing_canListSuppliers', 'sourcing_canListSuppliers')
);
?>