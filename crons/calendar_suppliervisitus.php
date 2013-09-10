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
$lang = new Language('english');
$lang->load('messages');
$affiliates_query = $db->query("SELECT aff.*,ce.* FROM calendar_events ce 
								JOIN affiliates aff  on aff.affid=ce.affid  WHERE ce.fromDate BETWEEN ".strtotime('tomorrow')." AND ".strtotime('+2 days -1 second', strtotime('today'))."");
/* send to affiliates  one day prior to the visit of a supplier. */

if($db->num_rows($affiliates_query) > 0) {
	while($affiliates = $db->fetch_assoc($affiliates_query)) {
		$aff_obj = new Affiliates($affiliates['affid'], false);
		$affiliate_events[$affiliates['affid']][$affiliates['ceid']] = $affiliates;
	}
}

if(is_array($affiliate_events)) {
	foreach($affiliate_events as $affid => $affevent) {
		$body_message = '';
		$aff_obj = new Affiliates($affid, false);
		$affdata = $aff_obj->get();

		foreach($affevent as $data) {
			$affevent['title'] = $data['title'];
		}
		$body_message .= '<ul><li>'.$lang->sprint($lang->suppliervisit_reminder_message, $affdata['name'], $affevent['title']).'</li></ul>';

		if(empty($body_message)) {
			continue;
		}
		$email_data = array(
				'to' => $affdata['mailingList'],
				'from_email' => $core->settings['adminemail'],
				'from' => 'OCOS Mailer',
				'subject' => $lang->suppliervisit_subject,
				'message' => $body_message
		);
		$mail = new Mailer($email_data, 'php');
		if($mail->get_status() === true) {
			$log->record('suppliervisitreminder', array('to' => $affdata['mailingList']));
		}
	}
}
?>
