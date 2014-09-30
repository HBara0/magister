<?php
$module['name'] = 'budgeting';
$module['title'] = $lang->budgeting;
$module['homepage'] = 'create';
$module['globalpermission'] = 'canUseBudgeting';
$module['menu'] = array('file' => array('create', 'generate', 'createfinbudget', 'importbudget'),
        'title' => array('create', 'generate', 'fillfinancialbudget', 'importbudget'),
        'permission' => array('budgeting_canFillBudget', 'budgeting_canFillBudget', 'budgeting_canFillFinBudgets', 'budgeting_canFillBudget')
);
?>
