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
                $inputfields = array('actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent'); //'actualPrevYear', 'budgetPrevYear'
                $budgeting_investexpenses_item = '';
                $items = $category->get_items();
                if(is_array($items)) {
                    foreach($items as $item) {
                        unset($budgetinvst, $disabledfield);
                        $invest_expenses = BudgetInvestExpenses::get_data(array('biiid' => $item->biiid, 'bfbid' => $options['financialbudget']->bfbid), array('simple' => false));

                        if(is_object($options['prevfinancialbudget']) && !is_object($invest_expenses)) {
                            $prevyear_invest_expenses = BudgetInvestExpenses::get_data(array('biiid' => $item->biiid, 'bfbid' => $options['prevfinancialbudget']->bfbid), array('simple' => false));
                            if(isset($prevyear_invest_expenses->budgetCurrent) && !empty($prevyear_invest_expenses->budgetCurrent)) {
                                $disabledfield = 'readonly';
                                $budgetinvst['budgetPrevYear'] = $prevyear_invest_expenses->budgetCurrent;
                                $subtotal['budgetPrevYear'] +=$budgetinvst['budgetPrevYear'];
                            }
                        }
                        if(is_object($invest_expenses)) {
                            foreach($inputfields as $field) {
                                $budgetinvst[$field] = $invest_expenses->$field;
                                $subtotal[$field] +=$invest_expenses->$field;
                            }
                            // $budgetinvst['percVariation'] = sprintf("%.2f", $invest_expenses->percVariation);
                        }

                        $fields = array('actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent'); // percVariation,'actualPrevYear', 'budgetPrevYear',
                        foreach($fields as $input) {
                            $column_output .=' <td style="width:10%">';
                            if(isset($options['mode']) && $options['mode'] == 'fill') {
                                $type = 'number';
//                                if($input == 'budgetPrevYear') {
//                                    $readonly = $disabledfield;
//                                }
//                                if($input == 'percVariation') {
//                                    $type = 'hidden';
//                                    $column_output .='<div id="budgetinvst_'.$item->biiid.'_'.$item->bicid.'_'.$input.'">'.$budgetinvst[$input].'</div>';
//                                }
                                $column_output .= parse_textfield('budgetinvst['.$item->biiid.']['.$input.']', 'budgetinvst_'.$item->biiid.'_'.$item->bicid.'_'.$input, $type, $budgetinvst[$input], array('accept' => 'numeric', 'required' => 'required', 'step' => 'any', $readonly => $readonly, 'style' => 'width:100%;')).'</td>';
                                unset($readonly);
                            }
                            else {
                                if(isset($options['investmentfollowup']) && !empty($options['investmentfollowup'])) {
                                    $budgetinvst = $options['investmentfollowup'];
                                    $budgetinvst[$input] = $budgetinvst[$item->biiid][$input];
                                    if(!empty($budgetinvst[$input])) {
                                        $subtotal[$input] +=$budgetinvst[$input];
                                    }
                                }
                                $column_output .='<span>'.$budgetinvst[$input].'</span></td>';
                            }
                        }
                        eval("\$budgeting_investexpenses_item .= \"".$template->get('budgeting_investexpenses_item')."\";");
                        $field_output = $column_output = '';
                    }
                }
//                if($subtotal['yefPrevYear'] != 0 && ($subtotal['yefPrevYear'] - $subtotal['budgetPrevYear']) != 0) {
//                    $subtotal['percVariation'] = sprintf("%.2f", (($subtotal['yefPrevYear'] - $subtotal['budgetPrevYear']) / $subtotal['budgetPrevYear']) * 100).'%';
//                }
                foreach($inputfields as $field) {
                    $total[$field] += $subtotal[$field];
                }
                eval("\$budgeting_investexpenses_category  .= \"".$template->get('budgeting_investexpenses_category')."\";");
            }
        }
//        if($total ['yefPrevYear'] != 0 && ($total ['yefPrevYear'] - $total ['budgetPrevYear']) != 0) {
//            $total['percVariation'] = sprintf("%.2f", (($total ['yefPrevYear'] - $total ['budgetPrevYear']) / $total ['budgetPrevYear']) * 100).'%';
//        }
        $budgeting_investexpenses_category .= '<tr><td style="width:25%;font-weight:bold;">'.$lang->total.'</td>';
        foreach($fields as $field) {
            $budgeting_investexpenses_category .='<td><div style="font-weight:bold;width:12.5%" id="total_'.$field.'">'.$total[$field].'</div></td>';
        }
        $budgeting_investexpenses_category .='</tr>';
        return $budgeting_investexpenses_category;
    }

}
?>