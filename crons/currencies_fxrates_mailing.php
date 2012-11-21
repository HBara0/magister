<?php
require '../inc/init.php';
$core->input['action'] = 'autosendfxrates';

if($_REQUEST['authkey'] == 'asfasdkj%2j!h4k23jh4k2_3h4k23jh') {
	$currency_obj = new Currencies('USD');
	$finmanagers = array();
	$fxrates = array();
	$query = $db->query('SELECT aff.affid, u.uid, aff.name, displayName, u.email 
						FROM '.Tprefix.'affiliates aff
						INNER JOIN '.Tprefix.'users u ON (aff.finManager = u.uid)');

	$from = strtotime('first day of last month'); //mktime(0, 0, 0, date("m", strtotime("last month")), 1, date("Y", strtotime("last month")));
	$to = strtotime('last day of last month'); //mktime(23, 59, 59, date("m", strtotime("last month")), date("t", strtotime("last month")), date("Y", strtotime("last month")));

	if($db->num_rows($query) > 0) {
		$email_data = array(
				'from_email' => $core->settings['maileremail'],
				'from' => 'OCOS Mailer',
				'subject' => 'FX Rates for '.date('F Y', strtotime('lastmonth'))
		);

		$fxrates['EUR']['latest'] = $currency_obj->get_lastmonth_fxrate('EUR', array('year' => date('Y', TIME_NOW), 'month' => date('m', TIME_NOW)));
		$fxrates['EUR']['average'] = $currency_obj->get_average_fxrate('EUR', array('from' => $from, 'to' => $to));

		while($finmanager = $db->fetch_assoc($query)) {
			if(!isset($finmanagers[$finmanager['uid']]['details'])) {
				$finmanagers[$finmanager['uid']]['details'] = $finmanager;
			}
			$country_currency = $db->fetch_assoc($db->query('SELECT cur.alphaCode, cur.name 
							FROM '.Tprefix.'countries c INNER JOIN '.Tprefix.'currencies cur ON (c.mainCurrency = cur.numCode)
							WHERE affid='.$finmanager['affid']));

			if(!isset($fxrates[$country_currency['alphaCode']]['latest'])) {
				$fxrates[$country_currency['alphaCode']]['latest'] = $currency_obj->get_lastmonth_fxrate($country_currency['alphaCode'], array('year' => date('Y', TIME_NOW), 'month' => date('m', TIME_NOW)));
			}

			if(!isset($fxrates[$country_currency['alphaCode']]['average'])) {
				$fxrates[$country_currency['alphaCode']]['average'] = $currency_obj->get_average_fxrate($country_currency['alphaCode'], array('from' => $from, 'to' => $to));
			}

			$finmanagers[$finmanager['uid']]['currencies'][$country_currency['alphaCode']] = $fxrates[$country_currency['alphaCode']];
		}
	}

	foreach($finmanagers as $uid => $user) {
		$email_data['to'] = $user['details']['email'];
		$email_data['message'] = 'Dear '.$user['details']['displayName'].',<br />';
		$email_data['message'] .= 'Please find below the average USD exchange rates for the past month:<br />';
		$email_data['message'] .= '<ul><li>EUR Avg: '.$fxrates['EUR']['average'].' Last: '.$fxrates['EUR']['latest'].'</li>';
		foreach($user['currencies'] as $alphacode => $rates) {
			$email_data['message'] .= '<li>'.$alphacode.' Avg: '.$rates['average'].' Last: '.$rates['latest'].'</li>';
		}
		$email_data['message'] .= '</ul>';

		$mail = new Mailer($email_data, 'php');
		if($mail->get_status() == true) {
			$log->record($user['details']['displayName']);
		}
		else {
			$log->record($user['details']['displayName'], 'failed');
		}
	}
}
else {
	die('Unauthorized Access');
}
?>
