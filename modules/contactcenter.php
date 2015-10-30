<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: contactcenter.php
 * Created:        @hussein.barakat    Aug 5, 2015 | 2:53:34 PM
 * Last Update:    @hussein.barakat    Aug 5, 2015 | 2:53:34 PM
 */

$module['name'] = 'contactcenter';
$module['title'] = $lang->contactcenter;
$module['homepage'] = 'generatelist';
$module['globalpermission'] = 'canUseCc';
$module['menu'] = array(
        'file' => array(
                'generatelist',
        ),
        'title' => array('generate'),
        'permission' => array('canUseCc')
);
?>