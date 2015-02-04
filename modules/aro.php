<?php
$module['name'] = 'aro';
$module['title'] = $lang->aro;
$module['homepage'] = 'listwarehouses';
$module['globalpermission'] = 'canUseAro';
$module['menu'] = array('file' => array('managewarehousepolicies', 'warehousespolicieslist'),
        'title' => array('managewarehousepolicies', 'warehousespolicieslist'),
        'permission' => array('aro_canManageWarehousePolicies', 'aro_canManageWarehousePolicies')
);
?>