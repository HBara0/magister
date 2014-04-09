<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: generatexpensesreport.php
 * Created:        @tony.assaad    Apr 7, 2014 | 2:52:35 PM
 * Last Update:    @tony.assaad    Apr 7, 2014 | 2:52:35 PM
 */


if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['attendance_canGenerateExpreport'] == 0) {
	error($lang->sectionnopermission);
	exit;
}
if(!$core->input['action']) {
	/* Preparing USers sectio --START */
	if($core->usergroup['attendance_canViewExpenses'] == 1) {
		$identifier = substr(md5(microtime(uniqid())), 0, 10);
		$aff_obj = new Affiliates($core->user['affiliates']);
		$employees = $aff_obj->get_users();
		foreach($employees as $employee) {
			$business_managers[$employee['uid']] = $employee['displayName'];
		}
		$employees_list = parse_selectlist('expencesreport[filter][employees][]', 1, $business_managers, $core->user['uid'], 1, '', '');
	}
	elseif($core->usergroup['attendace_canViewAllAffExpenses'] == 1) {

		$user_objs = Users::get_allusers();
		foreach($user_objs as $user_obj) {
			$employees = $user_obj->get();
			$business_managers[$employees['uid']] = $employees['displayName'];
		}

		$employees_list = parse_selectlist('expencesreport[filter][employees][]', 1, $business_managers, $core->user['uid'], 1, '', '');
	}
	else {
		$aff_obj = new Affiliates($core->user['hraffids']);
		$employees = $aff_obj->get_users();

		foreach($employees as $employee) {
			$business_managers[$employee['uid']] = $employee['displayName'];
		}
		$employees_list = parse_selectlist('expencesreport[filter][employees][]', 1, $business_managers, $core->user['uid'], 1, '', '');
	}
	/* Preparing USers sectio --END */

	// Here we get affiliate for user assigned to, or he can audit
	$afffiliates_users = $core->user['affiliates'] + $core->user['auditfor'];
	foreach($afffiliates_users as $affid => $affiliates) {
		$selected = '';
		$affiliate_obj = new Affiliates($affiliates);
		$affiliates_data = $affiliate_obj->get();
		if($affiliates_data['affid'] == $core->user['mainaffiliate']) {
			$selected = " selected='selected'";
		}
		$affiliates_list.='<option value='.$affiliates_data['affid'].' '.$selected.'>'.$affiliates_data['name'].'</option>';
	}

	$leavetype_objs = Leavetypes::get_allleavetypes();
	foreach($leavetype_objs as $leavetype_obj) {
		$leavetypes = $leavetype_obj->get();
		$leaves_types[$leavetypes['ltid']] = $leavetypes['title'];
	}
	$leavetype_list = parse_selectlist('expencesreport[filter][leavetype][]', 1, $leaves_types, '', 1, '', '');

	/* Leave Expences type */
	$leave_expencestypes = Leavetypes::get_allleaveexptypes();

	foreach($leave_expencestypes as $leave_expencestype) {
		$leave_expencestypes[$leave_expencestype['aletid']] = $leave_expencestype['title'];
	}

	$leave_expencestypes_list = parse_selectlist('expencesreport[filter][leaveexptype][]', 1, $leave_expencestypes, '', 1, '', '');

	$dimensions = array('affid' => $lang->affiliate, 'uid' => $lang->employee, 'ltid' => $lang->leavetype, 'aletid' => $lang->leaveexptype, 'lid' => $lang->leaves);

	foreach($dimensions as $dimensionid => $dimension) {
		$dimension_item.='<li class="ui-state-default" id='.$dimensionid.' title="Click and Hold to move the '.$dimension.'">'.$dimension.'</li>';
	}
	eval("\$expencesreport_options = \"".$template->get('attendance_expencesreport_options')."\";");
	output($expencesreport_options);
}
?>