<?php

if (strpos(strtolower($_SERVER['PHP_SELF']), 'integration_init.php') !== false) {
    die('Not allowed.');
}
require_once ROOT . INC_ROOT . 'integration_config.php';


$integration_affiliates = Affiliates::get_affiliates('integrationOBOrgId IS NOT NULL', array('operators' => array('integrationOBOrgId' => 'CUSTOMSQLSECURE')));
foreach ($integration_affiliates as $affid => $affiliate) {
    $affiliates_index[$affiliate->integrationOBOrgId] = $affid;
}

$integration_affiliates_index = $affiliates_index;
$integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client'], $affiliates_index, $intgconfig['openbravo']['system']['id']);

$status = $integration->get_status();
if (!empty($status)) {
    echo 'Unable to establish integration connection.';
    exit;
}

$intgdb = $integration->get_dbconn();
?>