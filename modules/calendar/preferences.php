<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Calendar User Preferences
 * $module: calendar
 * $id: preferences.php
 * Created: 	@zaher.reda 	May 09, 2011 | 03:04 PM
 * Last Update: @zaher.reda 	September 15, 2011 | 09:41 AM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

/*if($core->usergroup['filesharing_canViewSharedfiles'] == 0) {
	error($lang->sectionnopermission);
	exit;
}*/

if(!$core->input['action']) {
	$query = $db->query("SELECT * FROM ".Tprefix."calendar_userpreferences WHERE uid={$core->user[uid]}");
	if($db->num_rows($query) > 0) {
		$preferences = $db->fetch_array($query);

		if($preferences['excludeHolidays'] == 1) {
			$checkboxes['excludeHolidays'] = ' checked="checked"';
		}
		
		if($preferences['excludeEvents'] == 1) {	
			$checkboxes['excludeEvents'] = ' checked="checked"';
		}
		
		if($preferences['excludeLeaves'] == 1) {	
			$checkboxes['excludeLeaves'] = ' checked="checked"';
		}
			
		$selected['defaultView'][$preferences['defaultView']] = ' selected = "selected"';
		
		$excludedusers_query = $db->query("SELECT * FROM ".Tprefix."calendar_userpreferences_excludedusers u JOIN calendar_userpreferences p ON (u.cpid=p.cpid) WHERE p.uid={$core->user[uid]}");
		if($db->num_rows($excludedusers_query) > 0) {
			while($excludedusers = $db->fetch_array($excludedusers_query)) {
				$user_excludedemployees[$excludedusers['euid']] = $excludedusers['euid'];
			}
		}
		
		$excludedaffiliates_query = $db->query("SELECT * FROM ".Tprefix."calendar_userpreferences_excludedaffiliates a JOIN calendar_userpreferences p ON (a.cpid=p.cpid) WHERE p.uid={$core->user[uid]}");  
		if($db->num_rows($excludedaffiliates_query) > 0) {
			while($excludedaffiliates = $db->fetch_array($excludedaffiliates_query)) {
				$user_excludedaffiliates[$excludedaffiliates['affid']] = $excludedaffiliates['affid'];
			}
		}
	}
	
	$affiliates_query = $db->query("SELECT a.affid, a.name, ae.isMain
						  FROM ".Tprefix."affiliates a LEFT JOIN ".Tprefix."affiliatedemployees ae ON (ae.affid=a.affid)
						  WHERE ae.uid='{$core->user[uid]}'
						  ORDER BY a.name ASC");

	while($affiliate = $db->fetch_array($affiliates_query)) {
		if($affiliate['isMain'] == '1') {
			$affiliates['main']['name'] = $affiliate['name'];
			$affiliates['main']['affid'] = $affiliate['affid'];
		}
		else
		{
			$affiliates['name'][$affiliate['affid']] = $affiliate['name'];
			$affiliates['affid'][$affiliate['affid']] = $affiliate['affid'];
		} 
		
		$checkboxes['affids'] = '';
		if(isset($user_excludedaffiliates[$affiliate['affid']])) {
			$checkboxes['affids'] = ' checked ="checked"';
		}
		
		$row_class = alt_row($row_class);
		if($affiliate['isMain'] == '1') {
			$relatedaffiliates_list .= '<tr class="'.$row_class.'"><td style="width:5%;">&nbsp;</td><td style="width:90%;"><a href="index.php?module=profiles/affiliateprofile&affid='.$affiliate['affid'].'">'.$affiliate['name'].'</a></td></tr>';
		}
		else
		{
			$relatedaffiliates_list .= '<tr class="'.$row_class.'"><td style="width:5%;"><input type="checkbox" value="'.$affiliate['affid'].'" name="excludeaffiliates['.$affiliate['affid'].']" id="excludeaffiliates_'.$affiliate['affid'].'"'.$checkboxes['affids'].' /> </td><td style="width:90%;"><a href="index.php?module=profiles/affiliateprofile&affid='.$affiliate['affid'].'" target="_blank">'.$affiliate['name'].'</a></td></tr>';
		}
	}
	
	$supervisedaff_query = $db->query("SELECT affid, name FROM ".Tprefix."affiliates WHERE supervisor='{$core->user[uid]}' ORDER BY name ASC");
	if($db->num_rows($supervisedaff_query) > 0) {
		while($affiliate = $db->fetch_assoc($supervisedaff_query)) {
			if(!isset($affiliates['affid'][$affiliate['affid']])) {
				if($affiliate['affid'] == $affiliates['main']['affid']) {
					continue;
				}
				$row_class = alt_row($row_class);
				$affiliates['name'][$affiliate['affid']] = $affiliate['name'];
				$affiliates['affid'][$affiliate['affid']] = $affiliate['affid'];
				$relatedaffiliates_list .= '<tr class="'.$row_class.'"><td style="width:5%;"><input type="checkbox" value="'.$affiliate['affid'].'" name="excludeaffiliates['.$affiliate['affid'].']" id="excludeaffiliates_'.$affiliate['affid'].'"'.$checkboxes['affids'].' /> </td><td style="width:90%;"><a href="index.php?module=profiles/affiliateprofile&affid='.$affiliate['affid'].'" target="_blank">'.$affiliate['name'].'</a></td></tr>';

			}
		}
	}
	
	$affiliates['affid'][$affiliates['main']['affid']] = $affiliates['main']['affid'];
	$users = array();
	foreach($affiliates['affid'] as $affid => $affiliate) {
		$affiliateusers_query = $db->query("SELECT u.uid, Concat(u.firstName, ' ', u.lastName) AS employeename, reportsTo
											FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees ae ON (ae.uid=u.uid)
											WHERE affid='{$affiliate}' AND isMain='1' AND gid!=7
											ORDER BY employeename ASC");
		if($db->num_rows($affiliateusers_query)) {
			while($user = $db->fetch_assoc($affiliateusers_query)) {
				if(!isset($users['uid'][$user['uid']])) {
					if($user['uid'] == $core->user['uid']) {
						continue;
					}
					$users['name'][$user['uid']] = $user['employeename'];
					$users['uid'][$user['uid']] = $user['uid'];
				}
				
				$row_class = alt_row($row_class);
				$checkboxes['excludeusers'] = $reportsto_icon = '';
				if(isset($user_excludedemployees[$user['uid']])) {
					$checkboxes['excludeusers'] = ' checked ="checked"';
				}
				
				if($user['reportsTo'] == $core->user['uid']) {
					$reportsto_icon  = '<img src="'.$core->settings['rootdir'].'/images/icons/people.png" alt="&curren;" border="0" />';
				}
				$relatedemployees_list .= '<tr class="'.$row_class.'"><td style="width:5%;"><input type="checkbox" value="'.$user['uid'].'" name="excludeusers['.$user['uid'].']" id="excludeusers_'.$user['uid'].'"'.$checkboxes['excludeusers'].' /> </td><td style="width:90%;"><a href="users.php?action=profile&amp;uid='.$user['uid'].'" target="_blank">'.$user['employeename'].'</a></td><td>'.$reportsto_icon.'</td></tr>';
			}
		}
	}
		
	eval("\$preferencespage = \"".$template->get('calendar_preferences')."\";");
	output_page($preferencespage);	
}
else
{
	if($core->input['action'] == 'save_calendarpreferences') {
		$newpreferences = array(
					'excludeHolidays'  => $core->input['excludeHolidays'],
					'excludeEvents'	=> $core->input['excludeEvents'],
					'excludeLeaves' 	=> $core->input['excludeLeaves'],
					'defaultView' 	=> $core->input['defaultView'],
					'uid' 			  => $core->user['uid']);
		
		if(value_exists('calendar_userpreferences', 'uid', $core->user['uid'])) {	
			$query = $db->update_query('calendar_userpreferences', $newpreferences, "uid={$core->user[uid]}");
			
			$cpid = $db->fetch_field($db->query("SELECT cpid FROM ".Tprefix."calendar_userpreferences WHERE uid='{$core->user[uid]}'"), 'cpid');
			
			$deleteusers_query = $db->delete_query("calendar_userpreferences_excludedusers", "cpid = {$cpid}");	
			$deleteaffiliate_query = $db->delete_query("calendar_userpreferences_excludedaffiliates ", "cpid = {$cpid}");	
		}
		else
		{		
			$query = $db->insert_query('calendar_userpreferences', $newpreferences);
			$cpid = $db->last_id();
		}

		if($query) {
			if(is_array($core->input['excludeusers'])) {
				foreach($core->input['excludeusers'] as $key => $uid) {
					if(empty($uid)) {
						continue;
					}
					$excludedusers =  array(
						'cpid'  => $cpid,
						'euid'  => $uid
						);
					
					$excludedusers_query = $db->insert_query('calendar_userpreferences_excludedusers', $excludedusers);
				}
			}
			
			if(is_array($core->input['excludeaffiliates'])) {
				foreach($core->input['excludeaffiliates'] as $key => $affid) {
					if(empty($affid)) {
						continue;
					}
					$excludedaffiliates = array(
						'cpid'  => $cpid,
						'affid'  => $affid
						);
					
					$excludedaffiliates_query = $db->insert_query('calendar_userpreferences_excludedaffiliates', $excludedaffiliates);
				}
			}
			$log->record($cpid);
			output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
		}
		else
		{
			output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
		}  
	}
}
?>