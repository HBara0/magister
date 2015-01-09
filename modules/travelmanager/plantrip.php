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

    if(isset($core->input['id']) && !empty($core->input['id'])) {
        $planid = $db->escape_string($core->input['id']);
        $plan_obj = new TravelManagerPlan($planid);
        if($plan_obj->is_finalized()) {
            redirect('index.php?module=travelmanager/viewplan&id='.$planid);
        }
        $plantrip = $plan_obj->parse_existingsegments();
        output($plantrip);
    }
    //$tools_addnewtab = '<a id="createtab" class="showpopup" href="#" title="'.$lang->addsegment.'"><img border="0" alt="Create New Tab" src="images/addnew.png"></img> </a>';
    else {
        $segments = null;
        /* Popuplate basic information from the leave based on the lid passed via ajax */
        $leave_obj = new Leaves(array('lid' => $leaveid), false);
        $leave = $leave_obj->get();
        $leave['fromDate_output'] = date($core->settings['dateformat'], $leave['fromDate']);
        $leave['toDate_output'] = date($core->settings['dateformat'], $leave['toDate']);
        $leave['type_output'] = $leave_obj->get_leavetype()->get()['title'];
        if(!empty($segments)) {

        }
        else {
            $segment['countryleave'] = $leave['coid'];
            $segment[$sequence]['fromDate_output'] = date($core->settings['dateformat'], $leave['fromDate']);
            $segment[$sequence]['fromDate_formatted'] = date('d-m-Y', $leave['fromDate']);
            $segment[$sequence]['toDate_output'] = date($core->settings['dateformat'], $leave['toDate']);
            $segment[$sequence]['toDate_formatted'] = date('d-m-Y', $leave['toDate']);
            $leave[$sequence]['toDate'] = $leave['toDate'];
            $fromDate = new DateTime($segment[$sequence]['fromDate_output']);
            $todate = new DateTime($segment[$sequence]['toDate_output']);

            $segment[$sequence]['numberdays'] = $fromDate->diff($todate)->format(' %d days');
            $origincity_obj = new Cities($leave['sourcecity']);
            $segment[$sequence]['origincity'] = $origincity_obj->get();
            $segment[$sequence]['origincity']['name'] = $segment[$sequence]['origincity'] ['name'];
            $segment[$sequence]['origincity']['ciid'] = $segment[$sequence]['origincity']['ciid'];
            $descity_obj = new Cities($leave['destinationcity']);
            $segment[$sequence]['destinationcity'] = $descity_obj->get();                 /* Will get the capital city of the visited country of leave */
            $segment[$sequence]['destinationcity']['name'] = $segment[$sequence]['destinationcity']['name'];  /* Will get the capital city of the visited country of leave */
            $segment[$sequence]['destinationcity']['ciid'] = $segment[$sequence]['destinationcity']['ciid'];  /* Will get the capital city of the visited country of leave */
            $disabled = 'disabled="true"';
//$leave_destcity
            $cityprofile_output = $leave_obj->get_destinationcity()->parse_cityreviews();
            $citybriefings_output = $leave_obj->get_destinationcity()->parse_citybriefing();
            $leave_purposes = array($leave_obj->get_purpose()->get()['ltpid'] => $leave_obj->get_purpose()->get()['name']);
            $segment_purposlist = parse_selectlist('segment['.$sequence.'][purpose]', 5, $leave_purposes, '');

            //   $origincity_obj = $leave_obj->get_sourcecity(false);
            $origintcity = $origincity_obj->get();
            $origintcity['country'] = $origincity_obj->get_country()->get()['name'];

            // $descity_obj = $leave_obj->get_destinationcity($false);
            $destcity = $descity_obj->get();
            $destcity['country'] = $descity_obj->get_country()->get()['name'];
            $destcity['drivemode'] = 'transit';
            $destcity['departuretime'] = $db->escape_string(($leave['fromDate']));

            $transsegments_output = Cities::parse_transportations(array('origincity' => $origintcity, 'destcity' => $destcity, 'departuretime' => $destcity['departuretime']), $sequence);

            $hotelssegments_output = $descity_obj->parse_approvedhotels($sequence, $destcity);
            $transpmode_apimaplink = 'https://www.google.com/maps/dir/'.$origintcity['name'].',+'.$origintcity['country'].'/'.$destcity['name'].',+'.$destcity['country'].'/';

            /* parse expenses --START */
            $rowid = 1;
            $expensestype[$sequence][$rowid]['display'] = "display:none;";
            $expensestype_obj = new Travelmanager_Expenses_Types();
            $segments_expenses_output = $expensestype_obj->parse_expensesfield('', $sequence, $rowid, $expensestype);

            /* parse expenses --END */

            /* Get unaprroved hotel of the destcity  for the purpose to acquire the tmhid */
            $unapproved_hotelobjs = $descity_obj->get_unapprovedhotels();
            $approved_hotels['tmhid'] = $unapproved_hotelobjs->tmhid;

            $mainaffobj = new Affiliates($core->user['mainaffiliate']);
            /* ffilter the currency  either get the curreny of the destination city or  the currencies of the country of the main affiliate */
            $currency['filter']['numCode'] = 'SELECT mainCurrency FROM countries where capitalCity='.$destcity['ciid'].' OR numCode IN(SELECT mainCurrency FROM countries where coid='.$mainaffobj->get_country()->coid.')';
            $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
            $curr_objs[840] = new Currencies(840);
            $currencies_list .= parse_selectlist('segment['.$sequence.'][tmhid]['.$approved_hotels['tmhid'].'][currency]', 4, $curr_objs, '840');


            eval("\$otherhotels_output = \"".$template->get('travelmanager_plantrip_segment_otherhotels')."\";");
            eval("\$plansegmentscontent_output = \"".$template->get('travelmanager_plantrip_segmentcontents')."\";");

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
        $leave_ouput = '  <div class="ui-state-highlight ui-corner-all" style="padding: 5px; font-style: italic;">'.$leave['type_output'].' - '.$leave['fromDate_output'].' - '.$leave['toDate_output'].'</div>';

        $segmentstabs = '   <li><a href="#segmentstabs-1">Segment 1</a></li>  ';
        eval("\$plantript_segmentstabs= \"".$template->get('travelmanager_plantrip_segmentstabs')."\";");
        eval("\$plantrip = \"".$template->get('travelmanager_plantrip')."\";");
        output_page($plantrip);
    }
}
else {
    if($core->input['action'] == 'add_segment') {
        $querystring = array('leaveid' => $core->input['lid'], 'sequence' => $core->input['sequence'], 'toDate' => $core->input['toDate'], 'leavetoDatetime' => $core->input['leavetoDatetime'], 'destcity' => $core->input['destcity']);

        foreach($querystring as $key => $val) {
            $$key = $db->escape_string($val);
        }
        $leave_obj = new Leaves(array('lid' => $leaveid), false);
        $leave = $leave_obj->get();
        /* prevent adding new segment if to date  greater than original  leave end date */
        $leave[$sequence]['toDate'] = $leave['toDate'];
        // $leave[$sequence]['toDate'] = strtotime(date('Y-m-d 00:00:00', $leave[$sequence]['toDate']));

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
            $segment[$sequence]['toDate_formatted'] = date('d-m-Y', ($leave[$sequence]['toDate'])); // leave to date
            $segment[$sequence]['fromDate_output'] = date($core->settings['dateformat'], strtotime($core->input['toDate']));
            $segment[$sequence]['fromDate_formatted'] = $core->input['toDate'];
            $leave_purposes = array($leave_obj->get_purpose()->get()['ltpid'] => $leave_obj->get_purpose()->get()['name']);
            $segment_purposlist = parse_selectlist('segment['.$sequence.'][purpose]', 5, $leave_purposes, '');

            /* Popuplate basic information from the leave based on the lid passed via ajax */

            $segment['countryleave'] = $leave['coid'];
            //if($core->input['toDate']) > leavedate
// from date todate origin city loaded via js from the prevsegment
            $disabled = '';
            /* parse expenses --START */
//            $rowid = 1;
//            $expensestype_obj = new Travelmanager_Expenses_Types();
//            $segments_expenses_output = $expensestype_obj->parse_expensesfield($sequence, $rowid);
            /* parse expenses --END */

            eval("\$plantrip_createsegment= \"".$template->get('travelmanager_plantrip_createsegment')."\";");
            output($plantrip_createsegment);
        }
    }
    elseif($core->input['action'] == 'populatecontent') {
        $origincityid = $db->escape_string($core->input['origincity']);
        $destcityid = $db->escape_string($core->input['destcity']);
        $sequence = $db->escape_string($core->input['sequence']); /* get the  sequence to differentiate the content of each */

        $descity_obj = new Cities($destcityid);
        $destcity = $descity_obj->get();
        $destcity['country'] = $descity_obj->get_country()->get()['name'];
        $destcity['drivemode'] = 'transit';
        $destcity['departuretime'] = $db->escape_string(strtotime($core->input['departuretime']));

        $origincity_obj = new Cities($origincityid);
        $origintcity = $origincity_obj->get();
        $origintcity['country'] = $origincity_obj->get_country()->get()['name'];
        $transpmode_apimaplink = 'https://www.google.com/maps/dir/'.$origintcity['name'].',+'.$origintcity['country'].'/'.$destcity['name'].',+'.$destcity['country'].'/';

        /* Load proposed transproration */
        $transsegments_output = Cities::parse_transportations(array('origincity' => $origintcity, 'destcity' => $destcity, 'departuretime' => $destcity['departuretime']), $sequence);
        /* load approved hotels */
        $hotelssegments_output = $descity_obj->parse_approvedhotels($sequence, $destcity);
        /* parse expenses --START */
        $rowid = 1;
        $expensestype_obj = new Travelmanager_Expenses_Types();
        $segments_expenses_output = $expensestype_obj->parse_expensesfield('', $sequence, $rowid);
        /* parse expenses --END */


        /* Get unaprroved hotel of the destcity  for the purpose to acquire the tmhid */
        $unapproved_hotelobjs = $descity_obj->get_unapprovedhotels();
        $approved_hotels['tmhid'] = $unapproved_hotelobjs->tmhid;

        $mainaffobj = new Affiliates($core->user['mainaffiliate']);
        /* ffilter the currency  either get the curreny of the destination city or  the currencies of the country of the main affiliate */
        $currency['filter']['numCode'] = 'SELECT mainCurrency FROM countries where capitalCity='.$destcity['ciid'].' OR numCode IN(SELECT mainCurrency FROM countries where coid='.$mainaffobj->get_country()->coid.')';
        $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
        $curr_objs[840] = new Currencies(840);
        $currencies_list .= parse_selectlist('segment['.$sequence.'][tmhid]['.$approved_hotels['tmhid'].'][currency]', 4, $curr_objs, '840');


        eval("\$otherhotels_output = \"".$template->get('travelmanager_plantrip_segment_otherhotels')."\";");
        eval("\$plansegmentscontent_output = \"".$template->get('travelmanager_plantrip_segmentcontents')."\";");
        output($plansegmentscontent_output);
    }
    elseif($core->input['action'] == 'populatecityprofile') {
        $destcityid = $db->escape_string($core->input['destcity']);
        if(!empty($destcityid)) {
            $city_obj = new Cities($destcityid);
        }
        /* Parse city reviews content */
        if(is_object($city_obj)) {
            $cityprofile_output = $city_obj->parse_cityreviews();
            output($cityprofile_output);

            $citybriefings_output = $city_obj->parse_citybriefing();
        }
        else {
            $citybriefings_output = $lang->na;
        }
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
        $travelplan = new TravelManagerPlan();
        $travelplanexist = new TravelManagerPlan($core->input[planid]);
        if($travelplanexist->is_finalized()) {
            output_xml("<status>false</status><message>{$lang->finalizedplan}</message>");
            exit;
        }
        else {
            if(is_array($core->input['segment'])) {
                $travelplan->set($core->input);
                $travelplan->save();
                // $travelplan_obj->create($core->input['segment']);
            }
            switch($travelplan->get_errorcode()) {
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
            $hotel['reviewby'] = $lang->na;
            $hotel['review'] = $lang->noreviews;
        }
        eval("\$hotel_reviews = \"".$template->get('popup_hotel_review')."\";");
        output($hotel_reviews);
    }
    elseif($core->input['action'] == 'ajaxaddmore_expenses') {
        //  $expensestype_obj = Travelmanager_Expenses_Types::get_data('', array('returnarray' => false));

        $expensestypeobj = new Travelmanager_Expenses_Types();
        $rowid = $db->escape_string($core->input['value']) + 1;
        //   $segexpenses_ojbs = Travelmanager_Expenses::get_data(array('tmetid' => key($expensestype_obj)), array('returnarray' => true));
        $sequence = $db->escape_string($core->input['id']);

        $expenses = $expensestypeobj->parse_expensesfield($expensestype_obj, $sequence, $rowid, '', array('mode' => 'addrows'));
        //eval("\$expenses = \"".$template->get('travelmanager_expenses_types')."\";");
        echo $expenses;
    }
    elseif($core->input['action'] == 'get_addnewhotel') {
        $ciy_sequence = explode('_', $db->escape_string($core->input['id']));
        $sequence = $ciy_sequence[0];
        $destcityid = $ciy_sequence[1];
        $segdescity_obj = new Cities($destcityid);
        $segdescity_country = $segdescity_obj->get_country()->get_displayname();
        $segdescity_obj_coid = $segdescity_obj->get_country()->coid;
        $segmentobj_destcityname = $segdescity_obj->get()['name'];
        eval("\$addhotel= \"".$template->get('popup_addhotel')."\";");
        output($addhotel);
    }
    elseif($core->input['action'] == 'do_add_otherhotel') {
        $hotelobj = new TravelManagerHotels();
        $hotelobj->set($core->input['otherhotel']);
        $hotelobj->save();
        switch($hotelobj->get_errorcode()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                break;
            case 2:
                output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
                exit;
        }
    }
    elseif($core->input['action'] == 'deletesegment') {
        $segmentid = 105; // dynamic later
        $plan_classes = array('TravelManagerPlanSegments', 'TravelManagerPlanTransps', 'TravelManagerPlanaccomodations', 'Travelmanager_Expenses', 'TravelManagerCityReviews');
        if(is_array($plan_classes)) {
            foreach($plan_classes as $object) {
                $data = $object::get_data('tmpsid='.$segmentid.'', array('returnarray' => true));
                if(is_array($data)) {
                    foreach($data as $object_todelete) {
                        $object_todelete->delete();
                    }
                }
            }
        }
    }
}
?>