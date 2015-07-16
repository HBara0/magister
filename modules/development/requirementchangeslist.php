<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: requirementchangeslist.php
 * Created:        @zaher.reda    May 17, 2015 | 4:53:39 PM
 * Last Update:    @zaher.reda    May 17, 2015 | 4:53:39 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {

    $requirements = RequirementsChanges::get_data(null, array('simple' => false, 'order' => array('by' => array('isCompleted', 'drid', 'refKey'), 'sort' => array('ASC'))));
    if(!is_array($requirements)) {
        error($lang->nomatchfound);
    }

    foreach($requirements as $requirement) {
        $rowclass = '';
        $parent = $requirement->get_requirement();
        if($requirement->isCompleted == 1) {
            $requirement->isCompleted_output = '<img src="images/valid.gif" border="0" alt="'.$lang->yes.'">';
            $rowclass = 'altrow2';
        }
        else {
            $requirement->isCompleted_output = '<a  href="index.php?module=development/requirementchangeslist&action=markchangecompleted&id='.$requirement->drcid.'">'.$lang->markascompleted.'</a>';
        }

        $parent->refKey = $parent->parse_fullreferencekey();
        $requirement->refKey = $parent->refWord.' '.$parent->refKey.' - C'.$requirement->refKey;
        eval("\$requirements_rows .= \"".$template->get('development_reqslist_trows')."\";");
    }

    eval("\$requirements_list = \"".$template->get('development_reqslist_table')."\";");


    eval("\$list = \"".$template->get('development_requirementslist')."\";");
    output_page($list);
}
elseif($core->input['action'] == 'markchangecompleted') {
    if($core->usergroup['development_canCreateReq'] == 0) {
        output_xml('<status>false</status><message>'.$lang->sectionnopermission.'</message>');
        exit;
    }
    $requirementchange = RequirementsChanges::get_data(array('drcid' => $core->input['id']), array('simple' => false));
    $requirementchange->isCompleted = 1;
    $requirementchange->save();
    redirect("index.php?module=development/requirementchangeslist");
}
?>