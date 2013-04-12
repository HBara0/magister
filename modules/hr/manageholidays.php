<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Add/Edit holidays
 * $module: hr
 * $id: manageholidays.php	
 * Created:		@najwa.kassem	October 29, 2010 | 9:30 AM
 * Last Update:	@zaher.reda		November 06, 2011 | 01:11 PM
 */
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['hr_canManageHolidays'] == 0) {
	error($lang->sectionnopermission);
}

if(!$core->input['action']) {
	$year_disabled = ' disabled="disabled"';

	$affid = $core->user['mainaffiliate'];
	if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
		if(is_array($core->user['hraffids']) && !empty($core->user['hraffids'])) {
			if(!in_array($core->user['mainaffiliate'], $core->user['hraffids'])) {
				$affid = $core->user['hraffids'][current($core->user['hraffids'])];
			}
		}
		else {
			error($lang->sectionnopermission);
			exit;
		}
	}

	for($i = 1; $i <= 12; $i++) {
		$months[$i] = $lang->{strtolower(date('F', mktime(0, 0, 0, $i, 1, 0)))};
	}

	for($i = 1; $i <= 31; $i++) {
		$days[$i] = $i;
	}

	$employees_query = $db->query("SELECT u.uid, CONCAT(firstName, ' ', lastName) as fullname 
									FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees a ON (a.uid=u.uid) 
									WHERE a.affid ={$core->user[mainaffiliate]} AND isMain=1 AND u.gid!=7");

	while($employee = $db->fetch_array($employees_query)) {
		$employees[$employee['uid']] = $employee['fullname'];
	}
	if(isset($core->input['id'])) {
		$query = $db->query("SELECT * FROM ".Tprefix."holidays WHERE hid=".$db->escape_string($core->input['id'])."");
		if($db->num_rows($query) > 0) {
			$holiday = $db->fetch_assoc($query);
			if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
				//if($holiday['affid'] != $core->user['mainaffiliate']) {
				if(!in_array($holiday['affid'], $core->user['hraffids'])) {
					redirect('index.php?module=hr/holidayslist');
				}
			}
			if(isset($holiday['validFrom']) && !empty($holiday['validFrom'])) {
				$holiday['validFromOuptut'] = date($core->settings['dateformat'], $holiday['validFrom']);
				$holiday['fromTime'] = date('h:i', $holiday['validFrom']);
			}


			if(isset($holiday['validTo']) && !empty($holiday['validTo'])) {
				$holiday['validToOutput'] = date($core->settings['dateformat'], $holiday['validTo']);
				$holiday['toTime'] = date('h:i', $holiday['validTo']);
			}

			$action = 'do_edit';
			$pagetitle = $lang->editholiday;

			$affid_field = '<input type="hidden" id="affid" name="affid" value="'.$holiday['affid'].'" />';
			$hid_field = '<input type="hidden" id="hid" name="hid" value="'.$core->input['id'].'" />';

			$exception_query = $db->query('SELECT uid FROM '.Tprefix.'holidaysexceptions 
											WHERE hid='.$db->escape_string($core->input['id']));

			while($exception = $db->fetch_assoc($exception_query)) {
				$exceptions[] = $exception['uid'];
			}

			$exceptionsemployees_list = parse_selectlist('uid[]', 7, $employees, $exceptions, 1);

			if($holiday['isOnce'] == 1) {
				$checkedboxes['isOnce'] = ' checked="checked"';
				$year_disabled = '';
			}
			else {
				$checkedboxes['isOnce'] = '';
				$year_disabled = ' disabled="disabled"';
			}

			$months_list = parse_selectlist('month', 1, $months, $holiday['month'], 0, '', '', array('required' => 'required'));
			$days_list = parse_selectlist('day', 1, $days, $holiday['day'], 0, '', array('required' => 'required'));
		}
		else {
			redirect('index.php?module=hr/holidayslist');
		}
	}
	else {
		$action = 'do_add';
		if($core->usergroup['hr_canHrAllAffiliates'] == 1) {
			$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'));
			$affid_field = $lang->affiliate.': '.parse_selectlist('affid', 1, $affiliates, $affid, 0);
		}
		else {
			if(is_array($core->user['hraffids']) && !empty($core->user['hraffids']) && count($core->user['hraffids']) > 1) {
				$affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, 'affid IN ('.implode(',', $core->user['hraffids']).')');
				$affid_field = $lang->affiliate.': '.parse_selectlist('affid', 1, $affiliates, $affid, 0);
			}
			else {
				$affid_field = '<input type="hidden" id="affid" name="affid" value="'.$core->user['mainaffiliate'].'" />';
			}
		}

		$pagetitle = $lang->addholiday;
		$months_list = parse_selectlist('month', 1, $months, 0, 0, '', array('required' => 'required'));
		$days_list = parse_selectlist('day', 1, $days, 0, 0, '', array('required' => 'required'));

		$exceptionsemployees_list = parse_selectlist('uid[]', 1, $employees, 0, 1);
	}

	eval("\$managepage = \"".$template->get('hr_manageholidays')."\";");
	output_page($managepage);
}
else {
	if($core->input['action'] == 'do_add' || $core->input['action'] == 'do_edit') {
		$action = $core->input['action'];

		if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
			if(is_array($core->user['hraffids']) && !empty($core->user['hraffids'])) {
				if(!in_array($core->input['affid'], $core->user['hraffids'])) {
					output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
					exit;
				}
			}
			else {
				output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
				exit;
			}
		}

		if(is_empty($core->input['title'], $core->input['numDays'])) {
			output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
			exit;
		}

		if(!is_empty($core->input['validFrom'], $core->input['fromTime'])) {
			$core->input['validFrom'] = strtotime($core->input['validFrom'].' '.$core->input['fromTime']);
		}
		if(!is_empty($core->input['validTo'], $core->input['toTime'])) {
			$core->input['validTo'] = strtotime($core->input['validTo'].' '.$core->input['toTime']);
		}

		$core->input['name'] = strtolower(trim($core->input['title']));
		$core->input['name'] = preg_replace('/\s+/', '', $core->input['name']);
		$core->input['name'] = preg_replace("/[^a-zA-Z0-9]/", '', $core->input['name']);

		$exceptions = $core->input['uid'];

		if(!isset($core->input['isOnce'])) {
			$core->input['isOnce'] = 0;
		}

		if($core->input['isOnce'] == 0) {
			$core->input['year'] = '';
		}
		elseif($core->input['isOnce'] == 1) {
			if(empty($core->input['year'])) {
				output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
				exit;
			}
		}

		if($action != 'do_edit') {
			if(value_exists('holidays', 'name', $core->input['name'], "affid = {$core->input[affid]} AND (year='{$core->input[year]}' OR year='') AND month={$core->input[month]} AND day={$core->input[day]}")) {
				output_xml("<status>false</status><message>{$lang->holidayexists}</message>");
				exit;
			}
		}

		unset($core->input['action'], $core->input['module'], $core->input['uid'], $core->input['fromTime'], $core->input['toTime']);

		if($action == 'do_edit') {
			$query = $db->update_query('holidays', $core->input, "hid='".$db->escape_string($core->input['hid'])."'");
		}
		else {
			$query = $db->insert_query('holidays', $core->input);
		}

		if($query) {
			if($action == 'do_add') {
				$hid = $db->last_id();
			}
			else {
				$hid = $core->input['hid'];
			}

			if(isset($exceptions)) {
				foreach($exceptions as $key => $uid) {
					$db->insert_query('holidaysexceptions', array('hid' => $hid, 'uid' => $uid));
				}
			}
			$log->record($hid);
			output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
		}
		else {
			output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
		}
	}
	elseif($core->input['action'] == 'get_affiliateemployees') {
		$employees_query = $db->query("SELECT u.uid, CONCAT(firstName, ' ', lastName) as fullname 
								FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees a ON (a.uid=u.uid) 
								WHERE a.affid='".$db->escape_string($core->input['affid'])."' AND isMain=1 AND u.gid!=7");

		while($employee = $db->fetch_array($employees_query)) {
			$employees[$employee['uid']] = $employee['fullname'];
		}
		if(empty($employees)) {
			$employees[] = '';
		}
		echo parse_selectlist('uid[]', 7, $employees, '', 1);
	}
}
?>