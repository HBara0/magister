<?php
$module['name'] = 'grouppurchase';
$module['title'] = $lang->grouppurchase;
$module['homepage'] = 'generateforecast';
$module['globalpermission'] = 'canUseGroupPurchase';
$module['menu'] = array('file' => array('pricing', 'priceslist', 'createforecast', 'generateforecast'),
        'title' => array('priceproduct', 'priceslist', 'createforecast', 'generateforecast'),
        'permission' => array('grouppurchase_canPrice', 'canUseGroupPurchase', 'grouppurchase_canUpdateForecast', 'grouppurchase_canGenerateReports')
);
?>