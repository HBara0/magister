<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Remind to Fill Surveys
 * $id: surveys_fillsurvey_reminder.php.
 * Created:	   	@tony.assaad   	July 04, 2012 | 3:13 PM
 * Last Update: @zaher.reda 	July 05, 2012 | 10:10 AM
 */

require_once '../inc/init.php';
$lang = new Language('english');
$lang->load('messages');

$invitations_query = $db->query("SELECT s.identifier, s.subject, s.dateCreated, si.*, u.email, u.displayName
						FROM ".Tprefix."surveys_invitations si
						JOIN ".Tprefix."users u ON (u.uid=si.invitee)
						JOIN ".Tprefix."surveys s ON (s.sid=si.sid)
						WHERE s.isExternal=0 AND (s.closingDate = 0 OR ".TIME_NOW."<s.closingDate) AND (si.isDone IS NULL OR si.isDone=0)
						ORDER BY s.dateCreated DESC");

if($db->num_rows($invitations_query) > 0) {
    while($invitation = $db->fetch_assoc($invitations_query)) {
        $invitations[$invitation['uid']][$invitation['sid']] = $invitation;
    }

    foreach($invitations as $inviteeuid => $invitationdata) {
        $body_message = '';
        foreach($invitations[$inviteeuid] as $invitationdetails) {
            $body_message .= '<li><a href='.DOMAIN.'/index.php?module=surveys/fill&amp;identifier='.$invitationdetails['identifier'].' target="_blank">'.$invitationdetails['subject'].'</a> - '.date($core->settings['dateformat'], $invitationdetails['dateCreated']).'.</li>';
        }

        if(empty($body_message)) {
            continue;
        }
        /* Prepare the email_data array to pass the argument to the mail object */
        $email_data = array(
                'to' => $invitationdetails['email'],
                'from_email' => $core->settings['maileremail'],
                'from' => 'OCOS Mailer',
                'subject' => $lang->survey_reminder_subject,
                'message' => $lang->sprint($lang->surveys_reminder_message, $invitationdetails['displayName']).'<ul>'.$body_message.'</ul>'
        );

        $mail = new Mailer($email_data, 'php');
        if($mail->get_status() === true) {
            $log->record('crmvisitreportreminder', array('to' => $leavesdetails['email']));
        }
    }
}
?>