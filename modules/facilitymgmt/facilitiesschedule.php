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
if($core->usergroup['facilitymgmt_canManageFacilities'] == 0) {
    error($lang->sectionnopermission);
}
if(!isset($core->input['action'])) {
    $facilities = FacilityMgmtFacilities::get_data(array('isActive' => 1), array('returnarray' => true, 'simple' => fales));
    if(is_array($facilities)) {
        foreach($facilities as $facilitiy) {
            $reservations = FacilityMgmtReservations::get_data(array('fmfid' => $facilitiy->fmfid), array('returnarray' => true, 'simple' => false));
            if(is_array($reservations)) {
                foreach($reservations as $reservation) {
                    $reserved_data[$facilitiy->fmfid]['title'] = $facilitiy->name;
                    $reserved_data[$facilitiy->fmfid]['start'] = date(DATE_ATOM, $reservation->fromDate);
                    $reserved_data[$facilitiy->fmfid]['end'] = date(DATE_ATOM, $reservation->toDate);
                    $reserved_data[$facilitiy->fmfid]['color'] = '#'.$facilitiy->idColor;
                }
            }
        }
    }
    $reserved_data = json_encode($reserved_data);
    eval("\$facilitiestree= \"".$template->get('facilitymgmt_facilitiesschedule')."\";");
    output_page($facilitiestree);
}
else if($core->input['action'] == 'fetchevents') {
    $facilities = FacilityMgmtFacilities::get_data(array('isActive' => 1), array('returnarray' => true, 'simple' => fales));
    if(is_array($facilities)) {
        foreach($facilities as $facilitiy) {
            $reservations = FacilityMgmtReservations::get_data(array('fmfid' => $facilitiy->fmfid), array('returnarray' => true, 'simple' => false));
            if(is_array($reservations)) {
                foreach($reservations as $reservation) {
                    $reserved_data[$facilitiy->fmfid]['title'] = $facilitiy->name;
                    $reserved_data[$facilitiy->fmfid]['start'] = date(DATE_ATOM, $reservation->fromDate);
                    $reserved_data[$facilitiy->fmfid]['end'] = date(DATE_ATOM, $reservation->toDate);
                    $reserved_data[$facilitiy->fmfid]['color'] = '#'.$facilitiy->idColor;
                }
            }
        }
    }
    echo(json_encode($reserved_data[1]));
}