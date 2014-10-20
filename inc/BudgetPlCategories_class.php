<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetPlCategories_class.php
 * Created:        @rasha.aboushakra    Oct 13, 2014 | 2:34:06 PM
 * Last Update:    @rasha.aboushakra    Oct 13, 2014 | 2:34:06 PM
 */

class BudgetPlCategories extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'bplcid';
    const TABLE_NAME = 'budgeting_plcategory';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = 'bplcid, name, title';
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

    public function get_items(array $data = array()) {
        return BudgetPlItems::get_data(array(self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY]), array('returnarray' => true, 'simple' => false));
    }

    public static function parse_plfields($plcategories, $options = array()) {
        global $template, $lang, $db;
        if(is_array($plcategories)) {
//            $headerfields = array('actual', 'budget', 'yef', 'yefactual', 'yefbud', 'budgetCurrent', 'budyef');
//            $budgeting_header .='<tr class="thead"><td style="width:25%"></td>';
//            foreach($headerfields as $field) {
//                $budgeting_header .= '<td style="width:12.5%">'.$lang->$field.'</td>';
//            }
//            $budgeting_header .='</tr>';
//            $output .= $budgeting_header;
            foreach($plcategories as $category) {
                $plitems = $category->get_items();
                if(is_array($plitems)) {
                    foreach($plitems as $item) {
                        $plexpenses_current = BudgetPlExpenses::get_data(array('bpliid' => $item->bpliid, 'bfbid' => $options['financialbudget']->bfbid));
                        $fields = array('actualPrevTwoYears', 'budgetPrevYear', 'yefPrevYear', 'yefactual', 'yefbud', 'budgetCurrent', 'budyef');
                        $column_output .= '<td style="width:25%">'.$item->title.'<input type="hidden" name="placcount['.$item->bpliid.'][bpliid]" value='.$item->bpliid.'></td>';
                        foreach($fields as $input) {
                            if($input === 'yefactual' || $input === 'yefbud' || $input === 'budyef') {
                                if($plexpenses_current->actualPrevTwoYears != 0) {
                                    $plexpenses['yefactual'] = (($plexpenses_current->yefPrevYear - $plexpenses_current->actualPrevTwoYears) / $plexpenses_current->actualPrevTwoYears);
                                }
                                $column_output .='<td style="width:8.3%" class="border_left"><div id="placcount_'.$category->name.'_'.$input.'_'.$item->bpliid.'" >'.$plexpenses[$input].'</div></td>';
                            }
                            //placcount_operatingprofit_yefactual_3
                            else {
                                if(isset($options['mode']) && $options ['mode'] == 'fill') {
                                    $column_output .='<td style="width:12.5%" class="border_left">'.parse_textfield('placcount['.$item->bpliid.']['.$input.']', 'placcount_'.$category->name.'_'.$input.'_'.$item->bpliid, 'number', $plexpenses_current->$input, array('accept' => 'numeric', 'step' => 'any', $readonly => $readonly, 'style' => 'width:100%;')).'</td>';
                                }
                                else {
                                    if(isset($options['placcount']) && !empty($options['placcount'])) {
                                        $placcount = $options['placcount'];
                                        $plexpenses_current = new BudgetPlExpenses();
                                        $plexpenses_current->$input = $placcount[$item->bpliid][$input];
                                    }
                                    $column_output .=' <td style="width:12.5%">'.$plexpenses_current->$input.'</td>';
                                    //  $total[$input] += $headcount[$group->posgid][$input];
                                    // }
                                }
                            }
                        }
                        eval("\$category_item .= \"".$template->get('budgeting_plcategory_item')."\";");
                        $output .=$category_item;
                        $category_item = $column_output = '';
                    }
                    $title = $category->title;
                    $column_output .= '<td style="width:25%;font-weight:bold;">'.$title.'</td>';
                    foreach($fields as $input) {
                        $column_output .=' <td style="width:12.5%;font-weight:bold;"><div id="total_'.$category->name.'_'.$input.'" value="">'.$placcount[$input].'</div>';
                        $column_output .=parse_textfield('', 'total_'.$category->name.'_'.$input, 'hidden', $placcount[$input]).'</td>';
                        $column_output .= '</td>';
                    }
                    eval("\$category_total .= \"".$template->get('budgeting_plcategory_item')."\";");
                    $output .=$category_total;
                    $category_total = $column_output = '';
                }
                else {
                    if($category->name == 'sales') {
                        if(is_array($options['bid'])) {
                            foreach($options['bid'] as $key => $id) {
                                if(isset($id) && !empty($id)) {
                                    $operator = '=';
                                    if(is_array($id)) {
                                        $id = "(".implode(',', $id).")";
                                        $operator = 'IN';
                                    }
                                    $query = $db->query("SELECT saleType,sum(amount) AS amount, sum(income) AS income,sum(actualAmount) AS actualAmount, sum(actualIncome) AS actualIncome FROM ".Tprefix."budgeting_budgets_lines where bid ".$operator." ".$id." GROUP BY saleType");
                                    if($db->num_rows($query) > 0) {
                                        $amount = 'amount';
                                        $income = 'income';
                                        while($budget = $db->fetch_assoc($query)) {
                                            if($key == 'prevtwoyears') {
                                                $amount = 'actualAmount';
                                                $income = 'actualIncome';
                                            }
                                            $combudget[$key][$budget['saleType']]['amount'] = $budget[$amount];
                                            $combudget[$key][$budget['saleType']]['income'] = $budget[$income];
                                        }
                                    }
                                }
                            }
                            $saletypes = SaleTypes::get_data();
                            foreach($saletypes as $type) {
                                $combudget[yef][$type->stid]['amount'] = $combudget[yef][$type->stid]['income'] = 10;

                                $commercialbudget_item_rows = array('amount', 'income');
                                foreach($commercialbudget_item_rows as $row) {
                                    $combudget[yefactual][$type->stid][$row] = $combudget[yefbud][$type->stid][$row] = 0;
                                    if($combudget[prevtwoyears][$type->stid][$row] != 0) {
                                        $combudget[yefactual][$type->stid][$row] = sprintf("%.2f", (($combudget[yef][$type->stid][$row] - $combudget[prevtwoyears][$type->stid][$row]) / $combudget[prevtwoyears][$type->stid][$row]) * 100);
                                    }
                                    if($combudget[prevyear][$type->stid][$row] != 0) {
                                        $combudget[yefbud][$type->stid][$row] = sprintf("%.2f", (($combudget[yef][$type->stid][$row] - $combudget[prevyear][$type->stid][$row]) / $combudget[prevyear][$type->stid][$row]) * 100);
                                    }
                                    if($combudget[yef][$type->stid][$row] != 0) {
                                        $combudget[budyef][$type->stid][$row] = sprintf("%.2f", (($combudget[current][$type->stid][$row] - $combudget[yef][$type->stid][$row]) / $combudget[yef][$type->stid][$row]) * 100);
                                    }
                                }
                                $fields = array('prevtwoyears', 'prevyear', 'yef', 'yefactual', 'yefbud', 'current', 'budyef');
                                $amount_output .=' <td style="width:25%;font-weight:bold;">'.$type->title.'</td>';
                                $income_output .='<td style="width:25%">'.$lang->accountedcommissions.'</td>';
                                foreach($fields as $field) {
                                    if($field == 'yefactual' || $field == 'yefbud' || $field == 'budyef') {
                                        $amount_output .='<td style="width:8.3%" class="border_left"><div id="placcount_'.$category->name.'_'.$field.'_'.$type->stid.'">'.$combudget[$field][$type->stid]['amount'].'</div></td>';
                                        $income_output .='<td style="width:8.3%" class="border_left"><div id="placcount_'.$category->name.'_'.$field.'_'.$type->stid.'">'.$combudget[$field][$type->stid]['income'].'</div></td>';
                                    }
                                    else {

                                        $amount_output .='<td style="width:12.5%" class="border_left">'.$combudget[$field][$type->stid]['amount'].'</td>';
                                        $income_output .='<td style="width:12.5%" class="border_left">'.$combudget[$field][$type->stid]['income'].'</td>';
                                        $totalincome[$field] += $combudget[$field][$type->stid]['income'];
                                        $combudget[$field][$type->stid]['perc'] = 0;
                                        if($combudget[$field][$type->stid]['amount'] != 0) {
                                            $combudget[$field][$type->stid]['perc'] = sprintf("%.2f", ($combudget[$field][$type->stid]['income'] / $combudget[$field][$type->stid]['amount']) * 100);
                                        }
                                    }
                                }
                                $row = 'perc';
                                if($combudget[prevtwoyears][$type->stid][$row] != 0) {
                                    $combudget[yefactual][$type->stid][$row] = sprintf("%.2f", (($combudget[yef][$type->stid][$row] - $combudget[prevtwoyears][$type->stid][$row]) / $combudget[prevtwoyears][$type->stid][$row]) * 100);
                                }
                                if($combudget[prevyear][$type->stid][$row] != 0) {
                                    $combudget[yefbud][$type->stid][$row] = sprintf("%.2f", (($combudget[yef][$type->stid][$row] - $combudget[prevyear][$type->stid][$row]) / $combudget[prevyear][$type->stid][$row]) * 100);
                                }
                                if($combudget[yef][$type->stid][$row] != 0) {
                                    $combudget[budyef][$type->stid][$row] = sprintf("%.2f", (($combudget[current][$type->stid][$row] - $combudget[yef][$type->stid][$row]) / $combudget[yef][$type->stid][$row]) * 100);
                                }
                                $rowclass = alt_row($rowclass);
                                eval("\$output .= \"".$template->get('budgeting_plitem')."\";");
                                $income_output = $amount_output = '';
                            }
                            $column_output .='<td style="width:25%"></td>';
                            foreach($fields as $field) {
                                $width = '12.5%;';
                                if($field == 'yefactual' || $field == 'yefbud' || $field == 'budyef') {
                                    $width = '8.3%;';
                                }
                                $column_output.'<td style="width:'.$width.'"><input type="hidden" id="total_'.$category->name.'_'.$field.'" value="'.$totalincome[$field].'"></td>';
                            }
                            eval("\$output .= \"".$template->get('budgeting_plcategory_item')."\";");
                            unset($column_output);
                        }
                    }
                    if($category->name == 'admcomexpenses') {
                        $rows = array('adminexpenses', 'commercialexpenses', 'totaladmcom');
                        $budgets = array('actualPrevTwoYears' => 'finGenAdmExpAmtApty', 'budgetPrevYear' => 'finGenAdmExpAmtBpy', 'yefPrevYear' => 'finGenAdmExpAmtYpy', 'yefactual' => 'yefactual', 'yefbud' => 'yefbud', 'budgetCurrent' => 'finGenAdmExpAmtCurrent', 'budyef' => 'budyef');

                        if(is_object($options['financialbudget'])) {
                            $finbudgetitems = BudgetComAdminExpenses::get_data(array('bfbid' => $options['financialbudget']->bfbid), array('returnarray' => true));
                        }
                        else { //for generate report (mode=display) case where more than one affiliate is selected
                            $financialbudget_query = $db->query("SELECT sum(finGenAdmExpAmtApty) AS finGenAdmExpAmtApty, sum(finGenAdmExpAmtBpy) AS finGenAdmExpAmtBpy,sum(finGenAdmExpAmtYpy) AS finGenAdmExpAmtYpy, sum(finGenAdmExpAmtCurrent) AS finGenAdmExpAmtCurrent FROM ".Tprefix."budgeting_financialbudget where bfbid IN (".implode(', ', $options['filter'])." )");
                            if($db->num_rows($financialbudget_query) > 0) {
                                while($budget = $db->fetch_assoc($financialbudget_query)) {
                                    $financialbudget = $budget;
                                }
                            }
                            $finbudgetitems = BudgetComAdminExpenses::get_data(array('bfbid' => $options['filter']), array('returnarray' => true, 'operators' => array('bfbid' => IN)));
                        }
                        foreach($rows as $row) {
                            $style = 'style = "width:25%"';
                            if($row === 'totaladmcom') {
                                $style = 'style = "width:25%;font-weight:bold;"';
                            }
                            ${"output_".$row} .='<td '.$style.'>'.$lang->$row.'</td>';
                        }
                        foreach($budgets as $key => $value) {
                            $width = '12.5%;';
                            foreach($finbudgetitems as $item) {
                                $comercialbudget[$key] +=$item->$key;
                            }

                            if(empty($financialbudget[$value])) {
                                $financialbudget[$value] = $options['financialbudget']->$value;
                            }
                            $financialbudget[$key] = $financialbudget[$value];
                            $commercialexpenses[$key] = $comercialbudget[$key] - $financialbudget[$value];
                            if($key === 'yefactual' || $key === 'yefbud' || $key === 'budyef') {
                                $width = '8.3%';

                                if($comercialbudget['actualPrevTwoYears'] != 0) {
                                    $comercialbudget['yefactual'] = sprintf("%.2f", (($comercialbudget['yefPrevYear'] - $comercialbudget['actualPrevTwoYears']) / $comercialbudget['actualPrevTwoYears']) * 100);
                                }
                                if($comercialbudget['budgetPrevYear'] != 0) {
                                    $comercialbudget['yefbud'] = sprintf("%.2f", (($comercialbudget['yefPrevYear'] - $comercialbudget['budgetPrevYear']) / $comercialbudget['budgetPrevYear']) * 100);
                                }
                                if($comercialbudget['yefPrevYear'] != 0) {
                                    $comercialbudget['budyef'] = sprintf("%.2f", (($comercialbudget['budgetCurrent'] - $comercialbudget['yefPrevYear']) / $comercialbudget['yefPrevYear']) * 100);
                                }

                                if($commercialexpenses['actualPrevTwoYears'] != 0) {
                                    $commercialexpenses['yefactual'] = sprintf("%.2f", (($commercialexpenses['yefPrevYear'] - $commercialexpenses['actualPrevTwoYears']) / $commercialexpenses['actualPrevTwoYears']) * 100);
                                }
                                if($commercialexpenses['budgetPrevYear'] != 0) {
                                    $commercialexpenses['yefbud'] = sprintf("%.2f", (($commercialexpenses['yefPrevYear'] - $commercialexpenses['budgetPrevYear']) / $commercialexpenses['budgetPrevYear']) * 100);
                                }
                                if($commercialexpenses['yefPrevYear'] != 0) {
                                    $commercialexpenses['budyef'] = sprintf("%.2f", (($commercialexpenses['budgetCurrent'] - $commercialexpenses['yefPrevYear']) / $commercialexpenses['yefPrevYear']) * 100);
                                }

                                if($financialbudget['actualPrevTwoYears'] != 0) {
                                    $financialbudget['yefactual'] = sprintf("%.2f", (($financialbudget['yefPrevYear'] - $financialbudget['actualPrevTwoYears']) / $financialbudget['actualPrevTwoYears']) * 100);
                                }
                                if($financialbudget['budgetPrevYear'] != 0) {
                                    $financialbudget['yefbud'] = sprintf("%.2f", (($financialbudget['yefPrevYear'] - $financialbudget['budgetPrevYear']) / $financialbudget['budgetPrevYear']) * 100);
                                }
                                if($financialbudget['yefPrevYear'] != 0) {
                                    $financialbudget['budyef'] = sprintf("%.2f", (($financialbudget['budgetCurrent'] - $financialbudget['yefPrevYear']) / $financialbudget['yefPrevYear']) * 100);
                                }
                            }

                            $output_adminexpenses .='<td style = "width:'.$width.'" class="border_left"><div id = "adminexpenses_'.$key.'">'.$financialbudget[$key].'</div></td>';
                            $output_commercialexpenses .='<td style = "width:'.$width.'" class="border_left"><div id = "total_'.$category->name.'_'.$key.'">'.$commercialexpenses[$key].'</div></td>';
                            $output_totaladmcom .='<td style = "width:'.$width.'font-weight:bold;" class="border_left"><div id = "commercialexpenses_'.$key.'">'.$comercialbudget[$key].'</div></td>';
                        }
                        foreach($rows as $row) {
                            $column_output = ${"output_".$row};
                            eval("\$output .= \"".$template->get('budgeting_plcategory_item')."\";");
                            unset($column_output);
                        }
                    }
                }
            }
        }
        return $output;
    }

}
?>