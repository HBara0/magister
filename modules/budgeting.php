<?php
$module['name'] = 'budgeting';
$module['title'] = $lang->budgeting;
$module['homepage'] = 'create';
$module['globalpermission'] = 'canUseBudgeting';
$module['menu'] = array('file' => array('create', 'generate', 'createyearendforecast', 'generateyearendforecast', 'createfinbudget', 'generatefinbudget', 'listfxrates', 'importbudget', 'massupdate', 'generatepresentation'),
        'title' => array('createbudget', 'generatebudget', 'createyearendforecast', 'generateyearendforecast', 'fillfinancialbudget', 'generatefinbudget', 'listfxrates', 'importbudget', 'massupdate', 'generatepresentation'),
        'permission' => array('budgeting_canFillBudget', 'canUseBudgeting', 'budgeting_canFillBudget', 'budgeting_canFillBudget', 'budgeting_canFillFinBudgets', 'budgeting_canFillFinBudgets', 'budgeting_canFillFinBudgets', 'canAdminCP', 'budgeting_canMassUpdate', 'canUseBudgeting')
);
?>
