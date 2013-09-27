<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * Send reminder one day prior to event
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
								WHERE isPublic=1 AND ce.fromDate BETWEEN ".strtotime('tomorrow')." AND ".strtotime('+2 days -1 second', strtotime('today')));

	if($db->num_rows($affiliates_query) > 0) {
		while($affiliates = $db->fetch_assoc($affiliates_query)) {
			$affiliate_events[$affiliates['affid']][$affiliates['ceid']] = $affiliates;
		}
	}

	if(is_array($affiliate_events)) {
		foreach($affiliate_events as $affid => $events) {
			$aff_obj = new Affiliates($affid, false);
			$affiliate = $aff_obj->get();

			if(empty($affiliate['mailingList'])) {
				continue;
			}
			
			$email_data = array(
					'to' => $affiliate['mailingList'],
					'from_email' => $core->settings['maileremail'],
					'from' => 'OCOS Mailer',
					'subject' => $lang->upcomingevents,
			);

			foreach($events as $event) {
				$email_data['message'] .= '<strong>'.$event['title'].'</strong> (';

				$email_data['message'] .= date($core->settings['dateformat'], $event['fromDate']);
				if($event['toDate'] != $event['fromDate']) {
					$email_data['message'] .= ' - '.date($core->settings['dateformat'], $event['toDate']);
				}
				$email_data['message'] .= ')<br />';
				$email_data['message'] .= str_replace("\n", '<br />', $event['description']).'<br />';
				$email_data['message'] .= $event['place'].'<br /><br />';
			}
			$mail = new Mailer($email_data, 'php');
			if($mail->get_status() === true) {
				$log->record('suppliervisitreminder', array('to' => $affiliate['mailingList']));
			}
		}
	}
}
?>