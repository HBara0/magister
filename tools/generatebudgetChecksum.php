<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: generatebudgetChecksum.php
 * Created:        @rasha.aboushakra    Nov 20, 2014 | 3:10:43 PM
 * Last Update:    @rasha.aboushakra    Nov 20, 2014 | 3:10:43 PM
 */

require '../inc/init.php';


$sql = "SELECT blid, inputChecksum FROM ".Tprefix."budgeting_budgets_lines WHERE inputChecksum='' OR inputChecksum IS NULL";
$query = $db->query($sql);
if($db->num_rows($query) > 0) {
    while($budgeline = $db->fetch_assoc($query)) {
        $budgetline['inputChecksum'] = generatechecksum();
        $db->update_query('budgeting_budgets_lines', array('inputChecksum' => $budgetline['inputChecksum']), "blid=".$budgeline['blid']);
    }
}
function generatechecksum() {
    global $db;
    $checksum = generate_checksum('budget');
    $sql2 = "SELECT blid FROM ".Tprefix."budgeting_budgets_lines WHERE inputChecksum='".$checksum."'";
    $query2 = $db->query($sql2);
    if($db->num_rows($query2) > 0) {
        generatechecksum();
    }
    else {
        return $checksum;
    }
}
