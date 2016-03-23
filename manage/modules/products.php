<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: entities.php
 * Created:        @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 * Last Update:    @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 */
$module['name'] = 'products';
$module['title'] = $lang->products;
$module['homepage'] = 'add';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' => array('add', 'applications', 'basicingredients', 'characteristicslist', 'chemicals', 'functions', 'generics', 'segmentcategory', 'segments', 'types', 'view'),
        'title' => array('add', 'applications', 'basicingredients', 'characteristicslist', 'chemicals', 'functions', 'generics', 'segmentcategory', 'segments', 'types', 'view'),
        'permission' => array('canAddProducts', 'canManageSegments', 'canManageapllicationsProducts', 'canManageapllicationsProducts', 'canAddProducts', 'canAddProducts', 'canManageGenericProducts', 'canManageSegments', 'canManageSegments', 'canAddProducts', 'canManageProducts')
);
?>