<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: entities.php
 * Created:        @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 * Last Update:    @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 */
$module['name'] = 'regions';
$module['title'] = $lang->regions;
$module['homepage'] = 'affiliatemanagement';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' => array('affiliatemanagement', 'affiliates', 'countries'),
        'title' => array('affiliatemanagement', 'affiliates', 'countries'),
        'permission' => array('canAdminCP', 'canManageAffiliates', 'canManageCountries')
);
?>