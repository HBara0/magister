<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: edithotel.php
 * Created:        @hussein.barakat    Jun 24, 2015 | 4:52:28 PM
 * Last Update:    @hussein.barakat    Jun 24, 2015 | 4:52:28 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->input['referrer'] == 'approve') {
    if($core->usergroup['travelmanager_canApproveHotels'] == 0) {
        $approve = '<h5 style="color:red">You don\'t have the required permission to approve hotels</h5>';
    }
    else {
        $id = $db->escape_string($core->input['id']);
        $hotel_obj = new TravelManagerHotels($id, false);
        if(is_object($hotel_obj)) {
            $approved = $hotel_obj->approve_hotel();
            $approve = '<h5 style="color:red">Error while approving</h5>';
            if($approved) {
                $approve = '<h5 style="color:green">Hotel Is Now Approved</h5>';
            }
        }
    }
}
if(!$core->input['action']) {
    if(!empty($core->input['id'])) {
        $id = $db->escape_string($core->input['id']);
        $hotel_obj = new TravelManagerHotels($id, false);
        if(is_object($hotel_obj)) {
            $hotel = $hotel_obj->get();
            $cityname = $hotel_obj->get_city()->get_displayname();
            $countryname = $hotel_obj->get_country()->get_displayname();
            if($hotel_obj->isContracted == '1') {
                $check_contract = 'checked="checked"';
            }
            if($hotel_obj->isApproved == '1') {
                $check_approve = 'checked="checked"';
            }
            if(!empty($hotel_obj->phone)) {
                $phone_parts = explode('-', $hotel_obj->phone);
                if(is_array($phone_parts)) {
                    $telephone_intcode = $phone_parts[0];
                    $telephone_areacode = $phone_parts[1];
                    $telephone_number = $phone_parts[2];
                }
            }
        }
    }
    $ratingval = 0;
    if(!empty($hotel_obj->stars) || $hotel_obj->stars > 0) {
        $ratingval = $hotel_obj->stars;
    }
    $criteriaandstars .= '<div class="evaluation_criterium" name="'.$hotel_obj->tmhid.'"><div class="criterium_name" style="display:inline-block; width:30%; padding: 2px;"></div>';
    $criteriaandstars .= '<div class="ratebar" style="width:40%; display:inline-block;">';
    $header_ratingjs = '$(".rateit").click(function() {
					if(sharedFunctions.checkSession() == false) {
						return;
					}
					var targetid = $(this).parent().parent().attr("name");
					var returndiv = "";
                                        var val=$("#rating_"+targetid).val();
                                        var ids=targetid.split("_");
                                        if(ids[0].length < 1 ){
                                        return;
                                        }
                                        if(val.length >0){
					sharedFunctions.requestAjax("post", "index.php?module=travelmanager/edithotel&action=do_ratehotel", "target="+ids[0]+"&value="+val, returndiv, returndiv, "html");
                                        }
				});';
    $criteriaandstars .= '<input type="range" min="0" max="5" value="'.$ratingval.'" step="1" id="rating_'.$hotel_obj->tmhid.'" class="ratingscale">';
    $criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-resetable="false" data-rateit-backingfld="#rating_'.$hotel_obj->tmhid.'" data-rateit-value="'.$ratingval.'"></div>';
    $criteriaandstars .= '</div></div>';
// $criteriaandstars .='<input type="hidden" name="marketreport['.$segment[psid].'][rating]" id="segmentrating_'.$segment['psid'].'" value="'.$ratingval.'">';
    $currencies = Currencies::get_data();
    $currency_list = parse_selectlist('hotel[currency]', '', $currencies, $hotel_obj->currency, '', '', array('id' => 'currency', 'class' => 'form-control'));
    $contractedchekbox = '<input id="iscontracted" class="form-control" type="checkbox" name="hotel[isContracted]" value="1" '.$check_contract.'">';
    if($core->usergroup['travelmanager_canApproveHotels'] == 0) {
        $disableapprove = 'disabled="disabled"';
    }
    $approvcheckbox = '<input class="form-control" type="checkbox" id="isapproved" name="hotel[isApproved]" value="1" '.$check_approve.' '.$disableapprove.'">';
    $country = new Countries(1);
    $countriescodes = $country->get_phonecodes();
    $countriescodes_list = parse_selectlist('hotel[telephone_intcode]', $tabindex, $countriescodes, $telephone_intcode, '', '', array('id' => 'telephone_intcode'));
    eval("\$edithotel = \"".$template->get('travelmanager_edithotel')."\";");
    output_page($edithotel);
}
else {
    if($core->input['action'] == 'do_ratehotel') {
        $hotelid = $db->escape_string($core->input['target']);
        $hotel = new TravelManagerHotels($hotelid, false);
        if(is_object($hotel)) {
            $hotel_ar = $hotel->get();
            if(isset($core->input['value']) && !empty($core->input['value'])) {
                $hotel_ar['stars'] = $core->input['value'];
                $hotel->set($hotel_ar);
                $hotel->save();
            }
        }
    }
    elseif($core->input['action'] == 'do_perform_edithotel') {
        $hotel_obj = new TravelManagerHotels();
        $hotel_obj->set($core->input['hotel']);
        $hotel_obj->save();
        switch($hotel_obj->get_errorcode()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                exit;
            case 2:
                output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
                exit;
            case 3:
                output_xml("<status>false</status><message>{$lang->invalidemailaddress}</message>");
                exit;
        }
    }
}