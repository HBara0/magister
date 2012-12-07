<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * List Requests for Chemcials
 * $module: Sourcing
 * $id:  listchemcialsrequests.php	
 * Created By: 		@tony.assaad		November 15, 2012 | 3:30 PM
 * Last Update: 	@tony.assaad		November 19, 2012 | 9:13 AM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['sourcing_canListSuppliers'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	$potential_supplier = new Sourcing();
	$sort_url = sort_url();
	$chemicalrequests = $potential_supplier->get_chemicalrequests();
	if(is_array($chemicalrequests)) {
		foreach($chemicalrequests as $chemicalrequest) {
			/* colorate Satisfied request */
			if($chemicalrequest['isClosed'] == 1) {
				$feedback_icon = 'valid.gif';
				$rowcolor = 'greenbackground';
			}
			elseif($chemicalrequest['isClosed'] == 0) {
				$feedback_icon = 'edit.gif';
				$rowcolor = 'unapproved';
			}


			$chemicalrequest['timeRequested_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $chemicalrequest['timeRequested']);
			eval("\$chemcialsrequests_rows .= \"".$template->get('sourcing_listchemcialsrequests_rows')."\";");
		}
	}
	else {
		$chemcialsrequests_rows = '<tr><td colspan="4">'.$lang->na.'</td></tr>';
	}
	eval("\$sourcing_listchemcialsrequests = \"".$template->get('sourcing_listchemcialsrequests')."\";");
	output_page($sourcing_listchemcialsrequests);
}
else {
	if($core->input['action'] == 'get_feedbackform') {
		$request_id = $core->input['id'];
		$potential_supplier = new Sourcing();

		$feedback = $potential_supplier->get_feedback($request_id);
		if($feedback['isClosed'] == 1) {
			$feedback['feedbackTime_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $feedback['feedbackTime']);
			eval("\$sourcingfeedback = \"".$template->get('popup_sourcing_readfeedback')."\";");
		}
		else {
			eval("\$sourcingfeedback = \"".$template->get('popup_sourcing_feedback')."\";");
		}
		output_page($sourcingfeedback);
		/*header('Content-type: text/html+javascript');
		'$("#popup_feedback").bind("clickoutside",function(){
								$("#popup_feedback").dialog("close");
								});';
		exit;*/
	}
	elseif($core->input['action'] == 'do_feedback') {
		$potential_supplier = new Sourcing();
		$request_id = $db->escape_string($core->input['request']['rid']);
		$requests_feedback = $potential_supplier->set_feedback($core->input['feedback'], $request_id);

		$requester_details = $db->fetch_assoc($db->query("SELECT scr.*, u.displayName, u.email
										FROM ".Tprefix."sourcing_chemicalrequests scr
										JOIN ".Tprefix."users u ON (u.uid = scr.uid) WHERE scr.scrid=".$request_id));

		if($requests_feedback && $requester_details['isClosed'] == 1) {


			$email_data = array(
					'to' => $requester_details['email'],
					'from_email' => 'sourcing@orkila.com',
					'from' => 'OCOS Mailer',
					'subject' => $lang->feedbacknotification_subject,
					'message' => $core->input['feedback']['feedback']
			);

			$mail = new Mailer($email_data, 'php');
			if($mail->get_status() === true) {
				$log->record('sourcingchemicalrequests', array('to' => $requester_details['email']));
			}
		}

		switch($potential_supplier->get_status()) {
			case 0:
				if($requester_details['isClosed'] == 1) {
					header('Content-type: text/xml+javascript');  /* colorate each selected <tr> has applicant id  after successfull update */
					output_xml('<status>true</status><message>'.$lang->successfullysaved.'<![CDATA[<script> $("tr[id^='.$request_id.']").each(function() {$(this).addClass("greenbackground");}); $("#popup_feedback").dialog("close");</script>]]></message>');
					exit;
				}

			case 1:
				output_xml("<status>false</status><message>{$lang->fieldrequired}</message>");
				break;
			case 2:
				output_xml("<status>false</status><message>{$lang->feedbackexsist}</message>");
				break;
		}
	}
}
?>