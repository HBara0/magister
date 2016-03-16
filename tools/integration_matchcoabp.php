<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: integration_matchcoabp.php
 * Created:        @zaher.reda    Nov 11, 2015 | 12:47:45 PM
 * Last Update:    @zaher.reda    Nov 11, 2015 | 12:47:45 PM
 */

require '../inc/init.php';

define('AUTHCODE', 'X1iIMm7pG06ip6o95HEa');
if($core->input['authCode'] == AUTHCODE) {
    $db_info = array('hostname' => '184.107.151.42', 'database' => 'openbrav_production', 'username' => 'openbrav_appuser', 'password' => '8w8;MFRy4g^3', 'engine' => 'postgre');
    //$db_info = array('hostname' => 'dev-server.orkilalb.local', 'database' => 'openbravo-eval', 'username' => 'openbrav_appuser', 'password' => '8w8;MFRy4g^3', 'engine' => 'postgre');

    $integration = new IntegrationOB($db_info, 'C08F137534222BD001345B7B2E8F182D', $affiliates_index, 3, array('from' => '2015-01-01'));

    $status = $integration->get_status();
    if(!empty($status)) {
        echo 'Error';
        exit;
    }

    $accts_suffix = array(
            'LBP' => '01',
            'EUR' => '11',
            'USD' => '02',
            'JOD' => '08',
            'GBP' => '05',
            'FRF' => '03'
    );
    $conn = $integration->get_dbconn();
    $config['schema'] = '54228FAD4BCC484C8EC50B7AE4284FB1';
    $schema = new IntegrationOBAcctSchema($config['schema'], $conn);
    $config['tree'] = '5F6577EE80EC45A1AF152C4494D238D3';
    $query = $conn->query('SELECT * FROM c_elementvalue WHERE c_element_ID =\''.$config['tree'].'\'');
    echo '<table width="100%">';
    while($account = $conn->fetch_assoc($query)) {
        if(($account['value'] < 411100000000 || $account['value'] > 411109999999) && ($account['value'] < 401110000000 || $account['value'] > 401119999999) && ($account['value'] < 401120000000 || $account['value'] > 401129999999) && ($account['value'] < 461900000000 || $account['value'] > 461999999999)) {
            continue;
        }
//
//        if(substr($account['value'], -2) != $accts_suffix[$schema->get_currency()->get()['iso_code']]) {
//            continue;
//        }

        $validcombination = IntegrationOBValidCombination::get_data('C_AcctSchema_ID=\''.$config['schema'].'\' AND account_id=\''.$account['c_elementvalue_id'].'\'');
        if(!is_object($validcombination)) {
            continue;
        }
        /**
         * IF APPLICABLE ONLY
         * Remove currency suffix from account
         */
        $account['value'] = substr_replace($account['value'], '', -2, 2);

        echo '<tr>';
        echo '<td>'.str_replace(array('4111', '40111', '40112', '4619'), '', $account['value']).' '.$account['name'].'</td>';
        $account['newnum'] = str_replace(array('4111', '40111', '40112', '4619'), '', $account['value']);
        $account['name2'] = $account['name'];
        $account['name'] = str_replace(array('USD', 'EUR', 'EURO', 'GBP', 'LBP'), '', $account['name']);
        $account['name'] = trim($account['name']);
        $account['name2'] = trim($account['name2']);
//        if(preg_match('/(USD|EUR|LBP|EURO|GBP|LBP)/', $account['name2'])) {
//            if(!strstr($account['name2'], $schema->get_currency()->get()['iso_code'])) {
//                continue;
//            }
//        }
        //$account['value'] = str_replace(array('USD', 'EUR', 'EURO', 'GBP', 'LBP'), '', $account['name']);


        $bps = IntegrationOBBPartner::get_data('LOWER(name)=LOWER(\''.$account['name'].'\')', array('returnarray' => true));
        if(is_array($bps)) {
            echo '<td>';
            foreach($bps as $bp) {
                echo $bp->name;

                $acct = IntegrationOBBPCustAcct::get_data('C_AcctSchema_ID=\''.$config['schema'].'\' AND c_bpartner_id=\''.$bp->{IntegrationOBBPartner::PRIMARY_KEY}.'\'');
                if(is_object($acct)) {
                    echo ' => '.$acct->{IntegrationOBBPCustAcct::PRIMARY_KEY};
                    // $query = $conn->update_query(IntegrationOBBPCustAcct::TABLE_NAME, array('C_Receivable_Acct' => $validcombination->get_id()), IntegrationOBBPCustAcct::PRIMARY_KEY.'=\''.$acct->{IntegrationOBBPCustAcct::PRIMARY_KEY}.'\'');
                    $existingvalue = IntegrationOBBPAuxAccounts::get_data('ad_org_id=\''.$schema->get_organisation()->get_id().'\' AND c_acctschema_id=\''.$config['schema'].'\' AND c_bpartner_id=\''.$bp->{IntegrationOBBPartner::PRIMARY_KEY}.'\'');
                    if(!is_object($existingvalue)) {
                        $array = array('ork_bpauxaccounts_id' => 'get_uuid()', 'ad_client_id' => 'C08F137534222BD001345B7B2E8F182D', 'ad_org_id' => $schema->get_organisation()->get_id(), 'c_acctschema_id' => $config['schema'], 'acctnum' => $account['newnum'], 'c_bpartner_id' => $bp->{IntegrationOBBPartner::PRIMARY_KEY}, 'createdby' => '0', 'created' => date('Y-m-d H:i:s'), 'updatedby' => '0', 'updated' => date('Y-m-d H:i:s'));
                        //$query2 = $conn->insert_query(IntegrationOBBPAuxAccounts::TABLE_NAME, $array, array('isfunction' => array('ork_bpauxaccounts_id' => true)));
                        if($query2) {
                            echo ' Done';
                        }
                    }
                    else {
                        echo ' Exists';
                    }
                }
                echo '<br />';
            }
            echo '</td>';
            $counts['match'] ++;
        }
        else {
            $account['name'] = str_replace(array('ltd', 'S.A.R.L.', 'S.A.R.L', 'SARL', 'S.A.L', 'SAL'), '', $account['name']);
            // $account['name'] = str_replace(array('´'), '\\\'', $account['name']);
            $account['name'] = trim($account['name']);
            $bps = IntegrationOBBPartner::get_data('LOWER(name)=LOWER(\''.$account['name'].'\')', array('returnarray' => true));
            if(is_array($bps)) {
                echo '<td>';
                foreach($bps as $bp) {
                    echo $bp->name;
                    $acct = IntegrationOBBPCustAcct::get_data('C_AcctSchema_ID=\''.$config['schema'].'\' AND c_bpartner_id=\''.$bp->{IntegrationOBBPartner::PRIMARY_KEY}.'\'');
                    if(is_object($acct)) {
                        echo ' => '.$acct->{IntegrationOBBPCustAcct::PRIMARY_KEY};
                        // $query = $conn->update_query(IntegrationOBBPCustAcct::TABLE_NAME, array('C_Receivable_Acct' => $validcombination->get_id()), IntegrationOBBPCustAcct::PRIMARY_KEY.'=\''.$acct->{IntegrationOBBPCustAcct::PRIMARY_KEY}.'\'');

                        $existingvalue = IntegrationOBBPAuxAccounts::get_data('ad_org_id=\''.$schema->get_organisation()->get_id().'\' AND c_acctschema_id=\''.$config['schema'].'\' AND c_bpartner_id=\''.$bp->{IntegrationOBBPartner::PRIMARY_KEY}.'\'');
                        if(!is_object($existingvalue)) {
                            $array = array('ork_bpauxaccounts_id' => 'get_uuid()', 'ad_client_id' => 'C08F137534222BD001345B7B2E8F182D', 'ad_org_id' => $schema->get_organisation()->get_id(), 'c_acctschema_id' => $config['schema'], 'acctnum' => $account['newnum'], 'c_bpartner_id' => $bp->{IntegrationOBBPartner::PRIMARY_KEY}, 'createdby' => '0', 'created' => date('Y-m-d H:i:s'), 'updatedby' => '0', 'updated' => date('Y-m-d H:i:s'));
                            // $query2 = $conn->insert_query(IntegrationOBBPAuxAccounts::TABLE_NAME, $array, array('isfunction' => array('ork_bpauxaccounts_id' => true)));

                            if($query2) {
                                echo ' Done';
                            }
                        }
                        else {
                            echo ' Exists';
                        }
                    }

                    echo '<br />';
                }
                echo '</td>';
                $counts['match'] ++;
            }
            else {
                echo '<td>No Match</td>';
                $counts['nomatch'] ++;
            }
        }
        echo '</tr>';
    }

    echo '</table>';

    print_R($counts);
}

