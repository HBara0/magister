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
			$body_message = '<ul>';
			foreach($incompleted_communications[$uid] as $invitationdetails) {
				$aff_obj = new Affiliates($invitationdetails['affid']);
				$entity_obj = new Entities($invitationdetails['ssid']);
				$invitationdetails['supplier'] = $entity_obj->get();
				$body_message.='<li><a target="_blank" href="'.DOMAIN.'/index.php?module=sourcing/supplierprofile&id='.$invitationdetails['supplier']['eid'].'#historybrief_'.$invitationdetails['sschid'].' ">'.$invitationdetails['supplier']['companyName'].'</a> Made on '.date('M d  Y ', $invitationdetails['date']).' - '.$aff_obj->get()['name'];
				$body_message .='<p><span><strong>'.$lang->description.' </strong>: '.$invitationdetails['description'].'</span></p>';
				$body_message .= '<p><span><strong>'.$lang->application.'</strong> : '.$invitationdetails['application'].'</span></p>';

				$body_message.='</li>';
				if(empty($body_message)) {
					continue;
				}

				$user_obj = new Users($invitationdetails['uid']);
				$invitationdetails['user'] = $user_obj->get();
			}
			$body_message .= '</ul>';

			/* Prepare the email_data array to pass the argument to the mail object */
			$email_data = array(
					'to' => $invitationdetails['user']['email'],
					'from_email' => $core->settings['maileremail'],
					'from' => 'OCOS Mailer',
					'subject' => $lang->uncompletedsubject,
					'message' => $lang->sprint($lang->uncompletedcommunication, $invitationdetails['user']['displayName']).'</br>'.$body_message.''
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
