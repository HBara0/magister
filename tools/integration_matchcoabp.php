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
    $db_info = array('database' => 'openbrav_production', 'username' => 'openbrav_appuser', 'password' => '8w8;MFRy4g^3', 'engine' => 'postgre');
    $integration = new IntegrationOB($db_info, 'C08F137534222BD001345B7B2E8F182D', $affiliates_index, 3, array('from' => '2015-01-01'));

    $status = $integration->get_status();
    if(!empty($status)) {
        echo 'Error';
        exit;
    }
    $conn = $integration->get_dbconn();
    $config['schema'] = '0C9E56E5089B40DFB98431ABA43D24AC';
    $config['tree'] = '5F6577EE80EC45A1AF152C4494D238D3';
    $query = $conn->query('SELECT * FROM c_elementvalue WHERE c_element_ID =\''.$config['tree'].'\'');
    echo '<table width="100%">';
    while($account = $conn->fetch_assoc($query)) {
        if(intval($account['value']) < 411100000000 || intval($account['value']) > 411109999999) {
            continue;
        }

        $validcombination = IntegrationOBValidCombination::get_data('C_AcctSchema_ID=\''.$config['schema'].'\' AND account_id=\''.$account['c_elementvalue_id'].'\'');
        if(!is_object($validcombination)) {
            continue;
        }

        echo '<tr>';
        echo '<td>'.$account['value'].' '.$account['name'].'</td>';
        $account['name'] = str_replace(array('USD', 'EUR', 'EURO', 'GBP', 'LBP'), '', $account['name']);
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
                    if($query) {
                        echo ' Done';
                    }
                }
                echo '<br />';
            }
            echo '</td>';
            $counts['match'] ++;
        }
        else {
            $account['name'] = str_replace(array('ltd', 'S.A.R.L.', 'S.A.R.L', 'SARL', 'S.A.L', 'SAL'), '', $account['name']);
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
                        if($query) {
                            echo ' Done';
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

