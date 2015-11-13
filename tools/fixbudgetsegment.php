<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: fixbudgetsemgment.php
 * Created:        @zaher.reda    Nov 6, 2015 | 7:05:19 PM
 * Last Update:    @zaher.reda    Nov 6, 2015 | 7:05:19 PM
 */

require '../inc/init.php';

$budgetlines = BudgetLines::get_data(array('psid' => '(psid=0 OR psid IS NULL)', 'bid' => 'SELECT bid FROM budgeting_budgets WHERE year=2016'), array('operators' => array('psid' => 'CUSTOMSQLSECURE', 'bid' => 'IN')));
echo count($budgetlines);
echo '<hr/>';
$count = 0;
foreach($budgetlines as $blid => $line) {
    $product = $line->get_product();
    $segment = $product->get_segment();
//    print_r($line);
//    print_r($product);
//    print_r($segment);
//    echo '<hr/>';
    $query = $db->update_query(BudgetLines::TABLE_NAME, array('psid' => $segment['psid']), 'blid='.$blid.' AND psid IS NULL');
    if($db->affected_rows() > 0) {
        $count++;
    }
}
echo $count.' Affected';
//$yeflines = BudgetingYEFLines::get_data(array('psid' => 'psid=0', 'yefid' => 'SELECT yefid FROM budgeting_yearendforecast WHERE year=2015'), array('operators' => array('psid' => 'CUSTOMSQLSECURE', 'yefid' => 'IN')));
//echo count($yeflines);
//echo '<hr/>';
//$count = 0;
//foreach($yeflines as $yeflid => $line) {
//    $product = $line->get_product();
//    $segment = $product->get_segment();
//
//    print_r($line);
//    print_r($product);
//    print_r($segment);
//    echo '<hr/>';
////    $query = $db->update_query(BudgetingYEFLines::TABLE_NAME, array('psid' => $segment['psid']), 'yeflid='.$yeflid.' AND psid=0');
////    if($db->affected_rows() > 0) {
////        $count++;
////    }
//}
//echo $count.' Affected';
