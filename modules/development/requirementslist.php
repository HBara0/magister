<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Requirements List
 * $module: development
 * $id: requirementslist.php
 * Created By: 		@zaher.reda			May 21, 2012 | 09:38 PM
 * Last Update: 	@zaher.reda			May 21, 2012 | 09:38 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    if($core->input['view'] == 'tabular') {
        $requirements = Requirements::get_data(null, array('order' => array('by' => array('isCompleted', 'refWord', 'parent'), 'sort' => array('ASC'))));
        if(!is_array($requirements)) {
            error($lang->nomatchfound);
        }

        foreach($requirements as $requirement) {
            $parent = $requirement->get_parent();
            if(!is_object($parent)) {
                $parent = new Requirements();
            }


            $rowclass = '';
            if($requirement->isCompleted == 1) {
                $requirement->isCompleted_output = $lang->yes;
                $rowclass = 'altrow2';
            }
            else {
                $requirement->isCompleted_output = '<a target="_blank" href="index.php?module=development/viewrequirement&action=markcompleted&id='.$requirement->get_id().'">Mark as Completed</a>';
            }

            $requirement->refKey = $requirement->parse_fullreferencekey();
            $parent->refKey = $parent->parse_fullreferencekey();
            eval("\$requirements_rows .= \"".$template->get('development_reqslist_trows')."\";");
        }

        eval("\$requirements_list = \"".$template->get('development_reqslist_table')."\";");
    }
    else {
        $requirements = new Requirements();
        $requirements_list = $requirements->read_user_requirements();
        if(is_array($requirements_list)) {
            $requirements_list = $requirements->parse_requirements_list($requirements_list);
        }
    }

    eval("\$list = \"".$template->get('development_requirementslist')."\";");
    output_page($list);
}
?>