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
if($core->input['referrer'] == 'complete') {
    $url = 'http://184.107.151.42:8080/openbravo_test/org.openbravo.service.json.jsonrest/BusinessPartner?where=active=1&_startRow=1&_endRow=10';
}
else {
    $url = 'http://184.107.151.42:8080/openbravo_test/org.openbravo.service.json.jsonrest/BusinessPartner?where=active=1 AND creationDate >'.date('Y-m-d 00:00:00', TIME_NOW - (24 * 60 * 60)).'&_startRow=1&_endRow=10';
}
$alldata = get_curldata($url);
$data = $alldata->response->data;
$identifier = '$_identifier';
if(is_array($data)) {
    $positivfields = array('noQReportReq', 'NoQReportSend', 'isActive', 'isSynced');
    foreach($data as $bp) {
        $integration_data = array();
        $insert_data = array();
        //check if entity exists in DB by name and short name direct matching
        $existing_entity = Entities::get_data('companyName LIKE "%'.$bp->_identifier.'%" OR companyNameShort LIKE "%'.$bp->_identifier.'%"', array('returnarray' => true, 'operators' => array('filter' => 'CUSTOMSQLSECURE')));
        //if entity with same name does not exists proceeed to creation
        if(!is_array($existing_entity)) {
            //check entity type
            switch($bp->{'businessPartnerCategory$_identifier'}) {
                case 'Customer':
                    $insert_data['type'] = 'c';
                    break;
                case 'Supplier':
                case 'Non-Chemical Companies & Vendors':
                    $insert_data['type'] = 's';
                    break;
                //if type is none of the above, then skip this business partner
                default:
                    continue;
            }
            foreach($positivfields as $field) {
                $insert_data[$field] = 1;
            }
            $insert_data['dateAdded'] = TIME_NOW;
            $insert_data['createdOn'] = TIME_NOW;
            $insert_data['companyName'] = $db->escape_string($bp->_identifier);
            $insert_data['companyNameShort'] = $db->escape_string($bp->_identifier);
            $query = $db->insert_query(Entities::TABLE_NAME, $insert_data);
            if($query) {
                //check if entity exists in the integration table for system 3 (OB) by the local id, if not proceed in creating it
                $existing_integrationentity = IntegrationMediationEntities::get_data(array('foreignSystem' => 3, 'localId' => $db->last_id()), array('returnarray' => true));
                if(!is_array($existing_integrationentity)) {
                    $integration_data['localId'] = $db->last_id();
                    $integration_data['foreignSystem'] = 3;
                    $integration_data['foreignName'] = $bp->_identifier;
                    $integration_data['entityType'] = $insert_data['type'];
                    $integrationquery = $db->insert_query(IntegrationMediationEntities::TABLE_NAME, $integration_data);
                }
            }
        }
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
