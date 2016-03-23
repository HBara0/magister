<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: entities.php
 * Created:        @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 * Last Update:    @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 */
$module['name'] = 'integration';
$module['title'] = $lang->integration;
$module['homepage'] = 'matchdata';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' => array('importcustomers', 'matchdata'),
        'title' => array('importcustomers', 'matchdata'),
        'permission' => array('canAdminCP', 'canAdminCP')
);
?>