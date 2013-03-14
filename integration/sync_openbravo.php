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
	$db_info = array('database' => 'openbrav_tests', 'username' => 'openbrav_appuser', 'password' => '8w8;MFRy4g^3', 'engine' => 'postgre');
	$affiliates_index = array(
		'C08F137534222BD001345BAA60661B97'	=> 19
	);

	$integration = new IntegrationOB($db_info, 'C08F137534222BD001345B7B2E8F182D', $affiliates_index, 3, array('from' => 'last year'));

	$status = $integration->get_status();
	if(!empty($status)) {
		echo 'Error';
		exit;
	}
	
	$integration->sync_products(array('0A36650996654AD2BA6B26CBC8BA7347'));
	$integration->sync_businesspartners();
	$integration->sync_purchases(array('C08F137534222BD001345BAA60661B97'), array('products' => array('0A36650996654AD2BA6B26CBC8BA7347')));
	$integration->sync_sales(array('C08F137534222BD001345BAA60661B97'), array('products' => array('0A36650996654AD2BA6B26CBC8BA7347')));
}
?>
