<?php
$module['name'] = 'contents';
$module['title'] = $lang->contents;
$module['homepage'] = 'addentities&amp;type=customer';
$module['globalpermission'] = 'canUseContents';
$module['menu'] = array('file' => array('addproducts', "addentities&amp;type=supplier", 'addentities&amp;type=customer', 'createlocations', 'createwarehouses', 'listwarehouses'),
        'title' => array('addproducts', 'addsuppliers', 'addcustomers', 'createlocations', 'createwarehouses', 'warehouseslist'),
        'permission' => array('canAddProducts', 'canAddSuppliers', 'canAddCustomers', 'contents_canManageLocations', 'contents_canManageWarehouses', 'contents_canManageWarehouses')
);
?>