<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Draft Visit Reports Reminder 
 * $id: crm_visitreport_reminder.php.
 * Created:	   	@tony.assaad   	July 01, 2012 | 1:13 PM
 * Last Update: @zaher.reda 	July 04, 2012 | 10:58 AM
*/

require_once '../inc/init.php';
$lang = new Language('english');
$lang->load('messages');

$leaves_query = $db->query("SELECT l.lid,vr.lid, vr.identifier, vr.finishDate, vr.date, u.uid, u.displayName, u.email, u.reportsTo, e.companyName AS customer
							FROM ".Tprefix."leaves l
							JOIN ".Tprefix."visitreports vr ON (l.lid = vr.lid) 
							JOIN ".Tprefix."entities e ON (vr.cid=e.eid)   
							JOIN  ".Tprefix."users u ON (vr.uid=u.uid)
							WHERE (vr.isDraft = 1 AND ".TIME_NOW.">=(vr.date + 604800))");  // send  after 2 days from the visit report creation.		
																							
if($db->num_rows($leaves_query) > 0) {
	while($user_leaves = $db->fetch_assoc($leaves_query)) {						
		$leaves[$user_leaves['uid']][$user_leaves['lid']] = $user_leaves;
	}
		
	foreach($leaves as $leaveuid =>  $leavesdata) {
		$body_message = ''; 
		foreach($leaves[$leaveuid] as $leavesdetails) {
			$body_message .= '<li><a href='.DOMAIN.'/index.php?module=crm/fillvisitreport&amp;identifier='.$leavesdetails['identifier'].' target="_blank">'.$leavesdetails['customer'].'</a>,  '.date($core->settings['dateformat'], $leavesdetails['date']).'.</li>';
		}	
					
		if(empty($body_message)) {
			continue;	
		}
		/* Prepare the email_data array to pass the argument to the mail object */
		$email_data = array(
			'to'	      => $leavesdetails['email'],
			'from_email'  => $core->settings['adminemail'],
			'from'	      => 'OCOS Mailer',
			'subject'     => $lang->visitreport_reminder_subject,
			'message'     => $lang->sprint($lang->visitreport_reminder_message, $leavesdetails['displayname']).'<ul>'.$body_message.'</ul>'
		);
		
		/* If the visit is more than 2 weeks ago, include employee supervisor*/		
		if((TIME_NOW >= ($user_leaves['date']+1209600) && !empty($leavesdetails['reportsTo']))) {
			$email_data['cc'] = $db->fetch_field($db->query("SELECT email FROM ".Tprefix."users WHERE uid={$leavesdetails[reportsTo]}"), 'email');
		} 
		$mail = new Mailer($email_data, 'php');
		
		if($mail->get_status() === true) {
			$log->record('crmvisitreportreminder', array('to' => $leavesdetails['email']));
		}		
	}
}	
?>