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
            foreach($plcategories as $category) {
                $plitems = $category->get_items();
                if(is_array($plitems)) {
                    foreach($plitems as $item) {
                        $plexpenses_current = BudgetPlExpenses::get_data(array('bpliid' => $item->bpliid, 'bfbid' => $options['financialbudget']->bfbid));
                        $fields = array('actualPrevTwoYears', 'budgetPrevYear', 'yefPrevYear', 'yefactual', 'yefbud', 'budgetCurrent', 'budyef');
                        $column_output .= '<td style="width:25%">'.$item->title.'<input type="hidden" name="placcount['.$item->bpliid.'][bpliid]" value='.$item->bpliid.'></td>';
                        foreach($fields as $input) {
                            if($input === 'yefactual' || $input === 'yefbud' || $input === 'budyef') {
                                $plexpenses[$input] = '0.00%';
                                if($plexpenses_current->actualPrevTwoYears != 0) {
                                    $plexpenses['yefactual'] = sprintf("%.2f", (($plexpenses_current->yefPrevYear - $plexpenses_current->actualPrevTwoYears) / $plexpenses_current->actualPrevTwoYears) * 100).' %';
                                }
                                if($plexpenses_current->budgetPrevYear != 0) {
                                    $plexpenses['yefbud'] = sprintf("%.2f", (($plexpenses_current->yefPrevYear - $plexpenses_current->budgetPrevYear) / $plexpenses_current->budgetPrevYear) * 100).' %';
                                }
                                if($plexpenses_current->yefPrevYear != 0) {
                                    $plexpenses['budyef'] = sprintf("%.2f", (($plexpenses_current->budgetCurrent - $plexpenses_current->yefPrevYear) / $plexpenses_current->yefPrevYear) * 100).' %';
                                }
                                $column_output .='<td style="width:8.3%" class="border_left"><div id="placcount_'.$category->name.'_'.$input.'_'.$item->bpliid.'" >'.$plexpenses[$input].'</div></td>';
                                $total['plexpenses'][$input] += $plexpenses[$input];
                            }
                            else {
                                if(isset($options['mode']) && $options['mode'] === 'fill') {
                                    $column_output .='<td style="width:12.5%" class="border_left">'.parse_textfield('placcount['.$item->bpliid.']['.$input.']', 'placcount_'.$category->name.'_'.$input.'_'.$item->bpliid, 'number', sprintf("%.2f", $plexpenses_current->$input), array('step' => 'any', $readonly => $readonly, 'style' => 'width:100%;
                                    ')).'</td>';
                                }
                                else {
                                    if(isset($options['placcount']) && !empty($options['placcount'])) {
                                        $placcount = $options['placcount'];
                                        $plexpenses_current = new BudgetPlExpenses();
                                        $plexpenses_current->$input = sprintf("%.2f", $placcount[$item->bpliid][$input]);
                                    }
                                    $column_output .=' <td style = "width:12.5%">'.$plexpenses_current->$input.'</td>';
                                }
                                $total['plexpenses'][$input] +=$plexpenses_current->$input;
                            }
                        }
                        eval("\$category_item .= \"".$template->get('budgeting_plcategory_item')."\";");
                        $output .=$category_item;
                        unset($plexpenses);
                        $category_item = $column_output = '';
                    }
                    $title = $category->title;
                    $column_output .= '<td style = "width:25%;font-weight:bold;">'.$title.'</td>';
                    foreach($fields as $input) {
                        switch($category->name) {
                            case'income':
                                $total['plexpenses'][$input] = sprintf("%.2f", $total['plexpenses'][$input] + $totalincome[$input]);
                                break;
                            case'operatingprofit':
                                $total['plexpenses'][$input] = sprintf("%.2f", $total['plexpenses'][$input] + $comercialbudget[$input]);
                                break;
                        }
                        if($input === 'yefactual' || $input === 'yefbud' || $input === 'budyef') {
                            if($total['plexpenses']['actualPrevTwoYears'] != 0) {
                                $total['plexpenses']['yefactual'] = sprintf("%.2f", (( $total['plexpenses']['yefPrevYear'] - $total['plexpenses']['actualPrevTwoYears']) / $total['plexpenses']['actualPrevTwoYears']) * 100).' %';
                            }
                            if($total['plexpenses']['budgetPrevYear'] != 0) {
                                $total['plexpenses']['yefbud'] = sprintf("%.2f", (( $total['plexpenses']['yefPrevYear'] - $total['plexpenses']['budgetPrevYear']) / $total['plexpenses']['budgetPrevYear']) * 100).' %';
                            }
                            if($total['plexpenses']['yefPrevYear'] != 0) {
                                $total['plexpenses']['budyef'] = sprintf("%.2f", (( $total['plexpenses']['budgetCurrent'] - $total['plexpenses']['yefPrevYear']) / $total['plexpenses']['yefPrevYear']) * 100).' %';
                            }
                        }
                        $column_output .=' <td style = "width:12.5%;font-weight:bold;"><div id = "total_'.$category->name.'_'.$input.'">'.$total['plexpenses'][$input].'</div>';
                        $column_output .=parse_textfield('', 'total_'.$category->name.'_'.$input, 'hidden', $total['plexpenses'][$input]).'</td>';
                        if($category->name === 'netincome' && $input === 'budgetCurrent') {
                            $column_output .=parse_textfield('financialbudget[income]', 'total_'.$category->name.'_'.$input, 'hidden', $total['plexpenses'][$input]).'</td>';
                        }
                        $column_output .= '</td>';
                    }
                    eval("\$category_total .= \"".$template->get('budgeting_plcategory_item')."\";");
                    $output .=$category_total;
                    $category_total = $column_output = '';
                }
                else {
                    if($category->name == 'sales') {
                        if(is_array($options['bid'])) {
//get commercial budgets data if exits
                            foreach($options['bid'] as $key => $budgetsids) {
                                if(isset($budgetsids) && !empty($budgetsids)) {
                                    if(is_array($budgetsids)) {
                                        foreach($budgetsids as $budgetid) {
                                            $budgetobject = Budgets::get_data(array('bid' => $budgetid), array('simple' => false));
                                            if(!is_object($budgetobject)) {
                                                continue;
                                            }

                                            $budgetlines = BudgetLines::get_data(array('bid' => $budgetobject->bid), array('returnarray' => true));
                                            if(is_array($budgetlines)) {
                                                foreach($budgetlines as $budgetline) {
                                                    $budget_currencies[$budgetline->blid] = $budgetline->originalCurrency;
                                                }
                                                $budget_currencies = array_unique($budget_currencies);
                                            }

                                            $dal_config = array(
                                                    'operators' => array('fromCurrency' => 'in', 'affid' => ' = ', 'year' => ' = '),
                                                    'simple' => false,
                                                    'returnarray' => true
                                            );

                                            $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budget_currencies, 'toCurrency' => $options['tocurrency'], 'affid' => $budgetobject->affid, 'year' => $budgetobject->year), $dal_config);
                                            if(is_array($fxrates_obj)) {
                                                if(count($budget_currencies) != count($fxrates_obj)) {
                                                    foreach($fxrates_obj as $budgetrate) {
                                                        $budget_currency[] = $budgetrate->fromCurrency;
                                                    }
                                                    $currencies_diff = array_diff($budget_currencies, $budget_currency);
                                                    if(is_array($currencies_diff)) {
                                                        foreach($currencies_diff as $currencyid) {
                                                            $currency = new Currencies($currencyid);
                                                            $output_currname .= $comma.$currency->name;
                                                            $comma = ', ';
                                                        }
                                                    }
                                                    if($currencyid != $options['tocurrency']) {
                                                        error($lang->sprint($lang->noexchangerate, $output_currname, $options['tocurrency'], $budgetobject->year), $_SERVER['HTTP_REFERER']);
                                                    }
                                                }
                                            }
                                            else {
                                                error($lang->sprint($lang->noexchangerate, implode(', ', $budget_currencies), $options['tocurrency'], $budgetobject->year), $_SERVER['HTTP_REFERER']);
                                            }

                                            $fxrate_query = "(CASE WHEN budgeting_budgets_lines.originalCurrency=".intval($options['tocurrency'])." THEN 1 ELSE (SELECT rate FROM budgeting_fxrates WHERE affid=".$budgetobject->affid." AND year=".$budgetobject->year." AND fromCurrency=budgeting_budgets_lines.originalCurrency AND toCurrency=".intval($options['tocurrency']).") END)";
                                            $sql = "SELECT saleType,sum(amount*{$fxrate_query}) AS amount,sum(income*{$fxrate_query}) AS income, sum(actualAmount*{$fxrate_query}) AS actualAmount, sum(actualIncome*{$fxrate_query}) AS actualIncome FROM ".Tprefix."budgeting_budgets_lines where bid=".$budgetid." GROUP BY saleType";
                                            //echo $sql;
                                            //echo '<hr />';
                                            $query = $db->query($sql);
                                            if($db->num_rows($query) > 0) {
                                                $amount = 'amount';
                                                $income = 'income';
                                                while($budget = $db->fetch_assoc($query)) {
                                                    if($key == 'prevtwoyears') {
                                                        $amount = 'actualAmount';
                                                        $income = 'actualIncome';
                                                    }
                                                    $combudget[$key][$budget['saleType']]['amount'] += sprintf("%.2f", $budget[$amount] / 1000);
                                                    $combudget[$key][$budget['saleType']]['income'] += sprintf("%.2f", $budget[$income] / 1000);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $saletypes = SaleTypes::get_data();
//loop over salestypes to parse fields
                            foreach($saletypes as $type) {
                                $combudget[yef][$type->stid]['amount'] = $combudget[yef][$type->stid]['income'] = 10;

                                $fields = array('prevtwoyears', 'prevyear', 'yef', 'current');
                                foreach($fields as $field) {
                                    $combudget[$field][$type->stid]['perc'] = 0;
                                    if($combudget[$field][$type->stid]['amount'] != 0) {
                                        $combudget[$field][$type->stid]['perc'] = sprintf("%.2f", ($combudget[$field][$type->stid]['income'] / $combudget[$field][$type->stid]['amount']) * 100);
                                    }
                                }
//calculate yef/prev2years , yef/budgetprevyear and budgetCurrent/yef percentages
                                $commercialbudget_item_rows = array('amount', 'income', 'perc');
                                foreach($commercialbudget_item_rows as $row) {
                                    $combudget[yefactual][$type->stid][$row] = $combudget[yefbud][$type->stid][$row] = $combudget[budyef][$type->stid][$row] = '0.00%';
                                    if($combudget[prevtwoyears][$type->stid][$row] != 0) {
                                        $combudget[yefactual][$type->stid][$row] = sprintf("%.2f", (($combudget[yef][$type->stid][$row] - $combudget[prevtwoyears][$type->stid][$row]) / $combudget[prevtwoyears][$type->stid][$row]) * 100).' %';
                                    }
                                    if($combudget[prevyear][$type->stid][$row] != 0) {
                                        $combudget[yefbud][$type->stid][$row] = sprintf("%.2f", (($combudget[yef][$type->stid][$row] - $combudget[prevyear][$type->stid][$row]) / $combudget[prevyear][$type->stid][$row]) * 100).' %';
                                    }
                                    if($combudget[yef][$type->stid][$row] != 0) {
                                        $combudget[budyef][$type->stid][$row] = sprintf("%.2f", (($combudget[current][$type->stid][$row] - $combudget[yef][$type->stid][$row]) / $combudget[yef][$type->stid][$row]) * 100).' %';
                                    }
                                }
//parse fields
                                $fields = array('prevtwoyears', 'prevyear', 'yef', 'yefactual', 'yefbud', 'current', 'budyef');
                                $amount_output .=' <td style = "width:25%;font-weight:bold;">'.$type->title.'</td>';
                                $income_output .='<td style = "width:25%">'.$lang->accountedcommissions.'</td>';
                                foreach($fields as $field) {
                                    if(empty($combudget[$field][$type->stid]['amount'])) {
                                        $combudget[$field][$type->stid]['amount'] = 0;
                                    }
                                    if(empty($combudget[$field][$type->stid]['income'])) {
                                        $combudget[$field][$type->stid]['income'] = 0;
                                    }
                                    if($field == 'yefactual' || $field == 'yefbud' || $field == 'budyef') {
                                        $amount_output .='<td style = "width:8.3%" class = "border_left"><div id = "placcount_'.$category->name.'_'.$field.'_'.$type->stid.'">'.$combudget[$field][$type->stid]['amount'].'</div></td>';
                                        $income_output .='<td style = "width:8.3%" class = "border_left"><div id = "placcount_'.$category->name.'_'.$field.'_'.$type->stid.'">'.$combudget[$field][$type->stid]['income'].'</div></td>';
                                    }
                                    else {
                                        $amount_output .='<td style = "width:12.5%" class = "border_left">'.$combudget[$field][$type->stid]['amount'].'</td>';
                                        $income_output .='<td style = "width:12.5%" class = "border_left">'.$combudget[$field][$type->stid]['income'].'</td>';
                                        $totalincome[$field] += $combudget[$field][$type->stid]['income'];
                                    }
                                }
                                $rowclass = alt_row($rowclass);
                                eval("\$output .= \"".$template->get('budgeting_plitem')."\";");
                                $income_output = $amount_output = '';
                            }
                            $column_output .='<td style = "width:25%"></td>';
                            $hiddenfields = array('actualPrevTwoYears' => 'prevtwoyears', 'budgetPrevYear' => 'prevyear', 'yefPrevYear' => 'yef', 'yefactual' => 'yefactual', 'yefbud' => 'yefbud', 'budgetCurrent' => current, 'budyef' => 'budyef');
// parse hidden fields for Sales category total
                            foreach($hiddenfields as $key => $value) {
                                $width = '12.5%;
                                    ';
                                if($field == 'yefactual' || $field == 'yefbud' || $field == 'budyef') {
                                    $width = '8.3%;
                                    ';
                                }
                                $totalincome[$key] = $totalincome[$value];
                                $column_output.='<td style = "width:'.$width.'"><input type = "hidden" id = "total_'.$category->name.'_'.$key.'" value = "'.$totalincome[$key].'"></td>';
                            }
                            eval("\$output .= \"".$template->get('budgeting_plcategory_item')."\";");
                            unset($column_output);
                        }
                    }
//parse Adm.Com. Expenses section
                    if($category->name == 'admcomexpenses') {
                        $rows = array('adminexpenses', 'commercialexpenses', 'totaladmcom');
                        $budgets = array('actualPrevThreeYears' => 'finGenAdmExpAmtApthy', 'actualPrevTwoYears' => 'finGenAdmExpAmtApty', 'yefPrevYear' => 'finGenAdmExpAmtApty', 'yefactual' => 'yefactual', 'yefbud' => 'yefbud', 'budgetCurrent' => 'finGenAdmExpAmtCurrent', 'budyef' => 'budyef');
                        if(is_object($options['financialbudget'])) {
                            $options['filter'][] = $options['financialbudget']->bfbid;
//                            $finbudgetitems = BudgetComAdminExpenses::get_data(array('bfbid' => $options['financialbudget']->bfbid), array('returnarray' => true));
                        }

//for generate report (mode=display) case where more than one affiliate is selected
                        if(is_array($options['filter'])) {
                            $fxrate_query2 = '(CASE WHEN budgeting_financialbudget.currency = '.intval($options['tocurrency']).' THEN 1
                                    ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = budgeting_financialbudget.affid AND bfr.year = budgeting_financialbudget.year AND bfr.fromCurrency = budgeting_financialbudget.currency AND bfr.toCurrency = '.intval($options['tocurrency']).') END)';
                            $sql = "SELECT bfbid, sum(finGenAdmExpAmtApthy*{$fxrate_query2}) AS finGenAdmExpAmtApthy ,sum(finGenAdmExpAmtApty*{$fxrate_query2}) AS finGenAdmExpAmtApty, sum(finGenAdmExpAmtYpy*{$fxrate_query2}) AS finGenAdmExpAmtYpy, sum(finGenAdmExpAmtCurrent*{$fxrate_query2}) AS finGenAdmExpAmtCurrent FROM ".Tprefix."budgeting_financialbudget WHERE bfbid IN (".implode(', ', $options['filter']).")";
                            $query = $db->query($sql);
                            if($db->num_rows($query) > 0) {
                                while($budget = $db->fetch_assoc($query)) {
                                    $financialbudget['finGenAdmExpAmtApthy'] += $budget['finGenAdmExpAmtApthy'];
                                    $financialbudget['finGenAdmExpAmtApty'] += $budget['finGenAdmExpAmtApty'];
                                    $financialbudget['finGenAdmExpAmtYpy'] += $budget['finGenAdmExpAmtYpy'];
                                    $financialbudget['finGenAdmExpAmtCurrent'] += $budget['finGenAdmExpAmtCurrent'];
                                }
                            }
                            //   $finbudgetitems = BudgetComAdminExpenses::get_data(array('bfbid' => $options['filter']), array('returnarray' => true, 'operators' => array('bfbid' => IN)));
                            // $fxrate_query = '(CASE WHEN bfb(SELECT rate from budgeting_fxrates bfr JOIN budgeting_financialbudget bfb ON(bfb.affid = bfr.affid AND bfb.year = bfr.year) WHERE bfr.fromCurrency = bfb.currency AND bfr.toCurrency = '.intval($options['tocurrency']).' AND bfb.bfbid = budgeting_commadminexps.bfbid) END)';
                            $fxrate_query = '(CASE WHEN bfb.currency = '.intval($options['tocurrency']).' THEN 1
                                    ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = bfb.affid AND bfr.year = bfb.year AND bfr.fromCurrency = bfb.currency AND bfr.toCurrency = '.intval($options['tocurrency']).') END)';
                            $sql = "SELECT beciid, SUM(actualPrevThreeYears*{$fxrate_query}) AS actualPrevThreeYears, SUM(actualPrevTwoYears*{$fxrate_query}) AS actualPrevTwoYears, sum(yefPrevYear*{$fxrate_query}) AS yefPrevYear, sum(budgetCurrent*{$fxrate_query}) AS budgetCurrent FROM ".Tprefix."budgeting_commadminexps bcade JOIN ".Tprefix." budgeting_financialbudget bfb ON (bcade.bfbid=bfb.bfbid) WHERE bcade.bfbid IN (".implode(', ', $options['filter']).")";
                            $query = $db->query($sql);
                            if($db->num_rows($query) > 0) {
                                while($item = $db->fetch_assoc($query)) {
                                    $comercialbudget['actualPrevThreeYears'] = sprintf("%.2f", $item['actualPrevThreeYears']);
                                    $comercialbudget['actualPrevTwoYears'] = sprintf("%.2f", $item['actualPrevTwoYears']);
                                    $comercialbudget['yefPrevYear'] = sprintf("%.2f", $item['yefPrevYear']);
                                    $comercialbudget['budgetCurrent'] = sprintf("%.2f", $item['budgetCurrent']);
                                }
                            }
                            //  $finbudgetitems = BudgetComAdminExpenses::get_data(array('bfbid' => $options['filter']), array('returnarray' => true, 'operators' => array('bfbid' => IN)));
                        }

                        foreach($rows as $row) {
                            $style = 'style = "width:25%"';
                            if($row === 'totaladmcom') {
                                $style = 'style = "width:25%;font-weight:bold;"';
                            }
                            ${"output_".$row} .='<td '.$style.'>'.$lang->$row.'</td>';
                        }
                        foreach($budgets as $key => $value) {
                            $width = '12.5%;
                                    ';
                            if(empty($financialbudget[$value])) {
                                $financialbudget[$value] = $options['financialbudget']->$value;
                            }
                            $financialbudget[$key] = sprintf("%.2f", $financialbudget[$value]);
                            $commercialexpenses[$key] = sprintf("%.2f", $comercialbudget[$key] - $financialbudget[$value]);
                            if($key === 'yefactual' || $key === 'yefbud' || $key === 'budyef') {
                                $width = '8.3%';
                                $comercialbudget['yefactual'] = $comercialbudget['yefbud'] = $comercialbudget['budyef'] = '0.00%';
                                $commercialexpenses['yefactual'] = $commercialexpenses['yefbud'] = $commercialexpenses['budyef'] = '0.00%';
                                $financialbudget['yefactual'] = $financialbudget['yefbud'] = $financialbudget['budyef'] = '0.00%';
                                // !!! code need to be optimized
                                //calculatio of yef/actual  yef/bud and bud/yef fields
                                if($comercialbudget['actualPrevTwoYears'] != 0) {
                                    $comercialbudget['yefactual'] = sprintf("%.2f", (($comercialbudget['yefPrevYear'] - $comercialbudget['actualPrevTwoYears']) / $comercialbudget['actualPrevTwoYears']) * 100).' %';
                                }
                                if($comercialbudget['budgetPrevYear'] != 0) {
                                    $comercialbudget['yefbud'] = sprintf("%.2f", (($comercialbudget['yefPrevYear'] - $comercialbudget['budgetPrevYear']) / $comercialbudget['budgetPrevYear']) * 100).' %';
                                }
                                if($comercialbudget['yefPrevYear'] != 0) {
                                    $comercialbudget['budyef'] = sprintf("%.2f", (($comercialbudget['budgetCurrent'] - $comercialbudget['yefPrevYear']) / $comercialbudget['yefPrevYear']) * 100).' %';
                                }
                                if($commercialexpenses['actualPrevTwoYears'] != 0) {
                                    $commercialexpenses['yefactual'] = sprintf("%.2f", (($commercialexpenses['yefPrevYear'] - $commercialexpenses['actualPrevTwoYears']) / $commercialexpenses['actualPrevTwoYears']) * 100).' %';
                                }
                                if($commercialexpenses['budgetPrevYear'] != 0) {
                                    $commercialexpenses['yefbud'] = sprintf("%.2f", (($commercialexpenses['yefPrevYear'] - $commercialexpenses['budgetPrevYear']) / $commercialexpenses['budgetPrevYear']) * 100).' %';
                                }
                                if($commercialexpenses['yefPrevYear'] != 0) {
                                    $commercialexpenses['budyef'] = sprintf("%.2f", (($commercialexpenses['budgetCurrent'] - $commercialexpenses['yefPrevYear']) / $commercialexpenses['yefPrevYear']) * 100).' %';
                                }
                                if($financialbudget['actualPrevTwoYears'] != 0) {
                                    $financialbudget['yefactual'] = sprintf("%.2f", (($financialbudget['yefPrevYear'] - $financialbudget['actualPrevTwoYears']) / $financialbudget['actualPrevTwoYears']) * 100).' %';
                                }
                                if($financialbudget['budgetPrevYear'] != 0) {
                                    $financialbudget['yefbud'] = sprintf("%.2f", (($financialbudget['yefPrevYear'] - $financialbudget['budgetPrevYear']) / $financialbudget['budgetPrevYear']) * 100).' %';
                                }
                                if($financialbudget['yefPrevYear'] != 0) {
                                    $financialbudget['budyef'] = sprintf("%.2f", (($financialbudget['budgetCurrent'] - $financialbudget['yefPrevYear']) / $financialbudget['yefPrevYear']) * 100).' %';
                                }
                            }

                            $output_adminexpenses .='<td style = "width:'.$width.'" class = "border_left"><div id = "adminexpenses_'.$key.'">'.$financialbudget[$key].'</div></td>';
                            $output_commercialexpenses .='<td style = "width:'.$width.'" class = "border_left"><div id = "total_'.$category->name.'_'.$key.'">'.$commercialexpenses[$key].'</div></td>';
                            $output_totaladmcom .='<td style = "width:'.$width.'font-weight:bold;" class = "border_left"><div id = "commercialexpenses_'.$key.'">'.$comercialbudget[$key].'</div></td>';
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