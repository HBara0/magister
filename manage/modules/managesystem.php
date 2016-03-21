<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: entities.php
 * Created:        @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 * Last Update:    @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 */
$module['name'] = 'managesystem';
$module['title'] = $lang->managesystem;
$module['homepage'] = 'managereferencelist';
$module['globalpermission'] = 'admin_canManageSystemDef';
$module['menu'] = array('file' => array('managereferencelist', 'managetables', 'managewindows', 'referencelists', 'tableslist', 'windowslist'),
        'title' => array('managereferencelist', 'managetables', 'managewindows', 'referencelists', 'tableslist', 'windowslist'),
        'permission' => array('admin_canManageSystemDef', 'admin_canManageSystemDef', 'admin_canManageSystemDef', 'admin_canManageSystemDef', 'admin_canManageSystemDef', 'admin_canManageSystemDef')
        )
?>