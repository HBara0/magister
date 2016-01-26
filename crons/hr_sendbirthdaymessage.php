<?php
require '../inc/init.php';


$current_date = getdate(TIME_NOW);
$current_date['check'] = date('md', TIME_NOW);
$query = $db->query("SELECT u.uid,CONCAT(u.firstName, ' ', u.lastName) AS fullname, birthdate, email, birthdayIsPrivate
					FROM ".Tprefix."users u JOIN ".Tprefix."userhrinformation hr ON (hr.uid=u.uid)
					WHERE FROM_UNIXTIME(birthdate, '%m%d') = {$current_date[check]} AND (birthDate IS NOT NULL AND birthDate!=0) AND u.gid!=7");

if($db->num_rows($query) > 0) {
    while($birthday = $db->fetch_assoc($query)) {
        $email_data = array(
                'to' => $birthday['email'],
                'from_email' => $core->settings['maileremail'],
                'from' => 'OCOS Mailer',
                'subject' => 'Happy birthday!',
                'message' => 'Dear '.$birthday['fullname'].',<br />We would like to wish you a happy birthday.'
        );

        $mail = new Mailer($email_data, 'php');
        if($birthday['birthdayIsPrivate'] == 0) {
            $user = new Users($birthday['uid']);
            $mailinglist = $user->get_mainaffiliate()['mailingList'];
        }

        $emaildata = array(
                'from' => $core->settings['maileremail'],
                'to' => $mailinglist,
                'subject' => 'Happy birthday '.$user->get_displayname(),
                'message' => 'Dear All,<br/>It is '.$birthday['fullname'].' birthday today,Please join us in wishing him a Happy Birthday'
        );
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_type();
        $mailer->set_from($emaildata['from']);
        $mailer->set_subject($emaildata['subject']);
        $mailer->set_message($emaildata['message']);
        $mailer->set_to($emaildata['to']);
        $mailer->send();
    }

    $core->input['action'] = 'autosendbirthdaymessage';
    $log->record(count($audits));
}
?>