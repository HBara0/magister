<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Process to sync with Openbravo
 * $id: sync_openbravo.php
 * Created:        @zaher.reda    Feb 18, 2013 | 4:16:34 PM
 * Last Update:    @zaher.reda    Feb 18, 2013 | 4:16:34 PM
 */
require '../inc/init.php';
require_once ROOT.INC_ROOT.'integration_config.php';

define('AUTHCODE', 'X1iIMm7pG06ip6o95HEa');
//if($core->input['authCode'] == AUTHCODE) {
//$db_info = array('hostname' => 'dev-server.orkilalb.local', 'database' => 'openbravo-eval', 'username' => 'ocos_dev_team', 'password' => 'xgjE^dfhg324_3', 'engine' => 'postgre');
$affiliates_index = array(
        'C08F137534222BD001345BAA60661B97' => 19, //Orkila Tunisia
        '0B366EFAE0524FDAA97A1322A57373BB' => 22, //Orkila East Africa
        'DA0CE0FED12C4424AA9B51D492AE96D2' => 11, //Orkila Nigeria
        'F2347759780B43B1A743BEE40BA213AD' => 23, //Orkila Ghana
        'BD9DC2F7883B4E11A90B02A9A47991DC' => 1, //Orkila Lebanon
        '933EC892369245E485E922731D46FCB1' => 20, //Orkila Senegal
        '51FB1280AB104EFCBBB982D50B3B7693' => 21, //Orkila CI
        '7AD08388D369403A9DF4B8240E3AD7FF' => 27, //Orkila International
        'ED9F0447A2484096B8B0FFF4EC389100' => 2, //Orkila Jordan
        '8E2582F6D85A41D2A9906A9D1EED6262' => 2, //Orkila Iran
        '301ACDBEC89D4CAAA92B60FC6FA22D89' => 2//Orkila Cyprus
);

//$sync_documents = array(
//        'C08F137534222BD001345BAA60661B97' => 'order', //Orkila Tunisia
//        '0B366EFAE0524FDAA97A1322A57373BB' => 'invoice', //Orkila East Africa
//        'DA0CE0FED12C4424AA9B51D492AE96D2' => 'invoice', //Orkila Nigeria
//        'F2347759780B43B1A743BEE40BA213AD' => 'invoice', //Orkila Ghana
//        'BD9DC2F7883B4E11A90B02A9A47991DC' => 'invoice', //Orkila Lebanon
//        '933EC892369245E485E922731D46FCB1' => 'invoice', //Orkila Senegal
//        '51FB1280AB104EFCBBB982D50B3B7693' => 'invoice', //Orkila CI
//        '95CA85F7EDF147B193EBDF29565DCEB5' => 'invoice', //Orkila International,
//        'ED9F0447A2484096B8B0FFF4EC389100' => 'invoice' //Orkila Jordan
//);

$sync_documents = array(
        'C08F137534222BD001345BAA60661B97', //Orkila Tunisia
        '0B366EFAE0524FDAA97A1322A57373BB', //Orkila East Africa
        'DA0CE0FED12C4424AA9B51D492AE96D2', //Orkila Nigeria
        'F2347759780B43B1A743BEE40BA213AD', //Orkila Ghana
        'BD9DC2F7883B4E11A90B02A9A47991DC', //Orkila Lebanon
        '933EC892369245E485E922731D46FCB1', //Orkila Senegal
        '51FB1280AB104EFCBBB982D50B3B7693', //Orkila CI
        '95CA85F7EDF147B193EBDF29565DCEB5', //Orkila International,
        'ED9F0447A2484096B8B0FFF4EC389100', //Orkila Jordan
        '8E2582F6D85A41D2A9906A9D1EED6262', //Orkila Iran
        '301ACDBEC89D4CAAA92B60FC6FA22D89', //Orkila Cyprus
);
$integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client'], $affiliates_index, 3, array('from' => '2015-01-01'));

$status = $integration->get_status();
if(!empty($status)) {
    echo 'Error';
    exit;
}

//$integration->sync_products(array('0A36650996654AD2BA6B26CBC8BA7347'));
//$integration->sync_businesspartners();
//foreach($sync_documents as $orgid => $document) {
$integration->sync_purchases($sync_documents, array('products' => array('0A36650996654AD2BA6B26CBC8BA7347')), 'invoice');
$integration->sync_sales($sync_documents, array('products' => array('0A36650996654AD2BA6B26CBC8BA7347')), 'invoice');
$integration->sync_purchases($sync_documents, array('products' => array('0A36650996654AD2BA6B26CBC8BA7347')), 'order');
$integration->sync_sales($sync_documents, array('products' => array('0A36650996654AD2BA6B26CBC8BA7347')), 'order');
////    sleep(10);
// }

$integration->close_dbconn();
echo 'Done';
//}
?>