<?php
$module['name'] = 'grouppurchase';
$module['title'] = $lang->grouppurchase;
$module['homepage'] = 'createforecast';
$module['globalpermission'] = 'canUseGroupPurchase';
$module['menu'] = array('file' => array('pricing', 'priceslist', 'createforecast', 'generateforecast'),
        'title' => array('pricing', 'priceslist', 'createforecast', 'generateforecast'),
        'permission' => array('grouppurchase_canPrice', 'grouppurchase_canPrice', 'grouppurchase_canUpdateForecast', 'grouppurchase_canGenerateReports')
);
?>