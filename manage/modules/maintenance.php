<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: entities.php
 * Created:        @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 * Last Update:    @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 */
$module['name'] = 'maintenance';
$module['title'] = $lang->maintenance;
$module['homepage'] = 'backupdb';
$module['globalpermission'] = 'canPerformMaintenance';
$module['menu'] = array('file' => array('backupdb', 'logs', 'optimizedb', 'overview', 'phpinfo'),
        'title' => array('backupdb', 'logs', 'optimizedb', 'overview', 'phpinfo'),
        'permission' => array('canPerformMaintenance', 'canReadLogs', 'canPerformMaintenance', 'canPerformMaintenance', 'canPerformMaintenance')
);
