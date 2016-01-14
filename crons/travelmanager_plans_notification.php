<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: travelmanager_plans_notification.php
 * Created:        @rasha.aboushakra    Sep 16, 2014 | 11:20:36 AM
 * Last Update:    @rasha.aboushakra    Sep 16, 2014 | 11:20:36 AM
 */

require '../inc/init.php';
//if($_REQUEST['authkey'] == 'kia5ravb$op09dj4a!xhegalhj') {
$lang = new Language('english');
$lang->load('travelmanager_meta');

$dal_config = array(
        'returnarray' => true,
        'operators' => array('fromDate' => 'BETWEEN'),
);
$Leaves_obj = Leaves::get_data(array('fromDate' => array(strtotime("+2 days 00:00:00"), strtotime("+2 days 23:59:59"))), $dal_config);
if(!empty($Leaves_obj)) {
    foreach($Leaves_obj as $leave) {
        $segment_details = '';
        $segment_expenses = '';
        $plan_object = TravelManagerPlan::get_plan(array('lid' => $leave->lid));
        if(!is_object($plan_object)) {
            continue;
        }
        if(is_object($plan_object)) {
            $leave_type = $plan_object->get_leave()->get_type()->get()['name'];
            $segment_objs = TravelManagerPlanSegments::get_segments(array('tmpid' => $plan_object->tmpid), array('returnarray' => true));
            $plan_name = $leave_type.'-'.$plan_object->get_leave()->get_country()->get()['name'];
            $leave_details = $plan_object->get_leave();
            /* Get and parse all the possibe transportations */
            if(!empty($segment_objs)) {
                foreach($segment_objs as $segmentid => $segment) {
                    $segment_details .= $segment->parse_segment();
                    $segment_expenses = $segment->parse_expensesummary();
                }
            }

            eval("\$travelmanager_viewplan = \"".$template->get('travelmanager_viewlpanemail')."\";");
            $user = $plan_object->get_user();
            $mailer = new Mailer();
            $mailer = $mailer->get_mailerobj();
            $mailer->set_type();
            $mailer->set_from(array('name' => 'OCOS Mailer', 'email' => $core->settings['maileremail']));
            $mailer->set_subject($lang->plannotification);
            $mailer->set_message($travelmanager_viewplan);
            $mailer->set_to($user->email);
            $mailer->send();
//            $x = $mailer->debug_info();
//            print_R($x);
//            exit;
        }
    }
}
//}
?>