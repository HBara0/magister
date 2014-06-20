<?php
$module['name'] = 'profiles';
$module['title'] = 'Profiles';
$module['homepage'] = 'affiliateslist';
$module['globalpermission'] = 'canAccessSystem';
$module['menu'] = array('file' => array('affiliateslist', 'supplierslist', 'customerslist', 'segmentslist'),
        'title' => array('affiliateslist', 'supplierslist', 'customerslist', 'segmentslist'),
        'permission' => array('canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem')
);
?>