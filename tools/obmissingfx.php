<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: obmissingfx.php
 * Created:        @zaher.reda    Feb 16, 2016 | 4:13:15 PM
 * Last Update:    @zaher.reda    Feb 16, 2016 | 4:13:15 PM
 */
require '../inc/init.php';
date_default_timezone_set('Asia/Beirut');
ini_set('max_execution_time', 0);
$currency['from']['ocos'] = 978;
$currency['from']['ob'] = 102;

$currency['to']['ocos'] = 936;
$currency['to']['ob'] = 170;
$startingdate = '2013-10-01';
$enddate = '2014-12-31';
echo "Started...<br />";
$rates = CurrenciesFxRate::get_data(array('baseCurrency' => $currency['from']['ocos'], 'currency' => $currency['to']['ocos']), array('returnarray' => 'true'));
echo "Getting Rates...<br />";
//$fixedusdrate = 1507.5;
foreach($rates as $rate) {
    if($rate->date < strtotime($startingdate) || $rate->date > strtotime($enddate)) {
        continue;
    }
    $url = 'http://openbravo.orkila.com:8080/openbravo/org.openbravo.service.json.jsonrest/CurrencyConversionRate';
    // $url = 'http://dev-server.orkilalb.local:8080/openbravo-repo/org.openbravo.service.json.jsonrest/CurrencyConversionRate';
    if(isset($fixedusdrate) && $fixedusdrate > 0) {
        $conv['opposite'] = 1 / $rate->rate;
        $conv['newrate'] = $conv['opposite'] * $fixedusdrate;
    }
    else {
        $conv['newrate'] = $rate->rate;
    }
    $data['data'] = array(
            "entityName" => "CurrencyConversionRate",
            "active" => true,
            "currency" => array("id" => "".$currency['from']['ob'].""),
            "toCurrency" => array("id" => "".$currency['to']['ob'].""),
            "validFromDate" => date('Y-m-d 00:00:00', $rate->date),
            "validToDate" => date('Y-m-d 00:00:00', $rate->date),
            "conversionRateType" => "S",
            "multipleRateBy" => $conv[newrate] * 1,
            "divideRateBy" => 1 / $conv[newrate],
            "oBCRCCreateOpposite" => false
    );

    $request = json_encode($data);
    echo get_curldata($url, $request);
    echo '<hr />';

    $data['data'] = array(
            "entityName" => "CurrencyConversionRate",
            "active" => true,
            "toCurrency" => array("id" => "".$currency['from']['ob'].""),
            "currency" => array("id" => "".$currency['to']['ob'].""),
            "validFromDate" => date('Y-m-d 00:00:00', $rate->date),
            "validToDate" => date('Y-m-d 00:00:00', $rate->date),
            "conversionRateType" => "S",
            "divideRateBy" => $conv[newrate] * 1,
            "multipleRateBy" => 1 / $conv[newrate],
            "oBCRCCreateOpposite" => false
    );

    $request = json_encode($data);
    echo get_curldata($url, $request);
    echo '<hr />';
}
function get_curldata($url, $request) {
    $header = array("Content-Type: application/json");
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible;
                MSIE 6.0;
                Windows NT 5.1;
                SV1;
                .NET CLR 1.0.3705;
                .NET CLR 1.1.4322)');

    curl_setopt($ch, CURLOPT_USERPWD, "anto.khederlarian:@ntraork1245");
    $result = curl_exec($ch);
    echo 'Finished';
    curl_close($ch);
    return $result;
}
