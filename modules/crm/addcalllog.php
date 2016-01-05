<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * List CRM visit reports
 * $module: CRM
 * $id: listvisitreports.php
 * Created: 	@zaher.reda 	July 27, 2009 | 10:20 AM
 * Last Update: @zaher.reda 	July 06, 2012 | 04:29 PM
 */

if(!defined("DIRECT_ACCESS")) {
    die('Direct initialization of this file is not allowed.');
}

//if($core->usergroup['crm_canViewVisitReports'] == 0) {
//    error($lang->sectionnopermission);
//    exit;
//}


if(!$core->input['action']) {
    if(!empty($core->input['num'])) {
        $phonenumber = $db->escape_string($core->input['num']);
        $entities = Entities::get_data(" phone1 = '{$phonenumber}' OR phone2 = '{$phonenumber}' ORDER BY createdOn DESC", array('operators' => array('filter' => 'CUSTOMSQLSECURE'), 'returnarray' => true));
//        $entities = Entities::get_data('eid =1', array('returnarray' => true));
//        $entities = Entities::get_data('eid IN (1,2,3,4,5)', array('returnarray' => true));
        if(is_array($entities)) {
            if(count($entities) == 1) {
                foreach($entities as $entity) {
                    $entityid = '<input type="hidden" name="log[eid]" value="'.$entity->eid.'">';
                    $withentity = '&nbspwith '.$entity->get_displayname();
                    $entitylogs = CallLogs::get_data("eid= {$entity->eid} AND (isPrivate = 0 OR uid = {$core->user['uid']})", array('order' => 'createdOn DESC', 'limit' => 10, 'returnarray' => true));
                    if(is_array($entitylogs)) {
                        $logs = "<h2>{$lang->pastlogs}</h2>";
                        foreach($entitylogs as $log) {
                            $logtitle = date('l jS \of F Y h:i:s A', $log->createdOn);
                            $logdescription = $log->description;
                            $id = $log->{CallLogs::PRIMARY_KEY};
                            eval("\$logs .= \"".$template->get('crm_addcalllog_log')."\";");
                        }
                    }
                }
            }
            elseif(count($entities) > 1) {
                $entityid = ' <label for="entity"><strong>'.$lang->entity.'</strong></label><br>';
                $entityid .= parse_selectlist('log[eid]', 0, $entities, $selected_options, '', '', array('id' => 'entity'));
            }
            eval("\$managecallog = \"".$template->get('crm_addcalllog')."\";");
            output_page($managecallog);
            exit;
        }
    }

    redirect($core->settings['rootdir']);
}
else {
    if($core->input['action'] == 'do_perform_addcalllog') {
        $calllog = new CallLogs();
        $calllog->set($core->input['log']);
        $calllog = $calllog->save();
        switch($calllog->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            default:
                output_xml('<status>falsw</status><message>'.$lang->errorsaving.'</message>');
                break;
        }
    }
}
?>