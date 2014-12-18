<?php
$module['name'] = 'grouppurchase';
$module['title'] = $lang->grouppurchase;
$module['homepage'] = 'pricing';
$module['globalpermission'] = 'canUseGroupPurchase';
$module['menu'] = array('file' => array('pricing', 'priceslist', 'create', 'generate'),
        'title' => array('priceproduct', 'priceslist', 'createforecast', 'generateforecast'),
        'permission' => array('grouppurchase_canPrice', 'canUseGroupPurchase', 'grouppurchase_canUpdateForecast', 'grouppurchase_canUpdateForecast')
);
?>