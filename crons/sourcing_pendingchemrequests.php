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
    $unmetreq_query = $db->query("SELECT * FROM ".Tprefix."sourcing_chemicalrequests WHERE (timeRequested < ".strtotime('-1 week')." AND isClosed=0 AND feedbackTime=0) ORDER BY timeRequested ASC");

    if($db->num_rows($unmetreq_query) > 0) {
        while($unmetrequest = $db->fetch_assoc($unmetreq_query)) {
            $pendingrequests[$unmetrequest['scrid']] = $unmetrequest;
        }

        if(is_array($pendingrequests)) {
            $email_data = array(
                    'to' => 'sourcing@orkila.com',
                    'from_email' => $core->settings['maileremail'],
                    'from' => 'OCOS Mailer',
                    'subject' => $lang->pendingchemrequestssubject,
            );
            $email_data['message'] = $lang->pendingchemrequestsbody.'<br />';
            foreach($pendingrequests as $pendingrequest) {
                if(!empty($pendingrequest)) {
                    $chemsubstances_objs = new Chemicalsubstances($pendingrequest['csid']);
                    $pendingrequest['chemical'] = $chemsubstances_objs->get()['name'];
                }

                $pendingrequest['requestdate'] = date($core->settings['dateformat'], $pendingrequest['timeRequested']);
                $userobjs = new Users($pendingrequest['uid']);
                $pendingrequest['requester'] = $userobjs->get()['displayName'];

                $email_data['message'] .= '<p>';
                $email_data['message'] .= $pendingrequest['requester'].' - '.$pendingrequest['requestdate'].': '.$pendingrequest['chemical'].'<br /><em>'.$pendingrequest['requestDescription'].'</em>';
                $email_data['message'] .= '</p>';
            }

            $mail = new Mailer($email_data, 'php');
            if($mail->get_status() === true) {
                $log->record('sourcing_reminderunmetchemical', array('to' => 'sourcing@orkila.com'));
            }
        }
    }
}
?>
