<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: manageaffdesc.php
 * Created:        @rasha.aboushakra    Jul 22, 2015 | 8:51:43 AM
 * Last Update:    @rasha.aboushakra    Jul 22, 2015 | 8:51:43 AM
 */

if($core->usergroup['canUseCms'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $affid = intval($core->input['id']);
    if(empty($affid)) {
        redirect("index.php?module=cms/affdesclist");
    }
    $affiliate = Affiliates::get_affiliates(array('affid' => $affid), array('simple' => false));
    eval("\$manageaffdescription =\"".$template->get('cms_manageaffdescription')."\";");
    output_page($manageaffdescription);
}
elseif($core->input['action'] == 'do_perform_manageaffdesc') {
    $affiliate = new Affiliates($core->input['affid']);
    $affiliate_data = $affiliate->get();
    if(!empty($core->input['description'])) {
        $affiliate_data['description'] = $core->input['description'];
    }
    $query = $db->update_query('affiliates', $affiliate_data, 'affid = '.$affiliate->affid);
    if($query) {
        output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
    }
    else {
        output_xml('<status>false</status><message> Error while saving</message>');
    }
}