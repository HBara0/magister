<?php
/*
* Orkila Central Online System (OCOS)
* Copyright © 2009 Orkila International Offshore, All Rights Reserved
* 
* Set additional leaves
* $module: attendance
* $id: addadditionalleave.php	
* Created:	   	@najwa.kassem		Jan 18, 2011 | 9:37 AM
* Last Update: 	@zaher.reda		  	October 24, 2011 | 05:22 PM
*/

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canUseHR'] == 0) {
	error($lang->sectionnopermission);
}

if(!$core->input['action']) {	
	$query = $db->query("SELECT u.uid, CONCAT(firstName, ' ', lastName) as fullname 
						FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees a ON (a.uid=u.uid) 
						WHERE a.affid={$core->user[mainaffiliate]} AND isMain=1 AND u.gid!=7
						ORDER BY fullname ASC");
	while($user = $db->fetch_array($query)) {
		$users[$user['uid']] = $user['fullname'];
	}
			
	$users_list = parse_selectlist('uid[]', 1, $users, '', 1);
 
 	eval("\$addadditionalleaves = \"".$template->get('attendance_addadditionalleaves')."\";");
	output_page($addadditionalleaves);	
}
else
{
	if($core->input['action'] == 'do_addadditionalleaves') {
		if(is_empty($core->input['numDays'], $core->input['date'], $core->input['remark']) || (!is_array($core->input['uid']))) {
			output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
			exit;
		}
		else
		{
			$leavedate = explode('-', $core->input['date']);
		
			if(checkdate($leavedate[1], $leavedate[0], $leavedate[2])) {
				$core->input['date'] = mktime(0, 0, 0, $leavedate[1], $leavedate[0], $leavedate[2]);
			}
			else
			{
				output_xml("<status>false</status><message>{$lang->invalidtodate}</message>");
				exit;
			}
			
			if($core->input['correspondToDate'] == 1) {
				$period = $core->input['date'];
			}
			else
			{
				$period = TIME_NOW;
			}
			
			$uids = $core->input['uid'];
			$core->input['addedBy'] = $core->user['uid'];
			foreach($uids as $uid) {
				unset($core->input['module'],$core->input['action'],$core->input['correspondToDate']);
					
				$core->input['uid'] = $db->escape_string($uid);
				if(!value_exists('attendance_additionalleaves', 'uid', $core->input['uid'], 'date='.$db->escape_string($core->input['date']))) {
					$insert_query[$core->input['uid']] = $db->insert_query('attendance_additionalleaves', $core->input);
					$log->record($db->last_id());
					
					$leavestats_query =  $db->query("SELECT lsid, additionalDays FROM ".Tprefix."leavesstats
											WHERE uid={$uid} AND ltid=1 AND {$period} BETWEEN periodStart AND periodEnd");
					if($db->num_rows($leavestats_query) > 0) {
						while($leavestat = $db->fetch_array($leavestats_query)) {
							$additionalDays = $leavestat['additionalDays'];
							$lsid = $leavestat['lsid'];
						}
						$additionalDays += $core->input['numDays'] ;
						$db->update_query('leavesstats', array('additionalDays' => $additionalDays), "lsid={$lsid}");
					}
				}
				else
				{
					$errors['recordexists']['uid'][] = $core->input['uid'];
				}
			}
			
			if(is_array($errors)) {
				output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
			}
			else
			{
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
				$log->record($uids, $core->input['numDays'], $core->input['date']);
			}
		}			
	} 
}
?>