<?php
$module['name'] = 'contents';
$module['title'] = $lang->contents;
$module['homepage'] = 'addproducts';
$module['globalpermission'] = 'canUseContents';
$module['menu'] = array('file' => array('addproducts', "addentities&amp;type=supplier", 'addentities&amp;type=customer'),
        'title' => array('addproducts', 'addsuppliers', 'addcustomers'),
        'permission' => array('canAddProducts', 'canAddSuppliers', 'canAddCustomers')
);
?>