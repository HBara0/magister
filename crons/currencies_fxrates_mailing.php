<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require '../inc/init.php';


if($_REQUEST["authkey"] == 'currencyflip_only_if_you_know_why_you_re_calling_this') {
$currencies = array(
	422=>422,
	788=>788,
	818=>818,
	400=>400,
	952=>952
);
		$query = "select * from ".Tprefix."currencies_fxrates where currency IN (".implode(',',$currencies).")";
			$res = $db->query($query);
			if($db->num_rows($res) > 0) {
				while($row = $db->fetch_assoc($res)) {
						$rate=$row["rate"];
						if ($rate!=0) {
							$rate=1/$rate;
						}
						$db->update_query("currencies_fxrates",array("rate"=>$rate),"cfxid=".$row["cfxid"]);
				}
			}
			die("Done inverting fx rates values");
}

if($_REQUEST["authkey"] == 'asfasdkj%2j!h4k23jh4k2_3h4k23jh') {
	$currency_obj = new Currencies('USD');
	$query = "select ".Tprefix."affiliates.affid,".Tprefix."users.uid,".Tprefix."affiliates.name,".Tprefix."users.firstName,".Tprefix."users.lastName,".Tprefix."users.email from ".Tprefix."affiliates inner join users on ".Tprefix."affiliates.finManager = ".Tprefix."users.uid";
	$fm = array();
	$res = $db->query($query);

	$from = mktime(0, 0, 0, date("m", strtotime("last month")), 1, date("Y", strtotime("last month")));
	$to = mktime(23, 59, 59, date("m", strtotime("last month")), date("t", strtotime("last month")), date("Y", strtotime("last month")));

	if($db->num_rows($res) > 0) {
		while($row = $db->fetch_assoc($res)) {
			$fm[$row["uid"]]["firstname"] = $row["firstName"];
			$fm[$row["uid"]]["lastname"] = $row["lastName"];
			$fm[$row["uid"]]["email"] = $row["email"];
			$fm[$row["uid"]]["when"] = date("M Y", strtotime("last month"));
			$fm[$row["uid"]]["euro"]["average"] = $currency_obj->get_average_fxrate("EUR", array("from" => $from, "to" => $to));
			$fm[$row["uid"]]["euro"]["last"] = $currency_obj->get_lastmonth_fxrate("EUR", array("year" => date("Y", mktime()), "month" => date("m", mktime())));

			$query2 = "select ".Tprefix."currencies.alphaCode,".Tprefix."currencies.name from ".Tprefix."countries inner join ".Tprefix."currencies on ".Tprefix."countries.mainCurrency = ".Tprefix."currencies.numCode where affid=".$row["affid"];
			$res2 = $db->query($query2);

			if($db->num_rows($res2) > 0) {
				while($row2 = $db->fetch_assoc($res2)) {
					//echo date("d-m-Y",strtotime("first day of last month"))."<hr>";
					$rate = $currency_obj->get_lastmonth_fxrate($row2["alphaCode"], array("year" => date("Y", mktime()), "month" => date("m", mktime())));
					$rate2 = $currency_obj->get_average_fxrate($row2["alphaCode"], array("from"=>$from,"to"=>$to));
					$text = ' ->  1 [USD] = '.str_pad(number_format($rate, 6,".",""),11," ",STR_PAD_LEFT).' ['.$row2["alphaCode"]."] last: ".str_pad(number_format($rate2, 6,".",""),11," ",STR_PAD_LEFT)."\n";
					$fm[$row["uid"]]["data"][$row2["alphaCode"]] = $text;

				}
			}
		}
	}

	foreach($fm as $uid => $data) {
		$data["firstname"];
		$data["lastname"];
		$data["email"];
		$subject = "Orkila forex mailer ".$data["when"];
		$text = "\nDear ".$data["firstname"]." ".$data["lastname"].",\n\n";
		$text .="Please find below the average USD exchange rates for the past month of ".$data["when"].":\n\n";
		$text .= " ->  1 [USD] = ".str_pad(number_format($data["euro"]["average"], 6,".",""),11," ",STR_PAD_LEFT)." [EUR] last: ".str_pad(number_format($data["euro"]["last"],6,".",""),11," ",STR_PAD_LEFT)."\n";
		foreach($data["data"] as $curcode => $mail) {
			$text.=$mail;
		}
		echo '<pre>'.$subject."\n".$data["email"]."\n".$text."</pre><hr>";
		send_mail($data["email"], $text, $subject);
	}
}
else {
	die("Unauthorized Access");
}
function send_mail($recipient, $content, $subject) {
	global $log;
	$email_data = array(
			'to' => $recipient,
			'from_email' => $core->settings['maileremail'],
			'from' => 'OCOS Mailer',
			'subject' => $subject,
			'message' => $content
	);

	$mail = new Mailer($email_data, 'php');
	$core->input["module"]='fxratemailer';
	$core->input["action"]='sendmail';
	if($mail->get_status() == true) {
		$log->record("Sucecssfuly sent exchange rates to ".$recipient);
	}
	else {
		$log->record('currenciesFXmailer', "Failed to mail exchange rates to ".$recipient);
	}
}

?>
