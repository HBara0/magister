<?php
require '../inc/init.php';

$time_details = getdate(TIME_NOW);

if($time_details['mon'] == 12) {
	$addtime_query_string = ' OR ((year=0 OR year='.($time_details['year']+1).') AND month=1)';	
}
$query = $db->query("SELECT * FROM ".Tprefix."holidays WHERE ((year=0 OR year={$time_details[year]} OR year=(".($time_details['year']+1).")) AND month>={$time_details[mon]}){$addtime_query_string}");
while($holiday = $db->fetch_assoc($query)) {	
	if($holiday['year'] == 0) {
		$holiday['year'] = $time_details['year'];
		if($time_details['mon'] == 12 && $holiday['mon'] == 1) {
			$holiday['year'] == $time_details['year']+1;
		}
	}
	
	if($holiday['day'] < $time_details['mday'] && $holiday['month'] <= $time_details['mon'] && $holiday['year'] == $time_details['year']) {
		continue;
	}

	/*if(date('W', TIME_NOW) != date('W', mktime(1,0,0, $holiday['month'], $holiday['day'], $holiday['year']))) {
		continue;
	}*/
	if(((mktime($time_details['hours'], $time_details['minutes'], $time_details['seconds'], $holiday['month'], $holiday['day'], $holiday['year'])-TIME_NOW)/60/60/24) == 5) {
		$holidays[$holiday['affid']][] = $holiday;
	}
	
	/* Check for exceptions - START */
	$exceptions = get_specificdata('holidaysexceptions', array('heid', 'uid'), 'heid', 'uid', '', 0, 'hid='.$holiday['hid']);
	if(is_array($exceptions) && !empty($exceptions)) {
		$holiday_employees = $db->query("SELECT email 
										FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees ae ON (ae.uid=u.uid) 
										WHERE isMain=1 AND affid={$holiday[affid]} AND u.uid NOT IN (".implode(', ', $exceptions).") AND gid!=7");
		while($holiday_employee = $db->fetch_assoc($holiday_employees)) {
			$mailinglists[$holiday['affid']]['email'][] = $holiday_employee['email'];	
		}
	}
	/* Check for exceptions - END */
	
/*	if((date('z', mktime($time_details['hours'], $time_details['minutes'], $time_details['seconds'], $holiday['month'], $holiday['day'], $holiday['year'])) - date('z', TIME_NOW)) == 5) {
		$holidays[$holiday['affid']][] = $holiday;
	}*/
}

if(!is_array($holidays)) {
	exit;
}

$affids = array_keys($holidays);

$query = $db->query("SELECT affid, name, mailingList FROM ".Tprefix."affiliates WHERE affid IN (".implode(',', $affids).")");
while($affiliate = $db->fetch_assoc($query)) {
	if(!isset($mailinglists[$affiliate['affid']]) || empty($mailinglists[$affiliate['affid']])) {
		$mailinglists[$affiliate['affid']]['email'] = $affiliate['mailingList'];
	}
	$mailinglists[$affiliate['affid']]['name'] = $affiliate['name'];
}

foreach($holidays as $affid => $holidayslist) {
	$email_data = array(
		'to'		 => $mailinglists[$affid]['email'],
		'from_email'  => $core->settings['adminemail'],
		'from'	   => 'OCOS Mailer',
		'subject'	=> 'Your upcoming holidays'
	);
	
	$email_data['message'] = 'Following are the upcoming holidays of '.$mailinglists[$affid]['name'].': <ul>';
	foreach($holidayslist as $key => $val) {
		$email_data['message'] .= '<li>'.date('l, F j', mktime(0, 0, 0, $val['month'], $val['day'], $val['year'])).' - '.$val['title'].', '.$val['numDays'].' day(s).</li>';
	}
	$email_data['message'] .= '</ul>';
		//print_r($email_data);
	$mail = new Mailer($email_data, 'php');
}
$log->record($mailinglists);
?>