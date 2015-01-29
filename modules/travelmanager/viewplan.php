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

    /* Preview button from plantrip */
    if(isset($core->input['referrer']) && $core->input['referrer'] == 'plan') {
        $lid = $core->input['lid'];
        $plan_object = TravelManagerPlan::get_plan(array('tmpid' => $planid));
        if(!is_object($plan_object)) {
            $plan_object = TravelManagerPlan::get_plan(array('lid' => $lid));
            $planid = $plan_object->tmpid;
        }
    }
    /* Save and Preview button from plantrip */
    if(isset($core->input['referrer']) && $core->input['referrer'] == 'plantrip') {
        $plan_object = TravelManagerPlan::get_plan(array('tmpid' => $planid));
        $checkbox['confirm'] = '<div class="ui-state-highlight ui-corner-all" style="padding:5px; margin-bottom:10px;"><input type="checkbox" id="confirm_finalize"/>'.$lang->confirmfinalizeplan.'</div>';
        $finalize_button = '<input type="submit" disabled="disabled" class="button" value=" '.$lang->finalize.'" id="perform_travelmanager/viewplan_Button">';
    }

    if(!is_object($plan_object)) {
        redirect('index.php?module=travelmanager/listplans');
    }

    $leave = $plan_object->get_leave();
    $approvers = $leave->get_approvers();
    if(is_array($approvers)) {
        foreach($approvers as $approver) {
            $approver_chain[] = $approver->uid;
        }
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

    $segment_objs = TravelManagerPlanSegments::get_segments(array('tmpid' => $planid), array('returnarray' => true));
    $plan_name = $leave_type.'-'.$plan_object->get_leave()->get_country()->get()['name'];
    //$leave_details = $plan_object->get_leave()->parse_leave();

    if(is_array($segment_objs)) {
        foreach($segment_objs as $segmentid => $segment) {
            $segment_details .= $segment->parse_segment();
            $segment_expenses = $segment->parse_expensesummary();
        }
        /* Get and parse all the possibe transportations */
        $transportaion_fields_title = '<div style="font-size: 24px;color: #91B64F;font-weight: 100;">'.$lang->allpossibleflights.'</div>';
        foreach($segment_objs as $segmentid => $segment) {
            if(!empty($segment->get()[apiFlightdata])) {
                $transportaionsegment_fields .='<div style="horizontal-align: middle; font-weight: bold;border-bottom: 1px dashed #666;font-size: 14px;padding:5px; background-color: #92D050 ; ">'.$segment->get_origincity()->name.' - '.$segment->get_destinationcity()->name.'</div>';
                $transportaionsegment_fields .= TravelManagerAirlines::parse_bestflight($segment->get()[apiFlightdata], array(), $sequence, 'email');
            }
        }
        if(!empty($transportaionsegment_fields)) {
            $transportaion_fields .= $transportaion_fields_title.$transportaionsegment_fields;
            unset($transportaionsegment_fields, $transportaion_fields_title);
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
    print_R($mailer->debug_info());
    $mailer->send();
}
elseif($core->input['action'] == 'do_perform_viewplan') {
    $db->update_query('travelmanager_plan', array('isFinalized' => 1), tmpid.' = '.$core->input['planid']);
    $url = 'index.php?module=travelmanager/viewplan&id='.$core->input['planid'].'&action=email';
    header('Content-type: text/xml+javascript');
    output_xml('<status>true</status><message><![CDATA[<script>goToURL(\''.$url.'\');</script>]]></message>');
}