<?php
$module['name'] = 'facilitymgmt';
$module['title'] = $lang->facilitymgmt;
$module['homepage'] = 'createfacility';
$module['globalpermission'] = 'canUseFacM';
$module['menu'] = array('file' => array('managefacility', 'list'),
        'title' => array('managefacility', 'list'),
        'permission' => array('facilitymgmt_canManageFacilities', 'facilitymgmt_canManageFacilities')
);
?>