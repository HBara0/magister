<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Fill up a quarter report
 * $module: reporting
 * $id: fillreport.php
 * Last Update: @tony.assaad	February 11, 2013 | 03:55 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canFillReports'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

//$session->name_phpsession(COOKIE_PREFIX.'fillquarterreport'.$core->user['uid']);
$session->start_phpsession();

$lang->load('reporting_fillreport');
if(!$core->input['action']) {
    //$headerinc .= "<link href='{$core->settings[rootdir]}/css/jqueryuitheme/jquery-ui-1.7.2.custom.css' rel='stylesheet' type='text/css' />";

    if($core->input['stage'] == 'productsactivity') {
        if(isset($core->input['identifier']) && !empty($core->input['identifier'])) {
            $identifier = $db->escape_string($core->input['identifier']);
//            $core->input = unserialize($session->get_phpsession('reportmeta_'.$identifier));
        }
        else {
            if(!isset($core->input['year'], $core->input['quarter'], $core->input['spid'], $core->input['affid'])) {
                redirect('index.php?module=reporting/fillreport');
            }
            else {
                $identifier = md5(uniqid(microtime()));
            }
        }

        $saletypes = explode(';', $core->settings['saletypes']);
        foreach($saletypes as $key => $val) {
            $saletypes[$val] = ucfirst($val);
            unset($saletypes[$key]);
        }

        list($rid, $core->input['affiliate'], $core->input['supplier']) = $db->fetch_array($db->query("SELECT r.rid, a.name, s.companyName
												FROM ".Tprefix."reports r
												JOIN ".Tprefix."affiliates a ON (r.affid=a.affid)
												JOIN ".Tprefix."entities s ON (r.spid=s.eid)
												WHERE year='{$core->input[year]}' AND r.quarter='{$core->input[quarter]}' AND r.affid='{$core->input[affid]}' AND r.spid='{$core->input[spid]}'"));

        $core->input['rid'] = $rid;
        $qreport = new ReportingQr(array('rid' => $rid));
        /* Instantiate currencies object and get currencies rate of period - START */
        $core->input['baseCurrency'] = 'USD';
        $currency = new Currencies($core->input['baseCurrency']);
        $currencies_from = strtotime($core->input['year'].'-'.$core->settings['q'.$core->input['quarter'].'start']); //date_timestamp_get(date_create_from_format('j-m-Y', $core->settings['q'.$core->input['quarter'].'start']));
        $currencies_to = strtotime($core->input['year'].'-'.$core->settings['q'.$core->input['quarter'].'end']); //date_timestamp_get(date_create_from_format('j-m-Y', $core->input['year'].'-'.$core->settings['q'.$core->input['quarter'].'end']));
        $currencies = $currency->get_average_fxrates_transposed(array('GBP', 'EUR'), array('from' => $currencies_from, 'to' => $currencies_to), array('distinct_by' => 'alphaCode', 'precision' => 4));
        $currencies[1] = $core->input['baseCurrency'];

        //** $session->set_phpsession(array('reportcurrencies_'.$identifier => serialize($currencies)));
        /* Instantiate currencies object and get currencies rate of period - END */

        /* Check if audit - START */
        $core->input['auditor'] = 0;
        if(value_exists('affiliatedemployees', 'uid', $core->user['uid'], 'canAudit=1 AND affid='.$core->input['affid']) || value_exists('suppliersaudits', 'uid', $core->user['uid'], 'eid='.$core->input['spid']) || $core->usergroup['canAdminCP'] == 1 || $core->usergroup['canViewAllEmp'] == 1) {
            $core->input['auditor'] = 1;
        }
        /* Check if audit - END */

        $readonly_fields = array('productname' => '', 'turnOver' => '', 'quantity' => '', 'soldQty' => '');
        if($core->input['auditor'] != '1') {
            foreach($readonly_fields as $key => $val) {
                $readonly_fields[$key] = ' readonly';
            }

            $selectlists_disabled = true;
        }

        unset($core->input['module'], $core->input['stage']);
        //$session->set_phpsession(array('reportmeta_'.$rid => serialize($core->input)));
        //**  $session->set_phpsession(array('reportmeta_'.$identifier => serialize($core->input)));

        $productscount = 6; //Make it a setting

        if($core->input['auditor'] != 1) {
            $query_string = ' AND (uid='.$core->user['uid'].' OR uid=0)';
        }

        $query = $db->query("SELECT pa.*, p.name AS productname
								FROM ".Tprefix."productsactivity pa LEFT JOIN ".Tprefix."products p ON (pa.pid=p.pid)
								WHERE pa.rid='{$rid}'{$query_string}");
        $rowsnum = $db->num_rows($query);
        if($rowsnum > 0) {
            $i = 1;
            $paid_field = '';
            while($productactivity = $db->fetch_array($query)) {
                $productsactivity[$i] = $productactivity;
                $i++;
            }
            $productscount = $rowsnum;
        }
        else {
            if($session->isset_phpsession('productsactivitydata_'.$identifier)) {
                $productsactivitydata = unserialize($session->get_phpsession('productsactivitydata_'.$identifier));
                $productsactivity = $productsactivitydata['productactivity'];
            }
        }

        if(is_array($productsactivity)) {
            foreach($productsactivity as $rowid => $productactivity) {
                $product = new Products($productactivity['pid']);
                $segment = $product->get_segment();
                $usersegments = array_keys($core->user_obj->get_segments());

                if(is_array($usersegments)) {
                    if(!in_array($segment['psid'], $usersegments) && $core->input['auditor'] != '1') {
                        continue;
                    }
                }
                unset($usersegments, $segment, $product);
                $saletype_selectlist = parse_selectlist('productactivity['.$rowid.'][saleType]', 0, $saletypes, $productactivity['saleType'], 0, null, array('disabled' => $selectlists_disabled));
                $currencyfx_selectlist = parse_selectlist('productactivity['.$rowid.'][fxrate]', 0, $currencies, 1, '', '', array('id' => 'fxrate_'.$rowid, 'disabled' => $selectlists_disabled));

                if(isset($productactivity['fxrate']) && $productactivity['fxrate'] != 1) {
                    $productactivity['turnOver'] = $productactivity['turnOver'] / $productactivity['fxrate'];
                }

                if(isset($productactivity['paid']) && !empty($productactivity['paid'])) {
                    $paid_field = '<input type="hidden" value="'.$productactivity['paid'].'" id="paid_'.$rowid.'" name="productactivity['.$rowid.'][paid]" />';
                }

                /* Get preview Q data */
                $prev_productactivity = $db->fetch_assoc($db->query("SELECT pid, ROUND(SUM(soldQty) + ".$productactivity['soldQty'].", 2) AS soldQty, ROUND(SUM(quantity) + ".$productactivity['soldQty'].", 2) AS quantity, ROUND(SUM(turnOver) + ".$productactivity['soldQty'].", 2) AS turnOver
							FROM ".Tprefix."productsactivity pa
							JOIN ".Tprefix."reports r ON (r.rid=pa.rid)
							WHERE r.quarter<'".intval($qreport->quarter)."' AND r.year='".intval($qreport->year)."' AND r.affid='".intval($qreport->affid)."' AND r.spid='".intval($qreport->spid)."' AND pa.pid=".$productactivity['pid'].$query_string."
                                                        GROUP BY pid"));
                /* Get preview Q data - END */
                $reportinconsistency = '<td><a href="#" id="reportinconsistency_'.$productactivity['paid'].'_reporting/fillreport_loadpopupbyid"><img src="'.$core->settings['rootdir'].'/images/alert.png" title="{$lang->reportinconsistency}"/></a></td>';
                eval("\$productsrows .= \"".$template->get('reporting_fillreports_productsactivity_productrow')."\";");
                unset($reportinconsistency);
            }
        }
        else {
            for($rowid = 1; $rowid < $productscount; $rowid++) {
                $saletype_selectlist = parse_selectlist('productactivity['.$rowid.'][saleType]', 0, $saletypes, 'distribution', 0, null, array('disabled' => $selectlists_disabled));
                $currencyfx_selectlist = parse_selectlist('productactivity['.$rowid.'][fxrate]', 0, $currencies, 1, '', '', array('id' => 'fxrate_'.$rowid, 'disabled' => $selectlists_disabled));
                eval("\$productsrows .= \"".$template->get('reporting_fillreports_productsactivity_productrow')."\";");
            }
        }

        if($core->usergroup['canExcludeFillStages'] == 1) {
            $exludestage = '<br /><input type="checkbox" value="1" name="excludeProductsActivity" id="excludeProductsActivity" title="'.$lang->exclude_tip.'"> '.$lang->excludeproductsactivity;
        }

        $generic_attributes = array('gpid', 'title');
        $generic_order = array(
                'by' => 'title',
                'sort' => 'ASC'
        );

        $generics = get_specificdata('genericproducts', $generic_attributes, 'gpid', 'title', $generic_order, 1);
        $generics_list = parse_selectlist('gpid', 3, $generics, '');

        $popup_addsupplier_supplierfield = "{$core->input[supplier]}<input type='hidden' value='{$core->input[spid]}' name='spid' />";
        eval("\$addproduct_popup = \"".$template->get('popup_addproduct')."\";");


        eval("\$productsactivitypage = \"".$template->get('reporting_fillreports_productsactivity')."\";");


        /*         * **************88
          // elseif($core->input['stage'] == 'marketreport') {
          //        if(!isset($core->input['identifier'])) {
          //            redirect('index.php?module=reporting/fillreport');
          //        }
          //        $identifier = $db->escape_string($core->input['identifier']);
          //        if(!isset($core->input['rid'])) {
          //            $report_meta = unserialize($session->get_phpsession('reportmeta_'.$identifier));
          //            if(!isset($report_meta['rid'])) {
          //                redirect('index.php?module=reporting/fillreport');
          //            }
          //            else {
          //                $core->input['rid'] = $report_meta['rid'];
          //            }
          //        }
         * /************************
         */

//        if(strpos(strtolower($_SERVER['HTTP_REFERER']), 'reporting/preview') === false) {
//            if($core->input['stage'] == 'marketreport') {
//                $keycustomersdata = serialize($core->input);
//                $session->set_phpsession(array('keycustomersdata_'.$identifier => $keycustomersdata));
//            }
//        }

        /*         * *
          //        if(strpos(strtolower($_SERVER['HTTP_REFERER']), 'productsactivity') !== false) {
          //            $productsactivitydata = serialize($core->input);
          //            $session->set_phpsession(array('productsactivitydata_'.$identifier => $productsactivitydata));
          //        }

         * * */

        //**   $rid = intval($core->input['rid']);
        if(value_exists('marketreport', 'rid', $core->input['rid'])) {
            $ischecked = array();
            $query = $db->query("SELECT mr.*, r.quarter, r.year, r.spid, r.affid
								  FROM ".Tprefix."marketreport mr LEFT JOIN ".Tprefix."reports r ON (r.rid=mr.rid)
								  WHERE mr.rid='{$rid}'");
            while($marketreports_data = $db->fetch_assoc($query)) {
                $marketreport[$marketreports_data['psid']] = $marketreports_data;
                // $marketreportcompetetion[$marketreports_data['psid']][$marketreports_data['mrid']] = MarketReportCompetition::get_data(array('mrid' => $marketreports_data['mrid']), array('returnarray' => true));
            }
        }

        //delete session
//                  else {
//                      if($session->isset_phpsession('marketreport_'.$identifier)) {
//                          $marketreport = unserialize($session->get_phpsession('marketreport_'.$identifier));
//                          $marketreport = $marketreport['marketreport'];  /* read martketreport ARRAY from the market report session */
//                if(isset($marketreport_excluded[$key]['exclude']) && $marketreport_excluded[$key]['exclude'] == 1) {
//                    $ischecked[$key] = ' checked="checked"';
//                }
//            }
//        }

        $marketreport_excluded = unserialize($session->get_phpsession('excludesegment'.$identifier));
        if(is_array($marketreport)) {
            foreach($marketreport as $key => $val) {
                $marketreport[$key] = preg_replace("/<br \/>/i", "\n", $val);
                if(isset($marketreport_excluded[$key]['exclude']) && $marketreport_excluded[$key]['exclude'] == 1) {
                    $ischecked[$key] = ' checked="checked"';
                }
            }
        }

        // $reportmeta = unserialize($session->get_phpsession('reportmeta_'.$identifier));
        // $quarter = $reportmeta['quarter'];

        $reportmeta = $core->input;
        $quarter = $core->input['quarter'];
        if($quarter == 1) {
            $lastquarter = 4;
            $lastyear = $reportmeta['year'] - 1;
        }
        else {
            $lastyear = $reportmeta['year'];
            $lastquarter = $quarter - 1;
        }
//		$last_report = $db->fetch_array($db->query("SELECT mr.*
//													FROM ".Tprefix."marketreport mr LEFT JOIN reports r ON (r.rid=mr.rid)
//													WHERE r.year='{$lastyear}' AND r.quarter='{$lastquarter}' AND r.spid='{$reportmeta[spid]}' AND r.affid='{$reportmeta[affid]}'"));
//
        $query = $db->query("SELECT mr.* FROM ".Tprefix."marketreport mr LEFT JOIN reports r ON (r.rid=mr.rid)
							WHERE r.year='{$lastyear}' AND r.quarter='{$lastquarter}' AND r.spid='{$reportmeta[spid]}' AND r.affid='{$reportmeta[affid]}'");
        while($lastmarketreports_data = $db->fetch_assoc($query)) {
            $last_report[$lastmarketreports_data['psid']] = $lastmarketreports_data;
        }

        //$segments = get_specificdata('entitiessegments', '*', 'esid', 'psid', '', 0, "eid='{$reportmeta[spid]}'");
        //foreach($segments as $key => $val) {

        if($reportmeta['auditor'] == 0) {
            if(!value_exists('suppliersaudits', 'uid', $core->user['uid'], "eid='{$reportmeta[spid]}'")) {
                $filter_segments_query = " AND ps.psid IN (SELECT psid FROM ".Tprefix."employeessegments WHERE uid='{$core->user[uid]}')";
            }
        }
        $query = $db->query("SELECT es.psid, ps.title FROM ".Tprefix."entitiessegments es JOIN ".Tprefix."productsegments ps ON (ps.psid=es.psid) WHERE es.eid='{$reportmeta[spid]}'{$filter_segments_query}");
        if($db->num_rows($query) > 0) {
            while($segment = $db->fetch_assoc($query)) {
                if(is_array($marketreport[$segment['psid']])) {
                    $criteriaandstars .= '<div class="evaluation_criterium" name="'.$segment['psid'].'_'.$marketreport[$segment['psid']]['mrid'].'"><div class="criterium_name" style="display:inline-block; width:30%; padding: 2px;">'.$segment['title'].'</div>';
                    $criteriaandstars .= '<div class="ratebar" style="width:40%; display:inline-block;">';
                    if(!isset($marketreport[$segment['psid']]['rating']) || empty($marketreport[$segment['psid']]['rating'])) {
                        $ratingval = 0;
                    }
                    else {
                        $ratingval = $marketreport[$segment['psid']]['rating'];
                    }
                    if($core->input['auditor'] == 0) {
                        $criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="'.$ratingval.'"></div>';
                    }
                    else {
                        $header_ratingjs = '$(".rateit").click(function() {
					if(sharedFunctions.checkSession() == false) {
						return;
					}
					var targetid = $(this).parent().parent().attr("name");
					var returndiv = "";
                                        var val=$("#rating_"+targetid).val();
                                        var ids=targetid.split("_");
                                        if(ids[1].length < 1 || ids[0].length < 1 ){
                                        return;
                                        }
                                        if(val.length >0){
					sharedFunctions.requestAjax("post", "index.php?module=reporting/fillreport&action=do_ratesegment", "target="+ids[0]+"&value="+val+"&repid="+ids[1], returndiv, returndiv, "html");
                                        }
				});';
                        $criteriaandstars .= '<input type="range" min="0" max="5" value="'.$ratingval.'" step="1" id="rating_'.$segment['psid'].'_'.$marketreport[$segment['psid']]['mrid'].'" class="ratingscale">';
                        $criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-resetable="false" data-rateit-backingfld="#rating_'.$segment['psid'].'_'.$marketreport[$segment['psid']]['mrid'].'" data-rateit-value="'.$marketreport[$segment['psid']]['rating'].'"></div>';
                    }
                    $criteriaandstars .= '</div></div>';
                    // $criteriaandstars .='<input type="hidden" name="marketreport['.$segment[psid].'][rating]" id="segmentrating_'.$segment['psid'].'" value="'.$ratingval.'">';
                }
                eval("\$markerreport_fields .= \"".$template->get('reporting_fillreports_marketreport_fields')."\";");
                unset($criteriaandstars);
            }
            if(isset($marketreport[0])) {
                $segment['psid'] = 0;
                if(is_array($marketreport[$segment['psid']])) {
                    $criteriaandstars .= '<div class="evaluation_criterium" name="'.$segment['psid'].'_'.$marketreport[$segment['psid']]['mrid'].'"><div class="criterium_name" style="display:inline-block; width:30%; padding: 2px;">'.$segment['title'].'</div>';
                    $criteriaandstars .= '<div class="ratebar" style="width:40%; display:inline-block;">';
                    if(!isset($marketreport[$segment['psid']]['rating']) || empty($marketreport[$segment['psid']]['rating'])) {
                        $ratingval = 0;
                    }
                    else {
                        $ratingval = $marketreport[$segment['psid']]['rating'];
                    }
                    if($core->input['auditor'] == 0) {
                        $criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="'.$ratingval.'"></div>';
                    }
                    else {
                        $header_ratingjs = '$(".rateit").click(function() {
					if(sharedFunctions.checkSession() == false) {
						return;
					}
					var targetid = $(this).parent().parent().attr("name");
					var returndiv = "";
                                        var val=$("#rating_"+targetid).val();
                                        var ids=targetid.split("_");
                                        if(ids[1].length < 1 || ids[0].length < 1 ){
                                        return;
                                        }
                                        if(val.length >0){
					sharedFunctions.requestAjax("post", "index.php?module=reporting/fillreport&action=do_ratesegment", "target="+ids[0]+"&value="+val+"&repid="+ids[1], returndiv, returndiv, "html");
                                        }
				});';
                        $criteriaandstars .= '<input type="range" min="0" max="5" value="'.$ratingval.'" step="1" id="rating_'.$segment['psid'].'_'.$marketreport[$segment['psid']]['mrid'].'" class="ratingscale">';
                        $criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-resetable="false" data-rateit-backingfld="#rating_'.$segment['psid'].'_'.$marketreport[$segment['psid']]['mrid'].'" data-rateit-value="'.$marketreport[$segment['psid']]['rating'].'"></div>';
                    }
                    $criteriaandstars .= '</div></div>';
                    // $criteriaandstars .='<input type="hidden" name="marketreport['.$segment[psid].'][rating]" id="segmentrating_'.$segment['psid'].'" value="'.$ratingval.'">';
                }
                $segment['title'] = $lang->unspecifiedsegment;
                eval("\$markerreport_fields .= \"".$template->get('reporting_fillreports_marketreport_fields')."\";");
                unset($criteriaandstars);
            }
        }
        else {
            $segment['psid'] = 0;
            $segment['title'] = $lang->unspecifiedsegment;
            if(is_array($marketreport[$segment['psid']])) {
                $criteriaandstars .= '<div class="evaluation_criterium" name="'.$segment['psid'].'_'.$marketreport[$segment['psid']]['mrid'].'"><div class="criterium_name" style="display:inline-block; width:30%; padding: 2px;">'.$segment['title'].'</div>';
                $criteriaandstars .= '<div class="ratebar" style="width:40%; display:inline-block;">';
                if(!isset($marketreport[$segment['psid']]['rating']) || empty($marketreport[$segment['psid']]['rating'])) {
                    $ratingval = 0;
                }
                else {
                    $ratingval = $marketreport[$segment['psid']]['rating'];
                }
                if($core->input['auditor'] == 0) {
                    $criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="'.$ratingval.'"></div>';
                }
                else {
                    $header_ratingjs = '$(".rateit").click(function() {
					if(sharedFunctions.checkSession() == false) {
						return;
					}
					var targetid = $(this).parent().parent().attr("name");
					var returndiv = "";
                                        var val=$("#rating_"+targetid).val();
                                        var ids=targetid.split("_");
                                        if(ids[1].length < 1 || ids[0].length < 1 ){
                                        return;
                                        }
                                        if(val.length >0){
					sharedFunctions.requestAjax("post", "index.php?module=reporting/fillreport&action=do_ratesegment", "target="+ids[0]+"&value="+val+"&repid="+ids[1], returndiv, returndiv, "html");
                                        }
				});';
                    $criteriaandstars .= '<input type="range" min="0" max="5" value="'.$ratingval.'" step="1" id="rating_'.$segment['psid'].'_'.$marketreport[$segment['psid']]['mrid'].'" class="ratingscale">';
                    $criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-resetable="false" data-rateit-backingfld="#rating_'.$segment['psid'].'_'.$marketreport[$segment['psid']]['mrid'].'" data-rateit-value="'.$marketreport[$segment['psid']]['rating'].'"></div>';
                }
                $criteriaandstars .= '</div></div>';
                // $criteriaandstars .='<input type="hidden" name="marketreport['.$segment[psid].'][rating]" id="segmentrating_'.$segment['psid'].'" value="'.$ratingval.'">';
            }
            eval("\$markerreport_fields = \"".$template->get('reporting_fillreports_marketreport_fields')."\";");
            unset($criteriaandstars);
        }

        //$report_meta = unserialize($session->get_phpsession('reportmeta_'.$identifier));

        /* Parse MOM Specific Follow Up Actions - START */
        $quarter_start = strtotime($core->input['year'].'-'.$core->settings['q'.$core->input['quarter'].'start']);
        $quarter_end = strtotime($core->input['year'].'-'.$core->settings['q'.$core->input['quarter'].'end']);
        $momactions_where = '(date BETWEEN '.$quarter_start.' AND '.$quarter_end.') AND momid=(select momid from meetings_minsofmeeting where mtid IN '
                .'(select mtid from meetings_associations where idAttr="spid" AND id='.$reportmeta[spid].'))';
        $momactions = MeetingsMOMActions::get_data($momactions_where, array('returnarray' => true, 'operators' => array('filter' => CUSTOMSQLSECURE)));
        if(is_array($momactions)) {
            foreach($momactions as $key => $actions) {
                /* The actions are associated to the QR affiliate (primarily) or its employees are assigned to the actions (secondary) */
                $meetings_affassociations = MeetingsAssociations::get_data(array('id' => $reportmeta[affid], 'idAttr' => 'affid', 'mtid' => 'mtid=(select mtid from meetings_minsofmeeting where momid='.$actions->momid.')'), array('returnarray' => true, 'operators' => array('mtid' => 'CUSTOMSQL')));
                //If actions are associated to the QR affiliate -> continue
                if(is_array($meetings_affassociations)) {
                    continue;
                }
                //Else check if employees of the QR aff are assigned to the actions
                $employeesassigned = false;
                $momactionsassignees = MeetingsMOMActionAssignees::get_data(array('momaid' => $actions->momaid), array('returnarray' => true));
                if(is_array($momactionsassignees)) {
                    foreach($momactionsassignees as $assignee) {
                        if(isset($assignee->uid) && !empty($assignee->uid)) {
                            $user = new Users($assignee->uid);
                            if(is_object($user) && $user->get_mainaffiliate()->affid == $reportmeta['affid']) {
                                $employeesassigned = true;
                            }
                        }
                    }
                }
                if(!$employeesassigned) {
                    unset($momactions[$key]); // if no aff or employees associations do not parse actions
                }
            }
            $mom_obj = new MeetingsMOM();
            $mom_followupactions .= $mom_obj->parse_actions('QR', $momactions);
        }
        /* Parse MOM Specific Follow Up Actions - end */

        eval("\$marketreportpage .= \"".$template->get('reporting_fillreports_marketreport')."\";");
        eval("\$fillreportpage = \"".$template->get('reporting_fillreports_tabs')."\";");
    }
    elseif($core->input['stage'] == 'keycustomers') {

        if(!isset($core->input['identifier'])) {
            redirect('index.php?module=reporting/fillreport');
        }

        $identifier = $db->escape_string($core->input['identifier']);

        if(strpos(strtolower($_SERVER['HTTP_REFERER']), 'productsactivity') !== false) {
            $productsactivitydata = serialize($core->input);
            $session->set_phpsession(array('productsactivitydata_'.$identifier => $productsactivitydata));
        }

        if(!isset($core->input['rid'])) {
            $report_meta = unserialize($session->get_phpsession('reportmeta_'.$identifier));
            if(!isset($report_meta['rid'])) {
                redirect('index.php?module=reporting/fillreport');
            }
            else {
                $core->input['rid'] = $report_meta['rid'];
            }
        }
//create_cookie('rid', $core->input['rid'], (time() + (60*$core->settings['idletime']*2)));

        $rid = $db->escape_string($core->input['rid']);
        $customerscount = 5; //Make it a setting
        $query = $db->query("SELECT kc.*, e.companyName
							FROM ".Tprefix."keycustomers kc LEFT JOIN ".Tprefix."entities e ON (e.eid=kc.cid)
							WHERE kc.rid='{$rid}' ORDER BY kc.rank ASC");

        $rowsnum = $db->num_rows($query);

        if($rowsnum > 0) {
            $i = 1;
            while($customer = $db->fetch_array($query)) {
                $customers[$i] = $customer;
                $i++;
            }
            $customerscount = $rowsnum;
        }
        else {
            if($session->isset_phpsession('keycustomersdata_'.$identifier)) {
                $keycustomersdata = unserialize($session->get_phpsession('keycustomersdata_'.$identifier));

                $customers = $keycustomersdata['keycustomers'];
                $customerscount = $keycustomersdata['numrows'];
                if(empty($customerscount)) {
                    $customerscount = 5;
                }
            }
        }

        if(is_array($customers)) {
            foreach($customers as $i => $customer) {
                $rowid = $i;
                if($rowsnum > 0) {
                    $kcidfield = "<input type='hidden' value='{$customer[kcid]}' name='keycustomers[$rowid][kcid]' id='kcid_{$rowid}'/>";
                }

                eval("\$customersrows .= \"".$template->get("reporting_fillreports_keycustomers_customerrow")."\";");
            }
        }
        else {
            for($rowid = 1; $rowid <= 5; $rowid++) {
                eval("\$customersrows .= \"".$template->get("reporting_fillreports_keycustomers_customerrow")."\";");
            }
        }

        $report_meta = unserialize($session->get_phpsession('reportmeta_'.$identifier));
        /* If supplier does not have contract and contract Expired -START */
        $entity = new Entities($report_meta['spid'], '', false);
        $entity_data = $entity->get();
//|| (!empty($entity_data['contractExpiryDate'] && TIME_NOW > $entity_data['contractExpiryDate'])
        if(empty($entity_data['contractFirstSigDate']) && $entity_data['contractIsEvergreen'] != 1) {// && !empty($entity_data['contractExpiryDate']
            $exludestage_checked = ' checked="checked"';
            $excludekeycust_notifymessage = '<div class="ui-state-highlight ui-corner-all" style="padding: 5px; margin-top: 10px; margin-bottom: 10px;"><strong>'.$lang->notcontractedsupp.'</strong></div>';
        }

        /* If supplier does not have contract and contract Expired -END */
        if($core->usergroup['canExcludeFillStages'] == 1) {
            $exludestage = '<br /><input type="checkbox" value="1" name="excludeKeyCustomers"'.$exludestage_checked.' style="width:30px;" id="excludeKeyCustomers" title="'.$lang->exclude_tip.'" /> '.$lang->excludekeycustomers;
        }

//Parse add customer popup
        $affiliates_attributes = array('affid', 'name');
        $affiliates_order = array(
                'by' => 'name',
                'sort' => 'ASC'
        );
        if($core->usergroup['canViewAllAff'] == 0) {
            $inaffiliates = implode(',', $core->user['affiliates']);
            $affiliate_where = 'affid IN ('.$inaffiliates.')';
        }
        $affiliates = get_specificdata('affiliates', $affiliates_attributes, 'affid', 'name', $affiliates_order, 0, $affiliate_where);
        $affiliates_list = parse_selectlist("affid[]", 4, $affiliates, '', 1);

        $countries_attributes = array('coid', 'name');
        $countries_order = array(
                'by' => 'name',
                'sort' => 'ASC'
        );

        $countries = get_specificdata('countries', $countries_attributes, 'coid', 'name', $countries_order);
        $countries_list = parse_selectlist('country', 8, $countries, '');

        eval("\$addcustomer_popup = \"".$template->get('popup_addcustomer')."\";");

        $addmore_customers = '';
        if($customerscount < 5) {
            $addmore_customers = '<img src="images/add.gif" id="addmore_keycustomers_customer" alt="'.$lang->add.'">';
        }
        eval("\$fillreportpage = \"".$template->get('reporting_fillreports_keycustomers')."\";");
    }
    else {
        /* if($core->usergroup['canViewAllAff'] == 0) {
          $inaffiliates = implode(',', $core->user['affiliates']);
          $extra_where = '  AND affid IN ('.$inaffiliates.') ';
          }
          if($core->usergroup['canViewAllSupp'] == 0) {
          $insuppliers = implode(',', $core->user['suppliers']);
          $extra_where .= ' AND spid IN ('.$insuppliers.') ';
          } */
        $additional_where = getquery_entities_viewpermissions();

        $query = $db->query("SELECT DISTINCT(affid) FROM ".Tprefix."reports r WHERE type='q' AND isLocked = '0'{$additional_where[extra]}");
        if($db->num_rows($query) == 0) {
            $affiliates_list = $lang->noreportsavailable;
            eval("\$fillreportpage = \"".$template->get('reporting_fillreports_init')."\";");
            output_page($fillreportpage);
            exit;
        }
        while($affiliate = $db->fetch_array($query)) {
            $availableaffiliates .= $comma.$affiliate['affid'];
            $comma = ',';
        }

        $affiliates_attributes = array('affid', 'name');
        $affiliates_order = array(
                'by' => 'name',
                'sort' => 'ASC'
        );

        $affiliates = get_specificdata('affiliates', $affiliates_attributes, 'affid', 'name', $affiliates_order, 1, 'affid IN ('.$availableaffiliates.')');
        $affiliates_list = parse_selectlist('affid', 1, $affiliates, '');

        if($core->usergroup['reporting_canTransFillReports'] == '1') {

            $transfill_checkbox = "<br /><span class='smalltext'><input type='checkbox' name='transFill' id='transFill' value='1' title='{$lang->transfill_tip}'> {$lang->transparentlyfill}</span>";
        }
        eval("\$fillreportpage = \"".$template->get('reporting_fillreports_init')."\";");
    }

    output_page($fillreportpage);
}
else {
    if($core->input['action'] == 'get_supplierslist') {
        $affid = $db->escape_string($core->input['id']);

        /* if($core->usergroup['canViewAllSupp'] == 0) {
          $insuppliers = implode(',', $core->user['suppliers']);
          $extra_where = ' AND r.spid IN ('.$insuppliers.') ';
          } */
        $additional_where = getquery_entities_viewpermissions('suppliersbyaffid', $affid);
        $suppliers_list = "<option value='0'>&nbsp;</option>";
        $query = $db->query("SELECT DISTINCT(s.companyName), r.spid
							FROM ".Tprefix."entities s LEFT JOIN ".Tprefix."reports r ON (r.spid=s.eid)
							WHERE r.affid='{$affid}' AND r.isLocked = '0' AND r.type='q'{$additional_where[extra]}
							ORDER BY s.companyName ASC");
        while($supplier = $db->fetch_array($query)) {
            $suppliers_list .= "<option value='{$supplier[spid]}'>{$supplier[companyName]}</option>";
        }
        echo $suppliers_list;
    }
    elseif($core->input['action'] == 'get_quarters') {
        $spid = $db->escape_string($core->input['id']);
        $affid = $db->escape_string($core->input['affid']);

        $quarters_list = "<option value='0'>&nbsp;</option>";
        $query = $db->query("SELECT DISTINCT(quarter)
							FROM ".Tprefix."reports
							WHERE spid='{$spid}' AND affid='{$affid}' AND isLocked = '0' AND type='q'
							ORDER BY quarter ASC");
        while($quarter = $db->fetch_array($query)) {
            $quarters_list .= "<option value='{$quarter[quarter]}'>Q{$quarter[quarter]}</option>";
        }
        echo $quarters_list;
    }
    elseif($core->input['action'] == 'get_years') {
        $quarter = $db->escape_string($core->input['id']);
        $spid = $db->escape_string($core->input['spid']);
        $affid = $db->escape_string($core->input['affid']);

        $years_list = "<option value='0'>&nbsp;</option>";
        $query = $db->query("SELECT DISTINCT(year)
							FROM ".Tprefix."reports
							WHERE quarter='{$quarter}' AND affid='{$affid}' AND spid='{$spid}' AND isLocked = '0' AND type='q'
							ORDER BY year ASC");
        while($year = $db->fetch_array($query)) {
            $years_list .= "<option value='{$year[year]}'>{$year[year]}</option>";
        }
        echo $years_list;
    }
    elseif($core->input['action'] == 'save_productsactivity') {
        $rid = intval($core->input['rid']);
        $identifier = $db->escape_string($core->input['identifier']);
        $numrows = intval($core->input['numrows']);

        $report_meta = unserialize($session->get_phpsession('reportmeta_'.$identifier));
        $currencies = unserialize($session->get_phpsession('reportcurrencies_'.$identifier));

        /* Validate Forecasts - Start */
        $report = new ReportingQr(array('rid' => $rid));
        $validation = $report->validate_forecasts($core->input['productactivity'], $currencies);

        if($validation != true || is_array($validation)) {
            $corrections_output = '<table width="100%" class="datatable">';
            $corrections_output .= '<tr><th width="50%">'.$lang->product.'</th><th width="20%">'.$lang->purchaseamount.'</th><th width="20%">'.$lang->quantity.'</th></tr>';
            if(is_array($validation)) {
                foreach($validation as $corrections) {
                    $corrections_output .= '<tr><td>'.$corrections['name'].'</td><td>'.$corrections['sales'].'</td><td>'.$corrections['quantity'].'</td></tr>';
                }
            }
            $corrections_output .= '</table>';
            output_xml('<status>false</status><message>'.$lang->wrongforecastsexist.' <![CDATA['.$corrections_output.']]></message>');
            exit;
        }
        /* Validate Forecasts - End */

        if($report_meta['auditor'] != '1') {
            $existingentries_query_string = ' AND (uid='.$core->user['uid'].' OR uid=0)';
        }
//$oldentries = get_specificdata('productsactivity', array('paid'), 'paid', 'paid', '', 0, "rid='{$rid}'{$oldentries_query_string}");
        foreach($core->input['productactivity'] as $i => $productactivity) {
            if(empty($productactivity['pid'])) {
                if(!empty($productactivity['paid'])) {
                    $db->query("DELETE FROM ".Tprefix."productsactivity WHERE paid=".intval($productactivity['paid']));
                }
                continue;
            }

            if($productactivity['fxrate'] != 1 && isset($productactivity['fxrate'])) {
                $productactivity['turnOverOc'] = $productactivity['turnOver'];
                $productactivity['turnOver'] = round($productactivity['turnOver'] / $productactivity['fxrate'], 4);
                $productactivity['originalCurrency'] = $currencies[$productactivity['fxrate']];
            }

            if(value_exists('productsactivity', 'rid', $rid, 'pid='.intval($productactivity['pid']).$existingentries_query_string)) {
                $productactivity['uid'] = $core->user['uid'];
                if(isset($productactivity['paid']) && !empty($productactivity['paid'])) {
                    $update_query_where = 'paid='.intval($productactivity['paid']);
                }
                else {
                    unset($productactivity['paid']);
                    $update_query_where = 'rid='.$rid.' AND pid='.intval($productactivity['pid']).$existingentries_query_string;
                }
                unset($productactivity['productname'], $productactivity['fxrate']);
                $update = $db->update_query('productsactivity', $productactivity, $update_query_where);
                $processed_once = true;
                if(isset($productactivity['paid']) && !empty($productactivity['paid'])) {
                    $cachearr['usedpaid'][] = $productactivity['paid'];
                }
            }
            else {
                $productactivity['uid'] = $core->user['uid'];
                $productactivity['rid'] = $rid;

                unset($productactivity['productname'], $productactivity['fxrate'], $productactivity['paid']);
                $insert = $db->insert_query('productsactivity', $productactivity);

                $cachearr['usedpaid'][] = $db->last_id();
                $processed_once = true;
            }

            $cachearr['usedpids'][] = $productactivity['pid'];
        }
        if($processed_once === true) {
            /* if(is_array($oldentries)) {
              foreach($oldentries as $key => $val) {
              $db->delete_query('productsactivity', "paid='{$val}'");
              }
              } */
            if(is_array($cachearr['usedpaid'])) {
//$delete_query_where = ' OR ( paid NOT IN ('.implode(', ', $cachearr['usedpaid']).') AND pid NOT IN ('.implode(', ', $cachearr['usedpids']).'))';
            }
//            if(is_array($cachearr['usedpids']) && !empty($cachearr['usedpids'])) {
//                $del_query = $db->query("DELETE FROM ".Tprefix."productsactivity WHERE rid='{$rid}' AND (pid NOT IN (".implode(', ', $cachearr['usedpids'])."){$delete_query_where}){$existingentries_query_string}");
//                if($db->affected_rows($del_query) > 0) {
//                    $log->record('deleteproductsactivity', $cachearr);
//                }
//            }
            $update_status = $db->update_query('reports', array('prActivityAvailable' => 1), "rid='{$rid}'");
            if($update_status) {
                if($report_meta['transFill'] != '1') {
                    record_contribution($rid);
                }
                $log->record($rid);

                $outliers = $report->check_outliers();
                if(is_array($outliers)) {
                    $corrections_output = $lang->pactivityincosistent.'<ul>';
                    foreach($outliers as $pid => $outlier) {
                        $product = new Products($pid);

                        $corrections_output .= '<li>'.$product->get()['name'].'</li>';
                    }
                    $corrections_output .= '</ul>';
                }
                output_xml("<status>true</status><message>{$lang->savedsuccessfully} <![CDATA[<br />{$corrections_output}]]></message>");
            }
            else {
                output_xml("<status>false</status><message>{$lang->saveerror}</message>");
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->fillatleastoneproductrow}</message>");
        }
    }
    elseif($core->input['action'] == 'save_keycustomers') {
        $rid = $db->escape_string($core->input['rid']);
        $identifier = $db->escape_string($core->input['identifier']);
        $numrows = intval($core->input['numrows']);

        if(!is_array($core->input['keycustomers']) || empty($core->input['keycustomers'])) {
            output_xml("<status>false</status><message>{$lang->fillatleastonecustomerrow}</message>");
            exit;
        }

        $oldentries = get_specificdata('keycustomers', array('kcid'), 'kcid', 'kcid', '', 0, "rid='{$rid}'");
        foreach($core->input['keycustomers'] as $i => $keycustomer) {
            if(empty($keycustomer['cid'])) {
                continue;
            }
            $keycustomer['rid'] = $rid;
            unset($keycustomer['companyName']);
            $insert = $db->insert_query('keycustomers', $keycustomer);
            $processed_once = true;
        }

        if($processed_once === true) {
            if(is_array($oldentries)) {
                foreach($oldentries as $key => $val) {
                    $db->delete_query('keycustomers', "kcid='{$val}'");
                }
            }
            $update_status = $db->update_query('reports', array('keyCustAvailable' => 1), "rid='{$rid}'");
            if($update_status) {
                $report_meta = unserialize($session->get_phpsession('reportmeta_'.$identifier));
                if($report_meta['transFill'] != '1') {
                    record_contribution($rid);
                }
                $log->record($rid);
                output_xml("<status>true</status><message>{$lang->savedsuccessfully}</message>");
            }
            else {
                output_xml("<status>false</status><message>{$lang->saveerror}</message>");
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->fillatleastonecustomerrow}</message>");
        }
    }
    elseif($core->input['action'] == 'save_marketreport') {
        $rid = intval($core->input['rid']);
        $identifier = $db->escape_string($core->input['identifier']);
        if(!empty($val['exclude']) && $val['exclude'] == 1) {
            $marketreport_data[$key]['exclude'] = $val['exclude'];
            $session->set_phpsession(array('excludesegment'.$identifier => serialize($marketreport_data[$key]['exclude'])));
        }
        $emtpy_terms = array('na', 'n/a', 'none', 'nothing', 'nothing to mention');

        $found_one = $one_notexcluded = false;
        $devprojects = $core->input['marketreport']['customers'];
        unset($core->input['marketreport']['customers']);
        foreach($core->input['marketreport'] as $key => $val) {
            $section_allempty = true;
            if(isset($val['exclude']) && $val['exclude'] == 1) {
                $db->query('DELETE FROM '.Tprefix.'marketreport_authors WHERE mrid=(SELECT mrid FROM '.Tprefix.'marketreport WHERE rid='.$rid.' AND psid='.$key.')');
                $db->query('DELETE FROM '.Tprefix.'marketreport WHERE rid='.$rid.' AND psid='.$key);
                continue;
            }

            unset($val[segmenttitle], $val[exclude]);
            if($found_one == false) {
                if(!empty($val)) {
                    foreach($val as $k => $v) {
                        $v = $core->sanitize_inputs(preg_replace(array('~\x{00a0}~siu', '/\s/'), '', $v), array('method' => 'striponly', 'allowable_tags' => '', 'removetags' => true));
                        if($section_allempty == true) {
                            if(!in_array(strtolower($v), $emtpy_terms) && !preg_match('/^[n;.,-_+\*]+$/', $v)) {
                                $section_allempty = false;
                            }
                        }
                        if(empty($v)) {
                            $found_one = true;
                            break;
                        }
                    }
                }
                else {
                    $found_one = true;
                    break;
                }
            }
            else {
                break;
            }

            if($section_allempty == true) {
                continue;
            }

            $marketreport_data[$key] = $val;
            $marketreport_data[$key]['psid'] = $key;
            $marketreport_data[$key]['rid'] = $rid;
//unset($marketreport_data[$key]['segmenttitle']);
            $one_notexcluded = true;
        }

//        if($found_one == true || $one_notexcluded == false) {
//            output_xml("<status>false</status><message>{$lang->fillonemktreportsection}</message>");
//            exit;
//        }

        $report_meta = unserialize($session->get_phpsession('reportmeta_'.$identifier));

        if(!is_array($marketreport_data)) {
            exit;
        }
        foreach($marketreport_data as $val) {
            foreach($val as $k => $v) {
                $val[$k] = $core->sanitize_inputs(trim($v), array('method' => 'striponly', 'allowable_tags' => '<table><tbody><tr><td><th><thead><tfoot><span><div><a><br><p><b><i><del><strike><img><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><h1><h2><h3><h4><h5><h6>', 'removetags' => true));
            }

            if(value_exists('marketreport', 'rid', $rid, 'psid="'.$val['psid'].'"')) {
                $query = $db->update_query('marketreport', $val, "rid='{$rid}' AND psid='{$val[psid]}'");

                $mrid = $db->fetch_field($db->query("SELECT mrid FROM ".Tprefix."marketreport WHERE rid='{$rid}' AND psid='{$val[psid]}'"), 'mrid');
            }
            else {
                $query = $db->insert_query('marketreport', $val);
                $mrid = $db->last_id();
            }

            if($report_meta['transFill'] != '1' || !isset($report_meta['transFill'])) {
                if($db->fetch_field($db->query("SELECT COUNT(*) AS contributed FROM ".Tprefix."marketreport_authors WHERE mrid='{$mrid}' AND uid='{$core->user[uid]}'"), 'contributed') == 0) {
                    $db->insert_query('marketreport_authors', array('mrid' => $mrid, 'uid' => $core->user['uid']));
                }
            }
        }

        if($query) {
            $log->record($rid);
            $new_status = array('mktReportAvailable' => 1);

            /* Validate Forecasts - Start */
            $report = new ReportingQr(array('rid' => $rid));
            $forecast_validation = $report->validate_forecasts(unserialize($session->get_phpsession('productsactivitydata_'.$identifier))['productactivity'], $currencies);

            if($forecast_validation != true || is_array($forecast_validation)) {
                $output_message = $lang->savedsuccessfully.' | '.$lang->wrongforecastgoback;
                $process_success = 'false';
                $core->input['isDone'] = 0;
            }
            else {
                if($db->fetch_field($db->query("SELECT COUNT(*) as count FROM ".Tprefix."users u JOIN ".Tprefix."assignedemployees ae ON (u.uid=ae.uid) WHERE ae.affid='{$report_meta[affid]}' AND ae.eid='{$report_meta[spid]}' AND u.gid IN (SELECT gid FROM usergroups WHERE canUseReporting=1 AND canFillReports=1) AND u.uid NOT IN (SELECT uid FROM ".Tprefix."reportcontributors WHERE rid='{$rid}' AND isDone=1) AND u.uid!={$core->user[uid]}"), 'count') == 0) {
                    $new_status['status'] = 1;
                }
                $output_message = $lang->savedsuccessfully;
                $process_success = 'true';
            }
            /* Validate Forecasts - End */
            if($report_meta['transFill'] != '1' || !isset($report_meta['transFill'])) {
                record_contribution($rid, $core->input['isDone']);
            }

            $db->update_query('reports', $new_status, "rid='{$rid}'");
            output_xml('<status>'.$process_success."</status><message>{$output_message}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->saveerror}</message>");
        }
    }
    elseif($core->input['action'] == 'save_newproduct') {
        if(empty($core->input['spid']) || empty($core->input['gpid']) || empty($core->input['name'])) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }

        if(value_exists('products', 'name', $core->input['name'])) {
            output_xml("<status>false</status><message>{$lang->productalreadyexists}</message>");
            exit;
        }

        $log->record($core->input['name']);
        unset($core->input['action'], $core->input['module']);
//Temporary hardcode
        $core->input['defaultCurrency'] = 'USD';

        $query = $db->insert_query('products', $core->input);
        if($query) {
            $lang->productadded = $lang->sprint($lang->productadded, htmlspecialchars($core->input['name']));
            output_xml("<status>true</status><message>{$lang->productadded}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->erroraddingproduct}</message>");
        }
    }
    elseif($core->input['action'] == 'save_newcustomer') {
        $new_customer = $core->input;
        unset($new_customer['module'], $new_customer['action']);
        $entity = new Entities($new_customer);
        $log->record($entity->get_eid());
    }
    elseif($core->input['action'] == 'get_addnew_product') {
        $generic_attributes = array('gpid', 'title');

        $generic_order = array(
                'by' => 'title',
                'sort' => 'ASC'
        );

        $generics = get_specificdata('genericproducts', $generic_attributes, 'gpid', 'title', $generic_order, 1);
        $generics_list = parse_selectlist('gpid', 3, $generics, '');

        eval("\$addproductbox = \"".$template->get('popup_addproduct')."\";");
        output_page($addproductbox);
    }
    elseif($core->input['action'] == 'save_report') {
        $identifier = $db->escape_string($core->input['identifier']);

        $rawdata = unserialize($session->get_phpsession('reportrawdata_'.$identifier));

        $report_meta = unserialize($session->get_phpsession('reportmeta_'.$identifier));


        //  $report_meta['rid'] = $db->escape_string($report_meta['rid']);
        //**  $report_meta['rid'] = 1;
        $report = new ReportingQr(array('rid' => $report_meta['rid']));
        $currencies = unserialize($session->get_phpsession('reportcurrencies_'.$identifier));

        $cachearr = array();
        if(empty($report_meta['rid'])) {
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            exit;
        }

        list($islocked) = $db->fetch_field($db->query("SELECT isLocked FROM ".Tprefix."reports WHERE rid='{$report_meta[rid]}'"), 'isLocked');
        if($islocked == 1) {
            output_xml("<status>false</status><message>{$lang->reportlocked}</message>");
            exit;
        }
        //** $report_meta['excludeProductsActivity'] = 1;
        if(empty($report_meta['excludeProductsActivity'])) {
            if(empty($rawdata['productactivitydata'])) {
                output_xml("<status>false</status><message>{$lang->productsdataempty}</message>");
                exit;
            }
        }
//        if(empty($report_meta['excludeKeyCustomers'])) {
//            if(empty($rawdata['keycustomersdata'])) {
//                output_xml("<status>false</status><message>{$lang->keycustomersempty}</message>");
//                exit;
//            }
//        }

        if($report_meta['auditor'] != '1') {
            $products_deletequery_string = ' AND (uid='.$core->user['uid'].' OR uid=0)';
        }

//$db->query("DELETE FROM ".Tprefix."productsactivity WHERE rid='{$rawdata[rid]}'{$products_deletequery_string}");
        if(empty($report_meta['excludeProductsActivity'])) {
            $productsactivity_validation = $report->validate_forecasts($rawdata['productactivitydata'], $currencies);
            if($productsactivity_validation !== true) {
                output_xml("<status>false</status><message>{$lang->wrongforecastgoback}</message>");
                exit;
            }
            foreach($rawdata['productactivitydata'] as $i => $newdata) {
                if(empty($newdata['pid'])) {
                    if(!empty($newdata['paid'])) {
                        $db->query("DELETE FROM ".Tprefix."productsactivity WHERE paid=".intval($newdata['paid']));
                    }
                    continue;
                }
                if(isset($newdata['fxrate']) && $newdata['fxrate'] != 1) {
                    $newdata['turnOverOc'] = $newdata['turnOver'];
                    $newdata['turnOver'] = round($newdata['turnOver'] / $newdata['fxrate'], 4);
                    $newdata['originalCurrency'] = $currencies[$newdata['fxrate']];
                }

                unset($newdata['productname'], $newdata['fxrate']);
                if(value_exists('productsactivity', 'rid', $report_meta['rid'], 'pid='.$newdata['pid'].$products_deletequery_string)) {
                    if(isset($newdata['paid']) && !empty($newdata['paid'])) {
                        $update_query_where = 'paid='.$db->escape_string($newdata['paid']);
                    }
                    else {
                        unset($newdata['paid']);
                        $update_query_where = 'rid='.$report_meta['rid'].' AND pid='.$newdata['pid'].$products_deletequery_string;
                    }

                    $update = $db->update_query('productsactivity', $newdata, $update_query_where);
                }
                else {
                    $newdata['uid'] = $core->user['uid'];

                    $db->insert_query('productsactivity', $newdata);
                    $cachearr['usedpaid'][] = $db->last_id();
                }

                $cachearr['usedpids'][] = $newdata['pid'];
                if(isset($newdata['paid']) && !empty($newdata['paid'])) {
                    $cachearr['usedpaid'][] = $newdata['paid'];
                }
            }

//            if(is_array($cachearr['usedpaid'])) {
//                $delete_query_where = ' OR paid NOT IN ('.implode(', ', $cachearr['usedpaid']).')';
//                $db->query("DELETE FROM ".Tprefix."productsactivity WHERE rid='{$report_meta[rid]}' AND (pid NOT IN (".implode(', ', $cachearr['usedpids'])."){$delete_query_where}){$products_deletequery_string}");
//            }
        }
        else {
            if($report_meta['auditor'] != '1') {
                $products_deletequery_string = ' AND (uid='.$core->user['uid'].' OR uid=0)';
            }
            $db->query("DELETE FROM ".Tprefix."productsactivity WHERE rid='{$report_meta[rid]}'".$products_deletequery_string);
        }

        $db->query("DELETE FROM ".Tprefix."keycustomers WHERE rid='{$report_meta[rid]}'");
        if(empty($report_meta['excludeKeyCustomers'])) {
            if(is_array($rawdata['keycustomersdata'])) {
                foreach($rawdata['keycustomersdata'] as $rank => $newdata) {
                    $newdata['rid'] = $report_meta['rid'];
                    $newdata['rank'] = $rank;
                    unset($newdata['companyName']);
                    $db->insert_query('keycustomers', $newdata);
                }
            }
        }

        $emtpy_terms = array('na', 'n/a', 'none', 'nothing', 'nothing to mention');
        $marketreport_found_one = false;
        foreach($rawdata['marketreportdata'] as $key => $val) {
            if($val['exclude']) {
                continue;
            }
            $section_allempty = true;
            unset($val['segmenttitle'], $val['rid'], $val['psid']);

            if($marketreport_found_one == false) {
                if(!empty($val)) {
                    foreach($val as $k => $v) {
                        $v = preg_replace(array('/\s/', '~\x{00a0}~siu'), '', $core->sanitize_inputs($v, array('method' => 'striponly', 'allowable_tags' => '', 'removetags' => true)));
                        if($section_allempty == true) {
                            if(!in_array(strtolower(trim($v)), $emtpy_terms) && !preg_match('/^[n;.,-_+\*]+$/', $v)) {
                                $section_allempty = false;
                            }
                        }
                        if(empty($v)) {
                            $marketreport_found_one = true;
                            break;
                        }
                    }
                }
                else {
                    $marketreport_found_one = true;
                    break;
                }
            }
            else {
                break;
            }

            if($section_allempty == true) {
                unset($rawdata['marketreportdata'][$key]);
            }

//            if($marketreport_found_one == true) {
//                output_xml("<status>false</status><message>{$lang->incompletemarketreport}</message>");
//                exit;
//            }
        }
//$rawdata['marketreportdata']['rid'] = $rawdata['rid'];
        if(is_array($rawdata['marketreportdata']) && !empty($rawdata['marketreportdata'])) {
            foreach($rawdata['marketreportdata'] as $psid => $val) {
                if($val['exclude']) {
                    continue;
                }

                unset($val['segmenttitle'], $val['exclude']);
                foreach($val as $k => $v) {
                    $val[$k] = $core->sanitize_inputs(trim($v), array('method' => 'striponly', 'allowable_tags' => '<table><tbody><tr><td><th><thead><tfoot><span><div><a><br><p><b><i><del><strike><img><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><h1><h2><h3><h4><h5><h6>', 'removetags' => true));
                }
                $val['psid'] = $psid;

                if(value_exists('marketreport', 'rid', $report_meta['rid'], 'psid="'.$val['psid'].'"')) {
                    $db->update_query('marketreport', $val, "rid='{$report_meta[rid]}' AND psid='{$val[psid]}'");
                    $mrid = $db->fetch_field($db->query("SELECT mrid FROM ".Tprefix."marketreport WHERE rid='{$report_meta[rid]}' AND psid='{$val[psid]}'"), 'mrid');
                }
                else {
                    $val['rid'] = $report_meta['rid'];
                    $db->insert_query('marketreport', $val);
                    $mrid = $db->last_id();
                }

                if($report_meta['transFill'] != '1') {
                    if($db->fetch_field($db->query("SELECT COUNT(*) AS contributed FROM ".Tprefix."marketreport_authors WHERE mrid='{$mrid}' AND uid='{$core->user[uid]}'"), 'contributed') == 0) {
                        $db->insert_query('marketreport_authors', array('mrid' => $mrid, 'uid' => $core->user['uid']));
                    }
                }
            }
        }
        else {
//            output_xml("<status>false</status><message>{$lang->incompletemarketreport}</message>");
//            exit;
        }

        if($core->input['savetype'] == 'finalize') {
            $new_status = array(
                    'uidFinish' => $core->user['uid'],
                    'finishDate' => TIME_NOW,
                    'status' => 1,
                    'prActivityAvailable' => 1,
                    'keyCustAvailable' => 1,
                    'mktReportAvailable' => 1,
                    'isLocked' => 1
            );
        }
        else {
            $new_status = array('mktReportAvailable' => 1);
        }

        if(!empty($report_meta['excludeProductsActivity'])) {
            $new_status['prActivityAvailable'] = 0;
        }
//        if(!empty($report_meta['excludeKeyCustomers'])) {
//            $new_status['keyCustAvailable'] = 0;
//        }

        $update_status = $db->update_query('reports', $new_status, "rid='{$report_meta[rid]}'");
        if($update_status) {
            if($report_meta['transFill'] != '1') {
                record_contribution($report_meta['rid'], 1);
            }
            if($core->input['savetype'] == 'finalize') {
                /* Force recording of contribution if user is finalizing with transparency and no other contributor exist */
                if($report_meta['transFill'] == '1' && $db->num_rows($db->query('SELECT uid FROM '.Tprefix.'reportcontributors WHERE rid='.intval($report_meta['rid']))) == 0) {
                    record_contribution($report_meta['rid'], 1);
                }
                output_xml("<status>true</status><message>{$lang->reportfinalized}</message>");
            }
            else {
                output_xml("<status>true</status><message>{$lang->savedsuccessfully}</message>");
            }
            $log->record($report_meta['rid']);

            $current_report_details = $db->fetch_assoc($db->query("SELECT e.eid, e.companyName, r.year, r.quarter, e.noQReportSend FROM ".Tprefix."reports r LEFT JOIN ".Tprefix."entities e ON (r.spid=e.eid) WHERE r.rid='{$report_meta[rid]}'"));

            if($current_report_details['noQReportSend'] == 0) {
                if($db->fetch_field($db->query("SELECT COUNT(*) AS remainingreports FROM ".Tprefix."reports WHERE quarter='{$current_report_details[quarter]}' AND year='{$current_report_details[year]}' AND spid='{$current_report_details[eid]}' AND status='0' AND type='q'"), 'remainingreports') == 0) {
                    $query = $db->query("SELECT u.* FROM ".Tprefix."users u LEFT JOIN ".Tprefix."suppliersaudits sa ON (sa.uid=u.uid) WHERE sa.eid='{$current_report_details[eid]}' AND u.gid IN ('5', '13', '2')");
                    while($inform = $db->fetch_array($query)) {
                        $inform_employees[] = $inform['email'];
                    }

                    if(empty($inform_employees)) {
                        $inform_employees[] = $core->settings['sendreportsto'];
                    }

                    $query2 = $db->query("SELECT affid FROM ".Tprefix."reports WHERE quarter='{$current_report_details[quarter]}' AND year='{$current_report_details[year]}' AND spid='{$current_report_details[eid]}'");
                    while($ready_report = $db->fetch_assoc($query2)) {
                        $ready_affids[] = $ready_report['affid'];
                    }

                    $ready_reports_link = $core->settings['rootdir'].'/index.php?module=reporting/preview&referrer=direct&identifier='.base64_encode(serialize(array('year' => $current_report_details['year'], 'quarter' => $current_report_details['quarter'], 'spid' => $current_report_details['eid'], 'affid' => $ready_affids)));

                    $lang->load('messages');
                    $email_data = array(
                            'from_email' => 'no-reply@ocos.orkila.com',
                            'from' => 'OCOS Mailer',
                            'to' => $inform_employees,
                            'subject' => $lang->sprint($lang->reportsready, $current_report_details['quarter'], $current_report_details['year'], $current_report_details['companyName']),
                            'message' => $lang->sprint($lang->reportsreadymessage, $current_report_details['companyName'], $ready_reports_link)
                    );

                    $mail = new Mailer($email_data, 'php');
                }
            }
            $session->destroy_phpsession();
        }
    }
    elseif($core->input['action'] == 'get_addnew_customer') {
        $affiliates_attributes = array('affid', 'name');
        $affiliates_order = array(
                'by' => 'name',
                'sort' => 'ASC'
        );
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliates = get_specificdata('affiliates', $affiliates_attributes, 'affid', 'name', $affiliates_order, 0, 'affid IN ('.$inaffiliates.')');
        $affiliates_list = parse_selectlist("affid[]", 4, $affiliates, '', 1);

        $countries_attributes = array('coid', 'name');
        $countries_order = array(
                'by' => 'name',
                'sort' => 'ASC'
        );

        $countries = get_specificdata('countries', $countries_attributes, 'coid', 'name', $countries_order);
        $countries_list = parse_selectlist('country', 8, $countries, '');

        eval("\$addcustomerbox = \"".$template->get('popup_addcustomer')."\";");
        output_page($addcustomerbox);
    }
    elseif($core->input['action'] == 'do_ratesegment') {
        $mrid = $db->escape_string($core->input['repid']);
        $psid = $db->escape_string($core->input['target']);
        $marketreport_obj = MarketReport::get_data(array('mrid' => $mrid));
        if(is_object($marketreport_obj)) {
            $marketreport_obj->rating = $core->input['value'];
            $marketreport_obj->save();
        }
    }
    elseif($core->input['action'] == 'get_reportinconsistency') {
        $paid = $db->escape_string($core->input['id']);
        eval("\$report_inc = \"".$template->get('popup_fillreport_reportinconsistency')."\";");
        output($report_inc);
    }
    elseif($core->input['action'] == 'do_reportinconsistency') {
        if(is_array($core->input['productsactivity'])) {
            $productactivity_obj = new ProductsActivity($db->escape_string($core->input['productsactivity']['paid']), false);
            if(is_object($productactivity_obj)) {
                $reportobj = new ReportingQr($productactivity_obj->rid);
                $affiliate = $reportobj->get_affiliate();
                $currency = $productactivity_obj->originalCurrency;
                $auditors = $reportobj->get_report_supplier_audits();
                if(is_array($auditors)) {
                    foreach($auditors as $auditor) {
                        $ccs[] = $auditor['email'];
                    }
                }
                if(is_array($ccs)) {
                    $cc = implode(',', $ccs);
                }
                else {
                    $cc = '';
                }
                if(isset($currency) && !empty($currency)) {
                    $currency_obj = new Currencies($currency);
                    $selectedcur = $currency_obj->get_displayname();
                }
                else {
                    $selectedcur = 'USD';
                }
                if(isset($core->input['productsactivity']['comment']) && !empty($core->input['productsactivity']['comment'])) {
                    $comment = $core->input['productsactivity']['comment'];
                }
                else
                    $comment = 'NA';
                $user = new Users($core->user['uid']);
                $email_message = '<h1>QR Reporting Inconsistency</h1>';
                $email_message .= 'Inconsistency Submitted By :'.$user->get_displayname();
                $email_message .='Comment: <textarea disabled>'.$comment.'</textarea>';
                $email_message .= '<div style="width:100%">';
                $email_message.='<table><thead><tr><th style="width:35%">Product</th><th style="width:8%">Sold Quantity</th><th style="width:8%">Turnover</th><th style="width:10%">Currency</th><th style="width:15%">Sale Type</th><th style="width:8%">Forecast Purchase Amount</th><th style="width:8%">Forecast Purchase Qty</th></thead>';
                $email_message.='<tbody><tr><td style="width:35%">'.$productactivity_obj->get_product()->get_displayname().'</td><td style="width:8%">'.$productactivity_obj->soldQty.'</td><td style="width:8%">'.$productactivity_obj->turnOver.'</td><td style="width:10%">'.$selectedcur.'</td><td style="width:8%">'.$productactivity_obj->soldQty.'</td><td style="width:15%">'.$productactivity_obj->saleType.'</td><td style="width:8%">'.$productactivity_obj->soldQty.'</td><td style="width:8%">'.$productactivity_obj->quantityForecast.'</td><td style="width:8%">'.$productactivity_obj->soldQty.'</td><td style="width:8%">'.$productactivity_obj->salesForecast.'</td></tr></tbody>';
                $email_message.='</table></div>';
                $email_data = array(
                        'from_email' => $user->email,
                        'from' => $user->get_displayname(),
                        'cc' => $cc,
                        'to' => 'ocos@orkila.com',
                        'subject' => 'QR Product Activity Inconsistency Reported',
                        'message' => $email_message,
                );

                $mail = new Mailer($email_data, 'php');
                if($mail->get_status() === true) {
                    output_xml("<status>true</status><message>{$lang->reportsubmitted}</message>");
                }
                else {
                    output_xml("<status>false</status><message>{$lang->errorreporting}</message>");
                }
            }
            else {
                output_xml('<status>false</status><message>'.$lang->errorreporting.'</message>');
                exit;
            }
        }
    }
}
?>
