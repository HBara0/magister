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
    $plan_object = TravelManagerPlan::get_plan(array('tmpid' => $planid, 'isFinalized' => 1));
    $leave = $plan_object->get_leave();
    $approvers = $leave->get_approvers();
    foreach($approvers as $approver) {
        $approver_chain[] = $approver->uid;
    }
    /* Viewable only if user is the person travel  or  the user is in the apprval chain of the plan trip leave */
    if(!is_object($plan_object) || ($plan_object->uid != $core->user['uid'] && !in_array($core->user['uid'], $approver_chain))) {
        error($lang->sectionpermissions);
    }
    $leave_type = $leave->get_type();
    $leave_purpose = $leave_segment = $lang->na;
    if(is_object($leave->get_purpose())) {
        $leave_purpose = $leave->get_purpose()->get()['name'];
    }
    if(is_object($leave->get_segment())) {
        $leave_segment = $leave->get_segment()->get()['title'];
    }

    $plan_name = $leave_type->title.' - '.$plan_object->get_leave()->get_country()->get_displayname();
    //$leave_type = unserialize($plan_object->get_leave()->get_type()->get()['toApprove']);

    /* parse segment of plan */
    $segment_objs = TravelManagerPlanSegments::get_segments(array('tmpid' => $planid), array('order' => 'fromDate', 'returnarray' => true));
    if(is_array($segment_objs)) {
        foreach($segment_objs as $segmentid => $segment) {
            $segment_details .= $segment->parse_segment();
            $segment_expenses = $segment->parse_expensesummary();
        }
    }
    $transportaion_fields_title = $lang->allpossibletransportations;
    eval("\$leave_details = \"".$template->get('travelmanager_viewlpan_leavedtls')."\";");
    eval("\$travelmanager_viewplan = \"".$template->get('travelmanager_viewlpan')."\";");
    output_page($travelmanager_viewplan);
    //get  segment trans from db
}
elseif($core->input['action'] == 'email') {
    $planid = $db->escape_string($core->input['id']);
    $plan_object = TravelManagerPlan::get_plan(array('tmpid' => $planid, 'isFinalized' => 1));
    if(!is_object($plan_object)) {
        exit;
    }
    $leave_type = $plan_object->get_leave()->get_type()->get()['name'];
    $leave_requestey = $plan_object->get_leave()->get()['requestKey'];

    $segment_objs = TravelManagerPlanSegments::get_segments(array('tmpid' => $planid));
    $plan_name = $leave_type.'-'.$plan_object->get_leave()->get_country()->get()['name'];
    //$leave_details = $plan_object->get_leave()->parse_leave();

    /* Get and parse all the possibe transportations */
    foreach($segment_objs as $segmentid => $segment) {
        $segment_details .= $segment->parse_segment();
        $segment_expenses = $segment->parse_expensesummary();

        if(!empty($segment->get()[apiFlightdata])) {
            $transportaion_fields = TravelManagerAirlines::parse_bestflight($segment->get()[apiFlightdata], array(), $sequence, 'email');
        }
    }
    eval("\$travelmanager_viewplan = \"".$template->get('travelmanager_viewlpanemail')."\";");
    $mailer = new Mailer();
    $mailer = $mailer->get_mailerobj();
    $mailer->set_type();
    $mailer->set_from(array('name' => 'tony.assaad', 'email' => 'tony.assaad@ocos.local'));
    $mailer->set_subject('plantrip'.'['.$plan_name.']');
            $mailer->set_message($travelmanager_viewplan);
            $mailer->set_to('tony.assaad@ocos.local');
            $mailer->send();
            }