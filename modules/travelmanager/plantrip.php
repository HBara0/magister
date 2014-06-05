<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: plantrip.php
 * Created:        @tony.assaad    May 28, 2014 | 10:03:51 AM
 * Last Update:    @tony.assaad    May 28, 2014 | 10:03:51 AM
 */



if(!$core->input['action']) {

    $leaveid = $db->escape_string($core->input['lid']);
    $leaveid = 10982;
    $sequence = 1;

    $tools_addnewtab = '<a id="createtab" class="showpopup" href="#"><img border="0" alt="Create New Tab" src="images/addnew.png"></img> </a>';

    $segments = null;
    if(!empty($segments)) {

    }
    else {
        /* Popuplate basic information from the leave based on the lid passed via ajax */
        $leave_obj = new Leaves(array('lid' => $leaveid), false);
        $leave = $leave_obj->get();

        $segment[$sequence]['fromDate_output'] = date($core->settings['dateformat'], $leave['fromDate']);
        $segment[$sequence]['fromDate_formatted'] = $leave['fromDate'];
        $segment[$sequence]['toDate_output'] = date($core->settings['dateformat'], $leave['toDate']);

        $segment[$sequence]['toDate_formatted'] = date('d-m-Y', $leave['toDate']);
        print_r($segment);
        $fromDate = new DateTime($segment[$sequence][$leave['fromDate_output']]);
        $todate = new DateTime($segment[$sequence][$leave['toDate_output']]);

        $segment[$sequence]['numberdays'] = $fromDate->diff($todate)->format(' %d days');

        $segment[$sequence]['origincity'] = $leave_obj->get_sourcecity($leave['origincity'])->get();
        $segment[$sequence]['origincity']['name'] = $segment[$sequence]['origincity'] ['name'];
        $segment[$sequence]['origincity']['ciid'] = $segment[$sequence]['origincity']['ciid'];
        $segment[$sequence]['destinationcity'] = $leave_obj->get_destinationcity()->get();  /* Will get the capital city of the visited country of leave */
        $segment[$sequence]['destinationcity']['name'] = $segment[$sequence]['destinationcity']['name'];  /* Will get the capital city of the visited country of leave */
        $segment[$sequence]['destinationcity']['ciid'] = $segment[$sequence]['destinationcity']['ciid'];  /* Will get the capital city of the visited country of leave */
        $disabled = 'disabled="true"';

        eval("\$plantrip_createsegment= \"".$template->get('travelmanager_plantrip_createsegment')."\";");

        $segments_output = '<div id="segmentstabs-1">'.$plantrip_createsegment.'</div>';
        //$previoussegtodate = ($segment[$sequence]['fromDate']);
        $previoussegtodate = ($core->input['segment'][$sequence]['toDate']);
        $previoussegdestcity = $core->input['segment'][$sequence]['destinationCity'];
    }
    if(isset($core->input['continue']) && $core->input['continue'] == 'continue') {
        $core->input['action'] = 'continue';
        echo 'continue';
    }
    eval("\$plantrip = \"".$template->get('travelmanager_plantrip')."\";");
    output($plantrip);
}
elseif($core->input['action'] == 'add_segment') {
    $querystring = array('leaveid' => $core->input['lid'], 'sequence' => $core->input['sequence'], 'toDate' => $core->input['toDate'], 'destcity' => $core->input['destcity']);

    foreach($querystring as $key => $val) {
        $$key = $db->escape_string($val);
    }

//    $leaveid = $db->escape_string($core->input['lid']);
//    $sequence = $db->escape_string($core->input['sequence']);
//    $todate = $db->escape_string($core->input['todate']);
//    $destcity = $db->escape_string($core->input['destcity']);

    /* get prev city name */
    $city_obj = new Cities($core->input['destcity']);
    $descitydata = $city_obj->get();
    /* origin city of the new  segment is destination of previous segment */
    $segment[$sequence]['origincity']['name'] = $descitydata['name'];
    $segment[$sequence]['origincity']['ciid'] = $descitydata['ciid'];
    /* Overwrite from date of next segment with  TOdate of prev segment */

    $segment[$sequence]['fromDate_output'] = date($core->settings['dateformat'], strtotime($core->input['toDate']));

    $segment[$sequence]['fromDate_formatted'] = $core->input['toDate'];


    /* Popuplate basic information from the leave based on the lid passed via ajax */
    $leave_obj = new Leaves(array('lid' => $leaveid), false);
// from date todate origin city loaded via js from the prevsegment
    $disabled = '';
    eval("\$plantrip_createsegment= \"".$template->get('travelmanager_plantrip_createsegment')."\";");
    output($plantrip_createsegment);
}
elseif($core->input['action'] == 'populatecontent') {
    $origincityid = $db->escape_string($core->input['origincity']);
    $destcityid = $db->escape_string($core->input['destcity']);
    $destcity['departuretime'] = $db->escape_string(strtotime($core->input['departuretime']));
    $sequence = $db->escape_string($core->input['sequence']); /* get the  sequence to differentiate the content of each */

    $descity_obj = new Cities($destcityid);
    $descitydata = $descity_obj->get();
    $destcity['name'] = $descitydata[name];
    $destcity['country'] = $descity_obj->get_country()->get()['name'];
    $destcity['drivemode'] = 'transit';

    $origincity_obj = new Cities($origincityid);
    $origincitydata = $origincity_obj->get();
    $origintcity['name'] = $origincitydata[name];
    $origintcity['country'] = $origincity_obj->get_country()->get()['name'];
    $directionapi = TravelManagerPlan::get_availablecitytransp(array('origincity' => $origintcity, 'destcity' => $destcity, 'departuretime' => $destcity['departuretime']));  /* Get available tranportaion mode for the city proposed by google API */

    $drivingmode['url'] = $directionapi->routes[0]->legs[0]->steps[0]->transit_details->line->url;
    $drivingmode['vehiclename'] = $directionapi->routes[0]->legs[0]->steps[0]->transit_details->line->vehicle->name;
    $drivingmode['vehicletype'] = $directionapi->routes[0]->legs[0]->steps[0]->transit_details->line->vehicle->type;

    $drivingmode['transpcat'] = TravelManagerPlan::parse_transportation($drivingmode, $sequence);
    /* Load proposed transproration */

    eval("\$transsegments_output = \"".$template->get('travelmanager_plantrip_segment_transportation')."\";");
    /* load approved hotels */

    $approved_hotelsobjs = $descity_obj->get_approvedhotels();
    if(is_array($approved_hotelsobjs)) {
        foreach($approved_hotelsobjs as $approved_hotelsobj) {
            $approved_hotels = $approved_hotelsobj->get();
            $hotelname = array($approved_hotels['tmhid'] => $approved_hotels['name']);
            $review_tools .= ' <a href="#'.$approved_hotels['tmhid'].'" id="hotelreview_'.$approved_hotels['tmhid'].'_travelmanager/plantrip_loadpopupbyid" rel="hotelreview_'.$approved_hotels['tmhid'].'" title="'.$lang->sharewith.'"><img src="'.$core->settings['rootdir'].'/images/icons/reviewicon.png" title="'.$lang->readhotelreview.'" alt="'.$lang->readhotelreview.'" border="0" width="16" height="16"></a>';
            $hotelssegments_output .= '    <div style="display:block;">'.parse_radiobutton('segment['.$sequence.'][tmhid]', $hotelname, '', true, '&nbsp;&nbsp;', array('required' => $approved_hotels['isRequired'])).'<span>'.$review_tools.'</span></div>';
            //eval("\$hotelssegments_output  .= \"".$template->get('travelmanager_plantrip_segment_hotels')."\";");
            $review_tools = '';
        }
    }


    eval("\$plansegmentscontent_output = \"".$template->get('travelmanager_plantrip_segmentcontents')."\";");
    output($plansegmentscontent_output);
}
elseif($core->input['action'] == 'populatecityprofile') {
    $destcityid = $db->escape_string($core->input['destcity']);
    $city_obj = new Cities($destcityid);
    /* Parse ity reviews content */
    $city_reviewsobjs = $city_obj->get_reviews();
    if(is_array($city_reviewsobjs)) {
        $cityprofile_output = '<div> <strong>'.$lang->cityreview.'</strong></div>';
        foreach($city_reviewsobjs as $city_reviewsobj) {
            $destcityreview['review'] = $city_reviewsobj->get()['review'];
            $destcityreview['user'] = $city_reviewsobj->get_createdBy()->get();
            $destcityreview['reviewdby'] = $destcityreview['user']['displayName'];
            $cityprofile_output .='<div style="display:block;padding:8px;">
                <div>'.$destcityreview['review'].'</div>
                    <div class="smalltext"><a href="'.$core->settings['rootdir'].'/users.php?action=profile&uid='.$destcityreview['user']['uid'].'"  target="_blank">'.$destcityreview['reviewdby'].'</a></div>
                        </div>';
        }
        output($cityprofile_output);
    }
    else {
        $destcityreview['review'] = $lang->na;
    }
    $city_briefingsobj = $city_obj->get_latestbriefing();
    if(is_object($city_briefingsobj)) {
        $citybriefings_output = ' <div><strong>'.$lang->citybrfg.'</strong></div>';
        $destcitybriefing['briefing'] = $city_briefingsobj->get()['review'];
        $destcitybriefing['user'] = $city_briefingsobj->get_createdBy()->get();
        $destcitybriefing['briefedby'] = $destcitybriefing['user']['displayName'];
        $citybriefings_output = '<div style="display:block;padding:8px;">
         <div>'.$destcitybriefing['briefing'].'</div>
         <div class="smalltext"><a href="'.$core->settings['rootdir'].'/users.php?action=profile&uid='.$destcitybriefing['user']['uid'].' target="_blank">'.$destcitybriefing['briefedby'].'</a></div></div>';
    }
    else {
        $destcitybriefing['briefing'] = $lang->na;
    }
}
elseif($core->input['action'] == 'parsedetailstransp') {
    $catid = $db->escape_string($core->input['catid']);
    $sequence = $db->escape_string($core->input['sequence']);

    $transp_category_fields = TravelManagerPlan::parse_transportaionfields($catid, $sequence);

    eval("\$transsegments_output = \"".$template->get('travelmanager_plantrip_segment_transportation')."\";");
    output($transsegments_output);
}
elseif($core->input['action'] == 'do_perform_plantrip') {
    $travelplan_obj = new TravelManagerPlan();
    if(is_array($core->input[segment])) {
        $travelplan_obj->create($core->input[segment]);
    }
    exit;
    switch($travelplan_obj->get_errorcode()) {
        case 0:
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
            break;
        case 1:
            output_xml("<status>false</status><message>{$lang->planexist}</message>");
            exit;
        case 2:
            output_xml("<status>false</status><message>{$lang->planexist}</message>");
            exit;
        case 3:
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            exit;
    }
}
elseif($core->input['action'] == 'get_hotelreview') {
    $hotelid = $db->escape_string($core->input['id']);
    $hotel_obj = new TravelManagerHotels($hotelid);
    $hotel_review = $hotel_obj->get_review();
    if(is_array($hotel_review)) {
        $hotel['reviewdetails'] = $hotel_review->get();
        $hotel['review'] = $hotel['reviewdetails']['review'];
        $hotel['reviewby'] = $hotel_review->get_createdBy()->get()['displayName'];
    }
    else {
        $hotel['review'] = $hotel['reviewby'] = $lang->na;
    }
    eval("\$hotel_reviews= \"".$template->get('popup_hotel_review')."\";");
    output($hotel_reviews);
}
