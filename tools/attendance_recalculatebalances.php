<?php
/*
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 * 
 * Rebuilds all leaves balances for a given affiliate
 * $id: attendance_recalculatebalances.php
 * Created:        @zaher.reda    Dec 4, 2012 | 9:56:23 AM
 * Last Update:    @zaher.reda    Dec 4, 2012 | 9:56:23 AM
 */

require '../inc/init.php';
require '../inc/attendance_functions.php';

$options['affid'] = 5;

$query = $db->query('SELECT * 
	FROM '.Tprefix.'leaves 
	WHERE uid IN (SELECT uid FROM '.Tprefix.'affiliatedemployees WHERE isMain=1 AND affid='.intval($options['affid']).')
	ORDER BY fromDate ASC');

while($leave = $db->fetch_assoc($query)) {
	if($db->fetch_field($db->query("SELECT COUNT(*) AS count FROM leavesapproval WHERE isApproved=0 AND lid={$leave[lid]}"), 'count') == 0) {
		$leavetype_details = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."leavetypes WHERE ltid='".$leave['type']."'"));

		update_leavestats_periods($leave, $leavetype_details['isWholeDay']);
	}
	else
	{
		continue;
	}
}

$query = $db->query('SELECT * FROM '.Tprefix.'attendance_additionalleaves WHERE uid IN (SELECT uid FROM '.Tprefix.'affiliatedemployees WHERE isMain=1 AND affid='.intval($options['affid']).')');
while($adday = $db->fetch_assoc($query)) {
	$relstat = $db->fetch_assoc($db->query('SELECT lsid, additionalDays FROM '.Tprefix.'leavesstats WHERE ('.$adday['date'].' BETWEEN periodStart AND periodEnd) AND uid='.$adday['uid']));
	if(!empty($relstat)) {
		$db->update_query('leavesstats', array('additionalDays' => $relstat['additionalDays']+$adday['numDays']), 'lsid='.$relstat['lsid']);
	}
	else
	{
		echo 'Cannot find period for add days #'.$adday['adid'].'<br />';
	}
}
?>
