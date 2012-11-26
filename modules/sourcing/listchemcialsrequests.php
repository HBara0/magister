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
			if($chemicalrequest['isclosed'] == 1) {
				$feedback_link = '<a href="#"  rel="'.$chemicalrequest['scrid'].'" id="readfeedback_'.$chemicalrequest['scrid'].'_sourcing/listchemcialsrequests_loadpopupbyid">'.$chemicalrequest['displayName'].'</a>';
				$islocked = 'closed';
				$rowcolor = "greenbackground";
			}
			elseif($chemicalrequest['isclosed'] == 0) {
				$feedback_link = '<a href="#"  rel="'.$chemicalrequest['scrid'].'" id="feedback_'.$chemicalrequest['scrid'].'_sourcing/listchemcialsrequests_loadpopupbyid">'.$chemicalrequest['displayName'].'</a>';
				$islocked = '';
				$rowcolor = "unapproved";
			}
			$chemicalrequest['timeRequested_output'] = date($core->settings['timeformat'].'-'.$core->settings['dateformat'], $chemicalrequest['timeRequested']);
			eval("\$sourcing_listchemcialsrequests_rows .= \"".$template->get('sourcing_listchemcialsrequests_rows')."\";");
		}
	}

	eval("\$sourcing_listchemcialsrequests = \"".$template->get('sourcing_listchemcialsrequests')."\";");
	output_page($sourcing_listchemcialsrequests);
}
else {
	if($core->input['action'] == 'get_feedback') {
		eval("\$sourcingfeedback = \"".$template->get('popup_sourcing_feedback')."\";");
		output_page($sourcingfeedback);
	}
	elseif($core->input['action'] == 'get_readfeedback') {
		$request_id = $core->input['id'];
		$potential_supplier = new Sourcing();
		$read_feedback = $potential_supplier->get_feedback($request_id);
		$read_feedback['feedbackTime_output'] = date($core->settings['timeformat'], $read_feedback['feedbackTime']);
		eval("\$sourcingreedfeedback = \"".$template->get('popup_sourcing_readfeedback')."\";");
		output_page($sourcingreedfeedback);
	}
	/* Do Feedback */
	elseif($core->input['action'] == 'do_feedback') {
		$potential_supplier = new Sourcing();
		$request_id = $core->input['request']['rid'];
		$requests_feedback = $potential_supplier->set_feedback($core->input['feedback'], $request_id);
		switch($potential_supplier->get_status()) {
			case 0:
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
				break;
			case 1:
				output_xml("<status>false</status><message>{$lang->fieldrequired}</message>");
				break;
			case 2:
				output_xml("<status>false</status><message>{$lang->feedbackexsist}</message>");
				break;
		}
		$requester_detials = $db->fetch_assoc($db->query("SELECT scr.*,u.displayName,u.email
										FROM ".Tprefix."sourcing_chemicalrequests scr
										JOIN ".Tprefix."users u ON (u.uid = scr.uid) WHERE scr.scrid=".$request_id));
		/* colorate Satisfied request */
		if($requester_detials['isclosed'] == 1) {
			header('Content-type: text/xml+javascript');  /* colorate each selected <tr> has applicant id  after successfull update */

			output_xml('<status>true</status><message><![CDATA[<script> $("tr[id^='.$request_id.']").each(function() {$(this).addClass("greenbackground");});</script>]]></message>');
		}
		if($requests_feedback && $requester_detials['isclosed'] == 1) {


			/* Prepare the email_data array to pass the argument to the mail object */
			$body_message = '<li>'.$lang->feedbacksubmitted.'</li>';
			$email_data = array(
					'to' => $requester_detials['email'],
					'from_email' => $core->settings['maileremail'],
					'from' => 'OCOS Mailer',
					'subject' => $lang->feedback_subject,
					'message' => $lang->sprint($lang->feedback_message, $requester_detials['displayName']).'<ul>'.$body_message.'</ul>'
			);
			$mail = new Mailer($email_data, 'php');
			if($mail->get_status() === true) {
				$log->record('sourcingchemicalrequests', array('to' => $requester_detials['email']));
			}
		}
	}
}
?>