<?php
$module['name'] = 'crm';
$module['title'] = $lang->crm;
$module['homepage'] = 'fillvisitreport';
$module['globalpermission'] = 'canUseCRM';
$module['menu'] = array('file' => array('fillvisitreport', 'listvisitreports', 'generatevisitreport', 'salesreport', 'importcustomers', 'importsales', 'marketintelligencereport'),
        'title' => array('fillvisitreport', 'listvisitreports', 'generatevisitreport', 'generatesalesreport', 'importcustomers', 'importsales', 'mireport'),
        'permission' => array('crm_canFillVisitReports', 'crm_canViewVisitReports', 'crm_canGenerateVisitReports', 'crm_canGenerateSalesReports', 'crm_canImportCustomers', 'crm_canImportSales', 'crm_canGenerateMIRep')
);
?>