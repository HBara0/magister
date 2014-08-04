<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * View Requirement
 * $module: development
 * $id: viewrequirement.php
 * Created By: 		@zaher.reda			May 21, 2012 | 03:31 PM
 * Last Update: 	@zaher.reda			May 21, 2012 | 03:31 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    if(!empty($core->input['id'])) {
        $requirementobj = new Requirements($core->input['id']);
        $requirement = $requirementobj->get();
        $parent = $requirementobj->get_parent()->get();

        if(!empty($parent['refKey'])) {
            $reference_sep = '.';
        }

        if(is_array($requirement['children'])) {
            $children_list = '<div class="subtitle">'.$lang->children.'</div>';
            $children_list .= '<ul>';
            foreach($requirement['children'] as $id => $child) {
                $children_list .= '<li><a href="index.php?module=development/viewrequirement&amp;id='.$child['drid'].'">'.$child['title'].'</a></li>';
            }
            $children_list .= '</ul>';
        }

        $changes = $requirementobj->get_changes();
        if(is_array($changes)) {
            $changes_section = '<div class="subtitle">'.$lang->requirementchange.'</div>';
            foreach($changes as $id => $change) {
                $rowclass = alt_row($rowclass);
                fix_newline($change['description']);

                if(!empty($change['outcomeReq'])) {
                    $change['description'] .= '<br />Check: <a href="index.php?module=development/viewrequirement&amp;id='.$change['outcomeReq'].'">'.$change['drRefWord'].' '.$change['drRefKey'].' '.$change['outcomeReqTitle'].'</a>';
                }

                $change['identifier'] = $requirement['refWord'].' '.$parent['refKey'].$reference_sep.$requirement['refKey'].' - C'.$change['refKey'];
                $changes_section .= '<div class="'.$rowclass.'" style="margin-bottom: 10px;"><span style="font-weight:bold;">'.$change['identifier'].' '.$change['title'].'</span><br />'.$change['description'].'. <span class="smalltext">Added '.$change['dateCreated_output'].' by '.$change['createdByName'].'</span></div>';
            }
        }

        if($core->usergroup['development_canCreateReq'] == 1) {
            eval("\$changes_section .= \"".$template->get('development_requirementdetails_addchange')."\";");
        }

        $complete_button = '<img src="./images/valid.gif" /> <strong>Completed</strong>';
        if(empty($requirement['isCompleted'])) {
            $complete_button = '<a href="index.php?module=development/viewrequirement&action=markcompleted&id='.$requirement['drid'].'">Mark as Completed</a>';
        }

        eval("\$requirementdetails = \"".$template->get('development_requirementdetails')."\";");
        output_page($requirementdetails);
    }
}
else {
    if($core->input['action'] == 'markcompleted') {
        if($core->usergroup['development_canCreateReq'] == 0) {
            output_xml('<status>false</status><message>'.$lang->sectionnopermission.'</message>');
            exit;
        }
        $requirement = new Requirements($core->input['id']);
        $requirement->save(array('isCompleted' => 1));

        switch($requirement->get_errorcode()) {
            case 0:
                redirect('index.php?module=development/viewrequirement&id='.$core->input['id'], 1, $lang->successfullysaved);
                break;
            default:
                error($errorhandler->parse_errorcode($requirement->get_errorcode()));
                break;
        }
    }
    elseif($core->input['action'] == 'createreqchange') {
        if($core->usergroup['development_canCreateReq'] == 0) {
            output_xml('<status>false</status><message>'.$lang->sectionnopermission.'</message>');
            exit;
        }

        $reqchange_obj = new RequirementsChanges();
        $reqchange_obj->create($core->input);

        switch($reqchange_obj->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;
            case 602:
                output_xml('<status>false</status><message>'.$lang->entryexists.'</message>');
                break;
            case 602:
                output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
        }
    }
}
?>