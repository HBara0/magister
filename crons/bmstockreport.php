<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: bmstockreport.php
 * Created:        @rasha.aboushakra    Mar 3, 2016 | 3:13:41 PM
 * Last Update:    @rasha.aboushakra    Mar 3, 2016 | 3:13:41 PM
 */


//  Trigger BM Stock reports based on the reportsendlog
//  This cron checks the logs and send the reports accordingly to people who are assigned as saleRep.
//  Check : whenever end of month stock report has been send, distributed stock report per bm should be sent  (readyForSending=1)

require '../inc/init.php';
ini_set('max_execution_time', 0);

$affiliates = Affiliates::get_affiliates(array('integrationOBOrgId' => 'integrationOBOrgId IS NOT NULL'), array('simple' => false, 'returnarray' => true, 'operators' => array('integrationOBOrgId' => 'CUSTOMSQLSECURE')));
if(is_array($affiliates)) {
    foreach($affiliates as $affiliate) {
        if(strstr($affiliate->name, 'Holding')) {
            continue;
        }
        $bms = $affiliate->get_bms();
        $lastreport = ReportsSendLog::get_data(array('affid' => $affiliate->affid, 'report' => 'stockreport'), array('order' => array('by' => 'date', 'sort' => 'DESC'), 'limit' => '0, 1', 'operators' => array('date' => 'grt')));
        if(is_object($lastreport)) {
            if($lastreport->readyForSending == 1 && $lastreport->distributedReportsSent == 0) {
                // get affiliated employees who are assigned as saleRep.
                $bms = $affiliate->get_bms();
                if(is_array($bms)) {
                    foreach($bms as $bm) {
                        $message = '';
                        $bm_obj = Users::get_data(array('uid' => $bm['uid']), array('simple' => false));
                        /* START-- GENERATION OF STOCK REPORTS */
                        $core->input['action'] = "do_generatereport";
                        $core->input['referrer'] = 'bmstockreport';
                        $core->input['affid'] = $affiliate->affid;
                        $core->input['bm'] = $bm_obj;
                        $core->input['module'] = 'warehousemgmt/stockreportlive';
                        define('DIRECT_ACCESS', true);

                        include ("../modules/warehousemgmt/stockreportlive.php");
                        $message = $report;


                        $mailer = new Mailer();
                        $mailer = $mailer->get_mailerobj();
                        $mailer->set_required_contenttypes(array('html'));
                        $mailer->set_from(array('name' => 'OCOS Mailer', 'email' => $core->settings['maileremail']));
                        $mailer->set_subject('Stock Report - '.$affiliate['name'].' - '.$bm_obj->get_displayname().' - Week '.$date_info['week'].'/'.$date_info['year']);
                        $mailer->set_message($message);
                        /*
                         * BM Stock report recipients ~ START
                         * BM, LM, CM, GM, Coordinator, Supervisor and COO.
                         */
                        $recipients[] = $affiliateobj->get_generalmanager()->email;
                        $recipients[] = $affiliateobj->get_supervisor()->email;
                        $recipients[] = $affiliateobj->get_logisticsmanager()->email;
                        $recipients[] = $affiliateobj->get_commercialManager()->email;
                        $recipients[] = $affiliateobj->get_coo()->email;
                        $recipients[] = $bm_obj->email;

                        $permissions = $bm_obj->get_businesspermissions();
                        if(is_array($permissions['psid'])) {
                            foreach($permissions['psid'] as $psid) {
                                if($psid == 0) {
                                    continue;
                                }
                                $segment_objs = new ProductsSegments($psid);
                                $segment_coordobjs = $segment_objs->get_coordinators();
                                if(is_array($segment_coordobjs)) {
                                    foreach($segment_coordobjs as $coord) {
                                        $recipients[] = $coord->get_coordinator()->email;
                                    }
                                }
                            }
                        }
                        array_unique($recipients);
                        //BM Stock report recipients ~ END
                        $mailer->set_to($recipients);
//                        print_r($mailer->debug_info());
//                        exit;
                        $mailer->send();
                        if($mailer->get_status() !== true) {
//
                        }
                    }
                    //Update the report status to indicate that BM reports were sent
                    $query = $db->update_query('reportssendlog', array('distributedReportsSent' => 1), 'rslid='.$lastreport->rslid);
                }
            }
        }
    }
}



