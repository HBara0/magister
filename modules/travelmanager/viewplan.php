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
        $hide_close = 'style="display:none;"';
        $checkbox['confirm'] = '<div class="ui-state-highlight ui-corner-all" style="padding:5px; margin-bottom:10px;"><input type="checkbox" id="confirm_finalize"/>'.$lang->confirmfinalizeplan.'</div>';
        $finalize_button = '<input type="submit" disabled="disabled" class="button" value=" '.$lang->finalize.'" id="perform_travelmanager/viewplan_Button">';
    }

    if(!is_object($plan_object)) {
        redirect('index.php?module=travelmanager/listplans');
    }

    $leave = $plan_object->get_leave();
    $approvers = $leave->get_approvers(array('returnarray' => true));
    $approver_chain = array();
    if(is_array($approvers)) {
        foreach($approvers as $approver) {
            $approver_chain[] = $approver->uid;
        }
    }
    /* Viewable only if user is the person travel  or  the user is in the apprval chain of the plan trip leave */
    if(!is_object($plan_object)) {
        error($lang->sectionpermissions);
    }
    if(is_array($approver_chain)) {
        if($plan_object->uid != $core->user['uid'] && !in_array($core->user['uid'], $approver_chain)) {
            error($lang->sectionpermissions);
        }
    }
    $leave_type = $leave->get_type();
    $leave_purpose = $leave_segment = $lang->na;
//    if(is_object($leave->get_purpose())) {
//        $leave_purpose = $leave->get_purpose()->get()['name'];
//    }

    $reason = $leave->reason;
    fix_newline($reason);
    $leave_purpose = $reason;
    if(is_object($leave->get_segment())) {
        $leave_segment = $leave->get_segment()->get()['title'];
    }
    $plan_name = $leave_type->title.' - '.$plan_object->get_leave()->get_country()->get_displayname();
    $employee = $leave->get_user()->get_displayname();
    //$leave_type = unserialize($plan_object->get_leave()->get_type()->get()['toApprove']);

    /* parse segment of plan */
    $segment_objs = TravelManagerPlanSegments::get_segments(array('tmpid' => $planid), array('order' => array('by' => 'sequence', 'sort' => 'ASC'), 'returnarray' => true));
    if(is_array($segment_objs)) {
        foreach($segment_objs as $segmentid => $segment) {
            $segment_details .= $segment->parse_segment();
            $segment_expenses = $segment->parse_expensesummary();
        }
    }
    if($core->input['preview'] == 1) {
        $display_fin = $hide_close = 'style="display:none"';
    }

    eval("\$leave_details = \"".$template->get('travelmanager_viewlpan_leavedtls')."\";");
    eval("\$content = \"".$template->get('travelmanager_viewlpan_content')."\";");

    if($core->input['preview'] == 1) {
        $display_fin = $hide_close = 'style="display:none"';
        output_page($content);
    }
    else {
        eval("\$travelmanager_viewplan = \"".$template->get('travelmanager_viewlpan')."\";");
        output_page($travelmanager_viewplan);
    }
//get  segment trans from db
}
else {
    if($core->input['action'] == 'email') {
        $planid = intval($core->input['id']);
        $plan_object = TravelManagerPlan::get_plan(array('tmpid' => $planid, 'isFinalized' => 1));
        if(!is_object($plan_object)) {
            exit;
        }
        $leave = $plan_object->get_leave();
        $leave_type = $leave->get_type();
        $employee = $leave->get_user()->get_displayname();
        $leave_purpose = $leave_segment = $lang->na;
//        if(is_object($leave->get_purpose())) {
//            $leave_purpose = $leave->get_purpose()->get()['name'];
//        }
        $leave_purpose = $leave->reason;
        if(is_object($leave->get_segment())) {
            $leave_segment = $leave->get_segment()->get()['title'];
        }
        $plan_name = $leave_type->title.' - '.$leave->get_country()->get_displayname();
        $leave_requestey = $leave->requestKey;
        $approve_link = DOMAIN.'/index.php?module=attendance/listleaves&action=takeactionpage&requestKey='.base64_encode($leave->requestKey).'&id='.base64_encode($leave->lid).'&tmpid='.$planid;
        $segment_objs = TravelManagerPlanSegments::get_segments(array('tmpid' => $planid), array('order' => 'sequence', 'simple' => false, 'returnarray' => true));

        if(is_array($segment_objs)) {
            foreach($segment_objs as $segmentid => $segment) {
                $segment_details .= $segment->parse_segment();
                $segment_expenses = $segment->parse_expensesummary();
            }

            foreach($segment_objs as $segmentid => $segment) {
                $transportaion_fields_title = '<div style="font-size: 24px;color: #91B64F;font-weight: 100;">'.$segment->get_origincity()->name.' - '.$segment->get_destinationcity()->name.'</div>';
                /* Get and parse all the possibe Flights */
                if(!empty($segment->get()['apiFlightdata'])) {
                    $transportaionsegment_fields .= '<div style="horizontal-align: middle; font-weight: bold;border-bottom: 1px dashed #666;font-size: 14px;padding:5px; background-color: #92D050 ; ">'.$lang->allpossibleflights.'</div>';
                    $transportaionsegment_fields .= TravelManagerAirlines::parse_bestflight($segment->get()['apiFlightdata'], array(), $sequence, 'email');
                }
                /* Get and parse all the possibe Approved Hotels */
                $destcity = new Cities($segment->destinationCity);
                $approvedhotels = $destcity->get_country()->get_approvedhotels();
                if(is_array($approvedhotels)) {
                    foreach($approvedhotels as $hotel) {
                        $isselectedhotel = TravelManagerPlanaccomodations::get_data(array('tmpsid' => $segment->tmpsid, 'tmhid' => $hotel->tmhid));
                        if(is_object($isselectedhotel)) {
                            continue;
                        }

                        $path = "./images/invalid.gif";
                        $iscontractedicon = '<img src="data:image/png;base64,'.base64_encode(file_get_contents($path)).'" alt="'.$lang->no.'"/>';

                        if($hotel->isContracted == 1) {
                            $path = "./images/valid.gif";
                            $iscontractedicon = '<img src="data:image/png;base64,'.base64_encode(file_get_contents($path)).'" alt="'.$lang->yes.'"/>';
                        }
                        /* parse ratings */
                        eval("\$otherapprovedhotels .= \"".$template->get('travelmanager_approvedhotel_row')."\";");
                    }
                    $transportaionsegment_fields .= $transportaion_fields_title;
                    eval("\$transportaionsegment_fields .= \"".$template->get('travelmanager_viewplan_approvedhotels')."\";");
                }
                unset($otherapprovedhotels);
            }
            if(!empty($transportaionsegment_fields)) {
                $transportaion_fields .= $transportaionsegment_fields;
                unset($transportaionsegment_fields, $transportaion_fields_title);
            }
        }
        eval("\$leave_details = \"".$template->get('travelmanager_viewlpan_leavedtls')."\";");

        eval("\$travelmanager_viewplan = \"".$template->get('travelmanager_viewlpanemail')."\";");
        $leave->create_approvalchain();
        $firstapprover = $leave->get_firstapprover()->get_user();

        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_type();
        $mailer->set_from(array('name' => 'Orkila Attendance System', 'email' => 'attendance@ocos.orkila.com'));
        $mailer->set_subject($lang->sprint($lang->requestleavesubject, $employee, $leave_type->title.' - '.$plan_object->get_leave()->get_country()->get_displayname(), $leave_requestey));
        $mailer->set_message($travelmanager_viewplan);
        $mailer->set_to($firstapprover->email);
        $mailer->send();
//        $plan_object->email_finance($segment_expenses, $lang->tmplanfinancenotification, $employee);
        redirect('index.php?module=travelmanager/listplans');
    }
    elseif($core->input['action'] == 'do_perform_viewplan') {
        $tmplansegments = TravelManagerPlanSegments::get_data(array('tmpid' => $core->input['planid']), array('returnarray' => true));
        if(is_array($tmplansegments)) {
            foreach($tmplansegments as $segment) {
                if($segment->noAccomodation == 1) {
                    continue;
                }
                $travelmanageraccom = TravelManagerPlanaccomodations::get_data(array('tmpsid' => $segment->tmpsid), array('returnarray' => true));
                if(!is_array($travelmanageraccom)) {
                    header('Content-type: text/xml+javascript');
                    output_xml('<status>false</status><message>'.$lang->acomchecknote.' for '.$segment->name.'</message>');
                    exit;
                }
            }
        }
        $travelplan = new TravelManagerPlan();
        $travelplanexist = new TravelManagerPlan($core->input[planid]);
        if($travelplanexist->is_finalized()) {
            output_xml("<status>false</status><message>{$lang->finalizedplan}</message>");
            exit;
        }
        else {
            if(is_array($core->input['segment'])) {
                $travelplan->set($core->input);
                $travelplan->save();
            }
        }

        switch($travelplan->get_errorcode()) {
            case 0:
                $db->update_query(TravelManagerPlan::TABLE_NAME, array('isFinalized' => 1), TravelManagerPlan::PRIMARY_KEY.'='.$core->input['planid']);
                $url = 'index.php?module=travelmanager/viewplan&id='.$core->input['planid'].'&action=email';
                header('Content-type: text/xml+javascript');
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'<![CDATA[<script>goToURL(\''.$url.'\');</script>]]></message>');

                // output_xml("<status>true</status><message></message>");
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->planexist}</message>");
                exit;
            case 2:
                output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
                exit;
            case 3:
                output_xml("<status>false</status><message>{$lang->dateexceeded}</message>");
                exit;
            case 4:
                output_xml("<status>false</status><message>{$lang->segmenexist}</message>");
                exit;
            case 5:
                output_xml("<status>false</status><message>{$lang->oppositedate}</message>");
                exit;
            case 6:
                output_xml("<status>false</status><message> {$lang->errorcity}</message>");
                exit;
            case 7:
                output_xml("<status>false</status><message> {$lang->errordate} </message>");
                exit;
            case 8:
                output_xml("<status>false</status><message> {$lang->erroritinerarydate} </message>");
                exit;
        }
    }
}
?>