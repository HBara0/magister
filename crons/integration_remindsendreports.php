<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: integration_remindsendreports.php
 * Created:        @zaher.reda    Jul 1, 2015 | 1:02:24 PM
 * Last Update:    @zaher.reda    Jul 1, 2015 | 1:02:24 PM
 */

require '../inc/init.php';
if($_REQUEST['authkey'] == 'kia5ravb$op09dj4a!xhegalhj') {
    $reportypes = array('stockreport' => 'Stock Report', 'salesreport' => 'Sales Report');
    $links = array(
            'salesreport' => 'https://ocos.orkila.com/index.php?module=crm/salesreportlive',
            'stockreport' => 'https://ocos.orkila.com/index.php?module=warehousemgmt/stockreportlive'
    );

    $affiliates_addrecpt = array(
            19 => array(398),
            22 => array(248,),
            23 => array(416),
            1 => array(333),
            21 => array(158),
            27 => array(67),
            20 => array('fatimatou.diallo'),
            11 => array(111),
            2 => array('amal.dababneh')
    );
    $affiliates = Affiliates::get_affiliates(array('integrationOBOrgId' => 'integrationOBOrgId IS NOT NULL'), array('simple' => false, 'returnarray' => true, 'operators' => array('integrationOBOrgId' => 'CUSTOMSQLSECURE')));
    if(is_array($affiliates)) {
        foreach($affiliates as $affiliate) {
            $message = 'Openbravo/OCOS reports are due on the 5th of the month.<br /><ul>';
            $senders = array();
            foreach($reportypes as $report => $reportname) {
                //$recentreport = ReportsSendLog::get_data(array('date' => strtotime('last month'), 'affid' => $affiliate->affid, 'report' => $report), array('order' => array('by' => 'date', 'sort' => 'DESC'), 'limit' => '0,1', 'operators' => array('date' => 'lt')));
                //if(!is_object($recentreport)) {
                // $message .= 'Last '.$reportname.' is more than one month old';
                $message .= '<li><a href="'.$links[$report].'">'.$reportname.'</a>';
                $lastreport = ReportsSendLog::get_data(array('affid' => $affiliate->affid, 'report' => $report), array('order' => array('by' => 'date', 'sort' => 'DESC'), 'limit' => '0,1', 'operators' => array('date' => 'lt')));
                if(is_object($lastreport)) {
                    $senders[] = $lastreport->sentBy;
                    $message .= ', last sent on '.date($core->settings['dateformat'], $lastreport->date);
                }
                $message .= '</li>';
                //}
            }
            $message .= '</ul>';
            /* Send email */
            $mailer = new Mailer();
            $mailer = $mailer->get_mailerobj();
            $mailer->set_required_contenttypes(array('html'));
            $mailer->set_from(array('name' => 'OCOS Mailer', 'email' => $core->settings['maileremail']));
            $mailer->set_subject('Some Openbravo/OCOS reports for '.$affiliate->name.' are due');
            $mailer->set_message($message);

            $recpients = array(
                    $affiliate->get_generalmanager()->email,
                    $affiliate->get_supervisor()->email,
                    $affiliate->get_financialemanager()->email,
                    Users::get_data(array('uid' => 3))->email/* Always include User 3 */
            );

            if(isset($affiliates_addrecpt[$affiliate->affid])) {
                foreach($affiliates_addrecpt[$affiliate->affid] as $uid) {
                    if(!is_numeric($uid)) {
                        $adduser = Users::get_user_byattr('username', $uid);
                    }
                    else {
                        $adduser = new Users($uid);
                    }
                    $recpients[] = $adduser->get()['email'];
                }
            }

            if(is_array($recpients)) {
                foreach($senders as $sender) {
                    $recpients[] = Users::get_data(array('uid' => $sender))->email;
                }
            }
            $recpients = array_unique($recpients);
            $mailer->set_to($recpients);
            //$mailer->set_to('zaher.reda@orkila.com');
            // print_r($mailer->debug_info());
            //  echo '<hr />';
            // exit;
            $mailer->send();
        }
    }
}