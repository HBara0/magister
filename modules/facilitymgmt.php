<?php
$module['name'] = 'facilitymgmt';
$module['title'] = $lang->facilitymgmt;
$module['homepage'] = 'list';
$module['globalpermission'] = 'canUseFacM';
$module['menu'] = array('file' => array('managefacility', 'list', 'typeslist', 'managefacilitytype'),
        'title' => array('managefacility', 'list', 'facilitytypelist', 'facilitytypesmgmt'),
        'permission' => array('facilitymgmt_canManageFacilities', 'facilitymgmt_canManageFacilities', 'facilitymgmt_canManageFacilities', 'facilitymgmt_canManageFacilities')
);
?>