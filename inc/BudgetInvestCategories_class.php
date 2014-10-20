<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetsExpenseCategories.php
 * Created:        @rasha.aboushakra    Sep 23, 2014 | 1:25:08 PM
 * Last Update:    @rasha.aboushakra    Sep 23, 2014 | 1:25:08 PM
 */

class BudgetInvestCategories extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'bicid';
    const TABLE_NAME = 'budgeting_investcategory';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = 'bicid, name, title';
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
        $items = BudgetInvestItems::get_data(array(self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY]), array('returnarray' => true, 'simple' => false));
        return $items;
    }

    public static function parse_expensesfields($categories, $options = array()) {
        global $template, $lang;

        if(is_array($categories)) {
            foreach($categories as $category) {
                unset($subtotal);
                unset($readonly);
                $fields = array('budgetPrevYear', 'yefPrevYear', 'budgetCurrent');
                $budgeting_investexpenses_item = '';
                //$category = $this;
                $items = $category->get_items();
                if(is_array($items)) {
                    foreach($items as $item) {
                        $invest_expenses = BudgetInvestExpenses::get_data(array('biiid' => $item->biiid, 'bfbid' => $options['financialbudget']->bfbid), array('simple' => false));
                        if(is_object($invest_expenses)) {
                            foreach($fields as $field) {
                                $budgetinvst[$field] = $invest_expenses->$field;
                                $subtotal[$field] +=$invest_expenses->$field;
                            }
                            $budgetinvst['percVariation'] = sprintf("%.2f", $invest_expenses->budYefPerc).'%';
                            if($subtotal['yefPrevYear'] != 0 && ($subtotal['budgetCurrent'] - $subtotal['yefPrevYear']) != 0) {
                                $subtotal['percVariation'] = sprintf("%.2f", (($subtotal['budgetCurrent'] - $subtotal['yefPrevYear']) / $subtotal['yefPrevYear']) * 100).'%';
                            }
                        }
                        if(is_object($options['prevfinancialbudget'])) {
                            $prevyear_invest_expenses = BudgetInvestExpenses::get_data(array('biiid' => $item->biiid, 'bfbid' => $options['prevfinancialbudget']->bfbid), array('simple' => false));
                            $readonly = 'readonly';
                            $budgetinvst['budgetPrevYear'] = $prevyear_invest_expenses->budgetCurrent;
                            $subtotal['budgetPrevYear'] +=$budgetinvst['budgetPrevYear'];
                        }
                        $config_fields = array('budgetPrevYear', 'yefPrevYear', 'budgetCurrent');
                        if(isset($options['mode']) && $options['mode'] == 'fill') {
                            foreach($config_fields as $input) {
                                $column_output.=' <td style="width:10%">'.parse_textfield('budgetinvst['.$item->biiid.']['.$input.']', 'budgetinvst_'.$item->biiid.'_'.$item->bicid.'_'.'budgetPrevYear', 'text', $budgetinvst[$input], array('accept' => 'numeric')).'</td>';
                            }
                        }
                        else {
                            foreach($config_fields as $input) {
                                $column_output.= $budgetinvst[$input];
                            }
                            //eval view mode template
                        }
                        eval("\$budgeting_investexpenses_item .= \"".$template->get('budgeting_investexpenses_item')."\";");
                        $field_output = $column_output = '';
                    }
                    foreach($fields as $field) {
                        $total[$field] += $subtotal[$field];
                        if($total[$field] == 0) {
                            unset($total[$field]);
                        }
                    }
                }
                eval("\$budgeting_investexpenses_category  .= \"".$template->get('budgeting_investexpenses_category')."\";");
            }
        }

        return $budgeting_investexpenses_category;
    }

}
?>