<?php

require '../inc/init.php';
global $db;
$temp_citis = TempCities::get_data();
//looping through the temp table
if (is_array($temp_citis)) {
    foreach ($temp_citis as $temp_city) {
        if ($temp_city->operation == '=') {
            $basetemp_city = TempCities::get_data(array('cityName' => substr($temp_city->cityName, (strpos($temp_city->cityName, '=') + 2))), array('returnarray' => false));
            if (is_object($basetemp_city)) {
                $city = Cities::get_data(array('unlocode' => $basetemp_city->cityCode, 'country' => $basetemp_city->countryCode), array('returnarray' => false));
                if (is_object($city)) {
                    $ncity['name'] = trim($basetemp_city->cityName);
                    $ncity['alias'] = trim(substr($temp_city->cityName, 0, strpos($temp_city->cityName, '=')));
                    $db->update_query('cities', $ncity, 'ciid =' . $city->ciid);
                }
            }
        }
        if (empty($temp_city->cityCode)) {
            continue;
        }
        if (strpos($temp_city->cityName, '(') !== false) {
            $alias = substr($temp_city->cityName, (strpos($temp_city->cityName, '(') + 1), (strpos($temp_city->cityName, ')') - 1 - strpos($temp_city->cityName, '(')));
            $name = substr($temp_city->cityName, 0, strpos($temp_city->cityName, '(') - 1);
            $city = Cities::get_data(array('unlocode' => $temp_city->cityCode, 'country' => $temp_city->countryCode), array('returnarray' => false));
            if (is_object($city)) {
                $ncity['name'] = trim($name);
                $ncity['alias'] = trim($alias);
                $db->update_query('cities', $ncity, 'ciid =' . $city->ciid);
            }
        }
    }
}
