<?php

if (!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if ($core->usergroup['canAccessSystem'] == 0) {
    error($lang->sectionnopermission);
}
if (!isset($core->input['action'])) {
    $events_objs = Events::get_data(array('isActive' => 1), array('returnarray' => true, 'order' => 'fromDate DESC'));
    if (is_array($events_objs)) {
        foreach ($events_objs as $events_obj) {
            $event = $events_obj->get();
            $event_link = $events_obj->parse_link();
            $fromtime = $events_obj->get_fromdate();
            $totime = $events_obj->get_todate();

            $from_output = date($core->settings['dateformat'] . ' ' . $core->settings['timeformate'], $fromtime);
            $to_output = date($core->settings['dateformat'] . ' ' . $core->settings['timeformate'], $totime);
            if ($events_obj->description) {
                if (strlen($events_obj->description) > 150) {
                    $description = substr($events_obj->description, 0, 150) . '...';
                }
                else {
                    $description = $events_obj->description;
                }
            }
            else {
                $description = 'N/A';
            }
            if ($events_obj->is_subscribed($core->user['uid'])) {
                $subscribe_cell = 'data-sort="1"';
                $subscribed = '<span style="color:green;font-weight:bold">Yes <span class="glyphicon glyphicon-ok"></span></span>';
            }
            else {
                $subscribe_cell = 'data-sort="0"';
                $subscribed = '<span style="color:red;font-weight:bold"">No <span class="glyphicon glyphicon-remove"></span></span>';
            }

            $tool_items = ' <li><a target="_blank" href="' . $events_obj->get_link() . '"><span class="glyphicon glyphicon-eye-open"></span>&nbsp' . $lang->viewevent . '</a></li>';
            if ($events_obj->canManageEvent()) {
                $tool_items .= ' <li><a target="_blank" href="' . $events_obj->get_editlink() . '"><span class="glyphicon glyphicon-pencil"></span>&nbsp' . $lang->manageevent . '</a></li>';
            }
            $totalstudents = $events_obj->get_totalstudents();
            eval("\$tools = \"" . $template->get('tools_buttonselectlist') . "\";");
            eval("\$courses_list .= \"" . $template->get('events_eventslist_courserow') . "\";");
            unset($tool_items, $subscribe_cell);
        }
    }
    eval("\$page= \"" . $template->get('events_eventslist') . "\";");
    output_page($page);
}