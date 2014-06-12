<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Add products
 * $module: contents
 * $id: addproducts.php
 * Last Update: @zaher.reda 	Mar 21, 2009 | 11:03 AM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canAddProducts'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('contents_addproducts');
if(!$core->input['action']) {
    /* Quick workaround to be replaced by request product form */
    $requiredinfo = array('Full Name (Trade)', 'Chemcial Name', 'Supplier', 'Producer', 'Segment', 'Application', 'Chemical function');
    $emailink = 'mailto:'.$core->settings['adminemail'].'&subject=[OCOS] New Product Addition Request&body='.implode(":%0D%0A", $requiredinfo);

    eval("\$addproductspage = \"".$template->get('contents_products_add')."\";");
    output_page($addproductspage);
}
?>