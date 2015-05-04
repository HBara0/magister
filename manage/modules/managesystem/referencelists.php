<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: referencelists.php
 * Created:        @hussein.barakat    Apr 29, 2015 | 5:43:15 PM
 * Last Update:    @hussein.barakat    Apr 29, 2015 | 5:43:15 PM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}
if($core->usergroup['admin_canManageSystemDef'] == 0) {
    error($lang->sectionnopermission);
    exit;
}
$lang = new Language('english', 'admin');
$lang->load('managesystem');
$lang->load('global');

$reflist_objs = SystemReferenceLists::get_data('', array('returnarray' => true));
if(is_array($reflist_objs)) {
    foreach($reflist_objs as $reflist_obj) {
        $reflist = $reflist_obj->get();
        $rlid = $reflist['srlid'];
        eval("\$ref_list_row .= \"".$template->get('admin_referencelist_list_rows')."\";");
        unset($rlid);
    }
}
eval("\$ref_list = \"".$template->get('admin_referencelist_list')."\";");
output_page($ref_list);
