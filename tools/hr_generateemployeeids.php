<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * Code to generate employees number
 * $id: hr_generateemployeeids.php
 * Created:        @zaher.reda    Mar 12, 2013 | 9:50:00 AM
 * Last Update:    @zaher.reda    Mar 12, 2013 | 9:50:00 AM
 */

require '../inc/init.php';

$query = $db->query('SELECT u.uid, affid, displayName
	FROM users u
	LEFT JOIN userhrinformation ui ON (u.uid=ui.uid)
	JOIN affiliatedemployees ae ON (u.uid=ae.uid)
	WHERE isMain=1 AND gid!=7 AND (employeeNum = "" OR employeeNum IS NULL)
	ORDER BY affid ASC');

while($user = $db->fetch_assoc($query)) {
    $number = Accounts::generate_employeenumber($user['affid']);

    if(value_exists('userhrinformation', 'uid', $user['uid'])) {
        if($core->input['runtype'] != 'dry') {
            $db->update_query('userhrinformation', array('employeeNum' => $number), 'uid='.$user['uid']);
        }
        echo $user['displayName'].' - '.$number.'<br />';
    }
    else {
        if($core->input['runtype'] != 'dry') {
            $db->insert_query('userhrinformation', array('employeeNum' => $number, 'uid' => $user['uid']));
        }
        echo $user['displayName'].' - '.$number.'<br />';
    }
}
?>