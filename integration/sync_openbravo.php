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

define('AUTHCODE', 'X1iIMm7pG06ip6o95HEa');
if($core->input['authCode'] == AUTHCODE) {
	$db_info = array('database' => 'openbrav_production', 'username' => 'openbrav_appuser', 'password' => '8w8;MFRy4g^3', 'engine' => 'postgre');
	$affiliates_index = array(
		'C08F137534222BD001345BAA60661B97'	=> 19, //Orkila Tunisia
		'0B366EFAE0524FDAA97A1322A57373BB' => 22, //Orkila East Africa
		'DA0CE0FED12C4424AA9B51D492AE96D2' => 11, //Orkila Nigeria
		'F2347759780B43B1A743BEE40BA213AD' => 23 //Orkila Ghana
	);

	$integration = new IntegrationOB($db_info, 'C08F137534222BD001345B7B2E8F182D', $affiliates_index, 3, array('from' => '2011-01-01'));

	$status = $integration->get_status();
	if(!empty($status)) {
		echo 'Error';
		exit;
	}
	
	$integration->sync_products(array('0A36650996654AD2BA6B26CBC8BA7347'));
	$integration->sync_businesspartners();
	$integration->sync_purchases(array_keys($affiliates_index), array('products' => array('0A36650996654AD2BA6B26CBC8BA7347')), 'order');
	$integration->sync_sales(array_keys($affiliates_index), array('products' => array('0A36650996654AD2BA6B26CBC8BA7347')));
}
?>