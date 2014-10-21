<?php
$module['name'] = 'budgeting';
$module['title'] = $lang->budgeting;
$module['homepage'] = 'create';
$module['globalpermission'] = 'canUseBudgeting';
$module['menu'] = array('file' => array('create', 'generate', 'createfinbudget', 'generatefinbudget', 'importbudget'),
        'title' => array('create', 'generate', 'fillfinancialbudget', 'generatefinbudget', 'importbudget'),
        'permission' => array('budgeting_canFillBudget', 'budgeting_canFillBudget', 'budgeting_canFillFinBudgets', 'budgeting_cangenerateFinBudgets', 'budgeting_canFillBudget')
);
?>
