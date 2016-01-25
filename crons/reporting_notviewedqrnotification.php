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

    $offset = 20;

    $quarter = currentquarter_info();

    $recipients_query = $db->query("SELECT * FROM ".Tprefix."reporting_qrrecipients rqr
									 WHERE rqr.reportIdentifier IN (SELECT r.identifier FROM reports r WHERE r.year={$quarter[year]} AND r.quarter={$quarter[quarter]} AND r.status=1)
									AND (sentOn+(".($offset * 24 * 60 * 60).") < ".strtotime('today').")
                                                                        AND NOT EXISTS (SELECT rqv.rqrrid FROM ".Tprefix."reporting_qrrecipients_views rqv WHERE rqv.rqrrid = rqr.rqrrid)
                                                                        AND rpid IS NOT NULL");

    if($db->num_rows($recipients_query) > 0) {
        while($view = $db->fetch_assoc($recipients_query)) {
            $rid = $db->fetch_field($db->query("SELECT rid FROM ".Tprefix."reports WHERE identifier = '".$db->escape_string($view['reportIdentifier'])."'"), 'rid');

            $report = new ReportingQr(array('rid' => $rid));
            $reportdata = $report->get();
            $supplier_obj = $report->get_report_supplier(true);
            $reportdata['supplier'] = $supplier_obj->get()['companyName'];

            $auditor_obj = new Users($view['sentBy']);
            $auditor = $auditor_obj->get();

            $represenative = new Representatives($view['rpid']);
            $messages[$auditor['uid']]['info'] = $auditor;
            $messages[$auditor['uid']]['message'] .= 'Q'.$reportdata['quarter'].'/'.$reportdata['year'].' - '.$reportdata['supplier'].' - '.$represenative->get()['name'].' ('.$represenative->get()['email'].')</br>';
        }
    }

    if(is_array($messages)) {
        foreach($messages as $uid => $auditor) {
            if(empty($auditor['message'])) {
                continue;
            }
            $mailer = new Mailer();
            $mailer = $mailer->get_mailerobj();
            $mailer->set_to($auditor['info']['email']);
            $mailer->set_from(array('name' => 'OCOS Mailer', 'email' => $core->settings['maileremail']));
            $mailer->set_subject($lang->sprint($lang->reporting_notviewedqrnote_subj, $offset));
            $mailer->set_message($lang->sprint($lang->reporting_notviewedqrnote_body, $offset, $auditor['message']));

            $mailer->send();

            if($mailer->get_status() === true) {
                $log->record('reporting_reportnotviewd', array('to' => $reportdata['supplieraudit']['email']));
            }
        }
    }
}
?>
