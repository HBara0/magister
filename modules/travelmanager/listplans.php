<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: listplan.php
 * Created:        @tony.assaad    Aug 4, 2014 | 5:12:22 PM
 * Last Update:    @tony.assaad    Aug 4, 2014 | 5:12:22 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $travelplan_obj = TravelManagerPlan::get_plan(array('uid' => $core->user['uid']), array('returnarray' => true));
    if(is_array($travelplan_obj)) {
        foreach($travelplan_obj as $plan) {
            // $leave_type = $plan->get_leave()->get_type()->get()['name'];

            $plan->displayName = $plan->get_leave()->get_type()->name.' - '.$plan->get_leave()->get_country()->get()['name'];
            $employee = $plan->get_createdBy()->get()['displayName'];
            if(!empty($plan->createdOn)) {
                $createdon = date($core->settings['dateformat'], $plan->createdOn);
            }

            $plan->link = 'index.php?module=travelmanager/viewplan&id='.$plan->tmpid;
            if($plan->isFinalized == 0) {
                $plan->link = 'index.php?module=travelmanager/plantrip&lid='.$plan->lid;
            }
            eval("\$plan_rows .= \"".$template->get('travelmanager_listlpans_rows')."\";");
        }
    }
    eval("\$travelmanager_listlpant = \"".$template->get('travelmanager_listlpans')."\";");
    output_page($travelmanager_listlpant);
}