<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: manageevents.php
 * Created:        @rasha.aboushakra    Jan 27, 2015 | 9:18:40 AM
 * Last Update:    @rasha.aboushakra    Jan 27, 2015 | 9:18:40 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if(!$core->input['action']) {

    if(isset($core->input['id']) && !empty($core->input['id'])) {
        $event_obj = Events::get_data(array('ceid' => $core->input['id']), array('simple' => false));
        if(is_object($event_obj)) {
            $event = $event_obj->get();
            if($event['isFeatured'] == 1) {
                $checkedbox['isFeatured'] = "checked='checked'";
            }
            $event['fromDate_output'] = date($core->settings['dateformat'], $event['fromDate']);
            $event['toDate_output'] = date($core->settings['dateformat'], $event['toDate']);
            $event['toTime_output'] = gmdate("H:i:s", strtotime(date($core->settings['timeformat'], $event['toDate'])));
            $event['fromTime_output'] = gmdate("H:i:s", strtotime(date($core->settings['timeformat'], $event['fromDate'])));
            $disabled['alias'] = 'readonly="readonly"';
        }
    }

    $eventtypes = CalendarEventTypes::get_data('');
    $eventtypes_list = parse_selectlist('event[type]', '', $eventtypes, $event['type']);
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(', ', $core->user['affiliates']);
        $affiliate_where = 'affid IN ('.$inaffiliates.')';
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, $affiliate_where);
    $eventaffiliates_selectlist = parse_selectlist('event[affid]', 2, $affiliates, $event['affid'], '', '', array('blankstart' => 1));
    $affiliates_selectlist = parse_selectlist('event[restrictto][]', 1, $affiliates, '', 1);
    if($core->usergroup['calendar_canAddPublicEvents'] == 1) {
        $restriction_selectlist = '<div style = "display:block;padding-top:5px;"><div style = "width:15%; display:inline-block; vertical-align:top;">'.$lang->restricto.'</div><div style = "width:70%; display:inline-block;">'.$affiliates_selectlist.'</div></div>';
        $notifyevent_checkbox = '<div style = "display:block;padding-top:5px;"><div style = "width:15%; display:inline-block;">'.$lang->notifyevent.'</div><div style = "width:70%; display:inline-block;"><input name = "event[notify]" type = "checkbox" value = "1" /></div></div>';
    }

    /* parse invitees - START */
    $affiliatedemployees = AffiliatedEmployees::get_data(array('affid' => $core->user['affiliates']));
    $users = Users::get_data(array('uid' => array_keys($affiliatedemployees)), array('operators' => array('uid' => 'IN')));
    if(is_array($users)) {
        foreach($users as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $invitees_list .='<tr class = "'.$rowclass.'">';
            $invitees_list .='<td><input id = "affiliatefilter_check_'.$key.'" name = "event[invitees][]" type = "checkbox"'.$checked.' value = "'.$key.'">'.$value.'</td></tr>';
        }
    }
    eval("\$createevent=\"".$template->get('cms_events_add')."\";");
    output_page($createevent);
}
else if($core->input['action'] == 'do_perform_manageevents') {
    unset($core->input['identifier'], $core->input['module'], $core->input['action']);

    if(empty($core->input['event']['alias'])) {
        $core->input['event']['alias'] = generate_alias($core->input['event']['title']);
    }

    $cms_event = Events::get_data(array('alias' => $core->input['event']['alias']));
    if(!is_object($event)) {
        $cms_event = new Events();
    }
    $cms_event->set($core->input['event']);
    $cms_event->save();

    /* Parse Event Logo - START */
    if(!empty($_FILES['logo']['name'][0])) {
        $_FILES['logo']['newname'][0] = $core->input['event']['alias'];
        $upload_param['upload_allowed_types'] = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png');
        $upload_obj = new Uploader('logo', $_FILES, $upload_param['upload_allowed_types'], 'putfile', 5242880, 1, 1); //5242880 bytes = 5 MB (1024);
        $logo_path = './uploads/eventslogos';
        $upload_obj->set_upload_path($logo_path);
        $upload_obj->process_file();
        $upload_obj->resize(150, '');

        $logo = $upload_obj->get_filesinfo();
        $new_event['logo'] = $upload_obj->get_filename();
        if($upload_obj->get_status() != 4) {
            ?>
            <script language="javascript" type="text/javascript">
                $(function () {
                    top.$("#upload_Result").html("<span class='red_text'><?php echo $upload_obj->parse_status($upload_obj->get_status());?></span>");
                });
            </script>
            <?php
            exit;
        }
    }
    /* Parse Event Logo - END */
    switch($cms_event->get_errorcode()) {
        case 0:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 1:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
    }
}