<?php
$module['name'] = 'warehousemgmt';
$module['title'] = $lang->warehousemgmt;
$module['homepage'] = 'stockreportlive';
$module['globalpermission'] = 'canUseWarehouseMgmt';
$module['menu'] = array('file' => array('stockreportlive'),
        'title' => array('stockreport'),
        'permission' => array('warehousemgmt_canGenerateReports')
);
?>