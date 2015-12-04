<?php
$module['name'] = 'budgeting';
$module['title'] = $lang->budgeting;
$module['homepage'] = 'budgethome';
$module['globalpermission'] = 'canUseBudgeting';
$module['menu'] = array('file' => array('budgethome', 'createyearendforecast', 'generateyearendforecast', 'create', 'generate', 'createfinbudget', 'generatefinbudget', 'listfxrates', 'importbudget', 'massupdate', 'generatepresentation'),
        'title' => array('budgetingoverview', 'createyearendforecast', 'generateyearendforecast', 'createbudget', 'generatebudget', 'fillfinancialbudget', 'generatefinbudget', 'listfxrates', 'importbudget', 'massupdate', 'generatepresentation'),
        'permission' => array('canUseBudgeting', 'budgeting_canFillBudget', 'canUseBudgeting', 'budgeting_canFillBudget', 'canUseBudgeting', 'budgeting_canFillFinBudgets', 'budgeting_cangenerateFinBudgets', 'budgeting_canFillFinBudgets', 'canAdminCP', 'budgeting_canMassUpdate', 'canAdminCP')
);
?>
