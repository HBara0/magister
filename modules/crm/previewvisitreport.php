<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Fill up a visit report
 * $module: CRM
 * $id: fillvisitreport.php
 * Created: 	@zaher.reda 	June 26, 2009 | 03:35 PM
 * Last Update: @zaher.reda 	Agust 14, 2012 | 02:24 PM
 */

if(!defined("DIRECT_ACCESS")) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['crm_canViewVisitReports'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!isset($core->input['identifier'])) {
    $core->input['identifier'] = md5(uniqid(microtime()));
}
$session->name_phpsession(COOKIE_PREFIX.'fillvisitreport'.$core->input['identifier']);
$session->start_phpsession();

$lang->load('crm_visitreport');
if(!$core->input['action']) {
    if($core->input['referrer'] == 'fill') {
        $identifier = $db->escape_string($core->input['identifier']);
        $session->set_phpsession(array('visitreportcompetitiondata_'.$identifier => serialize($core->input)));
        $unserialized_session = unserialize($session->get_phpsession('visitreportdata_'.$identifier));
        $phpsession = $session->get_phpsession('visitreportdata_'.$identifier);
        if(empty($phpsession) || !is_array($unserialized_session) || !is_array($unserialized_session)) {
            redirect('index.php?module=crm/listvisitreports');
        }
        $visitreports[1] = array_merge(unserialize($session->get_phpsession('visitreportdata_'.$identifier)), unserialize($session->get_phpsession('visitreportvisitdetailsdata_'.$identifier)));
        foreach($visitreports[1]['comments'] as $key => $val) {
            if(is_array($core->input['comments'][$key])) {
                $visitreports[1]['comments'][$key] = array_merge($visitreports[1]['comments'][$key], $core->input['comments'][$key]);
            }
        }

        if(!empty($visitreports[1]['date'])) {
            $visitreportdate = explode('-', $visitreports[1]['date']);
            $visitreports[1]['formatteddate'] = date($core->settings['dateformat'], mktime(0, 0, 0, $visitreportdate[1], $visitreportdate[0], $visitreportdate[2]));
        }
        else {
            $visitreports[1]['formatteddate'] = date($core->settings['dateformat'], TIME_NOW);
        }

        $visitreports[1]['user'] = $core->user['displayName'];

        $pagetitle = $lang->previewvisitreport;

        eval("\$tools = \"".$template->get('crm_previewvisitreport_tools_finalize')."\";");
    }
    else {
        if(!isset($core->input['vrid'])) {
            redirect('index.php?module=crm/listvisitreports');
        }

        if($core->input['referrer'] == 'generate') {
            $vrid_where = ' vrid IN ('.$db->escape_string(implode(',', unserialize(base64_decode($core->input['vrid'])))).')';
        }
        else {
            $vrid_where = " vrid='".$db->escape_string($core->input['vrid'])."'";
        }
        $permissions = $core->user_obj->get_businesspermissions();

        $trasferedassignments = UsersTransferedAssignments::get_data(array('toUser' => $core->user['uid'], 'affid' => $permissions['affid']), array('returnarray' => true));
        if(is_array($trasferedassignments)) {
            foreach($trasferedassignments as $trasferedassignment) {
                $transfered_entities['cid'][] = $trasferedassignment->eid;
                $transfered_entities['uid'][] = $trasferedassignment->fromUser;
            }
        }
        $transfered_fields = array('cid', 'uid');
        foreach($transfered_fields as $transfered_field) {
            if(is_array($permissions[$transfered_field]) && is_array($transfered_entities[$transfered_field])) {
                $permissions[$transfered_field] = array_unique(array_merge($permissions[$transfered_field], $transfered_entities[$transfered_field]));
            }
        }
        unset($transfered_entities);
        $permissiontypes = array('affid' => 'affid', 'cid' => 'cid', 'uid' => 'vr.uid');
        foreach($permissiontypes as $type => $col) {
            if(isset($permissions[$type]) && !empty($permissions[$type])) {
                if(is_array($permissiontypes[$type])) {
                    $customers_extra_where .= ' AND '.$col.' IN ('.implode(',', $permissiontypes[$type]).')';
                }
            }
        }

//        if($core->usergroup['canViewAllCust'] == 0) {
//            $incustomers = implode(',', $core->user['customers']);
//            $customers_extra_where = ' AND cid IN ('.$incustomers.') ';
//        }
//        else {
//            if(isset($core->user['auditedaffids'])) {
//                $customers_extra_where = ' AND affid IN ('.implode(',', $core->user['auditedaffids']).')';
//                $customers_extra_where .= ' OR uid IN (Select uid from users WHERE reportsTo='.$core->user['uid'].')';
//            }
//            else {
//                $customers_extra_where = ' AND uid IN (Select uid FROM users WHERE reportsTo='.$core->user['uid'].')';
//            }
//        }

        $query = $db->query("SELECT * FROM ".Tprefix."visitreports WHERE{$vrid_where}{$customers_extra_where}");
        if($db->num_rows($query) == 0) {
            redirect('index.php?module=crm/listvisitreports');
        }
        $i = 1;
        $visitreports = array();
        while($visitreport_record = $db->fetch_assoc($query)) {
            if(empty($visitreport_record)) {
                continue;
            }
            $visitreports[$i] = $visitreport_record;
            /* if(!check_permissions($visitreports[$i])) {
              unset($visitreports[$i]);
              continue;
              } */
            $visitreports[$i]['spid'] = get_specificdata('visitreports_reportsuppliers', 'spid', 'spid', 'spid', '', 0, "vrid='{$visitreports[$i][vrid]}'");

            $visitreports[$i]['formatteddate'] = date($core->settings['dateformat'], $visitreports[$i]['date']);
            $visitreports[$i]['user'] = $db->fetch_field($db->query("SELECT displayName FROM ".Tprefix."users WHERE uid='{$visitreports[$i][uid]}'"), 'displayName');

            $visitreports[$i]['productLine'] = get_specificdata('visitreports_productlines', 'productLine', 'productLine', 'productLine', '', 0, "vrid='{$visitreports[$i][vrid]}'");

            $extra_where = getquery_entities_viewpermissions('suppliersbyaffid', $visitreports[$i]['affid'], '', 0, 'visitreports_comments');

            $comments_query = $db->query("SELECT * FROM ".Tprefix."visitreports_comments WHERE (vrid='{$visitreports[$i][vrid]}'{$extra_where[extra]}) OR (vrid='{$visitreports[$i][vrid]}' AND spid=0)");
            while($report_comment = $db->fetch_assoc($comments_query)) {
                $visitreports[$i]['comments'][$report_comment['spid']] = $report_comment;
            }

            if($core->input['incCompetitionDetails'] != 0 || !isset($core->input['incCompetitionDetails'])) {
                $visitreports[$i]['competitiondetail_numrows'] = 1;
                $competition_query = $db->query("SELECT * FROM ".Tprefix."visitreports_competition WHERE vrid='{$visitreports[$i][vrid]}'");
                while($competition = $db->fetch_array($competition_query)) {
                    $competitors[$i][$visitreports[$i]['competitiondetail_numrows']] = $competition;
                    $visitreports[$i]['competitiondetail_numrows'] ++;
                }
            }
            $i++;
        }

        $export_identifier = $core->input['identifier'];
        $tools = "<a href='".$_SERVER['REQUEST_URI']."&amp;action=exportpdf&amp;identifier={$export_identifier}' target='_blank'><img src='images/icons/pdf.gif' border='0' alt='{$lang->downloadpdf}'/></a>&nbsp;<a href='".$_SERVER['REQUEST_URI']."&amp;action=print&amp;identifier={$export_identifier}' target='_blank'><img src='images/icons/print.gif' border='0' alt='{$lang->printreport}'/></a>";
    }

    if(is_array($visitreports)) {
        foreach($visitreports as $key => $visitreport) {
            if(empty($visitreport)) {
                unset($visitreports[$key]);
                continue;
            }
            $visitreport['customerdetails'] = $db->fetch_assoc($db->query("SELECT e.*, c.name AS countryname
																	   FROM ".Tprefix."entities e LEFT JOIN ".Tprefix."countries c ON (c.coid=e.country)
																	   WHERE eid='".$db->escape_string($visitreport['cid'])."'"), "customername");
            $visitreports[$key]['customerName'] = $visitreport['customerdetails']['companyName'];

            if(is_array($visitreport['spid'])) {
                foreach($visitreport['spid'] as $k => $v) {
                    $visitreport['comments'][$v]['suppliername'] = $visitreport['suppliername'][] = $db->fetch_field($db->query("SELECT companyName AS suppliername FROM ".Tprefix."entities WHERE eid='".$db->escape_string($v)."'"), 'suppliername');
                }
            }

            if(is_array($visitreport['suppliername'])) {
                $reportsuppliers = implode('<br />', $visitreport['suppliername']);
            }
            // $visitreport['contactperson'] = $db->fetch_field($db->query("SELECT name AS contactperson FROM ".Tprefix."representatives WHERE rpid='".$db->escape_string($visitreport['rpid'])."'"), "contactperson");
            $contactperson = Representatives::get_data(array('rpid' => $visitreport['rpid']));
            if(is_object($contactperson)) {

                $visitreport['contactperson'] = '<a href="#" id="contactpersoninformation_'.base64_encode($contactperson->rpid).'_profiles/entityprofile_loadpopupbyid">'.$contactperson->name.'</a> - <a href = "mailto:'.$contactperson->email.'">'.$contactperson->email.'</a><br />';
            }
        }

        if(is_array($visitreport['productLine'])) {
            foreach($visitreport['productLine'] as $k => $v) {
                $visitreport['productline'][$v] = $db->fetch_field($db->query("SELECT title AS productline FROM ".Tprefix."productsegments WHERE psid='".$db->escape_string($v)."'"), "productline");
            }
        }
        if(is_array($visitreport['productline'])) {
            $reportproductlines = implode('<br />', $visitreport['productline']);
        }

        $cdetails_rowspan = 6;
        $accompaniedbyrow = '';
        if(isset($visitreport['sprpid']) && !empty($visitreport['sprpid'])) {
            $visitreport['accompaniedby'] = $db->fetch_field($db->query("SELECT name AS accompaniedby FROM ".Tprefix."representatives WHERE rpid='".$db->escape_string($visitreport['sprpid'])."'"), "accompaniedby");
            $cdetails_rowspan = 7;
            eval("\$accompaniedbyrow = \"".$template->get('crm_fillvisitreport_accompaniedbyrow')."\";");
        }

        if(!empty($visitreport['customerdetails']['addressLine1']) && empty($visitreports[1]['location'])) {
            $customerobj = new Customers($visitreports[1]['cid'], '', false);
            $customer_data = $customerobj->get();
            $visitreport['customerdetails']['addressDetails'] = $customer_data['addressLine1'].' - '.$customer_data['addressLine2'].' - '.$customer_data['city'].' - '.$customerobj->get_country()->get_displayname();
        }
        elseif(!empty($visitreports[1]['location'])) {
            $customer_location = new EntityLocations($visitreports[1]['location'], false);
            $visitreport['customerdetails']['addressDetails'] = $customer_location->get_displayname();
        }
        else {
            if($core->input['showLimitedCustDetails'] == 0) {
                if(!empty($visitreport['customerdetails']['addressLine1'])) {
                    $visitreport['customerdetails']['addressDetails'] .= $visitreport['customerdetails']['addressLine1'].', ';
                }

                if(!empty($visitreport['customerdetails']['city'])) {
                    $visitreport['customerdetails']['addressDetails'] .= $visitreport['customerdetails']['addressLine1'].', ';
                }

                if(!empty($visitreport['customerdetails']['phone1'])) {
                    $phone_details = "{$lang->phone}: {$visitreport[customerdetails][phone1]}";
                }
            }

            if(!empty($visitreport['customerdetails']['countryname'])) {
                $visitreport['customerdetails']['addressDetails'] .= $visitreport['customerdetails']['countryname'];
            }
        }

        parse_calltype($visitreport['type']);
        parse_callpurpose($visitreport['purpose']);

        parse_availabilityissues($visitreport['availabilityIssues']);
        parse_supplystatus($visitreport['supplyStatus']);

        if($core->input['incCompetitionDetails'] != 0 || !isset($core->input['incCompetitionDetails'])) {
            $aggression_box = $competitiondetails_list = '';
            if(is_array($competitors[$key])) {
                foreach($competitors[$key] as $v) {
                    $class = alt_row($class);

                    $v['competitorName'] = ucfirst($v['competitorName']);

                    switch($v['aggressionLevel']) {
                        case '1':
                            $v['aggressionLevel'] = $lang->extremeaggression;
                            break;
                        case '2':
                            $v['aggressionLevel'] = $lang->highaggression;
                            break;
                        case '3':
                            $v['aggressionLevel'] = $lang->mildaggression;
                            break;
                        default: break;
                    }

                    $v['productName'] = $db->fetch_field($db->query("SELECT name AS productname FROM ".Tprefix."products WHERE pid='".$db->escape_string($v['pid'])."'"), "productname");

                    parse_availabilityissues($v['availabilityIssues']);
                    parse_supplystatus($v['supplyStatus']);

                    extract($v);
                    eval("\$competitiondetails_list .= \"".$template->get('crm_visitreport_aggressionbox_row')."\";");
                }
                eval("\$aggression_box = \"".$template->get('crm_visitreport_aggressionbox')."\";");
            }
        }

        if(is_array($visitreport['spid'])) {
            foreach($visitreport['spid'] as $spidkey => $spidval) {
                foreach($visitreport['comments'][$spidval] as $key => $value) {
                    $visitreport['comments'][$spidval][$key] = $core->sanitize_inputs($value, array('method' => 'striponly', 'removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                }
            }
        }
        //array_walk_recursive($visitreport, '$core->sanitize_inputs');
        array_walk_recursive($visitreport, 'fix_newline');
        array_walk_recursive($visitreport, 'parse_ocode');

        if($core->input['incVisitDetails'] != 0 || !isset($core->input['incVisitDetails'])) {
            $visitdetails = '<div class = "subtitle">'.$lang->visitdetails.'</div>';
            if(is_array($visitreport['spid'])) {
                foreach($visitreport['spid'] as $k => $v) {
                    if(empty($v) && $v != 0) {
                        continue;
                    }

                    /* if($core->usergroup['canViewAllSupp'] == 0)  {
                      if(!in_array($k, $core->user['suppliers']['eid'])) {
                      continue;
                      }
                      } */

                    eval("\$visitdetails .= \"".$template->get('crm_visitreport_visitdetails')."\";");
                }
            }
        }

        if($core->input['incCommentsCompetition'] != 0 || !isset($core->input['incCommentsCompetition'])) {
            $competitioncomments = '<div class = "subtitle">'.$lang->commentsoncompetition.'</div>';
            if(is_array($visitreport['spid'])) {
                foreach($visitreport['spid'] as $k => $v) {
                    if(empty($v) && $v != 0) {
                        continue;
                    }
                    /* if($core->usergroup['canViewAllSupp'] == 0)  {
                      if(!in_array($k, $core->user['suppliers']['eid'])) {
                      continue;
                      }
                      } */
                    eval("\$competitioncomments .= \"".$template->get('crm_visitreport_competitioncomments')."\";");
                }
            }
        }

        /* Parse visit report MIdata timeline --START */
        $lang->load('profiles_meta');
        $maktintl_mainobj = new MarketIntelligence();
        $miprofile = $maktintl_mainobj->get_miprofconfig_byname('latestcustomersumbyproduct');
        $miprofile['next_miprofile'] = 'allprevious';
        $maktintl_objs = $maktintl_mainobj->get_marketintelligence_timeline(array('cid' => $visitreport['cid'], 'vrid' => $visitreport['vrid']), $miprofile);
        if(is_array($maktintl_objs)) {
            $core->input['module'] = 'profiles/entityprofile';
            foreach($maktintl_objs as $mktintldata) {
                $mktintldata['tlidentifier']['id'] = 'tlrelation-'.$visitreport['cid'];
                $mktintldata['tlidentifier']['value'] = array('cid' => $visitreport['cid']);

                $detailmarketbox .= $maktintl_mainobj->parse_timeline_entry($mktintldata, $miprofile, '', '', array('viewonly' => true));
            }

            $viewall_button = '<div style = "display:block; padding:25px;"><input type = "button" class = "button" value = "View All Market data" onClick = "window.open(\'index.php?module=profiles/entityprofile&amp;eid='.$mktintldata['cid'].'#misection\')" /></div>';
            eval("\$visitdetails_fields_mktidata = \"".$template->get('crm_fillvisitreport_visitdetailspage_marketdata')."\";");
        }

        /* Parse visit report MIdata timeline --END */
        eval("\$visitreportspages .= \"".$template->get('crm_visitreport')."\";");
        if($core->input['referrer'] != 'fill') {
            $session->set_phpsession(array("visitreports_{$export_identifier}" => $visitreportspages));
        }
    }

    if(empty($pagetitle)) {
        if(count($visitreports) == 1) {
            $pagetitle = $visitreports[1]['customerName'].' / '.$visitreports[1]['formatteddate'];
        }
        else {
            $pagetitle = $lang->aggregatedvisitreports;
        }
    }

    /* Get list of previous reports - START */
    if(count($visitreports) == 1 && $core->input['referrer'] != 'fill') {
        if($core->usergroup ['canViewAllSupp'] == 0) {
            $prev_reports_query_where = ' AND uid = '.$core->user['uid'];
        }
        $prev_reports_query = $db->query("SELECT vrid, date, type FROM ".Tprefix."visitreports WHERE cid='{$visitreports[1][cid]
                }' AND vrid!={$visitreports[1][vrid]}{$prev_reports_query_where} ORDER BY DATE DESC");
        if($db->num_rows($prev_reports_query) > 0) {
            $prev_visitreports_list = '<span class = "subtitle">'.$lang->listofvisitreports.':</span> <ul>';
            while($prev_visitreport = $db->fetch_assoc($prev_reports_query)) {
                parse_calltype($prev_visitreport['type']);
                $prev_visitreports_list .= '<li><a href = "index.php?module=crm/previewvisitreport&referrer=list&vrid='.$prev_visitreport['vrid'].'">'.date($core->settings['dateformat'], $prev_visitreport['date']).' - '.$prev_visitreport['type'].'</a></li>';
            }
            $prev_visitreports_list .= '</ul><hr />';
        }
    }
    /* Get list of previous reports - END */
    eval("\$visitreportpage = \"".$template->get('crm_previewvisitreport')."\";");
    output_page($visitreportpage);
}
else {
    if($core->input['action'] == 'exportpdf' || $core->input['action'] == 'print') {
        if($core->input['action'] == 'print') {
            $show_html = 1;
            $content .= "<link href=' {
                    $core->settings[rootdir]}/report_printable.css' rel='stylesheet' type='text/css' />";
            $content .= "<script language='javascript' type='text/javascript'>window.print();</script>";
        }
        else {
            $content = "<link href='css/styles.css' rel='stylesheet' type='text/css' />";
            $content .= "<link href='css/report.css' rel='stylesheet' type='text/css' />";
        }

        //	$export_id = unserialize(base64_decode($core->input['identifier']));
        $content .= $session->get_phpsession("visitreports_{$core->input[identifier]}");

        ob_end_clean();
        require_once ROOT.'/'.INC_ROOT.'html2pdf/html2pdf.class.php';
        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->pdf->SetTitle("{$report[supplier]}", true);
        $html2pdf->WriteHTML($content, $show_html);
        $html2pdf->Output('visitreports_'.$core->input['identifier'].'.pdf');
        $session->destroy_phpsession();
    }
}
function parse_supplystatus(&$value) {
    global $lang;

    switch($value) {
        case '1':
            $value = $lang->regular;
            break;
        case '2':
            $value = $lang->onspotbasis;
            break;
        case '3':
            $value = $lang->usedto;
            break;
        case '4':
            $value = $lang->never;
            break;
        default: break;
    }
}

function parse_availabilityissues(&$value) {
    global $lang;

    switch($value) {
        case '1':
            $value = $lang->available;
            break;
        case '2':
            $value = $lang->underspotshortage;
            break;
        case '3':
            $value = $lang->usuallyundershortage;
            break;
        default: break;
    }
}

function parse_calltype(&$value) {
    global $lang;

    switch($value) {
        case '1':
            $value = $lang->facetoface;
            break;
        case '2':
            $value = $lang->telephonecall;
            break;
        default: break;
    }
}

function parse_callpurpose(&$value) {
    global $lang;

    switch($value) {
        case '1':
            $value = $lang->followup;
            break;
        case '2':
            $value = $lang->service;
            break;
        case '3':
            $value = $lang->prospective;
            break;
        default: break;
    }
}

?>