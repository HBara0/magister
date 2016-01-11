<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: facilitiesschedule.php
 * Created:        @rasha.aboushakra    Nov 4, 2015 | 2:50:10 PM
 * Last Update:    @rasha.aboushakra    Nov 4, 2015 | 2:50:10 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['canUseFacM'] == 0) {
    error($lang->sectionnopermission);
}
if(!isset($core->input['action'])) {
//    $facilities = FacilityMgmtFacilities::get_data(array('isActive' => 1), array('returnarray' => true, 'simple' => fales));
//    if(is_array($facilities)) {
//        foreach($facilities as $facilitiy) {
//            $reservations = FacilityMgmtReservations::get_data(array('fmfid' => $facilitiy->fmfid), array('returnarray' => true, 'simple' => false));
//            if(is_array($reservations)) {
//                foreach($reservations as $reservation) {
//                    $reserved_data[$facilitiy->fmfid]['title'] = $facilitiy->name;
//                    $reserved_data[$facilitiy->fmfid]['start'] = date(DATE_ATOM, $reservation->fromDate);
//                    $reserved_data[$facilitiy->fmfid]['end'] = date(DATE_ATOM, $reservation->toDate);
//                    $reserved_data[$facilitiy->fmfid]['color'] = '#'.$facilitiy->idColor;
//                }
//            }
//        }
//    }
//    $reserved_data = json_encode($reserved_data);
    eval("\$facilitiestree= \"".$template->get('facilitymgmt_facilitiesschedule')."\";");
    output_page($facilitiestree);
}
else {
    if($core->input['action'] == 'get_creatreservation') {
        $statuses = FacilityManagementReserveType::get_data(null, array('returnarray' => true));
        if(is_array($statuses)) {
            $statuslist = parse_selectlist('reserve[status]', '1', $statuses, 2, '', '', array('id' => 'status'));
        }
        $purposes = FacilityManagementReservePurpose::get_data(null, array('returnarray' => true));
        if(is_array($purposes)) {
            foreach($purposes as $purpose) {
                if($purpose->fmrt == 0) {
                    $purposeoptions .= '<option value="'.$purpose->alias.'" >'.$purpose->get_displayname().'</option>';
                }
                else if($purpose->fmrt == 2) {
                    $purposeoptions .= '<option data-purpose="purpose_'.$purpose->fmrt.'" value="'.$purpose->alias.'" >'.$purpose->get_displayname().'</option>';
                }
                else {
                    $purposeoptions .= '<option data-purpose="purpose_'.$purpose->fmrt.'" value="'.$purpose->alias.'" style="display:none">'.$purpose->get_displayname().'</option>';
                }
            }
        }
        $date = strtotime($core->input['date']);
        $reservation['fromDate'] = $date;
        $reservation['fromTime_output'] = trim(preg_replace('/(AM|PM)/', '', date('H:i', $date)));
        $reservation['fromDate_output'] = date($core->settings['dateformat'], $date);
        $reservation['toDate'] = $date + 1;
        $reservation['toTime_output'] = trim(preg_replace('/(AM|PM)/', '', date('H:i', $date + 1)));
        $reservation['toDate_output'] = date($core->settings['dateformat'], $date + 1);
        $facinputname = 'reserve[fmfid]';
        $facilityid = '';
        $facilityname = '';
        $extra_inputids = ',pickDate_from,pickDate_to,altpickTime_to,altpickTime_from';
        eval("\$facilityreserve = \"".$template->get('facility_reserveautocomplete')."\";");
        eval("\$reserve = \"".$template->get('popup_reservefacility')."\";");
        output($reserve);
    }
    else if($core->input['action'] == 'fetchevents') {
        $reservations = FacilityMgmtReservations::get_data(array(), array('returnarray' => true, 'simple' => false));
        if(is_array($reservations)) {
            foreach($reservations as $reservation) {
                $facilitiy = $reservation->get_facility();
                if($facilitiy->isActive != 1) {
                    continue;
                }
                $reserved_data[] = array('title' => $facilitiy->name, 'start' => date(DATE_ATOM, $reservation->fromDate), 'end' => date(DATE_ATOM, $reservation->toDate), 'color' => $facilitiy->idColor);
            }
        }
        echo(json_encode($reserved_data));
    }
    else if($core->input['action'] == 'perform_createreservation') {
        if(is_empty($core->input['reserve']['fromDate'], $core->input['reserve']['toDate'], $core->input['reserve']['fmfid'], $core->input['reserve']['fromTime'], $core->input['reserve']['toTime'])) {
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            exit;
        }
        $core->input['reserve']['fromDate'] = strtotime($core->input['reserve']['fromDate'].' '.$core->input['reserve']['fromTime']);
        $core->input['reserve']['toDate'] = strtotime($core->input['reserve']['toDate'].' '.$core->input['reserve']['toTime']);
        $reservation = new FacilityMgmtReservations();
        $reservation->set($core->input['reserve']);
        $reservation = $reservation->save();
        switch($reservation->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'<![CDATA[<script>$("div[id^=\'popup_\']").dialog("close").remove(); $(\'#calendar\').fullCalendar("refetchEvents")</script>]]></message>');
                break;
            case 5:
                $error_output = $errorhandler->get_errors_inline();
                output_xml("<status>false</status><message>{$lang->errorsaving}<![CDATA[<br/>{$error_output}]]></message>");
                exit;
            default:
                output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
        }
    }
}