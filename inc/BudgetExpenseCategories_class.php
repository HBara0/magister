<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetsExpenseCategories.php
 * Created:        @rasha.aboushakra    Sep 23, 2014 | 1:25:08 PM
 * Last Update:    @rasha.aboushakra    Sep 23, 2014 | 1:25:08 PM
 */

class BudgetExpenseCategories extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'becid';
    const TABLE_NAME = 'budgeting_expense_categories';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = 'becid, name, title';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

    public function save(array $data = array()) {

    }

    public function get_items() {
        return BudgetExpenseItems::get_data(array(self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY]), array('returnarray' => true, 'simple' => false));
    }

    public function parse_financialadminfields($expensescategories, $options = array()) {
        global $template, $lang;
        if(is_array($expensescategories)) {
            foreach($expensescategories as $category) {
                unset($subtotal);
                $budgeting_commercialexpenses_item = '';
                //'actualPrevYear', 'budgetPrevYear'
                $fields = array('actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent');
                $expensesitems = $category->get_items();
                if(is_array($expensesitems)) {
                    foreach($expensesitems as $item) {
                        $comadmin_expenses = BudgetComAdminExpenses::get_data(array('beciid' => $item->beciid, 'bfbid' => $options['financialbudget']->bfbid), array('simple' => false));

                        //Fill budgetPrevYear fields is prev year budget exists & current budget is empty
                        if(is_object($options['prevfinancialbudget']) && !is_object($comadmin_expenses)) {
                            $prevyear_comadmin_expenses = BudgetComAdminExpenses::get_data(array('beciid' => $item->beciid, 'bfbid' => $options['prevfinancialbudget']->bfbid), array('simple' => false));
                            if(isset($prevyear_comadmin_expenses->budgetCurrent) && !empty($prevyear_comadmin_expenses->budgetCurrent)) {
                                $disabledfield = 'readonly';
                                $budgetexps['budgetPrevYear'] = $prevyear_comadmin_expenses->budgetCurrent;
                                $subtotal['budgetPrevYear'] += $budgetexps['budgetPrevYear'];
                            }
                        }
                        //Fill fields from database data If current budget exists and is not empty
                        if(is_object($comadmin_expenses)) {
                            foreach($fields as $field) {
                                $budgetexps[$field] = $comadmin_expenses->$field;
                                $subtotal[$field] += $comadmin_expenses->$field;
                            }
                            $budgetexps['budYefPerc'] = sprintf("%.2f", $comadmin_expenses->budYefPerc).'%';
                        }
                        //Get data from financialadminexpenses array for generate report
                        if(isset($options['financialadminexpenses']) && !empty($options['financialadminexpenses'])) {
                            $financialadminexpenses = $options['financialadminexpenses'];
                            $budgetexps['budYefPerc'] = sprintf("%.2f", $financialadminexpenses[$item->beciid][budYefPerc]).'%';
                        }
                        //parse fields as input or output for modes fill and display respectively
                        foreach($fields as $input) {
                            if(isset($options['mode']) && $options['mode'] == 'fill') {
                                if($input == 'budgetPrevYear') {
                                    $readonly = $disabledfield;
                                }
                                $column_output .=' <td style="width:10%;">'.parse_textfield('budgetexps['.$item->beciid.']['.$input.']', 'budgetexps_'.$item->beciid.'_'.$item->becid.'_'.$input, 'number', $budgetexps[$input], array('accept' => 'numeric', 'step' => 'any', 'required' => 'required', $readonly => $readonly, 'style' => 'width:100%')).'</td>';
                                unset($readonly);
                            }
                            else {
                                if(isset($financialadminexpenses[$item->beciid][$input]) && !empty($financialadminexpenses[$item->beciid][$input])) {
                                    $subtotal[$input] +=$financialadminexpenses[$item->beciid][$input];
                                    $budgetexps[$input] = $financialadminexpenses[$item->beciid][$input];
                                }
                                $column_output .=' <td style="width:10%"><span>'.$budgetexps[$input].'</span></td>';
                            }
                        }
                        eval("\$budgeting_commercialexpenses_item .= \"".$template->get('budgeting_commercialexpenses_item')."\";");
                        unset($column_output, $budgetexps);
                    }
                    foreach($fields as $field) {
                        $total[$field] += $subtotal[$field];
//                        if($total[$field] == 0) {
//                            unset($total[$field]);
//                        }
                    }
                    if($subtotal['yefPrevYear'] != 0) {
                        $subtotal['budYefPerc'] = sprintf("%.2f", (($subtotal['budgetCurrent'] - $subtotal['yefPrevYear']) / $subtotal['yefPrevYear']) * 100).'%';
                    }
                    $total['budYefPerc'] = '0.00%';
                    if($total['yefPrevYear'] != 0) {
                        $total['budYefPerc'] = sprintf("%.2f", (($total['budgetCurrent'] - $total['yefPrevYear']) / $total['yefPrevYear']) * 100).'%';
                    }
                    eval("\$budgeting_commercialexpenses_categories .= \"".$template->get('budgeting_commercialexpenses_category')."\";");
                }
            }
            eval("\$budgeting_commercialexpenses_categories .= \"".$template->get('budgeting_commercialexpenses_total')."\";");

            // parse Finance & General Admin. Expenses
            //'actualPrevYear' => 'finGenAdmExpAmtApy', 'budgetPrevYear' => 'finGenAdmExpAmtBpy',
            $financialbudget_fields = array('actualPrevThreeYears' => 'finGenAdmExpAmtApthy', 'actualPrevTwoYears' => 'finGenAdmExpAmtApty', 'yefPrevYear' => 'finGenAdmExpAmtYpy', 'budgetCurrent' => 'finGenAdmExpAmtCurrent');
            foreach($financialbudget_fields as $key => $value) {
                $financialbudgetdata[$key] = 0;
                if(isset($options['financialbudget']) && !empty($options['financialbudget'])) {
                    if(isset($options['financialbudget']->$value)) {
                        $financialbudgetdata[$key] = $options['financialbudget']->$value;
                    }
                    elseif(is_array($options['financialbudget'])) {
                        $financialbudgetdata[$key] = $options['financialbudget'][$value];
                    }
                }
                if(isset($options['mode']) && $options['mode'] == 'fill') {
                    $fields_output .=' <td>'.parse_textfield('financialbudget['.$value.']', 'finGenAdm_'.$key, 'number', $financialbudgetdata[$key], array('accept' => 'numeric', 'step' => 'any', 'min' => 0, 'max' => $total[$key], 'required' => 'required', 'style' => 'width:100%;'));
                    $fields_output .=parse_textfield('financialbudget[max'.$value.']', 'finGenAdm_max'.$key, 'hidden', $total[$key]).'</td>';
                }
                else {
                    $fields_output .=' <td>'.$financialbudgetdata[$key].'</td>';
                }

                foreach($fields as $field) {
                    if($total[$field] == 0) {
                        $comexpenses[$field] = 0;
                        $propfin[$field] = '0.00%';
                        $propcomexpenses[$field] = '0.00%';
                        continue;
                    }
                    $comexpenses[$field] = $total[$field] - $financialbudgetdata[$field];
                    $propfin[$field] = sprintf("%.2f", (($financialbudgetdata[$field] / $total[$field]) * 100)).'%';
                    $propcomexpenses[$field] = sprintf("%.2f", (( $comexpenses[$field] / $total[$field]) * 100)).'%';
                }
            }
            eval("\$budgeting_commercialexpenses_categories .= \"".$template->get('budgeting_financeexpenses')."\";");
            return $budgeting_commercialexpenses_categories;
        }
    }

}
?>