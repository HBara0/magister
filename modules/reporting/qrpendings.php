<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: qrpendings.php
 * Created:        @zaher.reda    May 27, 2015 | 10:55:28 AM
 * Last Update:    @zaher.reda    May 27, 2015 | 10:55:28 AM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $entities = Entities::get_data(array('noQReportReq' => 0, 'noQReportSend' => 0, 'type' => 's'), array('order' => Entities::DISPLAY_NAME, 'returnarray' => true));

    if(!is_array($entities)) {
        $page['content'] = $lang->na;
        eval("\$reportviewspage = \"".$template->get('general_container')."\";");
        output_page($reportviewspage);
        exit;
    }

    if(!isset($core->input['year'], $core->input['quarter'])) {
        $quarterinfo = currentquarter_info();
        $core->input['quarter'] = $quarterinfo['quarter'];
        $core->input['year'] = $quarterinfo['year'];
    }
    $output = '<h3>Pending Reports: Q'.$core->input['quarter'].' '.$core->input['year'].'</h3>';
    foreach($entities as $entity) {
        $query = $db->query('SELECT * FROM reports WHERE spid='.$entity->get_id().' AND status!=1 AND quarter='.intval($core->input['quarter']).' AND year='.intval($core->input['year']));

        if($db->num_rows($query) > 0) {
            $output .= '<br /><div class="subtitle">'.$entity->get_displayname().'</div>';
            while($report = $db->fetch_assoc($query)) {
                $contributors_output = $report_status = $report_status_comma = '';
                $assignedemployees = null;

                $affiliate = new Affiliates($report['affid']);
                $output .= $affiliate->get_displayname();

                if($report['prActivityAvailable'] == 0) {
                    $report_status = 'No products activity';
                    $report_status_comma = ', ';
                }

                if($report['mktReportAvailable'] == 0) {
                    $report_status .= $report_status_comma.'No market report';
                }
                if(!empty($report_status)) {
                    $output .= ' - '.$report_status;
                }

                $contributors = ReportContributors::get_data(array('rid' => $report['rid'], 'isDone' => 0), array('returnarray' => true));
                if(is_array($contributors)) {
                    foreach($contributors as $contributor) {
                        $user = $contributor->get_user();
                        $contributors_output .= ' <a href="mailto:'.$user->email.'">'.$user->get_displayname().'</a>';
                    }

                    $output .= '. Pending users: '.$contributors_output;
                }
                else {
                    $assignedemployees = AssignedEmployees::get_data(array('eid' => $report['spid'], 'affid' => $report['affid'], 'uid' => ' uid NOT IN (SELECT uid FROM users WHERE gid=7)'), array('operators' => array('uid' => 'CUSTOMSQLSECURE'), 'returnarray' => true));
                    if(!is_array($assignedemployees) || empty($assignedemployees)) {
                        $output .= '. <strong>No one assigned any longer.</strong>';
                    }
                    else {
                        if($report['mktReportAvailable'] == 1 && $core->input['markasfinalized'] == 1) {
                            $db->update_query('reports', array('status' => 1), 'rid='.$report['rid']);
                            $output .= '. <strong>Status updated</strong>';
                        }
                        elseif($report['mktReportAvailable'] == 1) {
                            $output .= '. <em>Consider finalization</em>';
                        }
                        else {
                            if($core->input['activatereminders'] == 1) {

                            }
                            $output .= '. <em>Consider activating reminders if not being sent</em>';
                        }
                    }
                }

                $output .= '<br />';
            }
        }
    }

    $page['content'] = $output;

    eval("\$reportviewspage = \"".$template->get('general_container')."\";");
    output_page($reportviewspage);
}
?>