<?php

if ($core->usergroup['canAccessSystem'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if (!$core->input['action']) {
    if (isset($core->input['id']) && !empty($core->input['id'])) {
        $event_obj = new Events(intval($core->input['id']));
        $event = $event_obj->get();
        $isActive = $event['isActive'];
        $event['fromdateoutput'] = $event_obj->get_fromdateoutput();
        $event['todateoutput'] = $event_obj->get_todateoutput();
        $event['fromtimeoutput'] = $event_obj->get_fromtimeoutput();
        $event['totimeoutput'] = $event_obj->get_totimeoutput();

        //parse subscription section
        $subscriber_objs = $event_obj->get_subsribers();
        if (is_array($subscriber_objs)) {
            foreach ($subscriber_objs as $subscriber_obj) {
                $subscribeoutput = $subscriber_obj->get_displayname();
                eval("\$subscriberssection_lines.= \"" . $template->get('events_manageevent_subscription_line') . "\";");
            }
            eval("\$studentsubscription_section= \"" . $template->get('events_manageevent_subscription') . "\";");
        }
    }
    else {
        $event['inputChecksum'] = generate_checksum();
    }
    $isactive_list = parse_selectlist2('event[isActive]', 1, array('1' => 'Yes', '0' => 'No'), $isActive);


    eval("\$managejevent= \"" . $template->get('events_manageevent') . "\";");
    output_page($managejevent);
}
elseif ($core->input['action'] == 'do_perform_manageevent') {
    $event_obj = new Events();
    $core->input['event']['alias'] = generate_alias($core->input['event']['title']);
    $event_obj->set($core->input['event']);
    $event_obj = $event_obj->save();
    switch ($event_obj->get_errorcode()) {
        case 0:
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
            break;
        case 1:
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            break;
        case 2:
            output_xml("<status>false</status><message>Wrong From Date & Time</message>");
            break;
        case 3:
            output_xml("<status>false</status><message>Wrong To Date & Time</message>");
            break;
        default:
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            break;
    }
}
?>