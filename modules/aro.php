<?php
$module['name'] = 'aro';
$module['title'] = $lang->aro;
$module['homepage'] = 'listwarehouses';
$module['globalpermission'] = 'canUseAro';
$module['menu'] = array('file' => array('managewarehousepolicies', 'warehousespolicieslist', 'manageapprovalchainspolicies'),
        'title' => array('managewarehousepolicies', 'warehousespolicieslist', 'manageapprovalchainspolicies'),
        'permission' => array('aro_canManageWarehousePolicies', 'aro_canManageWarehousePolicies', 'aro_canManageApprovalPolicies')
);
?>