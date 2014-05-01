<?php
$module['name'] = 'travelmanager';
$module['title'] = $lang->travelmanager;
$module['homepage'] = 'lookupflights';
$module['globalpermission'] = 'canUseTravelManager';
$module['menu'] = array('file' => array('lookupflights'),
        'title' => array('lookupflights'),
        'permission' => array('canUseTravelManager')
);
?>