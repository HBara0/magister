<?php
$module['name'] = 'travelmanager';
$module['title'] = $lang->travelmanager;
$module['homepage'] = 'lookupflights';
$module['globalpermission'] = 'canUseTravelManager';
$module['menu'] = array('file' => array('plantrip', 'listplans'),
        'title' => array('plantrip', 'listplans'),
        'permission' => array('canUseTravelManager', 'canUseTravelManager')
);
?>