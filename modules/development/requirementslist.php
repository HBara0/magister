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
    elseif($core->input['view'] == 'csv') {
        $requirements = Requirements::get_data(array('module' => '"Website"'), array('operators' => array('module' => 'NOT IN'), 'simple' => false, 'order' => array('by' => array('refWord', 'parent', 'refKey'), 'sort' => array('ASC'))));

        $cols = array('refWord', 'module', 'categoryname', 'link', 'title', 'reqtype', 'description', 'security', 'userInterface', 'performance', 'dateCreated_output', 'modifiedOn_output', 'isCompleted_output', 'requestedBy_output', 'dateRequested_output');
        $comma = '';
        foreach($cols as $col) {
            $line .= $comma.$col;
            $comma = ';';
        }
        $line .="\n";
        foreach($requirements as $requirement) {
            $parent = $requirement->get_parent();
            $parent_id = $parent->get_id();
            if(empty($parent_id)) {
                $children = $requirement->hasChildren();
                if($children == true) {
                    $comma = '';
                    $requirement->categoryname = $requirement->title;
                    $requirement->reqtype = 'Epic';
                    foreach($cols as $col) {
                        if(in_array($col, array('performance', 'security', 'userInterface'))) {
                            $line .= $comma.'""';
                        }
                        else {
                            $line .= $comma.'"'.$db->escape_string($requirement->{$col}).'"';
                        }
                        $comma = ';';
                    }
                    $line .="\n";
                }
            }
            else {
                $parent = $requirement->get_topoftree();
                $requirement->categoryname = $parent->title;
            }

            $requirement->reqtype = 'Story';
            $requirement->link = $requirement->categoryname;
            $requirement->categoryname = '';

            if($requirement->isCompleted == 1) {
                $requirement->isCompleted_output = 'Done';
            }
            else {
                $requirement->isCompleted_output = 'To Do';
            }

            $requirement->dateCreated_output = date('Y-m-d', $requirement->dateCreated);
            if(!empty($requirement->modifiedOn)) {
                $requirement->modifiedOn_output = date('Y-m-d', $requirement->modifiedOn);
            }
            $requirement->refKey = $requirement->parse_fullreferencekey();
            $requirement->refWord = $requirement->refWord.' '.$requirement->refKey;
            $comma = '';
            foreach($cols as $col) {
                $line .= $comma.'"'.$db->escape_string($requirement->{$col}).'"';
                $comma = ';';
            }
            $line .="\n";

            $reqkeys[] = $requirement->get_id();
        }

        $changes = RequirementsChanges::get_data(array('drid' => $reqkeys), array('operators' => array('drid' => 'IN'), 'simple' => false, 'order' => array('by' => array('drid', 'refKey'), 'sort' => array('ASC'))));
        foreach($changes as $requirement) {
            $requirement->dateCreated_output = date('Y-m-d', $requirement->dateCreated);
            if(!empty($requirement->modifiedOn)) {
                $requirement->modifiedOn_output = date('Y-m-d', $requirement->modifiedOn);
            }
            $requirement->dateRequested_output = date('Y-m-d', $requirement->dateRequested);
            if($requirement->isCompleted == 1) {
                $requirement->isCompleted_output = 'Done';
            }
            else {
                $requirement->isCompleted_output = 'To Do';
            }

            $req = $requirement->get_requirement();
            $parent = $req->get_topoftree();

            $requirement->reqtype = 'Story';
            $requirement->link = $parent->title;

            $requirement->module = $req->module.' > '.$req->title;

            $req->refKey = $req->parse_fullreferencekey();
            $requirement->refWord = $req->refWord.' '.$req->refKey.' - C'.$requirement->refKey;


            $requirement->requestedBy_output = $requirement->get_requester()->displayName;
            $comma = '';
            foreach($cols as $col) {
                $line .= $comma.'"'.$db->escape_string($requirement->{$col}).'"';
                $comma = ';';
            }
            $line .="\n";
        }
        echo $line;
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