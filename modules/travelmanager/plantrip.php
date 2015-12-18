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
    $leaveid = intval($core->input['lid']);
    $sequence = 1;

    if(isset($core->input['id']) && !empty($core->input['id'])) {
        $planid = $db->escape_string($core->input['id']);
        $plan_obj = new TravelManagerPlan($planid);
        if(!is_object($plan_obj)) {
            redirect('index.php?module=travelmanager/listplans');
        }
        if($plan_obj->is_finalized()) {
            redirect('index.php?module=travelmanager/viewplan&id='.$planid);
        }
        $plantrip = $plan_obj->parse_existingsegments();

        output($plantrip);
    }
    //$tools_addnewtab = '<a id="createtab" class="showpopup" href="#" title="'.$lang->addsegment.'"><img border="0" alt="Create New Tab" src="images/addnew.png"></img> </a>';
    else {
        if(empty($core->input['lid'])) {
            redirect('index.php?module=travelmanager/listplans');
        }
        $checked['othertranspssection'] = 'checked="checked"';
        $display['othertranspssection'] = "display:block'";
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
            //$segment['countryleave'] = $leave['coid'];
            $segment[$sequence]['fromDate_output'] = date($core->settings['dateformat'], $leave['fromDate']);
            $segment[$sequence]['fromDate_formatted'] = date('d-m-Y', $leave['fromDate']);
            $segment[$sequence]['toDate_output'] = date($core->settings['dateformat'], $leave['toDate']);
            $segment[$sequence]['toDate_formatted'] = date('d-m-Y', $leave['toDate']);
            $leave[$sequence]['toDate'] = $leave['toDate'];
            $fromDate = new DateTime($segment[$sequence]['fromDate_output']);
            $todate = new DateTime($segment[$sequence]['toDate_output']);
            $segment[$sequence]['numberdays'] = $fromDate->diff($todate)->format(' %d days');
            $origincity_obj = new Cities($leave['sourceCity']);
            $segment[$sequence]['origincity'] = $origincity_obj->get();
            $segment[$sequence]['origincity']['name'] = $segment[$sequence]['origincity'] ['name'];
            $segment[$sequence]['origincity']['ciid'] = $segment[$sequence]['origincity']['ciid'];
            $descity_obj = new Cities($leave['destinationCity']);
            $destcityid = $leave['destinationCity'];
            $segment[$sequence]['destinationcity'] = $descity_obj->get();                 /* Will get the capital city of the visited country of leave */
            $segment[$sequence]['destinationcity']['name'] = $segment[$sequence]['destinationcity']['name'];  /* Will get the capital city of the visited country of leave */
            $segment[$sequence]['destinationcity']['ciid'] = $segment[$sequence]['destinationcity']['ciid'];  /* Will get the capital city of the visited country of leave */
            $segment[$sequence][reason_output] = $leave['reason'];
            $affiliates = Affiliates::get_affiliates(array('isActive' => '1'));
            $affilate_list = parse_selectlist('segment['.$sequence.'][affid]', 1, $affiliates, $segmentobj->affid, '', '', array('blankstart' => true));
            $disabled = 'disabled="true"';
//$leave_destcity
            $otherhotel_checksum = generate_checksum('accomodation');
//            if(!empty($descity_obj->get_id())) {
//                $cityprofile_output = $descity_obj->parse_cityreviews();
//                $citybriefings_output = $descity_obj->parse_citybriefing();
//            }
            $display_external = $display_internal = 'style="display:none"';
            $leave_purposes = LeaveTypesPurposes::get_data(null);
            //$leave_purposes = array($leave_obj->get_purpose()->get()['ltpid'] => $leave_obj->get_purpose()->get()['name']);
            if(is_array($leave_purposes)) {
                foreach($leave_purposes as $leave_purpose) {
                    if($leave_purpose->category == 'internal') {
                        $interperp[$leave_purpose->ltpid] = $leave_purpose->title;
                    }
                    elseif($leave_purpose->category == 'external') {
                        $extpurps[$leave_purpose->ltpid] = $leave_purpose->title;
                    }
                }
                if(is_array($extpurps)) {
                    $extpurposes_checks = parse_checkboxes('segment['.$sequence.'][purpose]', $extpurps, '', '', 'external purposes', '<br>', 'purposes_checks_external_'.$sequence.'', 1);
                }
                if(is_array($interperp)) {
                    $internalpurposes_checks = parse_checkboxes('segment['.$sequence.'][purpose]', $interperp, '', '', 'internal purposes', '<br>', 'purposes_checks_internal_'.$sequence.'', 1);
                }
            }
            $affiliates = Affiliates::get_affiliates(array('isActive' => '1'));
            $afent_checksum = generate_checksum();
            $affilate_list = parse_selectlist('segment['.$sequence.'][assign][affid]['.$afent_checksum.']', '1', $affiliates, '', '', '', array('blankstart' => true));
            $affrowid = $entrowid = 0;
            eval("\$affiliates_output = \"".$template->get('travelmanager_plantrip_createsegment_affiliates')."\";");
            $afent_checksum = generate_checksum();
            eval("\$entities = \"".$template->get('travelmanager_plantrip_createsegment_entities')."\";");
            //   $origincity_obj = $leave_obj->get_sourcecity(false);
            $origintcity = $origincity_obj->get();
            $origintcity['country'] = $origincity_obj->get_country()->get()['name'];

            // $descity_obj = $leave_obj->get_destinationcity($false);
            $destcounrty_obj = $descity_obj->get_country();
            $destcity = $descity_obj->get();
            $destcity['country'] = $destcounrty_obj->get()['name'];
            $transp_requirements['drivemode'] = 'transit';
            $transp_requirements['departuretime'] = $db->escape_string(($leave['fromDate']));
            $transp_requirements['arrivaltime'] = $db->escape_string(($leave['toDate']));
            $transp = new TravelManagerPlanTransps();
            $transsegments_output = Cities::parse_transportations($transp, array('origincity' => $origintcity, 'destcity' => $destcity, 'transprequirements' => $transp_requirements, 'excludesuggestions' => 1), $sequence);
            $segmentobj = new TravelManagerPlanSegments();
            $approvedhotels = $descity_obj->get_approvedhotels();
            if(empty($approvedhotels)) {
                $approvedhotels = array();
            }
            $leavedays = abs($leave_obj->toDate - $leave_obj->fromDate);
            $leavedays = floor($leavedays / (60 * 60 * 24));
            $hotelssegments_output = $segmentobj->parse_hotels($sequence, $approvedhotels, $leavedays);
            if(is_object($destcounrty_obj)) {
                $otherapprovedhotels = TravelManagerHotels::get_data('country='.$destcounrty_obj->coid.' AND city != '.$descity_obj->ciid.' AND isApproved=1', array('returnarray' => true));
            }
            if(is_array($otherapprovedhotels)) {
                $hotelssegments_output.='<br /><a nohref="nohref" style="cursor:pointer;" id="countryhotels_'.$sequence.'_check"><div style="display:inline-block"><h4>Lookup Hotels in the same country <img src="'.$core->settings['rootdir'].'/images/right_arrow.gif" alt="Other Approved Hotels"></h4></div></a>';
                $hotelssegments_output.='<div id=countryhotels_'.$sequence.'_view style="display:none">';
                $hotelssegments_output.=$segmentobj->parse_hotels($sequence, $otherapprovedhotels, $leavedays);
                $hotelssegments_output.='</div>';
            }
            // $transpmode_apimaplink = 'https://www.google.com/maps/dir/'.$origintcity['name'].',+'.$origintcity['country'].'/'.$destcity['name'].',+'.$destcity['country'].'/';
            //parse Finances---Start
            $frowid = 1;
            $finance_obj = new TravelManagerPlanFinance();
            //segment[{$sequence}][tmpfid][$frowid][amount]
            $mainaffobj = new Affiliates($core->user['mainaffiliate']);
            $currencies_f[] = $descity_obj->get_country()->get_maincurrency();
            $currencies_f[] = $mainaffobj->get_country()->get_maincurrency();
            $currencies_f[] = new Currencies(840, true);
            $currencies_f[] = new Currencies(978, true);
            foreach($currencies_f as $currency) {
                if(is_object($currency)) {
                    $val_currencies[] = $currency->validate_currency();
                }
            }
            $currencies_f = array_filter(array_unique($val_currencies));
            $currencies_listf = parse_selectlist('segment['.$sequence.'][tmpfid]['.$frowid.'][currency]', 4, $currencies_f, '840', '', '', array("id" => 'segment_'.$sequence.'_tmpfid_'.$frowid.'_currency'));
            $segments_financess_output.=$currencies_listf;
            $finance_checksum = generate_checksum('finance');
            eval("\$finance_output = \"".$template->get('travelmanager_plantrip_segmentfinance')."\";");
            //parse Finances---End
            /* parse expenses --START */
            $rowid = 1;
            $expensestype[$sequence][$rowid]['display'] = "display:none;";
            $expensestype_obj = new Travelmanager_Expenses_Types();
            $segments_expenses_output = $expensestype_obj->parse_expensesfield('', $sequence, $rowid, $expensestype, array('destcity' => $descity_obj));

            /* parse expenses --END */

            /* Get unaprroved hotel of the destcity  for the purpose to acquire the tmhid */
            $unapproved_hotelobjs = $descity_obj->get_unapprovedhotels();
            //  $approved_hotels['tmhid'] = $unapproved_hotelobjs->tmhid;

            $mainaffobj = new Affiliates($core->user['mainaffiliate']);
            $destcity_obj = new Cities($destcity['ciid']);
            $dest_country = $destcity_obj->get_country();
            $destcountry_id = $dest_country->coid;
            $currencies[] = $dest_country->get_maincurrency();
            $currencies[] = $mainaffobj->get_country()->get_maincurrency();
            $currencies[] = new Currencies(840, true);
            $currencies[] = new Currencies(978, true);
            foreach($currencies as $currency) {
                if(is_object($currency)) {
                    $val_currencies[] = $currency->validate_currency();
                }
            }
            $currencies = array_filter(array_unique($val_currencies));
            $currencies_list = parse_selectlist('segment['.$sequence.'][tmhid]['.$otherhotel_checksum.'][currency]', 4, $currencies, '840', '', '', array('id' => 'currency_'.$sequence.'_'.$otherhotel_checksum.'_list'));
            $otherhotel['displaystatus'] = "display:none;";


            $paidby_entities = array(
                    'myaffiliate' => $lang->myaffiliate,
                    'supplier' => $lang->supplier,
                    'client' => $lang->client,
                    'myself' => $lang->myself,
                    'anotheraff' => $lang->anotheraff
            );
            $paidby_onchangeactions = 'if($(this).find(":selected").val()=="anotheraff"){$("#"+$(this).find(":selected").val()+"_otheraccomodations_'.$sequence.'_'.$otherhotel_checksum.'").effect("highlight",{ color: "#D6EAAC"}, 1500).find("input").first().focus().val("");}else{$("#anotheraff_otheraccomodations_'.$sequence.'_'.$otherhotel_checksum.'").hide();}';

            $paidbyoptions = parse_selectlist('segment['.$sequence.'][tmhid]['.$otherhotel_checksum.'][entites]', 5, $paidby_entities, $selectedhotel->paidBy, 0, $paidby_onchangeactions);
            $hideforfirstime = 'style="display:none"';
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

        $segmentstabs = '<li><a href="#segmentstabs-1">Segment 1</a></li>';
        // $identifier = substr(md5(uniqid(microtime())), 1, 10);
        eval("\$plantript_segmentstabs= \"".$template->get('travelmanager_plantrip_segmentstabs')."\";");

        $helptour = new HelpTour();
        $helptour->set_id('travelmanager_helptour');
        $helptour->set_cookiename('travelmanager_helptour');
        $plan = new TravelManagerPlan();
        $touritems = $plan->get_helptouritems();
        $helptour->set_items($touritems);
        $helptour = $helptour->parse();
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
        $leave[$sequence]['toDate'] = strtotime(date('Y-m-d 23:59:59', $leave[$sequence]['toDate']));
        if(strtotime($core->input['toDate']) >= $leave[$sequence]['toDate'] || $core->input['fromDate'] == 'undefined') {
            echo'<div style = "color:red;">'.$lang->dateexceeded.'</div>';
            exit;
        }
//        if(strtotime($core->input['toDate']) == strtotime(date('Y-m-d', $leave['toDate'])) && strtotime($core->input['fromDate']) == strtotime(date('Y-m-d', $leave['toDate']))) {
//            echo'<div style = "color:red;">'.$lang->dateexceeded.'</div>';
//            exit;
//        }
        /* get prev city name */
        $city_obj = new Cities($core->input['destcity']);
        $descitydata = $city_obj->get();
        /* origin city of the new  segment is destination of previous segment */
        $segment[$sequence][reason_output] = $segment[$sequence]['reason'];
        $segment[$sequence]['origincity']['name'] = $descitydata['name'];
        $segment[$sequence]['origincity']['ciid'] = $descitydata['ciid'];
        /* Overwrite from date of next segment with  TOdate of prev segment */
        $segment[$sequence]['toDate_output'] = date($core->settings['dateformat'], ($leave[$sequence]['toDate']));
        $segment[$sequence]['toDate_formatted'] = date('d-m-Y', ($leave[$sequence]['toDate'])); // leave to date
        $segment[$sequence]['fromDate_output'] = date($core->settings['dateformat'], strtotime($core->input['toDate']));
        $segment[$sequence]['fromDate_formatted'] = $core->input['toDate'];
        $leave_purposes = LeaveTypesPurposes::get_data(null);
        $display_external = $display_internal = 'style = "display:none"';
        if(is_array($leave_purposes)) {
            foreach($leave_purposes as $leave_purpose) {
                if($leave_purpose->category == 'internal') {
                    $interperp[$leave_purpose->ltpid] = $leave_purpose->title;
                }
                elseif($leave_purpose->category == 'external') {
                    $extpurps[$leave_purpose->ltpid] = $leave_purpose->title;
                }
            }
            if(is_array($extpurps)) {
                $extpurposes_checks = parse_checkboxes('segment['.$sequence.'][purpose]', $extpurps, $selectedpurpose, '', 'external purposes', '<br>', 'purposes_checks_external_'.$sequence.'', 1);
            }
            if(is_array($interperp)) {
                $internalpurposes_checks = parse_checkboxes('segment['.$sequence.'][purpose]', $interperp, $selectedpurpose, '', 'internal purposes', '<br>', 'purposes_checks_internal_'.$sequence.'', 1);
            }
        }
        $affiliates = Affiliates::get_affiliates(array('isActive' => '1'));
        $afent_checksum = generate_checksum();
        $affilate_list = parse_selectlist('segment['.$sequence.'][assign][affid]['.$afent_checksum.']', '1', $affiliates, '', '', '', array('blankstart' => true));
        $affrowid = $entrowid = 0;
        eval("\$affiliates_output = \"".$template->get('travelmanager_plantrip_createsegment_affiliates')."\";");
        $afent_checksum = generate_checksum();
        eval("\$entities = \"".$template->get('travelmanager_plantrip_createsegment_entities')."\";");

        /* Popuplate basic information from the leave based on the lid passed via ajax */

        //$segment['countryleave'] = $leave['coid'];
        //if($core->input['toDate']) > leavedate
// from date todate origin city loaded via js from the prevsegment
        $disabled = '';
        /* parse expenses --START */
//            $rowid = 1;
//            $expensestype_obj = new Travelmanager_Expenses_Types();
//            $segments_expenses_output = $expensestype_obj->parse_expensesfield($sequence, $rowid);
        /* parse expenses --END */
//        if($sequence == 2) {
//            $helptour = '';
//            $seg2helptour = new HelpTour();
//            $seg2helptour->set_id('travelmanagersegment2_helptour');
//            $seg2helptour->set_cookiename('travelmanagersegment2_helptour');
//            $plan = new TravelManagerPlan();
//            $seg2helptouritems = $plan->get_secondseghelptouritems();
//            $seg2helptour->set_items($seg2helptouritems);
//            $seg2helptour = $seg2helptour->parse();
//        }
        eval("\$plantrip_createsegment= \"".$template->get('travelmanager_plantrip_createsegment')."\";");
        output($plantrip_createsegment);
    }
    elseif($core->input['action'] == 'populatecontent') {

        $display['othertranspssection'] = "display:block'";
        $checked['othertranspssection'] = 'checked="checked"';


        $origincityid = $db->escape_string($core->input['origincity']);
        $destcityid = $db->escape_string($core->input['destcity']);
        $sequence = $db->escape_string($core->input['sequence']); /* get the  sequence to differentiate the content of each */
        $otherhotel_checksum = generate_checksum('accomodation');
        $transp_dispnone = 'style = "display:none"';
        $descity_obj = new Cities($destcityid);
        $destcity = $descity_obj->get();
        $dest_country = $descity_obj->get_country();
        $destcountry_id = $dest_country->coid;
        $destcity['country'] = $dest_country->get()['name'];
        if(isset($core->input['parsetransp']) && $core->input['parsetransp'] == 1) {
            $transp_dispnone = '';
            $transp_requirements['drivemode'] = 'transit';
            $transp_requirements['departuretime'] = $db->escape_string(strtotime($core->input['departuretime']));
            $transp_requirements['arrivaltime'] = $db->escape_string(strtotime($core->input['arrivaltime']));
            $origincity_obj = new Cities($origincityid);
            $origintcity = $origincity_obj->get();
            $origintcity['country'] = $origincity_obj->get_country()->get()['name'];
            $transpmode_apimaplink = 'https://www.google.com/maps/dir/'.$origintcity['name'].',+'.$origintcity['country'].'/'.$destcity['name'].',+'.$destcity['country'].'/';
            /* Load proposed transproration */
            $transp = new TravelManagerPlanTransps();
            $transp_requirements['oneway'] = 1;
            if(isset($core->input['transp']) && $core->input['transp'] == 1) {
                $transp_requirements['oneway'] = 0;
            }
            $transsegments_output = Cities::parse_transportations($transp, array('origincity' => $origintcity, 'destcity' => $destcity, 'transprequirements' => $transp_requirements, 'referrer' => $core->input['referrer'], 'excludesuggestions' => 1), $sequence);
        }
        /* load approved hotels */
        $leavedays = abs(strtotime($core->input['arrivaltime']) - strtotime($core->input['departuretime']));
        $leavedays = floor($leavedays / (60 * 60 * 24));
        // $segmentobj = new TravelManagerPlanSegments();
//        $segmentobj = TravelManagerPlanSegments::get_data(array('originCity' => $origincityid, 'destinationCity' => $destcityid));
//        if(is_object($segmentobj)) {
//            $approvedhotels = $segmentobj->get_destinationcity()->get_approvedhotels();
//            if(is_array($approvedhotels)) {
//                $hotelssegments_output = $segmentobj->parse_hotels($sequence, $approvedhotels, $leavedays);
//            }
//            $destcounrty_obj = $segmentobj->get_destinationcity()->get_country();
//            if(is_object($destcounrty_obj)) {
//                $otherapprovedhotels = TravelManagerHotels::get_data('country='.$destcounrty_obj->coid.' AND city != '.$segmentobj->get_destinationcity()->ciid.' AND isApproved=1', array('returnarray' => true));
//            }
//            if(is_array($otherapprovedhotels)) {
//                $hotelssegments_output.='<br /><a nohref="nohref" style="cursor:pointer;" id="countryhotels_'.$sequence.'_check"><button type="button" class="button">Lookup Hotels In The Same Country</button></a>';
//                $hotelssegments_output.='<div id=countryhotels_'.$sequence.'_view style="display:none">';
//                $hotelssegments_output.=$segmentobj->parse_hotels($sequence, $otherapprovedhotels, $leavedays);
//                $hotelssegments_output.='</div>';
//            }
//        }
//        else {
        $segmentobj = new TravelManagerPlanSegments();
        $approvedhotels = $descity_obj->get_approvedhotels();
        if(empty($approvedhotels)) {
            $approvedhotels = array();
        }
        $segmentobj->destinationCity = $destcityid;
        $hotelssegments_output = $segmentobj->parse_hotels($sequence, $approvedhotels, $leavedays);
        $destcounrty_obj = $descity_obj->get_country();
        if(is_object($destcounrty_obj)) {
            $otherapprovedhotels = TravelManagerHotels::get_data('country='.$destcounrty_obj->coid.' AND city != '.$descity_obj->ciid.' AND isApproved', array('returnarray' => true));
        }
        if(is_array($otherapprovedhotels)) {
            $hotelssegments_output.='<br /><a nohref="nohref" style="cursor:pointer;" id="countryhotels_'.$sequence.'_check"><button type="button" class="button">Lookup Hotels In The Same Country</button></a>';
            $hotelssegments_output.='<div id=countryhotels_'.$sequence.'_view style="display:none">';
            $hotelssegments_output.=$segmentobj->parse_hotels($sequence, $otherapprovedhotels, $leavedays);
            $hotelssegments_output.='</div>';
        }
        // }



        /* parse expenses - START */
        $rowid = 1;
        $expensestype_obj = new Travelmanager_Expenses_Types();
        $segments_expenses_output = $expensestype_obj->parse_expensesfield('', $sequence, $rowid, '', array('destcity' => $descity_obj));
        /* parse expenses - END */

        /* Get unaprroved hotel of the destcity  for the purpose to acquire the tmhid */
        $unapproved_hotelobjs = $descity_obj->get_unapprovedhotels();
        $approved_hotels['tmhid'] = $unapproved_hotelobjs->tmhid;


        /* get currency for the country of the destcity */

        /* append default usd currency to the object */

        /* append currency of the country of the main user affiliate to the object */

        $mainaffobj = new Affiliates($core->user['mainaffiliate']);
        $destcity_obj = new Cities($destcity['ciid']);
        $currencies[] = $destcity_obj->get_country()->get_maincurrency();
        $currencies[] = $mainaffobj->get_country()->get_maincurrency();
        $currencies[] = new Currencies(840, true);
        $currencies[] = new Currencies(978, true);
        foreach($currencies as $currency) {
            if(is_object($currency)) {
                $val_currencies[] = $currency->validate_currency();
            }
        }
        $currencies = array_filter(array_unique($val_currencies));
        $currencies_list = parse_selectlist('segment['.$sequence.'][tmhid]['.$otherhotel_checksum.'][currency]', 4, $currencies, '840');
        $otherhotel['displaystatus'] = "display:none;";
        $paidby_onchangeactions = 'if($(this).find(":selected").val()=="anotheraff"){$("#"+$(this).find(":selected").val()+"_otheraccomodations_'.$sequence.'_'.$otherhotel_checksum.'").effect("highlight",{ color: "#D6EAAC"}, 1500).find("input").first().focus().val("");}else{$("#anotheraff_otheraccomodations_'.$sequence.'_'.$otherhotel_checksum.'").hide();}';
        $paidby_entities = array(
                'myaffiliate' => $lang->myaffiliate,
                'supplier' => $lang->supplier,
                'client' => $lang->client,
                'myself' => $lang->myself,
                'anotheraff' => $lang->anotheraff
        );
        $paidbyoptions = parse_selectlist('segment['.$sequence.'][tmhid]['.$otherhotel_checksum.'][entites]', 5, $paidby_entities, $selectedhotel->paidBy, 0, $paidby_onchangeactions);
        $currencies_f = $currencies;
        $frowid = 1;
        $currencies_listf = parse_selectlist('segment['.$sequence.'][tmpfid]['.$frowid.'][currency]', 4, $currencies_f, $finance->currency, '', '', array("id" => 'segment_'.$sequence.'_tmpfid_'.$frowid.'_currency'));
        $finance_checksum = generate_checksum('finance');
        eval("\$finance_output = \"".$template->get('travelmanager_plantrip_segmentfinance')."\";");
        $hideforfirstime = 'style="display:none"';
        eval("\$otherhotels_output = \"".$template->get('travelmanager_plantrip_segment_otherhotels')."\";");
        eval("\$plansegmentscontent_output = \"".$template->get('travelmanager_plantrip_segmentcontents')."\";");
        output($plansegmentscontent_output);
    }
    elseif($core->input ['action'] == 'populatecityprofile') {
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
    elseif($core->input ['action'] == 'parsedetailstransp') {
        $catid = $db->escape_string($core->input['catid']);
        $sequence = $db->escape_string($core->input['sequence']);
        $categoryid = $db->escape_string($core->input['categoryid']);
        $transp_category_fields = TravelManagerPlan::parse_transportaionfields($catid, $sequence);
        eval("\$transsegments_output = \"".$template->get('travelmanager_plantrip_segment_transportation')."\";");

//output($transsegments_output);
    }
    elseif($core->input ['action'] == 'do_perform_plantrip') {
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
            }
            switch($travelplan->get_errorcode()) {
                case 0:
                    if(isset($core->input['finalizeplan']) && $core->input['finalizeplan'] == 1) {
                        $url = 'index.php?module=travelmanager/viewplan&referrer=plantrip&id=';
                        header('Content-type: text/xml+javascript');
                        output_xml('<status>true</status><message><![CDATA[<script>goToURL(\''.$url.$travelplan->tmpid.'\');</script>]]></message>');
                        exit;
                    }
                    else {
                        if(is_array($core->input['segment'][$core->input['sequence']])) {
                            $sequence = $core->input['sequence'];
                            $segment = $core->input['segment'][$core->input['sequence']];
                            if(is_array($segment['savesection'])) {
                                foreach($segment['savesection'] as $section => $val) {
                                    if($val == 1) {
                                        $nextsection = 'section'.(intval(substr($section, -1)) + 1);
                                        $shownextsection = '<script> $(function(){$("div[id=\'savingsection_'.$sequence.'_'.$nextsection.'\']").show();});</script>';
                                    }
                                }
                            }
                        }
                        output_xml("<status>true</status><message>{$lang->successfullysaved}<![CDATA[{$shownextsection}]]></message>");
                        exit;
                    }
                    output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                    break;
                case 1:
                    output_xml("<status>false</status><message>{$lang->planexist}</message>");
                    exit;
                case 2:
                    $error_output = $errorhandler->get_errors_inline();
                    output_xml("<status>false</status><message>{$lang->fillrequiredfields}<![CDATA[<br/>{$error_output}]]></message>");
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
                case 8:
                    if(isset($core->input['finalizeplan']) && $core->input['finalizeplan'] == 1) {
                        output_xml("<status>false</status><message> {$lang->erroritinerarydate} </message>");
                        exit;
                    }
                    output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                    break;

                case 9:
                    $error_output = $errorhandler->get_errors_inline();
                    output_xml("<status>false</status><message><![CDATA[{$error_output}]]></message>");
                    break;
            }
        }
    }
    elseif($core->input ['action'] == 'get_hotelreview') {
        $hotelid = $db->escape_string($core->input['id']);
        $hotel_obj = new TravelManagerHotels($hotelid);
        $hotel_review = $hotel_obj->get_review();
        if(is_array($hotel_review)) {
            $hotel['reviewdetails'] = $hotel_review->get();
            $hotel['review'] = $hotel['reviewdetails']['review'];
            $hotel['reviewby'] = $lang->reviewdby.': '.$hotel_review->get_createdBy()->get()['displayName'];
        }
        else {
            $hotel['reviewby'] = '';
            $hotel['review'] = $lang->noreviews;
        }
        eval("\$hotel_reviews = \"".$template->get('popup_hotel_review')."\";");
        output($hotel_reviews);
    }
    elseif($core->input ['action'] == 'ajaxaddmore_expenses') {
        //  $expensestype_obj = Travelmanager_Expenses_Types::get_data('', array('returnarray' => false));
        $expensestypeobj = new Travelmanager_Expenses_Types();
        $rowid = $db->escape_string($core->input ['value']) + 1;
        $destcity = new Cities($core->input['ajaxaddmoredata']['destcity']);
        //   $segexpenses_ojbs = Travelmanager_Expenses::get_data(array('tmetid' => key($expensestype_obj)), array('returnarray' => true));
        $sequence = $db->escape_string($core->input['id']);

        $expenses = $expensestypeobj->parse_expensesfield($expensestype_obj, $sequence, $rowid, '', array('mode' => 'addrows', 'destcity' => $destcity));
        //eval("\$expenses = \"".$template->get('travelmanager_expenses_types')."\";");
        echo $expenses;
    }
    elseif($core->input['action'] == 'get_addnewhotel') {
        $ciy_sequence = explode('_', $db->escape_string($core->input['id']));
        $sequence = $ciy_sequence[0];
        $destcityid = $core->input['destcity'];
        $segdescity_obj = new Cities($destcityid); // fix isuue in getting ciid
        $descountry = $segdescity_obj->get_country();
        $segdescity_country = $descountry->get_displayname();
        $segdescity_obj_coid = $descountry->coid;
        $segmentobj_destcityname = $segdescity_obj->get()['name'];
        $ratings = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5);
        $ratingselectlist = parse_selectlist('otherhotel[stars]', '', $ratings, '', '', '', array('blankstart' => true));
        $country = new Countries(1);
        $countriescodes = $country->get_phonecodes();
        $countriescodes_list = parse_selectlist('telephone_intcode', $tabindex, $countriescodes, $descountry->phoneCode, '', '', array('id' => 'telephone_intcode', 'width' => '150px'));
        eval("\$addhotel= \"".$template->get('popup_addhotel')."\";");
        output($addhotel);
    }
    elseif($core->input ['action'] == 'do_add_otherhotel') {
        $sequence = $core->input['sequence'];
        $core->input['otherhotel']['telephone_intcode'] = $core->input['telephone_intcode'];
        //  $core->input['otherhotel']['telephone_areacode'] = $core->input['telephone_areacode'];
        $core->input['otherhotel']['telephone_number'] = $core->input['telephone_number'];
        if(!isset($core->input['otherhotel']['isContracted']) || empty($core->input['otherhotel']['isContracted'])) {
            $core->input['otherhotel']['isContracted'] = 0;
        }
        $hotelobj = new TravelManagerHotels();
        $hotelobj->set($core->input['otherhotel']);
        $hotelobj->save();
        switch($hotelobj->get_errorcode()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successfullysaved}"
                        ."<![CDATA[<script>$('input[id=\"hotels_".$sequence."_cache_hotel_autocomplete\"]').val('".$hotelobj->name."');"
                        ."$('input[id=\"hotels_".$sequence."_cache_hotel_id\"]').val('".$hotelobj->tmhid."');</script>]]>"
                        ."</message>");
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
                exit;
            case 2:
                output_xml("<status>false</status><message>Error Saving</message>");
                exit;
        }
    }
    elseif($core->input ['action'] == 'deletesegment') {
        $segmentid = $db->escape_string($core->input['segmentid']);
        if(!empty($segmentid)) {
            $plan_classes = array('TravelManagerPlanSegments', 'TravelManagerPlanTransps', 'TravelManagerPlanaccomodations', 'Travelmanager_Expenses', 'TravelManagerCityReviews', 'TravelManagerPlanAffient', 'TravelManagerPlanSegPurposes', 'TravelManagerPlanFinance');
            if(is_array($plan_classes)) {
                foreach($plan_classes as $object) {
                    $data = $object::get_data('tmpsid = '.$segmentid.'', array('returnarray' => true));
                    if(is_array($data)) {
                        foreach($data as $object_todelete) {
                            $object_todelete->delete();
                        }
                    }
                }
            }
        }
    }
    else if($core->input ['action'] == 'ajaxaddmore_othertranspcat') {
        $rowid = $db->escape_string($core->input ['value']) + 1;
        $destcity = ($core->input['ajaxaddmoredata']['destcity']);
        $transp_requirements['drivemode'] = 'transit';
        $transp_requirements['departuretime'] = '';
        $sequence = $db->escape_string($core->input['id']);
        $transp = new TravelManagerPlanTransps();
        /* Note need to pass object for origin and destination city */
        $transsegments_output = Cities::parse_transportations($transp, array('origincity' => $origintcity, 'destcity' => $destcity, 'transprequirements' => $transp_requirements), $sequence, 'addmore');
        echo $transsegments_output;
    }
    else if($core->input ['action'] == 'refreshtransp') {
        $destcityid = $db->escape_string($core->input['destcity']);
        $origincityid = $db->escape_string($core->input['origincity']);
        $descity_obj = new Cities($destcityid);
        $destcity = $descity_obj->get();
        $destcity['country'] = $descity_obj->get_country()->get()['name'];
        $transp_requirements['drivemode'] = 'transit';
        $transp_requirements['referrer'] = 'todate';
        $transp_requirements['departuretime'] = $db->escape_string(strtotime($core->input['departuretime']));
        $transp_requirements['arrivaltime'] = $db->escape_string(strtotime($core->input['arrivaltime']));
        $origincity_obj = new Cities($origincityid);
        $origintcity = $origincity_obj->get();
        $origintcity['country'] = $origincity_obj->get_country()->get()['name'];


        $transp_requirements['oneway'] = 1;
        if(isset($core->input['transp']) && $core->input['transp'] == 1) {
            $transp_requirements['oneway'] = 0;
        }

        /* Load proposed transproration */
        $transp = new TravelManagerPlanTransps();
        $transsegments_output = Cities::parse_transportations($transp, array('origincity' => $origintcity, 'destcity' => $destcity, 'transprequirements' => $transp_requirements), $core->input['sequence']);
        output($transsegments_output);
    }
    else if($core->input['action'] == 'ajaxaddmore_finances') {
        $frowid = $db->escape_string($core->input['value']) + 1;
        $sequence = $db->escape_string($core->input['id']);
        $destcity = new Cities($core->input['ajaxaddmoredata']['destcity']);
        $mainaffobj = new Affiliates($core->user['mainaffiliate']);
        $currencies_f[] = $destcity->get_country()->get_maincurrency();
        $currencies_f[] = $mainaffobj->get_country()->get_maincurrency();
        $currencies_f[] = new Currencies(840, true);
        $currencies_f[] = new Currencies(978, true);
        foreach($currencies_f as $currency) {
            if(is_object($currency)) {
                $val_currencies[] = $currency->validate_currency();
            }
        }
        $currencies_f = array_filter(array_unique($val_currencies));
        $currencies_listf = parse_selectlist('segment['.$sequence.'][tmpfid]['.$frowid.'][currency]', 4, $currencies_f, 840, '', '', array("id" => 'segment_'.$sequence.'_tmpfid_'.$frowid.'_currency'));
        $segments_financess_output.=$currencies_listf;
        $finance_checksum = generate_checksum('finance');
        eval("\$finance_output = \"".$template->get('travelmanager_plantrip_segmentfinance')."\";");
        echo $finance_output;
    }
    elseif($core->input['action'] == 'ajaxaddmore_affiliate') {
        $affrowid = $db->escape_string($core->input['value']) + 1;
        $afent_checksum = generate_checksum();
        $sequence = $db->escape_string($core->input['id']);
        $affiliates = Affiliates::get_affiliates(array('isActive' => '1'));
        $affilate_list = parse_selectlist('segment['.$sequence.'][assign][affid]['.$afent_checksum.']', '1', $affiliates, '', '', '', array('blankstart' => true));
        eval("\$affiliates_output .= \"".$template->get('travelmanager_plantrip_createsegment_affiliates')."\";");
        echo $affiliates_output;
    }
    elseif($core->input['action'] == 'ajaxaddmore_entities') {
        $entrowid = $db->escape_string($core->input['value']) + 1;
        $ltpid = $core->input['ajaxaddmoredata']['ltpid'];
        $afent_checksum = generate_checksum();
        $sequence = $db->escape_string($core->input['id']);

        $leavetypepurpose = new LeaveTypesPurposes($ltpid);
//        if($leavetypepurpose->get_displayname() == 'Event & Fair') {
//            $calevents = Events::get_data(array('isPublic' => 1), array('returnarray' => true));
//            $entities = '<tr id = "'.$entrowid.'"><td '.$display_external.' data-purposes = "external_'.$sequence.'">Select Event</td><td '.$display_external.' data-purposes = "external_'.$sequence.'">';
//            $entities .= parse_selectlist("test", $tabindex, $calevents, $selected_options, '', '', array('width' => '200px'));
//            $entities .='</td></tr>';
//        }
//        else {
        eval("\$entities = \"".$template->get('travelmanager_plantrip_createsegment_entities')."\";");
        // }
        echo $entities;
    }
    elseif($core->input['action'] == 'checkpricevsavgprice') {
        $warnings['hotelprice'] = '';
        $data['avgprice'] = $core->input['avgprice'];
        $data['pricepernight'] = $core->input['pricepernight'];
        $data['currency'] = $core->input['currency'];
        $tmhotel = new TravelManagerHotels();
        $warnings['hotelprice'] = $tmhotel->get_warning($data);
        echo $warnings['hotelprice'];
    }
    elseif($core->input['action'] == 'validatefandbexpenses') {
        $tmexpensetype = new TravelManager_Expenses_Types($core->input['expensetype']);
        $warnings['foodandbeverage'] = '';
        if(is_object($tmexpensetype) && $tmexpensetype->title == 'Food & Beverage') {
            $data['numnights'] = $core->input['numnights'];
            $data['amount'] = $core->input['amount'];
            $data['currency'] = $core->input['currency'];
            $tmexpenses = new Travelmanager_Expenses();
            $warnings['foodandbeverage'] = $tmexpenses->validate_foodandbeverage_expenses($data);
        }
        echo $warnings['foodandbeverage'];
    }
    elseif($core->input['action'] == 'validatetranspclass') {
        $warnings['transpclass'] = '';
        $class = TravelManagerPlanTranspClass::get_data(array('tmptc' => intval($core->input['transpclass'])));
        if($class->get_displayname() == 'Business') {
            $warnings['transpclass'] = '<p style = "color:red;">'.$lang->transclasswarning.'</p>';
        }
        echo $warnings['transpclass'];
    }
    elseif($core->input['action'] == 'populateexternalpurpose') {
        $ltpid = $core->input['externalpurposetype'];
        $sequence = $core->input['sequence'];
        $display_externalevent = 'style = "display:block"';
        $entrowid = 0;
        $purpose_obj = LeaveTypesPurposes::get_data(array('ltpid' => $ltpid));
        if(is_object($purpose_obj) && $purpose_obj->name == 'eventfair') {
            $returneddata['event'] = 1;
            $afent_checksum = generate_checksum();
            $calevents = Events::get_data(array('isPublic' => 1), array('returnarray' => true));
            $returneddata['htmlcontent'] = '<td '.$display_externalevent.' >Select Event</td><td '.$display_external.'>';
            $returneddata['htmlcontent'] .= parse_selectlist("segment[{$sequence}][assign][ceid][{$afent_checksum}]", $tabindex, $calevents, $selected_options, '', '', array('width' => '200px'));
            $returneddata['htmlcontent'] .='</td>';
            $returneddata['eventpurposeid'] = $purpose_obj->ltpid;
        }
        else {
            $returneddata['event'] = 0;
        }
        echo json_encode($returneddata);
    }
//    elseif($core->input['action'] == 'validateamountneededinadvance') {
//        if(!is_array($core->input['totalamounts'])) {
//            $amountspayedinadvance = explode(',', $core->input['totalamounts']);
//            $amountspayedinadvance = array_filter($amountspayedinadvance);
//        }
//        if(is_array($amountspayedinadvance)) {
//            foreach($amountspayedinadvance as $amount) {
//                $amount = explode(':', $amount);
//                if(is_array($amount)) {
//                    switch($amount[0]) {
//                        case 'USD':
//                            $amounts[$amount[0]] = $amount[1];
//                            $total +=$amount[1];
//                            break;
//                        default:
//                            $curr_obj = new Currencies($amount[0]);
//                            $fxrate = $curr_obj->get_average_fxrate('USD');
//                            if(!is_empty($fxrate)) {
//                                $amounts[$amount[0]] = $fxrate * $amount[1];
//                                $total += ($fxrate * $amount[1]);
//                            }
//                            break;
//                    }
//                }
//            }
//        }
//        $total = $total;
//    }
}
?>