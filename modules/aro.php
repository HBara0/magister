<?php
$module['name'] = 'aro';
$module['title'] = $lang->aro;
$module['homepage'] = 'aroorderrequestlist';
$module['globalpermission'] = 'canUseAro';
$module['menu'] = array('file' => array('manageconfigurations' => array('managewarehousepolicies', 'arodocumentsequeneconf', 'managepolicies', 'manageapprovalchainspolicies'), 'configurationslists' => array('warehousespolicieslist', 'documentssequeneconflist', 'listpolicies', 'approvalchainspolicieslist'), 'arodocuments' => array('managearodouments', 'aroorderrequestlist')),
        'title' => array('manageconfigurations' => array('managewarehousepolicies', 'managedoumentsequence', 'managepolicies', 'manageapprovalchainspolicies'), 'configurationslists' => array('warehousespolicieslist', 'doumentsequenceconflist', 'aropolicieslist', 'approvalchainspolicieslist'), 'arodocuments' => array('managearodouments', 'aroorderrequestlist')),
        'permission' => array(array('canUseAro', 'aro_canManageWarehousePolicies', 'aro_canManagePolicies', 'aro_canManagePolicies', 'aro_canManageApprovalPolicies'), array('canUseAro', 'aro_canManageWarehousePolicies', 'aro_canManagePolicies', 'aro_canManagePolicies', 'aro_canManageApprovalPolicies'), array('canUseAro', 'aro_canFillAro', 'canUseAro'))
);
?>