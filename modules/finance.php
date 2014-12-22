<?php
$module['name'] = 'finance';
$module['title'] = $lang->finance;
$module['homepage'] = 'integration_trialbalance';
$module['globalpermission'] = 'canUseFinance';
$module['menu'] = array('file' => array('integration_trialbalance'),
        'title' => array('trialbalance'),
        'permission' => array('canUseFinance')
);
?>