<?php
require '../inc/init.php';
$core->input['action'] = 'autosendfxrates';

if($_REQUEST['authkey'] == 'asfasdkj%2j!h4k23jh4k2_3h4k23jh') {
	$currency_obj = new Currencies('USD');
	$finmanagers = array();
	$fxrates = array();
	$affiliates_currencies = array();
	$from = strtotime('first day of last month');
	$to = strtotime('last day of last month');

	$fxrates['EUR']['latest'] = $currency_obj->get_lastmonth_fxrate('EUR', array('year' => date('Y', TIME_NOW), 'month' => date('m', TIME_NOW)));
	$fxrates['EUR']['average'] = $currency_obj->get_average_fxrate('EUR', array('from' => $from, 'to' => $to));

	$email_data = array(
			'from_email' => $core->settings['maileremail'],
			'from' => 'OCOS Mailer',
			'subject' => 'FX Rates for '.date('F Y', strtotime('last month'))
	);


	/* Get Fin managers - START */
	$query = $db->query('SELECT finManager FROM '.Tprefix.'affiliates WHERE finManager IS NOT NULL');

	$users_list = array(66 => 66);
	if($db->num_rows($query) > 0) {
		while($finmanager = $db->fetch_assoc($query)) {
			$users_list[$finmanager['finManager']] = $finmanager['finManager'];
		}
	}
	/* Get Fin managers - END */

	$query = $db->query('SELECT u.uid, displayName, email, name, a.affid
				FROM '.Tprefix.'affiliatedemployees a
				INNER JOIN '.Tprefix.'users u ON (a.uid = u.uid)
				INNER JOIN '.Tprefix.'affiliates aff ON (aff.affid = a.affid)
				WHERE u.uid IN ('.implode(',', $users_list).')');

	if($db->num_rows($query) > 0) {
		while($finmanager = $db->fetch_assoc($query)) {
			if(!isset($finmanagers[$finmanager['uid']])) {
				$finmanagers[$finmanager['uid']]['name'] = $finmanager['displayName'];
				$finmanagers[$finmanager['uid']]['email'] = $finmanager['email'];
			}
			$finmanagers[$finmanager['uid']]['affiliates'][$finmanager['affid']] = $finmanager['name'];
		}
	}

	// get affiliates currencies
	$affiliatecurrenciesquery = $db->query('SELECT affid, cur.alphaCode, cur.name
				FROM '.Tprefix.'countries c 
				INNER JOIN '.Tprefix.'currencies cur ON (c.mainCurrency = cur.numCode)
				WHERE affid<>0');

	while($country_currency = $db->fetch_assoc($affiliatecurrenciesquery)) {
		$affiliates_currencies[$country_currency[affid]][$country_currency['alphaCode']]['name'] = $country_currency['name'];
		if(!isset($fxrates[$country_currency['alphaCode']]['latest'])) {
			$fxrates[$country_currency['alphaCode']]['latest'] = $currency_obj->get_lastmonth_fxrate($country_currency['alphaCode'], array('year' => date('Y', TIME_NOW), 'month' => date('m', TIME_NOW)));
		}
		if(!isset($fxrates[$country_currency['alphaCode']]['average'])) {
			$fxrates[$country_currency['alphaCode']]['average'] = $currency_obj->get_average_fxrate($country_currency['alphaCode'], array('from' => $from, 'to' => $to));
		}
	}

	foreach($finmanagers as $uid => $user) {
		$user['email'] = 'tony.assaad@orkila.com';
		$email_data['to'] = $user['email'];
		$email_data['message'] = '<pre>Dear '.$user['name'].",\n\n";
		$email_data['message'] .= "Please find below the average USD exchange rates for the past month\n\n";
		foreach($user['affiliates'] as $affid => $name) {
			foreach($affiliates_currencies[$affid] as $code => $cname) {
				$user['currencies'][$code] = $fxrates[$code];
			}
		}
		$email_data['message'] .= '[EUR] Avg: '.formatit($fxrates['EUR']['average']).' Last: '.formatit($fxrates['EUR']['latest'])."\n";
		foreach($user['currencies'] as $alphacode => $rates) {
			if(empty($rates['average']) && !empty($rates['latest'])) {
				$rates['average'] = $rates['latest'];
			}
			$email_data['message'] .= '['.$alphacode.'] Avg: '.formatit($rates['average']).' Last: '.formatit($rates['latest'])."\n";
			if(!empty($rates['average']) && !empty($rates['latest'])) {
				$email_data['message'].= '[USD]:['.$alphacode.'] '.formatit(1 / $rates['average']).' Last: '.formatit(1 / $rates['latest'])."\n";
			}
		}

		$email_data['message'].="\nBest Regards,\nSignature</pre>";
		$mail = new Mailer($email_data, 'php');
		if($mail->get_status() == true) {
			$log->record($user['name'], 'success');
		}
		else {
			$log->record($user['name'], 'failed');
		}
	}
}
else {
	die('Unauthorized Access');
}
function formatit($number) {
	if(isset($number)) {
		return str_pad(round(number_format($number, 6, '.', ''), 6), 11, ' ', STR_PAD_LEFT);
	}
	else {
		return str_pad('-', 11, ' ', STR_PAD_LEFT);
	}
}

?>
