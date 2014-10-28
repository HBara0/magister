<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: integration_importcustomers.php
 * Created:        @zaher.reda    Oct 26, 2014 | 5:06:59 PM
 * Last Update:    @zaher.reda    Oct 26, 2014 | 5:06:59 PM
 */

$filters['affid'] = 4;
$filters['foreignSystem'] = 6;
$filters['entityType'] = 'c';

$customers = IntegrationMediationEntities::get_entities($filters);

$affiliate = new Affiliates($filters['affid']);
$country = $affiliate->get_country();
$counter = 0;
if(is_array($customers)) {
    foreach($customers as $customer) {
        echo $customer->foreignName;
        if(value_exists('entities', 'companyName', trim($customer->foreignName))) {
            echo ': Skipped<br />';
            continue;
        }

        $newcust_data['companyName'] = ucwords(strtolower(trim($customer->foreignName)));
        $newcust_data['companyNameShort'] = $newcust_data['companyName'];
        $newcust_data['affid'][] = $customer->affid;
        $newcust_data['psid'][] = 20;
        $newcust_data['type'] = 'c';
        $newcust_data['presence'] = 'local';
        $newcust_data['country'] = $country->coid;
        $newcust_data['representative'][0]['rpid'] = 1;
        $newcust_data['users'][] = array('uid' => 0);
        $new_customer = new Entities($newcust_data);
        echo ': Added<br />';
        $counter++;
        unset($newcust_data);
    }
    echo $counter.' new customers';
}