<?php
$module['name'] = 'stock';
$module['title'] = $lang->stock;
$module['homepage'] = 'order';
$module['globalpermission'] = 'canUseStock';
$module['menu'] = array('file' => array('order', 'reports', 'reports&action=printlist'),
        'title' => array('stockorder', 'stockreportstitle', 'stocklistingtitle'),
        'permission' => array('stock_canOrderStock', 'stock_canGenerateReports', 'stock_canGenerateReports')
);
?>