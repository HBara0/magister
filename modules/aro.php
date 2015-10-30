<?php
$module['name'] = 'aro';
$module['title'] = $lang->aro;
$module['homepage'] = 'managepolicies';
$module['globalpermission'] = 'canUseAro';
$module['menu'] = array('file' => array('managewarehousepolicies', 'warehousespolicieslist', 'arodocumentsequeneconf', 'documentssequeneconflist', 'managepolicies', 'listpolicies', 'manageapprovalchainspolicies', 'managearodouments', 'approvalchainspolicieslist'),
        'title' => array('managewarehousepolicies', 'warehousespolicieslist', 'managedoumentsequence', 'doumentsequenceconflist', 'managepolicies', 'aropolicieslist', 'manageapprovalchainspolicies', 'managearodouments', 'approvalchainspolicieslist'),
        'permission' => array('aro_canManageWarehousePolicies', 'aro_canManageWarehousePolicies', 'aro_canManagePolicies', 'aro_canManagePolicies', 'aro_canManagePolicies', 'aro_canManagePolicies', 'aro_canFillAro', 'aro_canManageApprovalPolicies')
);
?>