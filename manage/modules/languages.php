<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: entities.php
 * Created:        @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 * Last Update:    @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 */
$module['name'] = 'languages';
$module['title'] = $lang->languages;
$module['homepage'] = 'list';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' => array('list', 'import', 'manage'),
        'title' => array('list', 'import', 'manage'),
        'permission' => array('canAdminCP', 'admin_canModifyLangFiles', 'admin_canModifyLangFiles')
);
