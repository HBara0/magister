<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2011 Orkila International Offshore, All Rights Reserved
 *
 * Add reputation
 * $module: reputation
 * $id: addreputation.php
 * Created:	    @najwa.kassem 	September 09, 2011 | 12:22 PM
 * Last Update: @najwa.kassem 	September 09, 2011 | 12:22 PM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['reputation_canAddLink'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $title = $lang->addreputation;
    $actiontype = 'addreputation';
    $action = 'add';
    eval("\$addpage = \"".$template->get('reputation_addreputation')."\";");
    output_page($addpage);
}
else {
    if($core->input['action'] == 'add') {
        if(is_empty($core->input['title'], $core->input['description'], $core->input['url'])) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }
        $core->input['url'] = $core->sanitize_URL($core->input['url']);
        if(!$core->validtate_URL($core->input['url'])) {
            output_xml("<status>false</status><message>{$lang->invalidurl}</message>");
            exit;
        }

        unset($core->input['action'], $core->input['module']);

        $core->input['addedBy'] = $core->user['uid'];
        $core->input['timeLine'] = TIME_NOW;
        $query = $db->insert_query('reputation', $core->input);
        if($query) {
            $log->record($db->last_id());
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
        }
    }
}
?>