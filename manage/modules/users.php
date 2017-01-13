<?php

/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: users.php
 * Created:        @rasha.aboushakra    Mar 18, 2016 | 12:44:50 AM
 * Last Update:    @rasha.aboushakra    Mar 18, 2016 | 12:44:50 AM
 */

$module['name'] = 'users';
$module['title'] = $lang->users;
$module['homepage'] = 'add';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' => array('add', 'edit', 'view'),
    'title' => array('add', 'edit', 'view'),
    'permission' => array('canAdminCP', 'canAdminCP', 'canAdminCP')
);
?>