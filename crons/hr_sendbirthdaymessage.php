<?php
require '../inc/init.php';

$current_date = getdate(TIME_NOW);
$current_date['check'] = date('md', TIME_NOW);
$query = $db->query("SELECT CONCAT(u.firstName, ' ', u.lastName) AS fullname, birthdate, email 
					FROM ".Tprefix."users u JOIN ".Tprefix."userhrinformation hr ON (hr.uid=u.uid) 
					WHERE FROM_UNIXTIME(birthdate, '%m%d') = {$current_date[check]} AND (birthDate IS NOT NULL AND birthDate!=0) AND u.gid!=7");
	
if($db->num_rows($query) > 0) {
	while($birthday = $db->fetch_assoc($query)) {
		$email_data = array(
		'to'		 => $birthday['email'],
		'from_email'  => $core->settings['maileremail'],
		'from'	   => 'OCOS Mailer',
		'subject'	=> 'Happy birthday!',
		'message'   => 'Dear '.$birthday['fullname'].',<br />We would like to wish you a happy birthday.'
		);
	
		$mail = new Mailer($email_data, 'php');
	}
	
	$core->input['action'] = 'autosendbirthdaymessage';
	$log->record(count($audits));
}
?>