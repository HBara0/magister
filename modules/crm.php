<?php
$module['name'] = 'crm';
$module['title'] = $lang->crm;
$module['homepage'] = 'home';
$module['globalpermission'] = 'canUseCRM';
$module['menu'] = array('file' => array('fillvisitreport', 'listvisitreports', 'generatevisitreport', 'salesreportlive', 'importcustomers', 'importsales', 'marketintelligencereport', 'marketpotentialdata'),
        'title' => array('fillvisitreport', 'listvisitreports', 'generatevisitreport', 'generatesalesreport', 'importcustomers', 'importsales', 'marketintelligencereport', 'marketpotentialdata'),
        'permission' => array('crm_canFillVisitReports', 'crm_canViewVisitReports', 'crm_canGenerateVisitReports', 'crm_canGenerateSalesReports', 'crm_canImportCustomers', 'crm_canImportSales', 'crm_canGenerateMIRep', 'profiles_canUseMktIntel')
);
?>