<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: topmanagementreport.php
 * Created:        @rasha.aboushakra    Nov 6, 2015 | 9:24:15 AM
 * Last Update:    @rasha.aboushakra    Nov 6, 2015 | 9:24:15 AM
 */

require '../inc/init.php';
//if($_REQUEST['authkey'] == 'kia5ravb$op09dj4a!xhegalhj') {
$lang = new Language('english');
$lang->load('topmanagementreport');
$affiliates = Affiliates::get_affiliates(array('name' => "name LIKE '%orkila%'", 'isActive' => 1), array('simple' => false, 'returnarray' => true, 'operators' => array('name' => CUSTOMSQLSECURE)));


if(is_array($affiliates)) {
//Affiliates Employees Count
    $table['employeespercountry'] = '<table style="width:50%;"><tr style="background-color:#92D050;"><th style="width:20%">'.$lang->affiliate.'</th><th style="width:20%">'.$lang->employees.'</th><th style="width:12%">'.$lang->employeescount.'</th><th style="width:12%">'.$lang->employeescountlastyear.'</th></tr>';
    foreach($affiliates as $affiliate) {
        $employees_count[$affiliate->affid] = $employees_count[$affiliate->country] = 0;
        $where = 'affid='.$affiliate->affid.' AND uid IN (SELECT uid FROM users WHERE gid !=7) AND isMain=1';
        $affiliated_employees = AffiliatedEmployees::get_data($where, array('returnarray' => true));
        if(is_array($affiliated_employees)) {
            $emp_count = 0;
            foreach($affiliated_employees as $affiliated_employee) {
                $uids[] = $affiliated_employee->uid;
            }

            $management_fields = array('supervisor', 'generalManager', 'hrManager', 'finManager');
            foreach($management_fields as $management_field) {
                $management_ids[] = $affiliate->$management_field;
            }
            $management_ids = array_unique($management_ids);
            foreach($management_ids as $management_id) {
                $user = Users::get_data(array('uid' => $management_id));
                if(is_object($user)) {
                    $employees[$affiliate->affid] .= $user->get_displayname().'<br/>';
                }
            }
            unset($management_ids);
            $employees[$affiliate->affid] .='<small><a href="'.DOMAIN.'/users.php?action=userslist&amp;filters[allenabledaffiliates][]='.$affiliate->affid.'" target="_blank">See all employees</a></small>';
            $query = $db->query("SELECT * FROM ".Tprefix."userhrinformation WHERE uid IN (".implode(',', $uids).") AND (leaveDate > ".strtotime('01-01-'.(date('Y') - 1))." OR leaveDate=0) AND joinDate  <".strtotime('01-01-'.date('Y').''));
            $employees_count_lastyear[$affiliate->affid] = $employees_count_lastyear[$affiliate->country] = $db->num_rows($query);
            $employees_count[$affiliate->affid] = $employees_count[$affiliate->country] = count($affiliated_employees);
            unset($uids);
        }
        $table['employeespercountry'] .='<tr style="background-color: #F2FAED;"><td>'.$affiliate->get_displayname().'</td><td style="padding:10px;">'.$employees[$affiliate->affid].'</td><td style="text-align:center;">'.$employees_count[$affiliate->affid].'</td><td style="text-align:center;">'.$employees_count_lastyear[$affiliate->affid].'</td></tr>';

        $country = new Countries($affiliate->country);
        $mapdata[$country->get_displayname()]+= $employees_count[$affiliate->country];
        $maplegend[$affiliate->get_displayname()] = $employees_count[$affiliate->country];
        $total +=$employees_count[$affiliate->country];
    }
    $table['employeespercountry'] .= '</table>';
}



$count['orkilacompanies'] = count($affiliates); //number of orkila affiliates
$count['orkilacountries'] = count($mapdata); // number of orkila countries /counted from map data
$table['nboforkilacompanies'] = '<table width="50%"><tr><th style="background-color:#92D050;width:40%;text-align:left;padding-left:10px">'.$lang->nboforkilacompanies.'</th><th style="background-color: #F2FAED;width:20%">'.$count['orkilacompanies'].'</th></tr>';
$table['nboforkilacompanies'] .= '<tr><th style="background-color:#92D050;width:40%;text-align:left;padding-left:10px">'.$lang->nbofcountries.'</th><th style="background-color: #F2FAED;width:20%">'.$count[orkilacountries].'</th></tr></table>';

// MAP Legend
$mapdata = json_encode($mapdata);
if(is_array($maplegend)) {
    asort($maplegend);
    $percformatter = new NumberFormatter('EN_en', NumberFormatter::PERCENT);
    $table['maplegend'] = '<table><tr style="background-color:#92D050;"><th>'.$lang->affiliate.'</th><th>'.$lang->employeescount.'</th><th>%</th></tr>';
    foreach($maplegend as $key => $value) {
        $table['maplegend'] .='<tr style="background-color: #F2FAED;"><td>'.$key.'</td><td style="text-align:center;">'.$value.'</td><td style="text-align:center;">'.$percformatter->format($value / $total).'</td></tr>';
    }
    $table['maplegend'] .= '<tr style="background-color:#92D050;"><td>'.$lang->total.'</td><td style="text-align:center;">'.$total.'</td><td style="text-align:center;">'.$percformatter->format($total / $total).'</td></tr></table>';
}

// Employees per Segment
$employeessegment = EmployeeSegments::get_data('uid IN (SELECT uid FROM users WHERE gid !=7)', array('returnarray' => true));
if(is_array($employeessegment)) {
    foreach($employeessegment as $employee_segment) {
        $segmentemployees[$employee_segment->psid][] = $employee_segment;
    }
}
$segments = ProductsSegments::get_data(array('isActive' => 1), array('returnarray' => true, 'order' => array('by' => 'title', 'SORT' => 'ASC')));
if(is_array($segments)) {
    $table['employeespersegment'] = '<table width="50%"><tr style="background-color:#92D050;"><th style="width:40%">'.$lang->segment.'</th><th style="width:20%">'.$lang->employeescount.'</th></tr>';
    foreach($segments as $segment) {
        $table['employeespersegment'] .='<tr style="background-color: #F2FAED;"><td>'.$segment->title.'</td><td style="text-align:center">'.count($segmentemployees[$segment->psid]).'</td></tr>';
    }
    $table['employeespersegment'] .='</table>';
}
require_once ROOT.INC_ROOT.'integration_config.php';
$period['from'] = TIME_NOW;
$integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client']);
$filters = "(dateinvoiced BETWEEN '".date('Y-01-01 00:00:00', $period['from'])."' AND '".date('Y-12-30 00:00:00', $period['from'])."')";
$invoices = $integration->get_saleinvoices($filters);

if(is_array($invoices)) {
    foreach($invoices as $invoice) {
        $customer_obj = $invoice->get_customer();
        if(is_object($customer_obj)) {
            $location = $customer_obj->get_bplocation();
            if(is_object($location)) {
                $country = $location->get_location()->get_country();
                if(is_object($country)) {
                    $countries[$country->c_country_id] = $country->name;
                }
            }
        }
        $orgs[] = $invoice->ad_org_id;
    }
    if(is_array($countries)) {
        $table['activesalesorgs'] = '<table style="width:50%;"><tr style="background-color:#92D050;"><th style="width:20%" colspan="2">'.$lang->countrieswithactivesales.'</th></tr>';

        foreach($countries as $country) {
            $table['activesalesorgs'].='<tr><td  colspan="2">'.$country.'</td></tr>';
        }
        $table['activesalesorgs'].='<tr style="background-color:#92D050;"><td>'.$lang->total.'</td><td>'.count($countries).'</td></tr>';
        $table['activesalesorgs'].='</table>';
    }
}

$evettypes = array('exhibition', 'seminar');
foreach($evettypes as $evettype) {
    $eventtype = CalendarEventTypes::get_data(array('name' => $evettype));
    if(is_object($eventtype)) {
        $from = strtotime(date('Y-01-01 00:00:00', TIME_NOW));
        $to = strtotime(date('Y-12-30 00:00:00', TIME_NOW));
        $where = ' type='.$eventtype->cetid.' AND (fromDate BETWEEN '.$from.' AND '.$to.') OR ( toDate BETWEEN '.$from.' AND '.$to.') ORDER BY fromDate ASC';
        $events = Events::get_data($where, array('returnarray' => true));
        if(is_array($events)) {
            foreach($events as $event) {
                $event->parse_dates();
                $allevents[$type][$event->month][] = $event;
            }
            $table[$evettype] = '<table style="width:50%;"><tr style="background-color:#92D050;"><th style="width:20%" colspan="3">'.$lang->$evettype.'</th></tr>';
            foreach($allevents[$type] as $month => $events) {
                $table[$evettype].='<tr style="background-color: #F2FAED;"><td colspan="3">'.$month.'</td><tr>';
                foreach($events as $event) {
                    $table[$evettype].='<tr><td style = "border-bottom: 1px dashed #CCCCCC;">&nbsp;</td><td style = "border-bottom: 1px dashed #CCCCCC;">'.$event->fromDate_output.' '.$event->toDate_output.'</td><td style = "border-bottom: 1px dashed #CCCCCC;">'.$event->get_displayname().' '.$event->boothNum.'<br/><small>'.$event->place.'</small></td></tr>';
                }
            }
            $table[$evettype].='</table>';
        }
    }
}


eval("\$topmanagementreport=\"".$template->get('topmanagementreport')."\";");
output($topmanagementreport);

$email_data = array(
        'to' => 'christophe.sacy@orkila.com',
        'from_email' => $core->settings['adminemail'],
        'from' => 'OCOS Mailer',
        'subject' => 'Top Management Report',
        'message' => $topmanagementreport
);
$mail = new Mailer($email_data, 'php');


