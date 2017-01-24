<?php

if (!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if ($core->usergroup['canAccessSystem'] == 0) {
    error($lang->sectionnopermission);
}
if (!isset($core->input['action'])) {

    eval("\$calendar= \"" . $template->get('main_calendar') . "\";");
    $additionalheaderinc = "<link href='" . $core->settings[rootdir] . "/css/fullcalendar.min.css' rel='stylesheet' />
                            <script src='" . $core->settings[rootdir] . "/js/moment.min.js'></script>
                            <script src='" . $core->settings[rootdir] . "/js/fullcalendar.min.js' type='text/javascript'></script>
                            <style>
                                #calendar {
                                    max-width: 900px;
                                    margin: 0 auto;
                                }
                            </style>";
    output_page($calendar, array('additionalheaderinc' => $additionalheader));
}
else {
    if ($core->input['action'] == 'get_createevent') {
        $event['inputChecksum'] = generate_checksum();
        $start_time = ($core->input['start'] / 1000);
        $end_time = ($core->input['end'] / 1000);
        $event['fromdateoutput'] = date('d-m-Y', $start_time);
        $event['todateoutput'] = date('d-m-Y', $end_time);
        $event['fromtimeoutput'] = date('h:i A', $start_time);
        $event['totimeoutput'] = date('h:i A', $end_time);

        eval("\$calendarpopup = \"" . $template->get('modal_createevent') . "\";");
        output($calendarpopup);
    }
    else if ($core->input['action'] == 'fetchevents') {
        //get calendarassignments
        $assignment_objs = CalendarAssignments::get_data(array('uid' => $core->user['uid'], 'isActive' => 1), array('returnarray' => true, 'simple' => false));
        if (is_array($assignment_objs)) {
            foreach ($assignment_objs as $assignment_obj) {
                $reserved_data[] = array('id' => $assignment_obj->get_id(), 'type' => $assignment_obj->get_type(), 'title' => $assignment_obj->get_displayname(), 'start' => date(DATE_ATOM, $assignment_obj->fromDate), 'end' => date(DATE_ATOM, $reservation->toDate), 'color' => $assignment_obj->get_color());
            }
        }
        //get events that came from recommendations
        $event_objs = Events::get_data("isActive=1 AND rid IS NOT NULL AND createdBy=" . $core->user['uid'], array('returnarray' => true, 'simple' => false));
        if (is_array($event_objs)) {
            foreach ($event_objs as $event_obj) {
                $reserved_data[] = array('id' => $event_obj->get_id(), 'type' => 'recommendationevent', 'title' => $event_obj->get_displayname(), 'start' => date(DATE_ATOM, $event_obj->fromDate), 'end' => date(DATE_ATOM, $event_obj->toDate), 'color' => $event_obj->get_color());
            }
        }
        //get deadlines
        $deadline_objs = Deadlines::get_data(array('uid' => $core->user['uid'], 'isActive' => 1), array('returnarray' => true, 'simple' => false));
        if (is_array($deadline_objs)) {
            foreach ($deadline_objs as $deadline_obj) {
                $reserved_data[] = array('id' => $deadline_obj->get_id(), 'type' => 'deadline', 'title' => $deadline_obj->get_displayname(), 'start' => date(DATE_ATOM, $deadline_obj->get_fromdate()), 'end' => date(DATE_ATOM, $deadline_obj->get_todate()), 'color' => $deadline_obj->get_color());
            }
        }
        //get course lectures
        $assignedcourses = AssignedCourses::get_data(array('uid' => $core->user['uid'], 'isActive' => 1), array('returnarray' => true, 'simple' => false));
        if (is_array($assignedcourses)) {
            foreach ($assignedcourses as $assignedcourse) {
                $course_obj = $assignedcourse->get_course();
                if (!is_object($course_obj)) {
                    continue;
                }
                //get lectures
                $lecture_objs = $course_obj->get_lectures();
                if (is_array($lecture_objs)) {
                    foreach ($lecture_objs as $lecture_obj) {
                        $reserved_data[] = array('id' => $lecture_obj->get_id(), 'type' => 'lecture', 'title' => $lecture_obj->get_displayname(), 'start' => date(DATE_ATOM, $lecture_obj->get_fromdate()), 'end' => date(DATE_ATOM, $lecture_obj->get_todate()), 'color' => $lecture_obj->get_color());
                    }
                }
                //get deadlines
                $deadline_objs = $course_obj->get_deadlines();
                if (is_array($deadline_objs)) {
                    foreach ($deadline_objs as $deadline_obj) {
                        $reserved_data[] = array('id' => $deadline_obj->get_id(), 'type' => 'deadline', 'title' => $deadline_obj->get_displayname(), 'start' => date(DATE_ATOM, $deadline_obj->get_fromdate()), 'end' => date(DATE_ATOM, $deadline_obj->get_todate()), 'color' => $deadline_obj->get_color());
                    }
                }
            }
        }
        echo(json_encode($reserved_data));
    }
    else if ($core->input['action'] == 'perform_createreservation') {
        if (is_empty($core->input['reserve']['fromDate'], $core->input['reserve']['toDate'], $core->input['reserve']['fmfid'], $core->input['reserve']['fromTime'], $core->input['reserve']['toTime'])) {
            output_xml('<status>false</status><message>' . $lang->fillrequiredfields . '</message>');
            exit;
        }
        $core->input['reserve']['fromDate'] = strtotime($core->input['reserve']['fromDate'] . ' ' . $core->input['reserve']['fromTime']);
        $core->input['reserve']['toDate'] = strtotime($core->input['reserve']['toDate'] . ' ' . $core->input['reserve']['toTime']);

        switch ($reservation->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>' . $lang->successfullysaved . '<![CDATA[<script>$("div[id^=\'popup_\']").dialog("close").remove(); $(\'#calendar\').fullCalendar("refetchEvents")</script>]]></message>');
                break;
            case 5:
                $error_output = $errorhandler->get_errors_inline();
                output_xml("<status>false</status><message>{$lang->errorsaving}<![CDATA[<br/>{$error_output}]]></message>");
                exit;
            default:
                output_xml('<status>false</status><message>' . $lang->errorsaving . '</message>');
                break;
        }
    }
    else if ($core->input['action'] == 'get_editevent') {
        if (!isset($core->input['id'])) {
            return false;
        }
        $id = intval($core->input['id']);
        $reserve = new FacilityMgmtReservations($id);
        if (is_object($reserve)) {
            $facilitiy = $reserve->get_facility();
            $facilityid = $facilitiy->fmfid;
            $facilityname = $facilitiy->get_displayname();
            $reservedby = $reserve->get_reservedBy()->get_displayname();
            if ($core->user['uid'] == $reserve->reservedBy && $reserve->mtid == 0) {
                $display_infobox = 'style="display:none"';
                $statuses = FacilityManagementReserveType::get_data(null, array('returnarray' => true));
                if (is_array($statuses)) {
                    $statuslist = parse_selectlist('reserve[status]', '1', $statuses, 2, $reserve->status, '', array('id' => 'status'));
                }
                $purposes = FacilityManagementReservePurpose::get_data(null, array('returnarray' => true));
                if (is_array($purposes)) {
                    foreach ($purposes as $purpose) {
                        if ($purpose->alias == $reserve->purpose) {
                            $selected = 'selected="selected"';
                        }
                        if ($purpose->fmrt == 0) {
                            $purposeoptions .= '<option ' . $selected . ' value="' . $purpose->alias . '" >' . $purpose->get_displayname() . '</option>';
                        }
                        else if ($purpose->fmrt == 2) {
                            $purposeoptions .= '<option ' . $selected . ' data-purpose="purpose_' . $purpose->fmrt . '" value="' . $purpose->alias . '" >' . $purpose->get_displayname() . '</option>';
                        }
                        else {
                            $purposeoptions .= '<option ' . $selected . ' data-purpose="purpose_' . $purpose->fmrt . '" value="' . $purpose->alias . '" style="display:none">' . $purpose->get_displayname() . '</option>';
                        }
                        $selected = '';
                    }
                }
                $reservation['fromDate'] = $reserve->fromDate;
                $reservation['fromTime_output'] = trim(preg_replace('/(AM|PM)/', '', date('H:i', $reserve->fromDate)));
                $reservation['fromDate_output'] = date($core->settings['dateformat'], $reserve->fromDate);
                $reservation['toDate'] = $reserve->toDate;
                $reservation['toTime_output'] = trim(preg_replace('/(AM|PM)/', '', date('H:i', $reserve->toDate)));
                $reservation['toDate_output'] = date($core->settings['dateformat'], $reserve->toDate);
                $facinputname = 'reserve[fmfid]';
                $extra_inputids = ',pickDate_from,pickDate_to,altpickTime_to,altpickTime_from';
                eval("\$facilityreserve = \"" . $template->get('facility_reserveautocomplete') . "\";");
            }
            else {
                $reservation['fromDate_output'] = date($core->settings['dateformat'], $reserve->fromDate) . ' , ' . trim(preg_replace('/(AM|PM)/', '', date('H:i', $reserve->fromDate)));
                $reservation['toDate_output'] = date($core->settings['dateformat'], $reserve->toDate) . ' , ' . trim(preg_replace('/(AM|PM)/', '', date('H:i', $reserve->toDate)));
                $show_status = 'style="display:none"';
                if (!empty($reserve->status)) {
                    $reservetype = new FacilityManagementReserveType(intval($reserve->status));
                    $reservation['status_output'] = $reservetype->get_displayname();
                    $show_status = '';
                }
                $show_purpose = 'style="display:none"';
                if ($reserve->mtid) {
                    $reservation['purpose_output'] = $lang->reservedfrommeetings;
                    $show_purpose = '';
                }
                elseif (!empty($reserve->purpose)) {
                    $reservepurpose = FacilityManagementReservePurpose::get_data(array('alias' => $reserve->purpose));
                    $reservation['purpose_output'] = $reservepurpose->get_displayname();
                    $show_purpose = '';
                }
                $display_form = 'style="display:none"';
            }
        }
        eval("\$reserve = \"" . $template->get('popup_reservefacility') . "\";");
        output($reserve);
    }
    else if ($core->input['action'] == 'do_perform_calendar') {
        $eventdadta = $core->input['event'];

        if ($core->input['type'] == 'event') {
            $managed_obj = new Events();
            if (!is_empty($eventdadta['toTime'], $eventdadta['toDate'])) {
                $eventdadta['toDate'] = strtotime($eventdadta['toDate'] . ' ' . $eventdadta['toTime']);
                unset($eventdadta['toTime']);
            }
            else {
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                exit;
            }

            if (!is_empty($eventdadta['fromTime'], $eventdadta['fromDate'])) {
                $eventdadta['fromDate'] = strtotime($eventdadta['fromDate'] . ' ' . $eventdadta['fromTime']);
                unset($eventdadta['fromTime']);
            }
            else {
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                exit;
            }
        }
        elseif ($core->input['type'] == 'deadline') {
            $managed_obj = new Deadlines();
            $eventdadta['time'] = strtotime($eventdadta['fromDate'] . ' ' . $eventdadta['fromTime']);
            unset($eventdadta['fromTime'], $eventdadta['fromDate']);
        }
        $managed_obj->set($eventdadta);
        $managed_obj->save();
        switch ($managed_obj->get_errorcode()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successfullysaved}<![CDATA[<script>$(function(){  $('#calendar_modal').modal('toggle');$('#calendar').fullCalendar( 'refetchEvents' ); });</script>]]></message>");
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            default:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                break;
        }
    }
}