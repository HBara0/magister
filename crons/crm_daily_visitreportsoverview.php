<?php
require '../inc/init.php';

/* Employee ID => Excp Supervisor ID */
$excp_notifications = array(21 => 20, 27 => 20);

$timeframe['from'] = strtotime('today');
$timeframe['to'] = strtotime('tomorrow');

$query = $db->query("SELECT vr.*, u.displayName AS employeename, u.reportsTo, e.companyName as customer, aff.vrAlwaysNotify
					FROM ".Tprefix."visitreports vr JOIN ".Tprefix."users u ON (u.uid=vr.uid) 
					JOIN ".Tprefix."entities e ON (e.eid=vr.cid)
					JOIN ".Tprefix."affiliates aff ON (aff.affid=vr.affid)
					WHERE finishDate BETWEEN {$timeframe[from]} AND {$timeframe[to]} ORDER BY uid ASC, cid ASC");

if($db->num_rows($query) > 0) {
	while($report = $db->fetch_assoc($query)) {
		$report['date_output'] = date($core->settings['dateformat'], $report['date']);
		
		if(!isset($cache['visitreport_type'][$report['type']]) || empty($cache['visitreport_type'][$report['type']])) {
			$cache['visitreport_type'][$report['type']] = parse_calltype($report['type']);
		}
		$report['type_output'] = $cache['visitreport_type'][$report['type']];
		
		$reports[$report['reportsTo']][$report['uid']][$report['vrid']] = $report;
		
		/* Parse supervisors who are always notified - START */
		if(!empty($report['vrAlwaysNotify'])) {
			$report['vrAlwaysNotify'] = unserialize($report['vrAlwaysNotify']);
			if(is_array($report['vrAlwaysNotify'])) {
				foreach($report['vrAlwaysNotify'] as $uid) {
					$reports[$uid][$report['uid']][$report['vrid']] = $report;	
				}
			}
		}
		/* Parse supervisors who are always notified - START */
		
		/* Temporary Solution for some employees */
		if(array_key_exists($report['uid'], $excp_notifications)) {
			$reports[$excp_notifications[$report['uid']]][$report['uid']][$report['vrid']] = $report;
		}
	}
}

if(is_array($reports)) {
	foreach($reports as $supervisor => $reports_users) {
		$supervisor_info = $db->fetch_assoc($db->query('SELECT displayName, email FROM '.Tprefix.'users WHERE uid='.$supervisor));
		if(empty($supervisor_info['email'])) {
			continue;
		}
		
		foreach($reports_users as $uid => $reports) {
			$user_section_parsed = false;
			$message_user_reports = '';
			foreach($reports as $vrid => $report) {
				if($user_section_parsed == false) {
					$user_section_parsed = true;
					$message_user_reports .= '<strong>'.$report['employeename'].'</strong><ul>';
				}
				$message_user_reports .= '<li><a href="'.DOMAIN.'/index.php?module=crm/previewvisitreport&referrer=list&vrid='.$vrid.'">'.$report['customer'].' - '.$report['type_output'].' ('.$report['date_output'].')</a></li>';
			}
			$message_user_reports .= '</ul>';
		}
		
		$message_output = '<html><head><title>Visit Reports Overview</title></head><body>Hello '.$supervisor_info['displayName'].',<br />';
		$message_output .= 'Please find below the visit reports pertaining to your subordinates which were completed today:<br />'.$message_user_reports.'</body></html>';
	
		$email_data = array(
			'from_email'  => $core->settings['adminemail'],
			'from'	   => 'OCOS Mailer',
			'subject'	=> 'Visit Reports Overview - '.date($core->settings['dateformat'], TIME_NOW),
			'message'	=> $message_output,
			'to'		=> $supervisor_info['email']
		);

		$mail = new Mailer($email_data, 'php');
			
		if($mail->get_status() === true) {
			$log->record('dailyvisitreportsoverview', $email_data['to']);
			$result['successfully'][]= $email_data['to'];
		}
		else
		{
			$result['error'][] = $email_data['to'];
		}
	}	
}

function parse_calltype($value) {	
	$lang = new Language('english');
	$lang->load('crm_visitreport');
	switch($value) {
		case '1': 
				return $lang->facetoface;
				break;
		case '2':
				return $lang->telephonecall;
				break;
		default: break;
	}
}
?>