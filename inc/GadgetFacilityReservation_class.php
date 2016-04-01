<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: GadgetFacilityReservation_class.php
 * Created:        @rasha.aboushakra    Apr 1, 2016 | 2:54:04 PM
 * Last Update:    @rasha.aboushakra    Apr 1, 2016 | 2:54:04 PM
 */

/**
 * Description of GadgetFacilityReservation_class
 *
 * @author rasha.aboushakra
 */
class GadgetFacilityReservation extends SystemGadget {
    protected $data = array();
    protected $widget_id = '5';

    const CLASSNAME = __CLASS__;
    const widget_id = 5;

    public function __construct() {
        parent::__construct();
    }

    public function create(array $data) {

    }

    public function update(array $data) {

    }

    /**
     *
     * @param array $instancedata
     * @return string
     */
    public function parse(array $instancedata) {
//        if(!empty($instancedata['serializedConfig'])) {
//            $serialized_config = $instancedata['serializedConfig'];
//            $configs = unserialize($serialized_config);
//            if(is_array($configs)) {
//                $timezones_array = explode(',', $configs['options']['timezones']);
//            }
//        }
        return $this->parse_facilitymgmtgadget($instancedata);
    }

    /**
     * @global type $lang
     * @global type $core
     * @global type $template
     * @param type $instancedata
     * @return string html
     */
    public function parse_facilitymgmtgadget($instancedata = '') {
        global $lang, $core, $template;
        $lang->load('facilitymgmt_meta');
        $statuses = FacilityManagementReserveType::get_data(null, array('returnarray' => true));
        if(is_array($statuses)) {
            $statuslist = parse_selectlist('reserve[status]', '1', $statuses, 2, '', '', array('id' => 'status', 'width' => '150px'));
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
        $date = TIME_NOW;
        $reservation['fromDate'] = $date;
        $reservation['fromTime_output'] = trim(preg_replace('/(AM|PM)/', '', date('H:i', $date)));
        $reservation['fromDate_output'] = date($core->settings['dateformat'], $date);
        $reservation['toDate'] = $date + 1;
        $reservation['toTime_output'] = trim(preg_replace('/(AM|PM)/', '', date('H:i', $date + 1)));
        $reservation['toDate_output'] = date($core->settings['dateformat'], $date + 1);
        $facinputname = 'reserve[fmfid]';
        $facilityid = '';
        $facilityname = '';
        $display_infobox = 'style="display:none"';
        $extra_inputids = ',pickDate_from,pickDate_to,altpickTime_to,altpickTime_from';
        eval("\$facilityreserve = \"".$template->get('facility_reserveautocomplete')."\";");
        eval("\$reserve = \"".$template->get('gadget_facilityreservation')."\";");
        return $reserve;
    }

}