<?php
require '../inc/init.php';
$core->input['action'] = 'autosendfxrates';

if($_REQUEST['authkey'] == 'asfasdkj%2j!h4k23jh4k2_3h4k23jh') {
	$currency_obj = new Currencies('USD');
	$finmanagers = array();
	$fxrates = array();
	$affiliates_currencies = array();
	$from = strtotime('first day of last month'); //mktime(0, 0, 0, date("m", strtotime("last month")), 1, date("Y", strtotime("last month")));
	$to = strtotime('last day of last month'); //mktime(23, 59, 59, date("m", strtotime("last month")), date("t", strtotime("last month")), date("Y", strtotime("last month")));

	$fxrates['EUR']['latest'] = $currency_obj->get_lastmonth_fxrate('EUR', array('year' => date('Y', TIME_NOW), 'month' => date('m', TIME_NOW)));
	$fxrates['EUR']['average'] = $currency_obj->get_average_fxrate('EUR', array('from' => $from, 'to' => $to));

	$email_data = array(
			'from_email' => $core->settings['maileremail'],
			'from' => 'OCOS Mailer',
			'subject' => 'FX Rates for '.date('F Y', strtotime('last month'))
	);


	$query = $db->query('SELECT aff.affid as maffid,a.affid,maff.name,u.uid, aff.name as mname, displayName, u.email
				FROM '.Tprefix.'affiliates aff
				INNER JOIN '.Tprefix.'users u ON (aff.finManager = u.uid)
				INNER JOIN '.Tprefix.'affiliatedemployees a ON (a.uid = u.uid)
				INNER JOIN '.Tprefix.'affiliates maff ON (maff.affid = a.affid)
				');

	// get finmanagers
	if($db->num_rows($query) > 0) {
		while($finmanager = $db->fetch_assoc($query)) {
			if(!isset($finmanagers[$finmanager['uid']])) {
				$finmanagers[$finmanager['uid']]['name'] = $finmanager["displayName"];
				$finmanagers[$finmanager['uid']]['email'] = $finmanager["email"];
			}
			$finmanagers[$finmanager['uid']]['affiliates'][$finmanager['affid']] = $finmanager['name'];
			$finmanagers[$finmanager['uid']]['affiliates'][$finmanager['maffid']] = $finmanager['mname'];
		}
	}

	// get affiliates currencies
	$affiliatecurrenciesquery = $db->query('SELECT affid,cur.alphaCode, cur.name
				FROM '.Tprefix.'countries c INNER JOIN '.Tprefix.'currencies cur ON (c.mainCurrency = cur.numCode)
				WHERE affid<>0');
	while($country_currency = $db->fetch_assoc($affiliatecurrenciesquery)) {
		$affiliates_currencies[$country_currency[affid]][$country_currency["alphaCode"]]["name"] = $country_currency["name"];
		if(!isset($fxrates[$country_currency['alphaCode']]['latest'])) {
			$fxrates[$country_currency['alphaCode']]['latest'] = $currency_obj->get_lastmonth_fxrate($country_currency['alphaCode'], array('year' => date('Y', TIME_NOW), 'month' => date('m', TIME_NOW)));
		}
		if(!isset($fxrates[$country_currency['alphaCode']]['average'])) {
			$fxrates[$country_currency['alphaCode']]['average'] = $currency_obj->get_average_fxrate($country_currency['alphaCode'], array('from' => $from, 'to' => $to));
		}
	}


	//echo '<pre>'.print_r($finmanagers, true).'</pre><hr>';
	//echo '<pre>'.print_r($affiliates_currencies, true).'</pre><hr>';
	//echo '<pre>'.print_r($fxrates, true).'</pre><hr>';

	foreach($finmanagers as $uid => $user) {
		$email_data['to'] = $user['email'];
		$email_data['message'] = 'Dear '.$user['name'].',<br/>'."\n";
		$email_data['message'] .= 'Please find below the average USD exchange rates for the past month:<br />'."\n";

		foreach($user["affiliates"] as $affid => $name) {
			foreach($affiliates_currencies[$affid] as $code => $cname) {
				$user["currencies"][$code] = $fxrates[$code];
			}
		}

		$email_data['message'] .= '<ul><li>EUR Avg: '.formatit($fxrates['EUR']['average']).' Last: '.formatit($fxrates['EUR']['latest']).'</li>'."\n";
		foreach($user['currencies'] as $alphacode => $rates) {
			$email_data['message'] .= '<li>'.$alphacode.' Avg: '.formatit($rates['average']).' Last: '.formatit($rates['latest']).'</li>'."\n";
		}


		$email_data['message'] .= '</ul>';
		$mail = new Mailer($email_data, 'php');
		if($mail->get_status() == true) {
			$log->record($user['name'],"Success");
			echo 'Sent to '.$user['name']."<Br>";
		}
		else {
			$log->record($user['name'], 'Failed');
			echo 'Failed sending to '.$user['name']."<Br>";
		}
	}
}
else {
	die('Unauthorized Access');
}
echo 'Done';

function formatit($number) {
	return str_pad(number_format($number, 6, ".", ""), 11, " ", STR_PAD_LEFT);
}

?>
