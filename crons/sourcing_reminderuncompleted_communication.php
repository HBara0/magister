<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: sourcing_reminderuncompleted_communication.php
 * Created:        @tony.assaad    Mar 5, 2014 | 9:25:10 AM
 * Last Update:    @tony.assaad    Mar 5, 2014 | 9:25:10 AM
 */


require_once '../inc/init.php';

if($_REQUEST['authkey'] == 'ac43bghy!h4k23jh4k2_3h4k23jh') {
	$lang = new Language('english');
	$lang->load('messages');
	$uncomplete_query = $db->query("SELECT * FROM ".Tprefix."sourcing_suppliers_contacthist sscth
									JOIN ".Tprefix."users u ON (u.uid=sscth.uid)
									WHERE   sscth.isCompleted=0
									ORDER BY sschid ASC");

	if($db->num_rows($uncomplete_query) > 0) {
		while($uncompleted_history = $db->fetch_assoc($uncomplete_query)) {
			$uncompleted_communications[$uncompleted_history['uid']][$uncompleted_history['sschid']] = $uncompleted_history;
		}
		foreach($uncompleted_communications as $uid => $communications) {
			$body_message = '';
			foreach($uncompleted_communications[$uid] as $invitationdetails) {
				$body_message .= '<li> '.$invitationdetails['description'].'</li>';
				$body_message .= '<li> '.$invitationdetails['application'].'</li>';
				$entity_obj = new Entities($invitationdetails['ssid']);
				$souring_supplier = $entity_obj->get();
			}
			if(empty($body_message)) {
				continue;
			}

			/* Prepare the email_data array to pass the argument to the mail object */
			$email_data = array(
					'to' => $invitationdetails['email'],
					'from_email' => $core->settings['maileremail'],
					'from' => 'OCOS Mailer',
					'subject' => $lang->uncompletedsubject,
					'message' => $lang->sprint($lang->uncompletedcommunication.'<strong>'.$souring_supplier['companyName'].'</strong> ', $invitationdetails['displayName']).'Made on '.date('M d  Y ', $invitationdetails['date']).'  communication Details :</br><ul>'.$body_message.'</ul>'
			);
			$email_data['cc'] = 'sourcing@orkila.com';

			$mail = new Mailer($email_data, 'php');
			if($mail->get_status() === true) {
				$log->record('sourcing_suppliers_contacthist', array('to' => $invitationdetails['email']));
			}
		}
	}
}
?>
