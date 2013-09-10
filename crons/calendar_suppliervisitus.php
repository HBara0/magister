<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: calendar_suppliervisitus.php
 * Created:        @tony.assaad    Sep 9, 2013 | 2:43:05 PM
 * Last Update:    @tony.assaad    Sep 9, 2013 | 2:43:05 PM
 */

require_once '../inc/init.php';

if($_REQUEST['authkey'] == 'asfasdkjj!h4k23jh4k2_3h4k23jh') {
	$lang = new Language('english');
	$lang->load('messages');
	$affiliates_query = $db->query("SELECT aff.*, ce.* 
								FROM ".Tprefix."calendar_events ce 
								JOIN ".Tprefix."affiliates aff ON (aff.affid=ce.affid)
								WHERE ce.fromDate BETWEEN ".strtotime('tomorrow')." AND ".strtotime('+2 days -1 second', strtotime('today')));

	if($db->num_rows($affiliates_query) > 0) {
		while($affiliates = $db->fetch_assoc($affiliates_query)) {
//			$aff_obj = new Affiliates($affiliates['affid'], false);
			$affiliate_events[$affiliates['affid']][$affiliates['ceid']] = $affiliates;
		}
	}

	if(is_array($affiliate_events)) {
		foreach($affiliate_events as $affid => $events) {
			$body_message = '';
			$aff_obj = new Affiliates($affid, false);
			$affiliate = $aff_obj->get();

			foreach($events as $data) {
				$event['title'] = $data['title'];
				$event['fromDate'] = $data['fromDate'];
				$event['toDate'] = $data['toDate'];
				$event['place'] = $data['place'];
				$event['description'] = $data['description'];
			}
			$body_message .= '<ul><li>'.$lang->sprint($lang->suppliervisit_reminder_message, $affiliate['name'], $event['title']).'</li></ul>';

			if(empty($body_message)) {
				continue;
			}
			$email_data = array(
					'to' => $affiliate['mailingList'],
					'from_email' => $core->settings['adminemail'],
					'from' => 'OCOS Mailer',
					'subject' => $lang->suppliervisit_subject,
					//'message' => $body_message
			);
			$email_data['message'] = '<strong>'.$event['title'].'</strong> (';

			$email_data['message'] .= date($core->settings['dateformat'], $event['fromDate']);
			if($event['toDate'] != $event['fromDate']) {
				$email_data['message'] .= ' - '.date($core->settings['dateformat'], $event['toDate']);
			}
			$email_data['message'] .= ')<br />';
			$email_data['message'] .= $event['place'].'<br />';
			$email_data['message'] .= str_replace("\n", '<br />', $event['description']);

			$mail = new Mailer($email_data, 'php');
			if($mail->get_status() === true) {
				$log->record('suppliervisitreminder', array('to' => $affiliate['mailingList']));
			}
		}
	}
}
?>