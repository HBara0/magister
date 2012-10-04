<?php
function fx_fromfeed($from_currency, $to_currency) {
	global $core;
	$core->settings['fxfeed'] = 'http://www.webservicex.net/CurrencyConvertor.asmx/ConversionRate?FromCurrency={FROM}&ToCurrency={TO}';
	$core->settings['fxfeed'] = str_replace('{FROM}', $from_currency, $core->settings['fxfeed']);
	$core->settings['fxfeed'] = str_replace('{TO}', $to_currency, $core->settings['fxfeed']);
	

	$session = curl_init($core->settings['fxfeed']);

	//curl_setopt ($session, CURLOPT_POST, true);
	//curl_setopt ($session, CURLOPT_POSTFIELDS, $postvars);

	curl_setopt($session, CURLOPT_HEADER, false);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	
	$rate = curl_exec($session);
	return  $rate;
	//header("Content-Type: text/xml");
	curl_close($session);
}
?>