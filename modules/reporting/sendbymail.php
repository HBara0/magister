<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Send reports by email
 * $module: reporting
 * $id: sendbyemail.php
 * Created:		@zaher.reda		August 17, 2009
 * Last Update: @tony.assaad	July 16, 2012 | 11:26 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['reporting_canSendReportsEmail'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$session->start_phpsession();
$lang->load('messages');
if(!$core->input['action']) {
    if(isset($core->input['identifier'])) {
        $core->input['identifier'] = $db->escape_string($core->input['identifier']);
        //$identifier = explode('_', $core->input['identifier']);
        $meta_data = unserialize($session->get_phpsession('reportmeta_'.$core->input['identifier']));
        /* 	list($suppliername, $eid) = $db->fetch_array($db->query("SELECT e.companyName AS suppliername, e.eid FROM ".Tprefix."entities e, ".Tprefix."reports r
          WHERE r.spid=e.eid AND r.rid='".$db->escape_string($meta_data['spid'][1])."'"));
         */
        switch($meta_data['type']) {
            case 'm': $type = 'monthly';
                $subject_monthquarter = $lang->{strtolower(date('F', mktime(0, 0, 0, $meta_data['month'], 1, 0)))};
                $default_cc = '';

                $attachments = "<li><a href='{$core->settings[exportdirectory]}{$type}reports_{$core->input[identifier]}.pdf' target'_blank'>{$type}reports_{$core->input[identifier]}.pdf</a></li>";
                $attachments .= "<input type='hidden' value='./{$core->settings[exportdirectory]}{$type}reports_{$core->input[identifier]}.pdf' name='attachment' />";
                break;
            case 'q':
            default: $type = 'quarterly';
                if(!empty($meta_data['quarter'])) {
                    $subject_monthquarter = 'Q'.$meta_data['quarter'];
                }
                $default_cc = $core->settings['sendreportsto'];
                $attachments = '<li>Quarterly reports do not have attachments; use {link} and {password} to automatically provide recipients with access to the report. Without these two items together, the recipients will not be able to view the report.</li>';
                break;
        }

        if($meta_data['spid'] == 0) {
            $suppliername = '';
            $eid = 0;
        }
        else {
            if(is_array($meta_data['spid'])) {
                $meta_data['spid'] = array_unique($meta_data['spid']);
                if(count($meta_data['spid']) == 1) {
                    list($suppliername, $eid) = $db->fetch_array($db->query("SELECT companyName AS suppliername, eid FROM ".Tprefix."entities WHERE eid='".$db->escape_string($meta_data['spid'][0])."'"));
                }
                else {
                    $suppliername = '';
                    $eid = 0;
                }
            }
            else {
                list($suppliername, $eid) = $db->fetch_array($db->query("SELECT companyName AS suppliername, eid FROM ".Tprefix."entities WHERE eid='".$db->escape_string($meta_data['spid'])."'"));
            }
        }

        if(!empty($eid)) {
            $query = $db->query("SELECT er.*, r.*
								FROM ".Tprefix."entitiesrepresentatives er LEFT JOIN ".Tprefix."representatives r ON (r.rpid=er.rpid)
								WHERE er.eid='{$eid}'");
            while($representative = $db->fetch_array($query)) {
                $representatives_list .= '<tr><td style="width: 20%; font-weight: bold;">';
                $representatives_list .= '<input type="checkbox" name="recipients[id]['.$representative['rpid'].']" id="recipient_id_'.$representative['rpid'].'"  value="'.$representative['rpid'].'" checked/> <input type="hidden" name="recipients[email]['.$representative['rpid'].']" id="recipient_email_'.$representative['rpid'].'" value="'.base64_encode($representative['email']).'" >'.$representative['name'].'</td>';
                $representatives_list .= '<td>'.$representative['email'].'</td></tr>';
            }
        }
        //$default_cc = $core->settings['sendreportsto'];
        if($core->user['email'] != $core->settings['sendreportsto']) {
            $default_cc .= ', '.$core->user['email'];
        }

        /* Parse Signature - START */
//		$profile = $db->fetch_assoc($db->query("SELECT *, CONCAT(firstName, ' ', lastName) AS fullname FROM ".Tprefix."users WHERE uid='{$core->user[uid]}'"));
//		$positions = $db->fetch_assoc($db->query("SELECT title FROM ".Tprefix."userspositions u JOIN ".Tprefix."positions p ON (u.posid=p.posid) WHERE uid='{$core->user[uid]}'"));
//		$postions = implode(', ', $positions);
//		$company = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."affiliates WHERE affid='{$core->user[mainaffiliate]}'"));
//		$country = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."countries WHERE coid=".$company['country']), 'name');
//
//		$signature = $profile['fullname'].'<br /><img src="'.$core->settings['rootdir'].'/images/Orkila_logo_2.jpg" width="50" /><br />'.$postions.'<br />'.$company['addressLine1'].' - PO box '.$company['poBox'].' - '.$company['city'].' - '.$country.'<br />Tel:'.$company['phone1'].'<br />Fax:'.$company['fax'].'<br />Mobile:'.$profile['mobile'].'<br />E-mail:'.$profile['email'].'<br />Website : www.orkila.com';
//		/* Parse Signature - END */

        $lang->sendbymailsubject = $lang->sprint($lang->sendbymailsubject, ucfirst($lang->{$type}), $subject_monthquarter.'/'.$meta_data['year'], $suppliername);
        $lang->sendbymaildefault = $lang->sprint($lang->sendbymaildefault, strtolower($lang->{$type}));

        eval("\$sendbymailpage = \"".$template->get('reporting_sendbymail')."\";");
        output_page($sendbymailpage);
    }
    else {
        redirect('index.php?module=reporting/generatereport');
    }
}
else {
    if($core->input['action'] == 'do_sendbymail') {
        $meta_data = unserialize($session->get_phpsession('reportmeta_'.$db->escape_string($core->input['identifier'])));
        $reports_meta_data = unserialize($session->get_phpsession('reportsmetadata_'.$core->input['identifier']));

        if(empty($core->input['recipients']) && empty($core->input['additional_recipients'])) {
            error($lang->norecipientsselected, $_SERVER['HTTP_REFERER']);
        }

        if(is_empty($core->input['subject'], $core->input['message'])) {
            error($lang->incompletemessage, $_SERVER['HTTP_REFERER']);
        }

        if($meta_data['type'] == 'q') {
            $report = new ReportingQr($meta_data);
        }

        if(is_array($core->input['recipients'])) {
            switch($meta_data['type']) {
                case 'q':
                    /*  Recorded in a recipients table - START */
                    if(is_array($core->input['recipients']['id']) && !empty($core->input['recipients']['id'])) {
                        foreach($core->input['recipients']['id'] as $rpid) {
                            $report->create_recipient($rpid, 'rpid');
                        }
                    }
                    /* Recorded in a recipients table - END */
                    break;
                case 'm':
                    foreach($core->input['recipients']['id'] as $recid => $val) {
                        $email = base64_decode($core->input['recipients']['email'][$recid]);
                        if(isvalid_email($email)) {
                            $valid_emails[] = $email;
                        }
                        else {
                            $bad_emails[] = $email;
                        }
                    }
                    break;
            }
        }

        $core->input['message'] = $core->sanitize_inputs($core->input['message'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true));
        if(!empty($core->input['additional_recipients']) || !empty($core->settings['qraddrecipients'])) {
            if(!empty($core->settings['qraddrecipients'])) {
                $core->input['additional_recipients'] .= ','.$core->settings['qraddrecipients'];
            }
            $additional_emails = explode(',', $core->input['additional_recipients']);

            foreach($additional_emails as $val) {
                if(isvalid_email(trim($val))) {
                    if($meta_data['type'] == 'q') {
                        /* Get uid by email  & register as receipient - START */
                        $user = new Users();
                        $user_byemail = $user->get_userbyemail($val);

                        if($user_byemail != false) {
                            $type = 'uid';
                            $cc_user = $user_byemail->get()['uid'];
                        }
                        else {
                            $type = 'unregisteredRcpts';
                            $cc_user = $val;
                        }
                        $report->create_recipient($cc_user, $type);
                        $allrecipients[$type][$cc_user] = $report->get_otherrecipient($cc_user, $type);
                        /* Get uid by email & register as receipient - END */
                    }
                    $cc_valid_emails[] = $val;
                }
                else {
                    $cc_bad_emails[] = $val;
                }
            }
        }

        switch($meta_data['type']) {
            case 'm':
                $email_data = array(
                        'from_email' => 'reporting@ocos.orkila.com',
                        'from' => 'Orkila Reporting System',
                        'to' => $valid_emails,
                        'cc' => $cc_valid_emails,
                        'subject' => $core->input['subject'],
                        'message' => $core->input['message'],
                        'attachments' => array($core->input['attachment'])
                );

                if(is_array($email_data)) {
                    $mail = new Mailer($email_data, 'php');
                    $email_sent = true;
                }
                break;
            case 'q':
                if(is_array($core->input['recipients']['id'])) {
                    $recipients = $report->get_recipient($core->input['recipients']['id']);
                    $allrecipients['representative'] = $recipients;
                    if(is_array($allrecipients)) {
                        foreach($allrecipients as $rtype => $recipients) {
                            foreach($recipients as $id => $recipient) {
                                if($rtype == 'representative' && !in_array($recipient['rpid'], $core->input['recipients']['id'])) {
                                    continue;
                                }
                                if($rtype == 'unregisteredRcpts') {
                                    $recipient['email'] = $recipient['unregisteredRcpts'];
                                }
                                if(is_array($recipients)) {

                                    $email_data = array(
                                            'from_email' => 'reporting@ocos.orkila.com',
                                            'from' => 'Orkila Reporting System',
                                            'to' => $recipient['email'],
                                            'subject' => $core->input['subject'],
                                            'message' => $core->input['message']
                                    );

                                    if($rtype == 'representative') {
                                        $email_data['cc'] = $cc_valid_emails;
                                    }

                                    $reportlink = 'http://www.orkila.com/qreport/'.$recipient['reportIdentifier'].'/'.$recipient['token'];
                                    if(strstr($email_data['message'], '{link}')) {
                                        $email_data['message'] = str_replace('{link}', $reportlink, $email_data['message']);
                                    }
                                    else {
                                        $email_data['message'] .= '<br />'.$lang->link.': '.$reportlink;
                                    }

                                    $recipient['password'] = str_replace($recipient['salt'], '', base64_decode($recipient['password']));
                                    if(strstr($email_data['message'], '{password}')) {
                                        $email_data['message'] = str_replace('{password}', $recipient['password'], $email_data['message']);
                                    }
                                    else {
                                        $email_data['message'] .= '<br />'.$lang->password.': '.$recipient['password'];
                                    }

                                    $mail = new Mailer($email_data, 'php');
                                    $email_sent = true;
                                }
                            }
                        }
                    }
                }
                else {
                    redirect($_SERVER['HTTP_REFERER'], 2, $lang->norepselected);
                }
                break;
        }

        if($email_sent == true) {
            if($mail->get_status() === true) {
                if(is_array($reports_meta_data['rid'])) {
                    $update_query_where = 'rid IN ('.implode(',', $reports_meta_data['rid']).')';
                }
                else {
                    $update_query_where = "rid = '{$reports_meta_data[rid]}'";
                }
                $db->update_query('reports', array('isSent' => 1, 'sentOn' => TIME_NOW, 'isApproved' => 1, 'isLocked' => 1), $update_query_where);

                $log->record($valid_emails, $cc_valid_email);
                if(is_array($core->input['attachment'])) {
                    unlink($core->input['attachment']);
                }
                redirect('index.php?module=reporting/generatereport', 1, $lang->messagesentsuccessfully);
            }
            else {
                error($lang->errorsendingemail);
            }
        }
    }
}
?>