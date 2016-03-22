<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: viewhotel.php
 * Created:        @hussein.barakat    01-Mar-2016 | 10:45:13
 * Last Update:    @hussein.barakat    01-Mar-2016 | 10:45:13
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if(!$core->input['action']) {
    if(empty($core->input['id'])) {
        error('No Hotel Chosen', $core->settings['rootdir'].'/index.php?module=travelmanager/hotelslist');
    }
    $id = $db->escape_string($core->input['id']);
    $hotel_obj = new TravelManagerHotels($id, false);
    if(is_object($hotel_obj) && !empty($hotel_obj->tmhid)) {
        $hotel = $hotel_obj->get();
        $hotel['cityname'] = $hotel_obj->get_city()->get_displayname();
        $hotel['countryname'] = $hotel_obj->get_country()->get_displayname();
        $contracted = '<span class="glyphicon glyphicon-remove" style="color:red">';
        if($hotel_obj->isContracted == '1') {
            $contracted = '<span class="glyphicon glyphicon-ok" style="color:green">';
        }
        $approved = '<span class="glyphicon glyphicon-remove" style="color:red">';
        if($hotel_obj->isApproved == '1') {
            $approved = '<span class="glyphicon glyphicon-ok" style="color:green">';
        }
        if(!empty($hotel['currency'])) {
            $hotel['currency_output'] = $hotel_obj->get_currency()->get_displayname();
        }
        if(!empty($hotel['createdBy'])) {
            $createdby_obj = Users::get_data(array('uid' => $hotel['createdBy']));
            if(is_object($createdby_obj)) {
                $hotel['createdBy_output'] = $createdby_obj->get_displayname();
            }
        }
        if(!empty($hotel['approvedBy'])) {
            $approvedby_obj = Users::get_data(array('uid' => $hotel['approvedBy']));
            if(is_object($approvedby_obj)) {
                $hotel['approvedBy_output'] = $approvedby_obj->get_displayname();
            }
        }
    }
    eval("\$viewhotel = \"".$template->get('travelmanager_viewhotel')."\";");
    output_page($viewhotel);
}