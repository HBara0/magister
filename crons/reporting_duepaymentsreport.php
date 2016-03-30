<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: report_duepaymentsreport.php
 * Created:        @hussein.barakat    16-Mar-2016 | 09:09:26
 * Last Update:    @hussein.barakat    16-Mar-2016 | 09:09:26
 */
if($_REQUEST['authkey'] == 'asfasdkjj!h4k23jh4k2_3h4k23jh') {
    require '../inc/init.php';
    require_once ROOT.INC_ROOT.'integration_config.php';
    $integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client']);
    $lang = new Language('english');
    $lang->load('misc_reports');
    eval("\$headerinc = \"".$template->get('headerinc')."\";");
    $report = IntegrationOBFinPaymentSchedule::paymentschedule_report('', $integration);
    output($headerinc.$report);
    exit;
}