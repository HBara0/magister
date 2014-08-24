<?php
$module['name'] = 'travelmanager';
$module['title'] = $lang->travelmanager;
$module['homepage'] = 'lookupflights';
$module['globalpermission'] = 'canUseTravelManager';
$module['menu'] = array('file' => array('lookupflights', 'viewplan', 'listplan'),
        'title' => array('lookupflights', 'viewplan', 'listplan'),
        'permission' => array('canUseTravelManager', 'canUseTravelManager', 'canUseTravelManager')
);
?>