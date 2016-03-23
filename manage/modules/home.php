<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: home.php
 * Created:        @rasha.aboushakra    Mar 18, 2016 | 12:47:59 AM
 * Last Update:    @rasha.aboushakra    Mar 18, 2016 | 12:47:59 AM
 */

$module['name'] = 'home';
$module['title'] = $lang->home;
$module['homepage'] = 'stats';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' => array('stats'),
        'title' => array('home'),
        'permission' => array('canAdminCP')
);
?>