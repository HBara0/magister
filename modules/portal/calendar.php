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
        $ispublic_list = parse_selectlist2('event[isPublic]', 1, array(1 => $lang->yes, 0 => $lang->no), '');
        eval("\$calendarpopup = \"" . $template->get('modal_createevent') . "\";");
        output($calendarpopup);
    }
    else if ($core->input['action'] == 'fetchevents') {

        //get deadlines
        $deadline_objs = Deadlines::get_data(array('uid' => $core->user['uid'], 'isActive' => 1), array('returnarray' => true, 'simple' => false));
        if (is_array($deadline_objs)) {
            foreach ($deadline_objs as $deadline_obj) {
                $existingdeadlines_ids[] = $deadline_obj->get_id();
                $reserved_data[] = array('id' => $deadline_obj->get_id(), 'type' => 'deadline', 'title' => $deadline_obj->get_displayname(), 'start' => date(DATE_ATOM, $deadline_obj->get_fromtime()), 'end' => date(DATE_ATOM, $deadline_obj->get_totime()), 'color' => $deadline_obj->get_color());
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
                        $reserved_data[] = array('id' => $lecture_obj->get_id(), 'type' => 'lecture', 'title' => $lecture_obj->get_displayname(), 'start' => date(DATE_ATOM, $lecture_obj->get_fromtime()), 'end' => date(DATE_ATOM, $lecture_obj->get_totime()), 'color' => $lecture_obj->get_color());
                    }
                }
                //get deadlines
                $deadline_objs = $course_obj->get_deadlines();
                if (is_array($deadline_objs)) {
                    foreach ($deadline_objs as $deadline_obj) {
                        $reserved_data[] = array('id' => $deadline_obj->get_id(), 'type' => 'deadline', 'title' => $deadline_obj->get_displayname(), 'start' => date(DATE_ATOM, $deadline_obj->get_fromtime()), 'end' => date(DATE_ATOM, $deadline_obj->get_totime()), 'color' => $deadline_obj->get_color());
                    }
                }
            }
        }

        //get calendarassignments
        $assignment_objs = CalendarAssignments::get_data(array('uid' => $core->user['uid'], 'isActive' => 1), array('returnarray' => true, 'simple' => false));
        if (is_array($assignment_objs)) {
            foreach ($assignment_objs as $assignment_obj) {
                //skip existing events and deadlines
//                if (($assignment_obj->get_type() == 'deadline' && is_array($existingdeadlines_ids) && in_array($assignment_obj->get_id(), $existingdeadlines_ids))) {
//                    continue;
//                }
                $reserved_data[] = array('id' => $assignment_obj->get_assignedid(), 'type' => 'assigned' . $assignment_obj->get_type(), 'title' => $assignment_obj->get_displayname(), 'start' => date(DATE_ATOM, $assignment_obj->get_fromtime()), 'end' => date(DATE_ATOM, $assignment_obj->get_totime()), 'color' => $assignment_obj->get_color());
            }
        }
        echo(json_encode($reserved_data));
    }
    else if ($core->input['action'] == 'get_editevent') {
        if (!isset($core->input['id'])) {
            return false;
        }
        $output = get_editpopup_output($core->input['id'], $core->input['type']);
        output($output);
    }
    else if ($core->input['action'] == 'do_perform_calendar') {
        $eventdadta = $core->input['event'];

        if ($core->input['type'] == 'event') {
            $managed_obj = new Events();
            if (!is_empty($eventdadta['toTime'], $eventdadta['toDate'])) {
                $eventdadta['toTime'] = strtotime($eventdadta['toDate'] . ' ' . $eventdadta['toTime']);
                unset($eventdadta['toDate']);
            }
            else {
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                exit;
            }

            if (!is_empty($eventdadta['fromTime'], $eventdadta['fromDate'])) {
                $eventdadta['fromTime'] = strtotime($eventdadta['fromDate'] . ' ' . $eventdadta['fromTime']);
                unset($eventdadta['fromDate']);
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

function get_editpopup_output($id, $type) {
    switch ($type) {
        case 'recommendationevent':
        case 'event':
            $event_obj = new Events(intval($id));
            return $event_obj->parse_popup();
        case 'deadline':
            $deadline_obj = new Deadlines(intval($id));
            return $deadline_obj->parse_popup();
        case 'lecture':
            $lecture_obj = new Lectures(intval($id));
            return $lecture_obj->parse_popup();
        case 'assignedevent':
            $event_obj = new Events(intval($id));

            return $event_obj->parse_popup();
        case 'assigneddeadline':
            $deadline_obj = new Deadlines(intval($id));
            return $deadline_obj->parse_popup();

        default:
            return false;
    }
    eval("\$reserve = \"" . $template->get('popup_reservefacility') . "\";");
}
