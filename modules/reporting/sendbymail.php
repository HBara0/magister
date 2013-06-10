<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Send reports by email
 * $module: reporting
 * $id: sendbyemail.php	
 * Created:		@zaher.reda		August 17, 2009
 * Last Update: @zaher.reda 	July 16, 2012 | 11:26 AM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['reporting_canSendReportsEmail'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

$session->start_phpsession();
$lang->load('messages');
if(!$core->input['action']) {
	if(isset($core->input['identifier'])) {
		$core->input['identifier'] = $db->escape_string($core->input['identifier']);
		//$identifier = explode('_', $core->input['identifier']);
		$meta_data = unserialize($session->get_phpsession('reportmeta_'.$core->input['identifier']));
		/* 	list($suppliername, $eid) = $db->fetch_array($db->query("SELECT e.companyName AS suppliername, e.eid FROM ".Tprefix."entities e, ".Tprefix."reports r 
		  WHERE r.spid=e.eid AND r.rid='".$db->escape_string($meta_data['spid'][1])."'"));
		 */
		switch($meta_data['type']) {
			case 'm': $type = 'monthly';
				$subject_monthquarter = $lang->{strtolower(date('F', mktime(0, 0, 0, $meta_data['month'], 1, 0)))};
				$default_cc = '';
				break;
			case 'q':
			default: $type = 'quarterly';
				if(!empty($meta_data['quarter'])) {
					$subject_monthquarter = 'Q'.$meta_data['quarter'];
				}
				$default_cc = $core->settings['sendreportsto'];
				break;
		}

		if($meta_data['spid'] == 0) {
			$suppliername = '';
			$eid = 0;
		}
		else {
			if(is_array($meta_data['spid'])) {
				$meta_data['spid'] = array_unique($meta_data['spid']);
				if(count($meta_data['spid']) == 1) {
					list($suppliername, $eid) = $db->fetch_array($db->query("SELECT companyName AS suppliername, eid FROM ".Tprefix."entities WHERE eid='".$db->escape_string($meta_data['spid'][0])."'"));
				}
				else {
					$suppliername = '';
					$eid = 0;
				}
			}
			else {
				list($suppliername, $eid) = $db->fetch_array($db->query("SELECT companyName AS suppliername, eid FROM ".Tprefix."entities WHERE eid='".$db->escape_string($meta_data['spid'])."'"));
			}
		}

		if(!empty($eid)) {
			$query = $db->query("SELECT er.*, r.* 
								FROM ".Tprefix."entitiesrepresentatives er LEFT JOIN ".Tprefix."representatives r ON (r.rpid=er.rpid)
								WHERE er.eid='{$eid}'");
			while($representative = $db->fetch_array($query)) {
				$representatives_list .= '<tr><td style="width: 20%; font-weight: bold;">';
				$representatives_list .= '<input type="checkbox" name="recipients[]" id="recipient_'.$representative['rpid'].'" value="'.base64_encode($representative['email']).'" checked/> '.$representative['name'].'</td>';
				$representatives_list .= '<td>'.$representative['email'].'</td></tr>';
				$hiddenfield.='<td><input type="hidden" name="recipientsid[]" value='.$representative['rpid'].'></td>';
			}
		}
		//$default_cc = $core->settings['sendreportsto'];
		if($core->user['email'] != $core->settings['sendreportsto']) {
			$default_cc .= ', '.$core->user['email'];
		}

		$attachments = "<li><a href='{$core->settings[exportdirectory]}{$type}reports_{$core->input[identifier]}.pdf' target'_blank'>{$type}reports_{$core->input[identifier]}.pdf</a></li>";
		$attachments .= "<input type='hidden' value='./{$core->settings[exportdirectory]}{$type}reports_{$core->input[identifier]}.pdf' name='attachment' />";

		/* Parse Signature - START */
		$profile = $db->fetch_assoc($db->query("SELECT *, CONCAT(firstName, ' ', lastName) AS fullname FROM ".Tprefix."users WHERE uid='{$core->user[uid]}'"));
		$positions = $db->fetch_assoc($db->query("SELECT title FROM ".Tprefix."userspositions u JOIN ".Tprefix."positions p ON (u.posid=p.posid) WHERE uid='{$core->user[uid]}'"));
		$postions = implode(', ', $positions);
		$company = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."affiliates WHERE affid='{$core->user[mainaffiliate]}'"));
		$country = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."countries WHERE coid=".$company['country']), 'name');

		$signature = $profile['fullname'].'<br /><img src="'.$core->settings['rootdir'].'/images/Orkila_logo_2.jpg" width="50" /><br />'.$postions.'<br />'.$company['addressLine1'].' - PO box '.$company['poBox'].' - '.$company['city'].' - '.$country.'<br />Tel:'.$company['phone1'].'<br />Fax:'.$company['fax'].'<br />Mobile:'.$profile['mobile'].'<br />E-mail:'.$profile['email'].'<br />Website : www.orkila.com';
		/* Parse Signature - END */

		$lang->sendbymailsubject = $lang->sprint($lang->sendbymailsubject, ucfirst($lang->{$type}), $subject_monthquarter.'/'.$meta_data['year'], $suppliername);
		$lang->sendbymaildefault = $lang->sprint($lang->sendbymaildefault, strtolower($lang->{$type}));

		eval("\$sendbymailpage = \"".$template->get('reporting_sendbymail')."\";");
		output_page($sendbymailpage);
	}
	else {
		redirect('index.php?module=reporting/generatereport');
	}
}
else {
	if($core->input['action'] == 'do_sendbymail') {
		$meta_data = unserialize($session->get_phpsession('reportmeta_'.$db->escape_string($core->input['identifier'])));
		if(empty($core->input['recipients']) && empty($core->input['additional_recipients'])) {
			error($lang->norecipientsselected, $_SERVER['HTTP_REFERER']);
		}

		if(is_empty($core->input['subject'], $core->input['message'])) {
			error($lang->incompletemessage, $_SERVER['HTTP_REFERER']);
		}

		if(is_array($core->input['recipients'])) {
			foreach($core->input['recipients'] as $recid => $val) {
				$email = base64_decode($val);
				if(isvalid_email($email)) {
					$valid_emails[] = $email;
				}
				else {
					$bad_emails[] = $email;
				}
			}
			switch($meta_data['type']) {
				case'q':
					/*  recorded in a recipients table --START */
					$report = new ReportingQr($meta_data);
					foreach($core->input[recipientsid] as $rpid) {
						$report->create_recipients($rpid, $core->input['identifier']);
					}

					/*  recorded in a recipients table --END */
					break;
			}
		}

		if(!empty($core->input['additional_recipients'])) {
			$additional_emails = explode(',', $core->input['additional_recipients']);
			foreach($additional_emails as $val) {
				if(isvalid_email(trim($val))) {
					$cc_valid_emails[] = trim($val);
				}
				else {
					$cc_bad_emails[] = $val;
				}
			}
		}
		$core->input['message'] = $core->sanitize_inputs($core->input['message'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true));
		switch($meta_data['type']) {
			case'm':
				$email_data = array(
						'from_email' => 'reporting@ocos.orkila.com',
						'from' => 'Orkila Reporting System',
						'to' => $valid_emails,
						'cc' => $cc_valid_emails,
						'subject' => $core->input['subject'],
						'message' => $core->input['message'],
						'attachments' => array($core->input['attachment'])
				);
				break;
		}


		switch($meta_data['type']) {
			case'q':
				$recipient = $report->get_recipient($core->input[recipientsid]);
				if(is_array($recipient)) {
					foreach($recipient as $rcid => $recipentdata) {
						$previewreport_link = '';
						$body_message = '';
						$recipentdata['password']= str_replace($recipentdata['salt'],'',base64_decode($recipentdata['password']));
						//$previewreport_link[$rcid] = 'http://www.orkila.com/reporting/preview&reportidentifier='.$recipentdata['reportIdentifier'].'&token='.$recipentdata['token'].'';
						$previewreport_link[$rcid] = 'http://10.0.0.98/website/index.php?module=reporting/preview&reportidentifier='.$recipentdata['reportIdentifier'].'&token='.$recipentdata['token'].'';
						$body_message[$rcid] = '<br>'.$previewreport_link[$rcid].' <br>Your new  passowrd is: '.$recipentdata['password'].'<br>';
						$recipientemail_data[$rcid] = array(
								'from_email' => 'reporting@ocos.orkila.com',
								'from' => 'Orkila Reporting System',
								'to' => $recipentdata['email'],
								'subject' => 'External link to view quarterly Report',
								'message' => 'follow the blow link :<br>'.$body_message[$rcid],
						);
						$mail = new Mailer($recipientemail_data[$rcid], 'php');
					}
				}
				redirect('index.php?module=reporting/generatereport', 1, $lang->messagesentsuccessfully);
				break;
		}
		if(is_array($email_data)) {
			$mail = new Mailer($email_data, 'php');

			if($mail->get_status() === true) {
				if(is_array($meta_data['rid'])) {
					$update_query_where = 'rid IN ('.implode(',', $meta_data['rid']).')';
				}
				else {
					$update_query_where = "rid = '{$meta_data[rid]}'";
				}
				$db->update_query('reports', array('isSent' => 1, 'isApproved' => 1, 'isLocked' => 1), $update_query_where);

				log_action($valid_emails, $cc_valid_email);
				if(is_array($core->input['attachment'])) {
					unlink($core->input['attachment']);
				}
				exit;
				redirect('index.php?module=reporting/generatereport', 1, $lang->messagesentsuccessfully);
			}
			else {
				error($lang->errorsendingemail);
			}
		}
	}
}
?>