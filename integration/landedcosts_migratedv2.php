<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: landedcosts_migratedv2.php
 * Created:        @zaher.reda    Dec 10, 2013 | 11:09:19 AM
 * Last Update:    @zaher.reda    Dec 10, 2013 | 11:09:19 AM
 */

exit;
require '../inc/init.php';

define('AUTHCODE', 'X1iIMm7pG06ip6o95HEa');
if($core->input['authCode'] == AUTHCODE) {
    $affiliates_index = array(
            'C08F137534222BD001345BAA60661B97' => 19, //Orkila Tunisia
            '0B366EFAE0524FDAA97A1322A57373BB' => 22, //Orkila East Africa
            'DA0CE0FED12C4424AA9B51D492AE96D2' => 11//Orkila Nigeria
    );

    $lc_bp = array(
            'C08F137534222BD001345BAA60661B97' => '54FC4616619F45398A26B9B9BBAA2B9F'//OTI Tunisie		
    );

    $current_affiliate = 'C08F137534222BD001345BAA60661B97';
    $fromdb = new PostgreSQLConnection('openbrav_production', 'localhost', 'openbrav_appuser', '8w8;MFRy4g^3');
    $todb = new PostgreSQLConnection('openbrav_production', 'localhost', 'openbrav_appuser', '8w8;MFRy4g^3');

    /* Migrate Landed Cost Types */

    /* $lct_query = $fromdb->query('SELECT * FROM m_landedcosts');
      while($lctype = $fromdb->fetch_assoc($lct_query)) {
      echo 'Document: '.$lctype['name'];
      $lctype['lc_m_landedcosts_id'] = $lctype['m_landedcosts_id'];
      unset($lctype['m_landedcosts_id']);
      $todb->insert_query('lc_m_landedcosts', $lctype);
      echo 'Done <br />';

      $lcta_query = $fromdb->query('SELECT * FROM m_landedcosts_acct WHERE m_landedcosts_id=\''.$lctype['lc_m_landedcosts_id'].'\'');
      while($lctacc = $fromdb->fetch_assoc($lcta_query)) {
      $lctacc['lc_m_landedcosts_acct_id'] = $lctacc['m_landedcosts_acct_id'];
      $lctacc['lc_m_landedcosts_id'] = $lctacc['m_landedcosts_id'];
      echo 'Line: '.$lctacc['lc_m_landedcosts_acct_id'].'<br />';
      unset($lctacc['m_landedcosts_id'], $lctacc['m_landedcosts_acct_id']);

      $todb->insert_query('lc_m_landedcosts_acct', $lctacc);
      }
      }
     */
    echo '<hr />';

    $inout_query = $fromdb->query("SELECT * 
							FROM m_inout
							WHERE docstatus NOT IN ('VO', 'CL') AND ad_org_id='".$current_affiliate."' 
							AND m_inout_id IN (SELECT DISTINCT(m_inout_id) FROM m_inout_lcosts)");
    while($order = $fromdb->fetch_assoc($inout_query)) {
        echo 'Document: '.$order['documentno'].'<br />';
        $landedcost = $landedcostline = array();

        $lc_query = $fromdb->query("SELECT * 
							FROM m_inout_lcosts
							WHERE m_inout_id='".$order['m_inout_id']."'");
        while($landedcost = $fromdb->fetch_assoc($lc_query)) {
            $landedcost['lc_m_inout_lcosts_id'] = $landedcost['m_inout_lcosts_id'];
            $landedcost['lc_m_landedcosts_id'] = $landedcost['m_landedcosts_id'];
            unset($landedcost['m_inout_lcosts_id'], $landedcost['m_landedcosts_id']);


            if($todb->num_rows($todb->query('SELECT * FROM m_inout WHERE m_inout_id=\''.$order['m_inout_id'].'\'')) > 0) {
                $todb->insert_query('lc_m_inout_lcosts', $landedcost);

                $lcline_query = $fromdb->query("SELECT * 
							FROM m_inoutline_lcosts
							WHERE m_inout_lcosts_id='".$landedcost['lc_m_inout_lcosts_id']."'");
                while($landedcostline = $fromdb->fetch_assoc($lcline_query)) {
                    $landedcostline['lc_m_inoutline_lcosts_id'] = $landedcostline['m_inoutline_lcosts_id'];
                    $landedcostline['lc_m_inout_lcosts_id'] = $landedcostline['m_inout_lcosts_id'];
                    $landedcostline['lc_m_landedcosts_id'] = $landedcostline['m_landedcosts_id'];
                    unset($landedcostline['m_inoutline_lcosts_id'], $landedcostline['m_inout_lcosts_id'], $landedcostline['m_landedcosts_id']);
                    $todb->insert_query('lc_m_inoutline_lcosts', $landedcostline);
                }
            }
        }
        /* Insert InOut Landed Costs */
        echo '<hr />';
    }
    /* Update Ready for Costing */
    echo 'Updating Ready for Cost';
    $readycosting_query = $fromdb->query('SELECT m_inout_id, isreadycosting FROM m_inout WHERE isreadycosting=\'Y\'');
    while($readycosting = $fromdb->fetch_assoc($readycosting_query)) {
        $id = $readycosting['m_inout_id'];
        $readycosting['em_lc_isreadycosting'] = $readycosting['isreadycosting'];
        unset($readycosting['isreadycosting'], $readycosting['m_inout_id']);
        if($todb->num_rows($todb->query('SELECT * FROM m_inout WHERE m_inout_id=\''.$id.'\'')) > 0) {
            //$todb->update_query('m_inout', $readycosting, 'm_inout_id=\''.$id.'\'');
        }
    }
    echo ' - Done <br />';

    /* Migrate Invoice Lines - Landed Cost */
    /* echo 'Updating Invoice Lines';
      $invoiceline_query = $fromdb->query('SELECT c_invoiceline_id, m_inout_lcosts_id FROM c_invoiceline WHERE m_inout_lcosts_id IS NOT NULL');
      while($invoiceline = $fromdb->fetch_assoc($invoiceline_query)) {
      $id = $invoiceline['c_invoiceline_id'];
      $invoiceline['em_lc_m_inout_lcosts_id'] = $invoiceline['m_inout_lcosts_id'];
      unset($invoiceline['m_inout_lcosts_id'], $invoiceline['c_invoiceline_id']);
      if($todb->num_rows($todb->query('SELECT * FROM c_invoiceline WHERE c_invoiceline_id=\''.$id.'\'')) > 0) {
      $todb->update_query('c_invoiceline', $invoiceline, 'c_invoiceline_id=\''.$id.'\'');
      }
      }
      echo ' - Done <br />'; */
}
?>
