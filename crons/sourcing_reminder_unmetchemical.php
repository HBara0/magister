<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: sourcing_reminder_unmetchemical.php
 * Created:        @tony.assaad    Jan 23, 2014 | 2:26:54 PM
 * Last Update:    @tony.assaad    Jan 23, 2014 | 2:26:54 PM
 */
require_once '../inc/init.php';

if($_REQUEST['authkey'] == 'ac43bghy!h4k23jh4k2_3h4k23jh') {
	$lang = new Language('english');
	$lang->load('messages');
	$unmetreq_query = $db->query("SELECT * FROM ".Tprefix."sourcing_chemicalrequests WHERE (timeRequested < ".strtotime('-1 month')." AND isClosed=0 AND feedbackTime=0)");
	if($db->num_rows($unmetreq_query) > 0) {
		while($unmetrequest = $db->fetch_assoc($unmetreq_query)) {
			$unmet_chemrequests[$unmetrequest['scrid']] = $unmetrequest;
		}
		if(is_array($unmet_chemrequests)) {
			$email_data = array(
					'to' => 'sourcing@orkila.co',
					'from_email' => $core->settings['maileremail'],
					'from' => 'OCOS Mailer',
					'subject' => $lang->unmetchemicalsubject,
			);
			$email_data['message'] = '<strong>'.$lang->unmetchemicalbody.'</strong><br />';
			foreach($unmet_chemrequests as $unmet_chemrequest) {
				$chemsubstances_objs = new Chemicalsubstances($unmet_chemrequest['csid']);
				$unmet_chemrequestdata['chemical'] = $chemsubstances_objs->get()['name'];
				$unmet_chemrequestdata['requestdate'] = date($core->settings['dateformat'], $unmet_chemrequest['timeRequested']);
				$userobjs = new Users($unmet_chemrequest['uid']);
				$unmet_chemrequestdata['requester'] = $userobjs->get()['displayName'];
				$email_data['message'] .= '<br />';
				$email_data['message'] .=$unmet_chemrequestdata['requester'].' - '.$unmet_chemrequestdata['requestdate'].' : '.$unmet_chemrequestdata['chemical'].'</br>';
				$email_data['message'] .= '<br />';
			}

			$mail = new Mailer($email_data, 'php');
			if($mail->get_status() === true) {
				$log->record('sourcing_reminderunmetchemical', array('to' => 'sourcing@orkila.com'));
			}
		}
	}
}
?>
