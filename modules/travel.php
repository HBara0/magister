<?php
$module['name'] = 'profiles';
$module['title'] = 'Profiles';
$module['homepage'] = 'affiliateslist';
$module['globalpermission'] = 'canAccessSystem';
$module['menu'] = array('file' => array('affiliateslist', 'supplierslist', 'customerslist', 'segmentslist', 'brandslist', 'representativeslist'),
        'title' => array('affiliateslist', 'supplierslist', 'customerslist', 'segmentslist', 'brandslist', 'representativeslist'),
        'permission' => array('canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem')
);
?>