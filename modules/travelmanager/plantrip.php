<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: plantrip.php
 * Created:        @tony.assaad    May 28, 2014 | 10:03:51 AM
 * Last Update:    @tony.assaad    May 28, 2014 | 10:03:51 AM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $leaveid = $db->escape_string($core->input['lid']);
    $sequence = 1;

    $tools_addnewtab = '<a id="createtab" class="showpopup" href="#" title="'.$lang->addsegment.'"><img border="0"  alt="Create New Tab" src="images/addnew.png"></img> </a>';

    $segments = null;
    if(!empty($segments)) {

    }
    else {
        /* Popuplate basic information from the leave based on the lid passed via ajax */
        $leave_obj = new Leaves(array('lid' => $leaveid), false);
        $leave = $leave_obj->get();

        $segment['countryleave'] = $leave['coid'];
        $segment[$sequence]['fromDate_output'] = date($core->settings['dateformat'], $leave['fromDate']);
        $segment[$sequence]['fromDate_formatted'] = date('d-m-Y', $leave['fromDate']);
        $segment[$sequence]['toDate_output'] = date($core->settings['dateformat'], $leave['toDate']);
        $segment[$sequence]['toDate_formatted'] = date('d-m-Y', $leave['toDate']);
        $leave[$sequence][toDate] = $leave['toDate'];
        $fromDate = new DateTime($segment[$sequence]['fromDate_output']);
        $todate = new DateTime($segment[$sequence]['toDate_output']);

        $segment[$sequence]['numberdays'] = $fromDate->diff($todate)->format(' %d days');

        $segment[$sequence]['origincity'] = $leave_obj->get_sourcecity($leave['origincity'])->get();
        $segment[$sequence]['origincity']['name'] = $segment[$sequence]['origincity'] ['name'];
        $segment[$sequence]['origincity']['ciid'] = $segment[$sequence]['origincity']['ciid'];
        $segment[$sequence]['destinationcity'] = $leave_obj->get_destinationcity()->get();                 /* Will get the capital city of the visited country of leave */
        $segment[$sequence]['destinationcity']['name'] = $segment[$sequence]['destinationcity']['name'];  /* Will get the capital city of the visited country of leave */
        $segment[$sequence]['destinationcity']['ciid'] = $segment[$sequence]['destinationcity']['ciid'];  /* Will get the capital city of the visited country of leave */
        $disabled = 'disabled="true"';

        $cityprofile_output = $leave_obj->get_destinationcity()->parse_cityreviews();
        $citybriefings_output = $leave_obj->get_destinationcity()->parse_citybriefing();

        $origincity_obj = $leave_obj->get_sourcecity();

        $origincitydata = $origincity_obj->get();
        $origintcity['name'] = $origincitydata['name'];
        $origintcity['country'] = $origincity_obj->get_country()->get()['name'];

        $descity_obj = $leave_obj->get_destinationcity();
        $descitydata = $descity_obj->get();
        $destcity['name'] = $descitydata['name'];
        $destcity['country'] = $descity_obj->get_country()->get()['name'];
        $destcity['drivemode'] = 'transit';
        $destcity['departuretime'] = $db->escape_string(($leave['fromDate']));

        $transsegments_output = Cities::parse_tranaportations(array('origincity' => $origintcity, 'destcity' => $destcity, 'departuretime' => $destcity['departuretime']), $sequence);

        $hotelssegments_output = $descity_obj->parse_approvedhotels($sequence);

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

    $querystring = array('leaveid' => $core->input['lid'], 'sequence' => $core->input['sequence'], 'toDate' => $core->input['toDate'], 'leavetoDatetime' => $core->input['leavetoDatetime'], 'destcity' => $core->input['destcity']);

    foreach($querystring as $key => $val) {
        $$key = $db->escape_string($val);
    }

    $leave_obj = new Leaves(array('lid' => $leaveid), false);
    $leave = $leave_obj->get();
    /* prevent adding new segment if to date  greater than original  leave end date */
    $leave[$sequence]['toDate'] = $leave['toDate'];
    $leave[$sequence]['toDate'] = strtotime(date('Y-m-d 00:00:00', $leave[$sequence]['toDate']));
    if(strtotime($core->input['toDate']) >= $leave[$sequence]['toDate']) {

        output_xml("<message>{$lang->dateexceeded}</message>");
        exit;
    }
    else {
        /* get prev city name */
        $city_obj = new Cities($core->input['destcity']);
        $descitydata = $city_obj->get();
        /* origin city of the new  segment is destination of previous segment */
        $segment[$sequence]['origincity']['name'] = $descitydata['name'];
        $segment[$sequence]['origincity']['ciid'] = $descitydata['ciid'];
        /* Overwrite from date of next segment with  TOdate of prev segment */
        $segment[$sequence]['toDate_output'] = date($core->settings['dateformat'], ( $leave[$sequence]['toDate']));
        $segment[$sequence]['toDate_formatted'] = date('d-m-Y', ( $leave[$sequence]['toDate'])); // leave to date
        $segment[$sequence]['fromDate_output'] = date($core->settings['dateformat'], strtotime($core->input['toDate']));
        $segment[$sequence]['fromDate_formatted'] = $core->input['toDate'];

        /* Popuplate basic information from the leave based on the lid passed via ajax */

        $segment['countryleave'] = $leave['coid'];
        //if($core->input['toDate']) > leavedate
// from date todate origin city loaded via js from the prevsegment
        $disabled = '';
        eval("\$plantrip_createsegment= \"".$template->get('travelmanager_plantrip_createsegment')."\";");
        output($plantrip_createsegment);
    }
}
elseif($core->input['action'] == 'populatecontent') {
    $origincityid = $db->escape_string($core->input['origincity']);
    $destcityid = $db->escape_string($core->input['destcity']);
    $destcity['departuretime'] = $db->escape_string(strtotime($core->input['departuretime']));
    $sequence = $db->escape_string($core->input['sequence']); /* get the  sequence to differentiate the content of each */

    $descity_obj = new Cities($destcityid);

    $descitydata = $descity_obj->get();
    $destcity['name'] = $descitydata['name'];
    $destcity['country'] = $descity_obj->get_country()->get()['name'];
    $destcity['drivemode'] = 'transit';

    $origincity_obj = new Cities($origincityid);
    $origincitydata = $origincity_obj->get();
    $origintcity['name'] = $origincitydata[name];
    $origintcity['country'] = $origincity_obj->get_country()->get()['name'];
    //$directionapi = TravelManagerPlan::get_availablecitytransp(array('origincity' => $origintcity, 'destcity' => $destcity, 'departuretime' => $destcity['departuretime']));  /* Get available tranportaion mode for the city proposed by google API */
    $transpmode_googledirections = ' https://www.google.com/maps/dir/'.$origintcity['name'].',+'.$origintcity['country'].'/'.$destcity['name'].',+'.$destcity['country'].'/';

//    for($i = 0; $i < count($directionapi->routes[0]->legs[0]->steps); $i++) {
//        if(!empty($directionapi->routes[0]->legs[0]->steps[$i]->transit_details->line->url)) {
//            $transitmode['url'] = $directionapi->routes[0]->legs[0]->steps[$i]->transit_details->line->url;
//        }
//        if(!empty($directionapi->routes[0]->legs[0]->steps[$i]->transit_details->line->vehicle->name)) {
//            $transitmode['vehiclename'] = $directionapi->routes[0]->legs[0]->steps[$i]->transit_details->line->vehicle->name;
//        }
//        if(!empty($directionapi->routes[0]->legs[0]->steps[$i]->transit_details->line->vehicle->type)) {
//            $transitmode['vehicletype'] = $directionapi->routes[0]->legs[0]->steps[$i]->transit_details->line->vehicle->type;
//        }
    // $drivingmode['transpcat'] = TravelManagerPlan::parse_transportation($transitmode, array('origincity' => $origintcity, 'destcity' => $destcity), $sequence);
    //   eval("\$transsegments_output  .= \"".$template->get('travelmanager_plantrip_segment_transportation')."\";");
    // unset($transitmode);
//}
    /* Load proposed transproration */
    $transsegments_output = Cities::parse_tranaportations(array('origincity' => $origintcity, 'destcity' => $destcity, 'departuretime' => $destcity['departuretime']), $sequence);


    /* load approved hotels */
    $hotelssegments_output = $descity_obj->parse_approvedhotels($sequence);

    eval("\$plansegmentscontent_output = \"".$template->get('travelmanager_plantrip_segmentcontents')."\";");
    output($plansegmentscontent_output);
}
elseif($core->input['action'] == 'populatecityprofile') {
    $destcityid = $db->escape_string($core->input['destcity']);
    $city_obj = new Cities($destcityid);

    /* Parse city reviews content */
    $cityprofile_output = $city_obj->parse_cityreviews();
    output($cityprofile_output);

    $citybriefings_output = $city_obj->parse_citybriefing();
    output($citybriefings_output);
}
elseif($core->input['action'] == 'parsedetailstransp') {
    $catid = $db->escape_string($core->input['catid']);
    $sequence = $db->escape_string($core->input['sequence']);
    $categoryid = $db->escape_string($core->input['categoryid']);

    $transp_category_fields = TravelManagerPlan::parse_transportaionfields($catid, $sequence);
    eval("\$transsegments_output = \"".$template->get('travelmanager_plantrip_segment_transportation')."\";");

//output($transsegments_output);
}
elseif($core->input['action'] == 'do_perform_plantrip') {
    $travelplan_obj = new TravelManagerPlan();
    if(is_array($core->input['segment'])) {

        $travelplan_obj->create($core->input['segment']);
    }
    switch($travelplan_obj->get_errorcode()) {
        case 0:
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
            break;
        case 1:
            output_xml("<status>false</status><message>{$lang->planexist}</message>");
            exit;
        case 2:
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        case 3:
            output_xml("<status>false</status><message>{$lang->dateexceeded}</message>");
            exit;
        case 4:
            output_xml("<status>false</status><message>{$lang->segmenexist}</message>");
            exit;

        case 5:
            output_xml("<status>false</status><message>{$lang->oppositedate}</message>");
            exit;
        case 6:
            output_xml("<status>false</status><message> {$lang->errorcity}</message>");
            exit;
        case 7:
            output_xml("<status>false</status><message> {$lang->errordate} </message>");
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
