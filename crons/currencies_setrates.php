<?php
require '../inc/init.php';
$currency = new Currencies('USD');
$currency->set_fx_rates('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml');

/* Other currencies */
$usd = 840;
$currencies = array(
		422 => 'LBP',
		788 => 'TND',
		818 => 'EGP',
		400 => 'JOD',
		952 => 'XOF',
		404 => 'KES',
		566 => 'NGN',
		760 => 'SYP',
		784 => 'AED',
		504 => 'MAD',
		368 => 'IQD',
		364 => 'IRR',
		586 => 'PKR',
		12 => 'DZD',
		682 => 'SAR',
		936 => 'GHS'
);

foreach($currencies as $numcode => $alphacode) {
	$currency->save_fx_rate_fromsource('http://rate-exchange.appspot.com/currency?from=USD&to='.$alphacode, 840, $numcode);
}
?>