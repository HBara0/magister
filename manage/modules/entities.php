<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: entities.php
 * Created:        @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 * Last Update:    @rasha.aboushakra    Mar 7, 2016 | 1:52:06 PM
 */
$module['name'] = 'entities';
$module['title'] = $lang->entities;
$module['homepage'] = 'add';
$module['globalpermission'] = 'canAdminCP';
$module['menu'] = array('file' => array('add', 'addmissingrates', 'edit', 'managebrandendproducts', 'managebrands', 'manageentitiesfiles', 'paymenttermslist', 'viewcustomers', 'viewsuppliers'),
        'title' => array('add', 'addmissingrates', 'edit', 'managebrandendproducts', 'managebrands', 'manageentitiesfiles', 'paymenttermslist', 'viewcustomers', 'viewsuppliers'),
        'permission' => array('canAdminCP', 'canManageSuppliers', 'canUseAttendance', 'canAdminCP', 'canManageapllicationsProducts', 'canAdminCP', 'canAdminCP', 'canManageSuppliers', 'canManageSuppliers')
);
?>