<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: webservice_products.php
 * Created:        @hussein.barakat    09-Dec-2015 | 11:11:35
 * Last Update:    @hussein.barakat    09-Dec-2015 | 11:11:35
 */
require '../inc/init.php';
require ROOT.INC_ROOT.'integration_config.php';
ini_set('max_execution_time', 0);
$url = 'http://174.142.192.135:8080/openbravo_test/org.openbravo.service.json.jsonrest/Product&_startRow=1&_endRow=5';

$alldata = get_curldata($url);
$data = $alldata->response->data;
$identifier = '$_identifier';
if(is_array($data)) {
    foreach($data as $product) {
        $localproduct_data['name'] = $product->name;
        /* check if product with same name exists */
        $existinglocalproduct = Products::get_data(array('name' => $localproduct_data['name']), array('returnarray' => true));
        if(is_array($existinglocalproduct)) {
            continue;
        }
        $localproduct_data['isSynced'] = 1;
        $localproduct_data['description'] = $product->description;
        $localproduct_data['syncCompleted'] = 0;
        $productcat = $product->{productCategory.$identifier};
        if(!empty($productcat)) {
            $gpidquery = $db->query("SELECT * FROM ".Tprefix."genericproducts WHERE isSegmentDefault=1 AND title like '%".$productcat."%' ");
            while($category = $db->fetch_array($gpidquery)) {
                $localproduct_data['gpid'] = $category['gpid'];
                $syncfinished = 1;
            }
        }
        else {
            $syncfinished = 2;
        }
        if(!empty($product->em_ork_producer)) {
            $syncfinished = 2;
            $integration_data['foreignSupplier'] = $product->em_ork_producer;
            $producer = IntegrationMediationEntities::get_entities(array('foreignId' => $product->em_ork_producer), array('returnarray' => FALSE));
            if(is_object($producer)) {
                $localsupplier = new Entities($producer->localId);
                if(is_object($localsupplier)) {
                    $localproduct_data['spid'] = $localsupplier->eid;
                    $syncfinished = 1;
                }
            }
        }
        else {
            $supplierurl = 'http://174.142.192.135:8080/openbravo_test/org.openbravo.service.json.jsonrest/MaterialMgmtMaterialTransaction?_where=movementQuantity > 0 AND product=\''.$product->id.'\' AND movementType=\'V+\'&_startRow=1&_endRow=5';
//                $supplierurl = 'http://174.142.192.135:8080/openbravo_test/org.openbravo.service.json.jsonrest/MaterialMgmtMaterialTransaction?_where=movementType=\'C-\'&_startRow=1&_endRow=5';
            $supdata = get_curldata($supplierurl);
            $sups = $supdata->response->data;
            if(is_array($sups)) {
                foreach($sups as $movementdata) {
                    if(!empty($movementdata->organisation) && !empty($movementdata->{organisation.$identifier})) {
                        if(strpos(strtolower($movementdata->{organisation.$identifier}), 'orkila')) {
                            continue;
                        }
                        $integration_data['foreignSupplier'] = $movementdata->organisation;
                        $producer = IntegrationMediationEntities::get_entities(array('foreignId' => $movementdata->organisation), array('returnarray' => FALSE));
                        if(is_object($producer)) {
                            $localsupplier = new Entities($producer->localId);
                            if(is_object($localsupplier)) {
                                $localproduct_data['spid'] = $localsupplier->eid;
                                if($syncfinished != 2) {
                                    $syncfinished = 1;
                                }
                                break;
                            }
                        }
                    }
                }
                $syncfinished = 2;
            }
            else {
                $syncfinished = 2;
            }
        }

        if($syncfinished == 1) {
            $localproduct_data['syncCompleted'] = 1;
        }
        if(!is_empty($localproduct_data['name'])) {
            $insertquery = $db->insert_query('products', $localproduct_data);
            if($insertquery) {
                /* check if product is already existing on ocos DB */
                $existing_integration_product = IntegrationMediationProducts::get_products(array('foreignId' => $product->id, 'foreignSystem' => 3), array('returnarray' => false));
                if(!is_object($existing_integration_product)) {
                    $syncfinished = 0;
                    $integration_data['localId'] = $db->last_id();
                    $integration_data['foreignId'] = $product->id;
                    $integration_data['foreignName'] = $product->name;
                    $integration_data['foreignSystem'] = 3;
                    if(!is_empty($integration_data['localId'], $integration_data['foreignId'], $integration_data['foreignName'])) {
                        $insertquery2 = $db->insert_query('integration_mediation_products', $integration_data);
                    }
                }
            }
        }
        unset($localproduct_data, $integration_data, $productcat, $producer);
    }
}
function get_curldata($url) {
    $header = array("Accept: application/json");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible;
                MSIE 6.0;
                Windows NT 5.1;
                SV1;
                .NET CLR 1.0.3705;
                .NET CLR 1.1.4322)   ');
//curl_setopt(cur obj, CURLOPT_USERPWD, "username:pass");
    curl_setopt($ch, CURLOPT_USERPWD, "hussein.barakat:h234569");
//    $retValue = curl_exec($ch);
    $response = json_decode(curl_exec($ch));
    curl_close($ch);
    return $response;
}
