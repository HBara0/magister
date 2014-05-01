<?php
$module['name'] = 'profiles';
$module['title'] = 'Profiles';
$module['homepage'] = 'affiliateslist';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' => array('affiliateslist', 'supplierslist', 'customerslist', 'segmentprofile'),
        'title' => array('affiliateslist', 'supplierslist', 'customerslist', 'segmentprofile'),
        'permission' => array('canAccessSystem', 'canAccessSystem', 'canAccessSystem', 'canAccessSystem')
);
?>