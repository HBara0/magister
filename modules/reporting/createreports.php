<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Create reports
 * $module: reporting
 * $id: createreports.php
 * Last Update: @zaher.reda 	June 14, 2010 | 04:55 PM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canCreateReports'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

//$lang->load("reporting_generatereport");
if(!$core->input['action']) {
    $quarter = currentquarter_info();
    $cache = new Cache();

    $query = $db->query("SELECT ae.*, a.name, e.companyName
						FROM ".Tprefix."affiliatedentities ae
						JOIN ".Tprefix."entities e ON (e.eid=ae.eid)
						JOIN ".Tprefix."affiliates a ON (ae.affid=a.affid)
						WHERE e.type='s' AND e.approved=1 AND e.noQReportReq=0
						AND (ae.affid, ae.eid) NOT IN (SELECT r.affid, r.spid FROM ".Tprefix."reports r WHERE r.type='q' AND r.quarter='{$quarter[quarter]}' AND r.year='{$quarter[year]}')
						ORDER BY a.name ASC, e.companyName ASC");
    while($report = $db->fetch_array($query)) {
        if($cache->iscached('entities', $report['eid'])) {
            $entity[$report['eid']] = $cache->data['entities'][$report['eid']];
        }
        else {
            $entity[$report['eid']] = new Entities($report['eid']);
            $cache->add('entities', $entity[$report['eid']], $report['eid']);
        }

        $reports_list_disabled = '';
        if($entity[$report['eid']]->has_assignedusers(array($report['affid'])) == false) {
            $reports_list_disabled = '  disabled="disabled"';
        }
        $reports_list .= '<option value="'.$report['affid'].'_'.$report['eid'].'"'.$reports_list_disabled.'>'.$report['name'].' - '.$report['companyName'].'</option>';
    }

    $selected[$quarter['quarter']] = " selected='selected'";
    eval("\$createpage = \"".$template->get('reporting_createreports')."\";");
    output_page($createpage);
}
else {
    if($core->input['action'] == 'get_reports') {
        $cache = new Cache();
        if(isset($core->input['quarter'], $core->input['year'])) {
            $quarter = currentquarter_info();
            /* if($core->input['year'] > $quarter['year']) {
              echo $lang->yeargreaterthancurrent;
              exit;
              } */

            $query = $db->query("SELECT ae.*, a.name, e.companyName
							FROM ".Tprefix."affiliatedentities ae
							JOIN ".Tprefix."entities e ON (e.eid=ae.eid)
							JOIN ".Tprefix."affiliates a ON (ae.affid=a.affid)
							WHERE e.type='s' AND e.approved=1 AND e.noQReportReq=0
							AND (ae.affid, ae.eid) NOT IN (SELECT r.affid, r.spid FROM ".Tprefix."reports r WHERE r.type='q' AND r.quarter='".$db->escape_string($core->input['quarter'])."' AND r.year='".$db->escape_string($core->input['year'])."')
							ORDER BY a.name ASC, e.companyName ASC");
            while($report = $db->fetch_array($query)) {
                if($cache->iscached('entities', $report['eid'])) {
                    $entity[$report['eid']] = $cache->data['entities'][$report['eid']];
                }
                else {
                    $entity[$report['eid']] = new Entities($report['eid']);
                    $cache->add('entities', $entity[$report['eid']], $report['eid']);
                }

                $reports_list_disabled = '';
                if($entity[$report['eid']]->has_assignedusers(array($report['affid'])) == false) {
                    $reports_list_disabled = '  disabled="disabled"';
                }
                $reports_list .= '<option value="'.$report['affid'].'_'.$report['eid'].'"'.$reports_list_disabled.'>'.$report['name'].' - '.$report['companyName'].'</option>';
            }
            if(empty($reports_list)) {
                $reports_list = '<option>'.$lang->allreportscreated.'</option>';
            }
            echo $reports_list;
        }
    }
    elseif($core->input['action'] == 'do_perform_createreports') {
        if(is_array($core->input['reports'])) {
            foreach($core->input['reports'] as $key => $val) {
                $ids = explode('_', $val);

                $newreport = array(
                        'quarter' => $db->escape_string($core->input['quarter']),
                        'year' => $db->escape_string($core->input['year']),
                        'identifier' => md5($db->escape_string($ids[1].$core->input['quarter'].$core->input['year'])),
                        'affid' => $ids[0],
                        'spid' => $ids[1],
                        'initDate' => TIME_NOW,
                        'createdBy' => $core->user['uid'],
                        'createdOn' => TIME_NOW,
                        'status' => 0
                );
                $db->insert_query('reports', $newreport);
            }
            output_xml("<status>true</status><message>{$lang->reportscreated}</message>");
        }
    }
}
?>