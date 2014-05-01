<?php
$module['name'] = 'budgeting';
$module['title'] = $lang->budgeting;
$module['homepage'] = 'create';
$module['globalpermission'] = 'canUseBudgeting';
$module['menu'] = array('file' => array('create', 'generate', 'importbudget'),
        'title' => array('create', 'generate', 'importbudget'),
        'permission' => array('budgeting_canFillBudget', 'budgeting_canFillBudget', 'budgeting_canFillBudget')
);
?>
