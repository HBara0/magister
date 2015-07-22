<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managehomepagedesc.php
 * Created:        @rasha.aboushakra    Jul 21, 2015 | 3:19:51 PM
 * Last Update:    @rasha.aboushakra    Jul 21, 2015 | 3:19:51 PM
 */

if($core->usergroup['canUseCms'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $affiliates = Affiliates::get_affiliates(array('isActive' => 1, 'publishOnWebsite' => 1), array('returnarray' => true, 'simple' => false));
    if(is_array($affiliates)) {
        foreach($affiliates as $affiliate) {
            $aff_data['name'] = '<a href="index.php?module=cms/manageaffdesc&id='.$affiliate->affid.'">'.$affiliate->get_displayname().'</a>';
            $aff_data['description'] = $affiliate->description;
            $cms_affdesc_rows .='<tr><td>'.$aff_data[name].'</td><td>'.$aff_data[description].'</td></tr>';
        }
    }
    eval("\$affdescriptionslist =\"".$template->get('cms_affdescription_list')."\";");
    output_page($affdescriptionslist);
}
?>