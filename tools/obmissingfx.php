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

ini_set('max_execution_time', 0);
$rates = CurrenciesFxRate::get_data(array('baseCurrency' => 840, 'currency' => 978), array('returnarray' => 'true'));
$fixedusdrate = 1507.5;
foreach($rates as $rate) {
    if($rate->date < 1429228800) {
        continue;
    }
    $url = 'http://dev-server.orkilalb.local:8080/Openbravo-online-2015-12-29/org.openbravo.service.json.jsonrest/CurrencyConversionRate';

    $conv['opposite'] = 1 / $rate->rate;
    $conv['newrate'] = $conv['opposite'] * $fixedusdrate;

    $data['data'] = array(
            "entityName" => "CurrencyConversionRate",
            "active" => true,
            "currency" => array("id" => "102"),
            "toCurrency" => array("id" => "342"),
            "validFromDate" => date('Y-m-d 00:00:00', $rate->date),
            "validToDate" => date('Y-m-d 00:00:00', $rate->date),
            "conversionRateType" => "S",
            "multipleRateBy" => $conv[newrate],
            "divideRateBy" => 1 / $conv[newrate]
    );

    $request = json_encode($data);
    echo get_curldata($url, $request);
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

    curl_setopt($ch, CURLOPT_USERPWD, "Openbravo:openbravo");
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
