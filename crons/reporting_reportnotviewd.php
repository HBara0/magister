<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: reporting_reportnotviewd.php
 * Created:        @tony.assaad    Jan 24, 2014 | 11:18:33 AM
 * Last Update:    @tony.assaad    Jan 24, 2014 | 11:18:33 AM
 */
require_once '../inc/init.php';
if($_REQUEST['authkey'] == 'kia5ravb$op09dj4a!xhegalhj') {
	$lang = new Language('english');
	$lang->load('messages');

	$qreport_objs = ReportingQr::get_reports();
//	WHERE ".TIME_NOW." > ( sentOn  ".+(strtotime('+1 month')-10).") 
	foreach($qreport_objs as $qreport_obj) {
		$reportdata = $qreport_obj->get();  //At least one of the external recipients has not seen it.
		$recipients_query = $db->query("SELECT * FROM ".Tprefix."reporting_qrrecipients
									 WHERE reportIdentifier='".$db->escape_string($reportdata['identifier'])."'
									AND rqrrid NOT IN(SELECT rqrrid FROM  ".Tprefix."reporting_qrrecipients_views)");
		if($db->num_rows($recipients_query) > 0) {
			$reportdata['supplieraudit'] = $qreport_obj->get_report_supplier_audits($reportdata['spid']);
			$reportdata['supplier'] = $qreport_obj->get_report_supplier()['companyName'];
			/* send each supplier auditor */
			$body_message = 'Q'.$reportdata['quarter'].' '.$reportdata['year'].'-'.$reportdata['supplier'].'</br>';
		}
		if(empty($body_message)) {
			continue;
		}
		$email_data = array(
				'to' => $reportdata['supplieraudit']['email'],
				'from_email' => $core->settings['maileremail'],
				'from' => 'OCOS Mailer',
				'subject' => $lang->report_notviewdsubject,
				'message' => '<strong>'.$lang->report_notviewdmessage.'</strong><br />'.$body_message
		);
		$body_message = '';
		$mail = new Mailer($email_data, 'php');
		if($mail->get_status() === true) {
			$log->record('reporting_reportnotviewd', array('to' => $reportdata['supplieraudit']['email']));
		}
	}
}
?>
