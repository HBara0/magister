<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * A page to view bug details
 * $id: viewbug.php
 * Created:        @zaher.reda    Jun 25, 2014 | 4:43:03 PM
 * Last Update:    @zaher.reda    Jun 25, 2014 | 4:43:03 PM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $bug = new DevelopmentBugs($core->input['id'], false);

    if(is_object($bug)) {
        $bug->stackTrace_output = nl2br(var_export(unserialize($bug->stackTrace), true));

        $bug->description_output = @unserialize($bug->description);
        if($bug->description_output == false) {
            $bug->description_output = $bug->description;
        }
        else {
            $bug->description_output = nl2br(var_export($bug->description_output, true));
        }

        $bug->reportedOn_output = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $bug->reportedOn);

        $bug->isFixed_output = $lang->no;
        if($bug->isFixed == 1) {
            $bug->isFixed_output = $lang->yes;

            eval("\$resolutionrow = \"".$template->get('development_bug_resolutionrow')."\";");
        }
        else {
            eval("\$resolutionform = \"".$template->get('development_bug_resolutionform')."\";");
        }


        if(intval($bug->relatedRequirement) > 0) {
            $bug->relatedRequirement_output = $bug->get_relatedreq()->parse_link();
        }

        if(intval($bug->reportedBy) > 0) {
            $bug->reportBy_output = $bug->get_user()->parse_link();
        }

        eval("\$bug_page = \"".$template->get('development_bug')."\";");
        output_page($bug_page);
    }
    else {
        redirect($_SERVER['HTTP_REFERER'], 2, $lang->nomatchfound);
    }
}
else {
    if($core->input['action'] == 'do_resolve') {
        $bug = new DevelopmentBugs($core->input['dbid']);
        unset($core->input['action'], $core->input['module']);
        $bug->save($core->input);
        $message = $errorhandler->parse_errorcode($bug->get_errorcode());
        if($message == true) {
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
        }
        else {
            output_xml('<status>false</status><message>'.$message.'</message>');
        }
    }
}
?>