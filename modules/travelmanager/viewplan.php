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
    $planid = intval($core->input['id']);
    $plan_object = TravelManagerPlan::get_plan(array('tmpid' => $planid));
    //set user permission
    if($core->user['uid'] != $plan_object->uid) {
        error($lang->sectionpermissions);
    }
    $leave_type = $plan_object->get_leave()->get_type()->get()['name'];

    $plan_name = $leave_type.' - '.$plan_object->get_leave()->get_country()->get()['name'];
    //$leave_type = unserialize($plan_object->get_leave()->get_type()->get()['toApprove']);

    /* parse leave */
    $leave_details = $plan_object->get_leave()->parse_leave();

    /* parse segment of plan */
    $segment_objs = TravelManagerPlanSegments::get_segments(array('tmpid' => $planid));
    foreach($segment_objs as $segmentid => $segment) {
        $segment_details .= $segment->parse_segment();
        $segment_expenses = $segment->parse_expensesummary();
    }

    eval("\$travelmanager_viewplan = \"".$template->get('travelmanager_viewlpan')."\";");
    output_page($travelmanager_viewplan);
    //get  segment trans from db
}
elseif($core->input['action'] == 'email') {
    $planid = $db->escape_string($core->input['id']);
    $plan_object = TravelManagerPlan::get_plan(array('tmpid' => $planid));
    $leave_type = $plan_object->get_leave()->get_type()->get()['name'];
    $leave_requestey = $plan_object->get_leave()->get()['requestKey'];

    $segment_objs = TravelManagerPlanSegments::get_segments(array('tmpid' => $planid));
    $plan_name = $leave_type.'-'.$plan_object->get_leave()->get_country()->get()['name'];
    $leave_details = $plan_object->get_leave()->parse_leave();

    /* Get and parse all the possibe transportations */
    foreach($segment_objs as $segmentid => $segment) {
        $segment_details .= $segment->parse_segment();
        $segment_expenses = $segment->parse_expensesummary();
        $transportaion_fields = TravelManagerAirlines::parse_bestflight($segment->apiFlightdata, array(), $sequence, 'email');
    }
    eval("\$travelmanager_viewplan = \"".$template->get('travelmanager_viewlpanemail')."\";");

    $mailer = new Mailer();
    $mailer = $mailer->get_mailerobj();
    $mailer->set_type();
    $mailer->set_from(array('name' => 'tony.assaad', 'email' => 'tony.assaad@ocos.local'));
    $mailer->set_subject('plantrip');
    $mailer->set_message($travelmanager_viewplan);
    $mailer->set_to('tony.assaad@ocos.local');
    $mailer->send();
}