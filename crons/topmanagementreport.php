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
if($_REQUEST['authkey'] == 'kia5ravb$op09dj4a!xhegalhj') {
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
                    if($emp_count < 5) {
                        $user = Users::get_data(array('uid' => $affiliated_employee->uid));
                        if(is_object($user)) {
                            $employees[$affiliate->affid] .= $user->get_displayname().'<br/>';
                            $emp_count ++;
                        }
                    }
                }
                $employees[$affiliate->affid] .='<small><a href="'.DOMAIN.'/users.php?action=userslist&amp;filters[allenabledaffiliates][]='.$affiliate->affid.'" target="_blank">See all employees</a></small>';
                $query = $db->query("SELECT * FROM ".Tprefix."userhrinformation WHERE uid IN (".implode(',', $uids).") AND (leaveDate > ".strtotime('01-01-'.(date('Y') - 1))." OR leaveDate=0) AND joinDate  <".strtotime('01-01-'.date('Y').''));
                $employees_count_lastyear[$affiliate->affid] = $employees_count_lastyear[$affiliate->country] = $db->num_rows($query);
                $employees_count[$affiliate->affid] = $employees_count[$affiliate->country] = count($affiliated_employees);
                unset($uids);
            }
            $table['employeespercountry'] .='<tr style="background-color: #F2FAED;"><td>'.$affiliate->get_displayname().'</td><td style="padding:10px;">'.$employees[$affiliate->affid].'</td><td style="align:right;">'.$employees_count[$affiliate->affid].'</td><td>'.$employees_count_lastyear[$affiliate->affid].'</td></tr>';

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
            $table['maplegend'] .='<tr style="background-color: #F2FAED;"><td>'.$key.'</td><td>'.$value.'</td><td>'.$percformatter->format($value / $total).'</td></tr>';
        }
        $table['maplegend'] .= '<tr style="background-color:#92D050;"><td>'.$lang->total.'</td><td>'.$total.'</td><td>'.$percformatter->format($total / $total).'</td></tr></table>';
    }

// Employees per Segment
    $employeessegment = EmployeeSegments::get_data('uid IN (SELECT uid FROM users WHERE gid !=7)', array('returnarray' => true));
    if(is_array($employeessegment)) {
        foreach($employeessegment as $employee_segment) {
            $segmentemployees[$employee_segment->psid][] = $employee_segment;
        }
    }
    $segments = ProductsSegments::get_data(array('isActive' => 1), array('returnarray' => true));
    if(is_array($segments)) {
        $table['employeespersegment'] = '<table width="50%"><tr style="background-color:#92D050;"><th style="width:40%">'.$lang->segment.'</th><th style="width:20%">'.$lang->employeescount.'</th></tr>';
        foreach($segments as $segment) {
            $table['employeespersegment'] .='<tr style="background-color: #F2FAED;"><td>'.$segment->title.'</td><td style="align:center">'.count($segmentemployees[$segment->psid]).'</td></tr>';
        }
        $table['employeespersegment'] .='</table>';
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
}