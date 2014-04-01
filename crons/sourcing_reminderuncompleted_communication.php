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
	$query = $db->query("SELECT * 
						FROM ".Tprefix."sourcing_suppliers_contacthist sscth
						WHERE sscth.isCompleted=0
						ORDER BY sschid ASC");

	if($db->num_rows($query) > 0) {
		while($incompleted_history = $db->fetch_assoc($query)) {
			$incompleted_communications[$incompleted_history['uid']][$incompleted_history['sschid']] = $incompleted_history;
		}

		foreach($incompleted_communications as $uid => $communications) {
			$body_message = '';
			foreach($incompleted_communications[$uid] as $invitationdetails) {
				$body_message .= '<li>'.$invitationdetails['description'].'</li>';
				$body_message .= '<li>'.$invitationdetails['application'].'</li>';
				$entity_obj = new Entities($invitationdetails['ssid']);
				$souring_supplier = $entity_obj->get();

				$user_obj = new Users($invitationdetails['uid']);
				$invitationdetails['user'] = $user_obj->get();
			}

			if(empty($body_message)) {
				continue;
			}

			/* Prepare the email_data array to pass the argument to the mail object */
			$email_data = array(
					'to' => $invitationdetails['user']['email'],
					'from_email' => $core->settings['maileremail'],
					'from' => 'OCOS Mailer',
					'subject' => $lang->uncompletedsubject,
					'message' => $lang->sprint($lang->uncompletedcommunication.'<strong>'.$souring_supplier['companyName'].'</strong> ', $invitationdetails['user']['displayName']).'Made on '.date('M d  Y ', $invitationdetails['date']).'  communication Details :</br><ul>'.$body_message.'</ul>'
			);
			print_R($email_data);
			$email_data['cc'] = 'sourcing@orkila.com';

//            $mail = new Mailer($email_data, 'php');
//            if ($mail->get_status() === true) {
//                $log->record('sourcing_suppliers_contacthist', array('to' => $invitationdetails['email']));
//            }
		}
	}
}
?>
