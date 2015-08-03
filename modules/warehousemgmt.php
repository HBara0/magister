<?php
$module['name'] = 'warehousemgmt';
$module['title'] = $lang->warehousemgmt;
$module['homepage'] = 'stockreportlive';
$module['globalpermission'] = 'canUseWarehouseMgmt';
$module['menu'] = array('file' => array('stockreportlive', 'pendingdeliveries', 'intermedaffshipment', 'clearancestatus'),
        'title' => array('stockreport', 'pendingdeliveries', 'intermedaffahipmentaummary', 'clearencestatus'),
        'permission' => array('warehousemgmt_canGenerateReports', 'warehousemgmt_canGenerateReports', 'warehousemgmt_canGenerateReports', 'warehousemgmt_canGenerateReports')
);
?>