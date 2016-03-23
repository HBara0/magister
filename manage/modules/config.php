<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: config.php
 * Created:        @rasha.aboushakra    Mar 18, 2016 | 12:46:56 AM
 * Last Update:    @rasha.aboushakra    Mar 18, 2016 | 12:46:56 AM
 */

$module['name'] = 'config';
$module['title'] = $lang->config;
$module['homepage'] = 'settings';
$module['globalpermission'] = 'canChangeSettings';
$module['menu'] = array('file' => array('settings'),
        'title' => array('settings'),
        'permission' => array('canChangeSettings')
);
?>