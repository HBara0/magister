<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: landecosts_migration.php
 * Created:        @zaher.reda    Jun 10, 2013 | 3:52:15 PM
 * Last Update:    @zaher.reda    Jun 10, 2013 | 3:52:15 PM
 */
exit;
require '../inc/init.php';

define('AUTHCODE', 'X1iIMm7pG06ip6o95HEa');
if($core->input['authCode'] == AUTHCODE) {
//	$new_db_info = array('database' => 'openbrav_sandbox', 'username' => 'openbrav_appuser', 'password' => '8w8;MFRy4g^3', 'engine' => 'postgre');
//	$affiliates_index = array(
//			'C08F137534222BD001345BAA60661B97' => 19, //Orkila Tunisia
//			'0B366EFAE0524FDAA97A1322A57373BB' => 22 //Orkila East Africa
//	);
//
//	$new_integration = new IntegrationOB($new_db_info, 'C08F137534222BD001345B7B2E8F182D', $affiliates_index, 3, array('from' => 'last year'));
//
//	$status = $integration->get_status();
//	if(!empty($status)) {
//		echo 'Error';
//		exit;
//	}
    $affiliates_index = array(
            'C08F137534222BD001345BAA60661B97' => 19, //Orkila Tunisia
            '0B366EFAE0524FDAA97A1322A57373BB' => 22 //Orkila East Africa
    );

    $lc_bp = array(
            'C08F137534222BD001345BAA60661B97' => '54FC4616619F45398A26B9B9BBAA2B9F'//OTI Tunisie
    );

    $current_affiliate = 'C08F137534222BD001345BAA60661B97';
    $fromdb = new PostgreSQLConnection('openbrav_production', 'localhost', 'openbrav_appuser', '8w8;MFRy4g^3');
    $todb = new PostgreSQLConnection('openbrav_tests', 'localhost', 'openbrav_appuser', '8w8;MFRy4g^3');

    $inout_query = $fromdb->query("SELECT *
							FROM m_inout
							WHERE EM_LC_ReadyCosting='Y' AND docstatus NOT IN ('VO', 'CL') AND ad_org_id='".$current_affiliate."'");
    while($order = $fromdb->fetch_assoc($inout_query)) {
        echo 'Document: '.$order['documentno'].'<br />';
        $landedcosts = array();
        $inoutlines_landedcosts = array();
        $inout_landedcosts_ids = array();
        $inout_lineno = 10;
        $inoutline_query = $fromdb->query("SELECT *
							FROM m_inoutline
							WHERE m_inout_id='".$order['m_inout_id']."'");
        while($orderline = $fromdb->fetch_assoc($inoutline_query)) {
            $query = $fromdb->query("SELECT *
							FROM lc_inoutcosts ioc
							JOIN lc_costtypes ct ON (ct.lc_costtypes_id=ioc.lc_costtypes_id)
							WHERE m_inoutline_id='".$orderline['m_inoutline_id']."'");
            while($lc = $fromdb->fetch_assoc($query)) {
                $inoutlines_landedcosts[$orderline['m_inoutline_id']][] = $lc;
                $landedcosts[$lc['value']] += $lc['amount'];
                $currency = $lc['c_currency_id'];
            }
        }

        /* Insert InOut Landed Costs */
        foreach($landedcosts as $key => $amount) {
            $new_lc = array(
                    'line' => $inout_lineno,
                    'm_inout_id' => $order['m_inout_id'],
                    'c_currency_id' => $currency,
                    'amount' => $amount,
                    'distributionmethod' => 'amt',
                    'createdby' => $order['createdby'],
                    'updatedby' => $order['updatedby'],
                    'ad_client_id' => $order['ad_client_id'],
                    'ad_org_id' => $order['ad_org_id'],
                    'c_bpartner_id' => $lc_bp[$current_affiliate]
            );
            $inout_lineno += 10;
            $new_lc['m_landedcosts_id'] = $todb->fetch_field($todb->query("SELECT m_landedcosts_id FROM m_landedcosts WHERE value='".$key."'"), 'm_landedcosts_id');
            $matched_lctypes[$key] = $new_lc['m_landedcosts_id'];
            if(empty($new_lc['m_landedcosts_id'])) {
                echo 'Error addeding LC '.$key.' for doc '.$order['documentno'];
                continue;
            }

            $new_lc['m_inout_lcosts_id'] = $todb->fetch_field($todb->query("SELECT p_nextno FROM Ad_Sequence_Next('m_inout_lcosts', '".$order['ad_client_id']."')"), 'p_nextno');

            $inout_landedcosts_ids[$key] = $new_lc['m_inout_lcosts_id'];
            $todb->insert_query('m_inout_lcosts', $new_lc);
        }

        $inoutline_lineno = 0;
        foreach($inoutlines_landedcosts as $inoutline_id => $inoutline_costs) {
            foreach($inoutline_costs as $key => $inoutline_cost) {
                $new_lcline = array(
                        'line' => $inoutline_lineno + 10,
                        'm_inout_lcosts_id' => $inout_landedcosts_ids[$inoutline_cost['value']],
                        'c_currency_id' => $currency,
                        'amount' => $inoutline_cost['amount'],
                        'm_landedcosts_id' => $matched_lctypes[$inoutline_cost['value']],
                        'm_inoutline_id' => $inoutline_id,
                        'createdby' => $order['createdby'],
                        'updatedby' => $order['updatedby'],
                        'ad_client_id' => $order['ad_client_id'],
                        'ad_org_id' => $order['ad_org_id']
                );
                $inoutline_lineno += 10;
                $new_lcline['m_inoutline_lcosts_id'] = $todb->fetch_field($todb->query("SELECT p_nextno FROM Ad_Sequence_Next('m_inoutline_lcosts', '".$order['ad_client_id']."')"), 'p_nextno');
                $todb->insert_query('m_inoutline_lcosts', $new_lcline);
            }
        }
        echo '<hr />';
    }
}
?>
