<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Get users that has birthday of this Month and  Send  Birthday Notification To affiliate Hr
 * $id: hr_monthly_birthdaynotification.php
 * Created:	   	@tony.assaad		February 28, 2012 | 4:13 PM
 * Modified:	   	@tony.assaad			March 05, 2012 | 4:06 PM
 */

require_once '../inc/init.php';

$current_date = getdate(TIME_NOW);

$users_query = $db->query("SELECT displayName AS employeeName, u.uid, ae.affid, birthDate, FROM_UNIXTIME(birthDate, '%d') as birthDay, FROM_UNIXTIME(birthDate, '%Y') as birthYear,email
						   FROM ".Tprefix."users u
							JOIN ".Tprefix."userhrinformation uh ON (u.uid=uh.uid)
							JOIN ".Tprefix."affiliatedemployees ae ON (ae.uid=u.uid)
                                                        WHERE u.gid !=7  AND FROM_UNIXTIME(birthDate, '%c')='{$current_date[mon]}' AND (birthDate IS NOT NULL AND birthDate!=0) AND isMain=1
                                                        GROUP BY u.uid
                                                        ORDER BY birthDay ASC");

if($db->num_rows($users_query) > 0) {
    while($users_birthdays = $db->fetch_assoc($users_query)) {
        /* create array and withh affid key and in each affiliate add array with  userid key => containing  the sql result value. */
        $birthday_affid[$users_birthdays['affid']][$users_birthdays['uid']] = $users_birthdays;
    }

    $hraffliate_query = $db->query("SELECT affid, name, hrManager, generalManager,coo FROM ".Tprefix."affiliates");
    if($db->num_rows($hraffliate_query) > 0) {
        while($hr_affiliates = $db->fetch_assoc($hraffliate_query)) {
//            if(empty($hr_affiliates['hrManager'])) {
//                // $hr_affiliates['hrManager'] = $hr_affiliates['generalManager'];
//            }
            $recepients[$hr_affiliates['affid']][] = $hr_affiliates['hrManager'];
            $recepients[$hr_affiliates['affid']][] = $hr_affiliates['generalManager'];
            $recepients[$hr_affiliates['affid']][] = $hr_affiliates['coo'];
            $recepients[$hr_affiliates['affid']] = array_unique(array_filter($recepients[$hr_affiliates['affid']]));
            if(!is_array($recepients[$hr_affiliates['affid']]) || empty($recepients[$hr_affiliates['affid']])) {
                continue;
            }
            $query = $db->query("SELECT uid, displayName, email FROM ".Tprefix."users WHERE uid IN (".implode(',', $recepients[$hr_affiliates['affid']]).")"); //uid={$hr_affiliates[hrManager]}");
            while($recepient_details = $db->fetch_assoc($query)) {
                $hr_affid[$recepient_details['uid']][$hr_affiliates['affid']] = $recepient_details;
            }
        }
    }
    foreach($hr_affid as $affuid => $recepient_details) {
        $body_message = '';
        $body_message .='<table width="50%">';
        foreach($hr_affid[$affuid] as $affid => $recepient_details) {
            if(is_array($birthday_affid[$affid]) && !empty($birthday_affid[$affid])) {
                $affiliate_obj = Affiliates::get_affiliates(array('affid' => $affid));
                $body_message_content .='<tr style="background-color:#92D050;width:100%;"><td colspan="3" style="width:100%;">'.$affiliate_obj->get_displayname().'</td></tr>';
                foreach($birthday_affid[$affid] as $uid => $user) {
                    $body_message_content .= '<tr style="background-color:#F1F1F1;width:100%;"><td style="width:35%">'.$user['employeeName'].'</td><td style="width:35%">'.date('l jS', mktime(0, 0, 0, $current_date['mon'], $user['birthDay'], $current_date['year'])).' ('.($current_date['year'] - $user['birthYear']).' years old)</td><td style="width:30%;"><a href="mailto:'.$user['email'].'?subject=Happy birthday!&body=Dear '.$user['employeeName'].', <br /> I would like to wish you a happy birthday."> '.$user['email'].'</a></td></tr>';
                }
            }
        }
        if(empty($body_message_content)) {
            continue;
        }
        $body_message .=$body_message_content.'</table>';
        unset($body_message_content);
        /* build the email_data array to pass the argument to the mail object */
        $email_data = array(
                'to' => $recepient_details['email'],
                'from_email' => $core->settings['maileremail'],
                'from' => 'OCOS Mailer',
                'subject' => 'Employee birthdays during '.$current_date['month'],
                'message' => 'Hello '.$recepient_details['displayName'].',<br />The Following birthdays are taking place during '.$current_date['month'].'</br></br />'.$body_message
        );
        $mail = new Mailer($email_data, 'php');
        if($mail->get_status() === true) {
            $log->record('hrbirthdaynotification', array('to' => $recepient_details['email']), 'emailsent');
        }
        else {
            $log->record('hrbirthdaynotification', array('to' => $recepient_details['email']), 'emailnotsent');
        }
    }
}
?>