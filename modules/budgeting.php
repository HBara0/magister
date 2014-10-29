<?php
$module['name'] = 'budgeting';
$module['title'] = $lang->budgeting;
$module['homepage'] = 'create';
$module['globalpermission'] = 'canUseBudgeting';
$module['menu'] = array('file' => array('create', 'generate', 'createfinbudget', 'generatefinbudget', 'importbudget'),
        'title' => array('createbudget', 'generatebudget', 'fillfinancialbudget', 'generatefinbudget', 'importbudget'),
        'permission' => array('budgeting_canFillBudget', 'canUseBudgeting', 'budgeting_canFillFinBudgets', 'budgeting_canFillFinBudgets', 'canAdminCP')
);
?>
