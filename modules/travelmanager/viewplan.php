<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: viewplan.php
 * Created:        @tony.assaad    Aug 4, 2014 | 4:48:46 PM
 * Last Update:    @tony.assaad    Aug 4, 2014 | 4:48:46 PM
 */


if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $planid = $db->escape_string($core->input['id']);
    $plan_object = TravelManagerPlan::get_plan(array('tmpid' => $planid));
    $leave_type = $plan_object->get_leave()->get_type()->get()['name'];

    $plan_name = $leave_type.'-'.$plan_object->get_leave()->get_country()->get()['name'];
    //set user permission
    if($core->user['uid'] != $plan_object->uid) {
        error($lang->sectionpermissions);
    }
    //$leave_type = unserialize($plan_object->get_leave()->get_type()->get()['toApprove']);

    /* parse leave */
    $leave_details = $plan_object->get_leave()->parse_leave();

    /* parse segment of plan */
    $segment_objs = TravelManagerPlanSegments::get_segments(array('tmpid' => $planid));
    foreach($segment_objs as $segment) {
        $segment_details .= $segment->parse_segment();
    }

    eval("\$travelmanager_viewplan = \"".$template->get('travelmanager_viewlpan')."\";");
    output_page($travelmanager_viewplan);
    //get  segment trans from db
}