<?php
$module['name'] = 'aro';
$module['title'] = $lang->aro;
$module['homepage'] = 'aroorderrequestlist';
$module['globalpermission'] = 'canUseAro';
$module['menu'] = array('file' => array('managewarehousepolicies', 'warehousespolicieslist', 'arodocumentsequeneconf', 'documentssequeneconflist', 'managepolicies', 'listpolicies', 'manageapprovalchainspolicies', 'approvalchainspolicieslist', 'managearodouments', 'aroorderrequestlist'),
        'title' => array('managewarehousepolicies', 'warehousespolicieslist', 'managedoumentsequence', 'doumentsequenceconflist', 'managepolicies', 'aropolicieslist', 'manageapprovalchainspolicies', 'approvalchainspolicieslist', 'managearodouments', 'aroorderrequestlist'),
        'permission' => array('aro_canManageWarehousePolicies', 'aro_canManageWarehousePolicies', 'aro_canManagePolicies', 'aro_canManagePolicies', 'aro_canManagePolicies', 'aro_canManagePolicies', 'aro_canManageApprovalPolicies', 'aro_canManageApprovalPolicies', 'aro_canFillAro', 'aro_canFillAro')
);
?>