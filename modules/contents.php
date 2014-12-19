<?php
$module['name'] = 'contents';
$module['title'] = $lang->contents;
$module['homepage'] = 'addentities&amp;type=customer';
$module['globalpermission'] = 'canUseContents';
$module['menu'] = array('file' => array('addproducts', "addentities&amp;type=supplier", 'addentities&amp;type=customer', 'createlocations'),
        'title' => array('addproducts', 'addsuppliers', 'addcustomers', 'createentityloc'),
        'permission' => array('canAddProducts', 'canAddSuppliers', 'canAddCustomers', 'canAddSuppliers')
);
?>