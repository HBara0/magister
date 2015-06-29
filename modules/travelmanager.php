<?php
$module['name'] = 'travelmanager';
$module['title'] = $lang->travelmanager;
$module['homepage'] = 'listplans';
$module['globalpermission'] = 'canUseTravelManager';
$module['menu'] = array('file' => array('plantrip', 'listplans', 'hotelslist'),
        'title' => array('plantrip', 'listplans', 'hotelslist'),
        'permission' => array('canUseTravelManager', 'canUseTravelManager', 'canUseTravelManager')
);
?>