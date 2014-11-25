<?php
$module['name'] = 'budgeting';
$module['title'] = $lang->budgeting;
$module['homepage'] = 'create';
$module['globalpermission'] = 'canUseBudgeting';
$module['menu'] = array('file' => array('create', 'generate', 'createfinbudget', 'generatefinbudget', 'listfxrates', 'importbudget', 'massupdate'),
        'title' => array('createbudget', 'generatebudget', 'fillfinancialbudget', 'generatefinbudget', 'listfxrates', 'importbudget', 'massupdate'),
        'permission' => array('budgeting_canFillBudget', 'canUseBudgeting', 'budgeting_canFillFinBudgets', 'budgeting_canFillFinBudgets', 'budgeting_canFillFinBudgets', 'canAdminCP', 'budgeting_canMassUpdate',)
);
?>
