<?php
$module['name'] = 'budgeting';
$module['title'] = $lang->budgeting;
$module['homepage'] = 'budgethome';
$module['globalpermission'] = 'canUseBudgeting';
$module['menu'] = array('file' => array('createyearendforecast', 'generateyearendforecast', 'create', 'generate', 'createfinbudget', 'generatefinbudget', 'listfxrates', 'importbudget', 'massupdate', 'generatepresentation'),
        'title' => array('createyearendforecast', 'generateyearendforecast', 'createbudget', 'generatebudget', 'fillfinancialbudget', 'generatefinbudget', 'listfxrates', 'importbudget', 'massupdate', 'generatepresentation'),
        'permission' => array('budgeting_canFillBudget', 'budgeting_canFillBudget', 'budgeting_canFillBudget', 'canUseBudgeting', 'budgeting_canFillFinBudgets', 'budgeting_canFillFinBudgets', 'budgeting_canFillFinBudgets', 'canAdminCP', 'budgeting_canMassUpdate', 'canAdminCP')
);
?>
