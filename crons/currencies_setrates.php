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
	760 => 'SYP'
);

foreach($currencies as $numcode => $alphacode) {
	$currency->save_fx_rate_fromsource('www.google.com/ig/calculator?hl=en&q=1USD=?'.$alphacode, 840, $numcode);
}
?>