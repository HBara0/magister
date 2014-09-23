<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: commercialexpenses.php
 * Created:        @rasha.aboushakra    Sep 23, 2014 | 11:01:11 AM
 * Last Update:    @rasha.aboushakra    Sep 23, 2014 | 11:01:11 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['budgeting_canFillComAdmExp'] == 1) {
    error($lang->sectionnopermission);
}

$categories = BudgetExpenseCategories::get_data('', array('returnarray' => true));
foreach($categories as $category) {
    $items = $category->get_items();
    $budgeting_commercialexpenses_item = '';
    foreach($items as $item) {

        $comadmin_expenses = BudgetComAdminExpenses::get_data(array('bfbid' => $item->beciid));
        $actualPrevTwoYears = $comadmin_expenses->actualPrevTwoYears;
        $disabled = 'disabled';
        if($comadmin_expenses->actualPrevTwoYears == 0) {
            $actualPrevTwoYears = '';
            $disabled = '';
        }
        eval("\$budgeting_commercialexpenses_item .= \"".$template->get('budgeting_commercialexpenses_item')."\";");
    }
    eval("\$budgeting_commercialexpenses_category .= \"".$template->get('budgeting_commercialexpenses_category')."\";");
}


eval("\$budgeting_financeexpenses = \"".$template->get('budgeting_financeexpenses')."\";");
eval("\$budgeting_commercialexpenses_categories = \"".$template->get('budgeting_commercialexpenses_categories')."\";");
eval("\$budgeting_commercialexpenses = \"".$template->get('budgeting_commercialexpenses')."\";");
output_page($budgeting_commercialexpenses);
?>