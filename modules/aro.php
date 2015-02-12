<?php
$module['name'] = 'aro';
$module['title'] = $lang->aro;
$module['homepage'] = 'listwarehouses';
$module['globalpermission'] = 'canUseAro';
$module['menu'] = array('file' => array('managewarehousepolicies', 'warehousespolicieslist', 'managepolicies', 'listpolicies', 'manageapprovalchainspolicies', 'managearodouments'),
        'title' => array('managewarehousepolicies', 'warehousespolicieslist', 'managepolicies', 'aropolicieslist', 'manageapprovalchainspolicies', 'managearodouments'),
        'permission' => array('aro_canManageWarehousePolicies', 'aro_canManageWarehousePolicies', 'aro_canManagePolicies', 'aro_canManagePolicies', 'aro_canManageApprovalPolicies', 'aro_canFillAro')
);
?>