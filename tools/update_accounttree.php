<?php
require '../inc/init.php';
set_time_limit(0);

$affid = 5;
$query = $db->query("SELECT acckey FROM affiliates_accountingtree WHERE affid='{$affid}' ORDER BY acckey ASC");
while($account = $db->fetch_assoc($query)) {
    $accountnumbers[$account['acckey']] = $account['acckey'];
}

foreach($accountnumbers as $accountnumber) {
    $query = $db->query("SELECT acckey, parent FROM affiliates_accountingtree WHERE acckey='{$accountnumber}' ORDER BY acckey ASC");
    //echo "SELECT acckey, parent FROM affiliates_accountingtree WHERE acckey='{$accountnumber}' ORDER BY acckey ASC<br />";
    while($account = $db->fetch_assoc($query)) {
        echo "<br /><br /><strong>Account:".$account['acckey'].'</strong> > Parent: '.$account['parent'].'<br />';

        $query2 = $db->query("SELECT acckey FROM affiliates_accountingtree WHERE (acckey LIKE '{$account{acckey}}%' AND acckey !='{$account{acckey}}') AND affid={$affid} ORDER BY acckey ASC");
        if($db->num_rows($query2) > 0) {
            if(empty($account['parent'])) {
                $db->update_query('affiliates_accountingtree', array('level' => 'E', 'summary' => 'Yes'), 'acckey="'.$account['acckey'].'"');
            }
            else {
                $db->update_query('affiliates_accountingtree', array('level' => 'C', 'summary' => 'Yes'), 'acckey="'.$account['acckey'].'"');
            }

            while($subaccount = $db->fetch_assoc($query2)) {
                $db->update_query('affiliates_accountingtree', array('parent' => $account['acckey']), 'acckey="'.$subaccount['acckey'].'"');
                echo 'Subaccount:'.$subaccount['acckey'].' > Parent: '.$account['acckey'].'<br />';
            }
        }
        else {
            $db->update_query('affiliates_accountingtree', array('level' => 'S', 'summary' => 'No'), 'acckey="'.$account['acckey'].'"');
            //$db->update_query('affiliates_accountingtree',  array('level' => 'C', 'summary' => 'Yes'), 'acckey="'.$account['parent'].'" AND (parent!=0 OR level!="E")');
        }
    }
}
?>