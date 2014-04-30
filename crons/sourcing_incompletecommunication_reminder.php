<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: sourcing_reminderincompleted_communication.php
 * Created:        @tony.assaad    Mar 5, 2014 | 9:25:10 AM
 * Last Update:    @tony.assaad    Mar 5, 2014 | 9:25:10 AM
 */

require_once '../inc/init.php';

if($_REQUEST['authkey'] == 'ac43bghy!h4k23jh4k2_3h4k23jh') {
    $lang = new Language('english');
    $lang->load('messages');
    $lang->load('sourcing_meta');
    $query = $db->query('SELECT sschid, uid
                        FROM '.Tprefix.'sourcing_suppliers_contacthist
                        WHERE isCompleted=0
                        ORDER BY date ASC');

    if($db->num_rows($query) > 0) {
        while($incompleted_history = $db->fetch_assoc($query)) {
            $incompleted_communications[$incompleted_history['uid']][$incompleted_history['sschid']] = $incompleted_history;
        }

        foreach($incompleted_communications as $uid => $communications) {
            $body_message = '<ul>';
            foreach($incompleted_communications[$uid] as $contactdetails) {
                $contacthist = new SourcingSuppContactHist($contactdetails['sschid']);
                $aff_obj = $contacthist->get_affiliate();
                $entity_obj = $contacthist->get_supplier();
                $user_obj = $contacthist->get_user();

                $contactdetails['supplier'] = $entity_obj->get();
                $body_message .= '<li><a target="_blank" href="'.DOMAIN.'/index.php?module=sourcing/supplierprofile&id='.$contactdetails['supplier']['ssid'].'#historybrief_'.$contactdetails['sschid'].' ">'.$contactdetails['supplier']['companyName'].'</a> made on '.date($core->settings['dateformat'], $contacthist->get()['date']).' - '.$aff_obj->get()['name'];
                $body_message .= '<p><strong>'.$lang->description.' </strong>: '.$contacthist->get()['description'].'</p>';
                $body_message .= '<p><strong>'.$lang->application.'</strong>: '.$contacthist->get()['application'].'</p>';

                $body_message.='</li>';
                if(empty($body_message)) {
                    continue;
                }

                $contactdetails['user'] = $user_obj->get();
            }
            $body_message .= '</ul>';

            /* Prepare the email_data array to pass the argument to the mail object */
            $email_data = array(
                    'to' => $contactdetails['user']['email'],
                    'from_email' => $core->settings['maileremail'],
                    'from' => 'OCOS Mailer',
                    'cc' => 'sourcing@orkila.com',
                    'subject' => $lang->souring_incompletecommunication_subject,
                    'message' => $lang->sprint($lang->souring_incompletecommunication_message, $contactdetails['user']['displayName']).'<br />'.$body_message
            );

            $mail = new Mailer($email_data, 'php');
            if($mail->get_status() === true) {
                $log->record('sourcing_suppliers_contacthist', array('to' => $contactdetails['email']));
            }
        }
    }
}
?>
