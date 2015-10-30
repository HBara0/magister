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
                        $fields = array('actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'yefactual', 'budgetCurrent', 'budyef');
                        foreach($fields as $input) {
                            $placcount[$input] = $plexpenses_current->$input;
                        }
                        $column_output .= '<td style="width:28%">'.$item->title.'<input type="hidden" name="placcount['.$item->bpliid.'][bpliid]" value='.$item->bpliid.'></td>';
                        foreach($fields as $input) {
                            if($input === 'yefactual' || $input === 'budyef') {
                                $plexpenses[$input] = '0.00 %';
                                if($placcount['actualPrevTwoYears'] != 0) {
                                    $plexpenses['yefactual'] = sprintf("%.2f", (($placcount['yefPrevYear'] - $placcount['actualPrevTwoYears']) / $placcount['actualPrevTwoYears']) * 100).' %';
                                }
                                if($placcount['yefPrevYear'] != 0) {
                                    $plexpenses['budyef'] = sprintf("%.2f", (($placcount['budgetCurrent'] - $placcount['yefPrevYear']) / $placcount['yefPrevYear'] ) * 100).' %';
                                }
                                $column_output .='<td style="width:9%" class="border_left"><div id="placcount_'.$category->name.'_'.$input.'_'.$item->bpliid.'" >'.$plexpenses[$input].'</div></td>';
                                $total['plexpenses'][$input] += $plexpenses[$input];
                            }
                            else {
                                if(isset($options['mode']) && $options['mode'] === 'fill') {
                                    $column_output .='<td style="width:9%" class="border_left">'.parse_textfield('placcount['.$item->bpliid.']['.$input.']', 'placcount_'.$category->name.'_'.$input.'_'.$item->bpliid, 'number', sprintf("%.2f", $plexpenses_current->$input), array('step' => 'any', $readonly => $readonly, 'style' => 'width:100%;')).'</td>';
                                }
                                else {
                                    if(isset($options['placcount']) && !empty($options['placcount'])) {
                                        $placcounts = $options['placcount'];
                                        $placcount = $placcounts[$item->bpliid];
                                        $placcount[$input] = sprintf("%.2f", $placcount[$input]);
                                    }
                                    $column_output .=' <td style="width:9%">'.$placcount[$input].'</td>';
                                }
                                $total['plexpenses'][$input] +=$placcount[$input];
                            }
                        }
                        eval("\$category_item .= \"".$template->get('budgeting_plcategory_item')."\";");
                        $output .= $category_item;
                        unset($plexpenses);
                        $category_item = $column_output = '';
                    }
                    $title = $category->title;
                    $column_output .= '<td style = "width:28%;font-weight:bold;">'.$title.'</td>';
                    foreach($fields as $input) {
                        switch($category->name) {
                            case'income':
                                $total['plexpenses'][$input] = sprintf("%.2f", $total['plexpenses'][$input] + $totalincome[$input]);
                                break;
                            case'operatingprofit':
                                $total['plexpenses'][$input] = sprintf("%.2f", $total['plexpenses'][$input] + $comercialbudget[$input]);
                                break;
                        }
                        if($input === 'yefactual' || $input === 'budyef') {
                            if($total['plexpenses']['actualPrevTwoYears'] != 0) {
                                $total['plexpenses']['yefactual'] = sprintf("%.2f", (( $total['plexpenses']['yefPrevYear'] - $total['plexpenses']['actualPrevTwoYears']) / $total['plexpenses']['actualPrevTwoYears']) * 100).' %';
                            }
                            if($total['plexpenses']['yefPrevYear'] != 0) {
                                $total['plexpenses']['budyef'] = sprintf("%.2f", (( $total['plexpenses']['budgetCurrent'] - $total['plexpenses']['yefPrevYear']) / $total['plexpenses']['yefPrevYear']) * 100).' %';
                            }
                        }
                        $column_output .= '<td style = "width:9%;font-weight:bold;"><div id = "total_'.$category->name.'_'.$input.'">'.$total['plexpenses'][$input].'</div>';
                        $column_output .= parse_textfield('', 'total_'.$category->name.'_'.$input, 'hidden', $total['plexpenses'][$input]).'</td>';
                        if($category->name === 'netincome' && $input === 'budgetCurrent') {
                            $column_output .= parse_textfield('financialbudget[income]', 'total_'.$category->name.'_'.$input, 'hidden', $total['plexpenses'][$input]).'</td>';
                        }
                        $column_output .= '</td>';
                    }
                    eval("\$category_total .= \"".$template->get('budgeting_plcategory_item')."\";");
                    $output .= $category_total;
                    $category_total = $column_output = '';
                }
                else {
                    if($category->name == 'sales') {
                        if(is_array($options['bid'])) {
                            /* Loop over commercial budgets and get data if it exits */
                            foreach($options['bid'] as $key => $budgetsids) {   // $key values(current, prevyear, prevtwoyears)
                                switch($key) {
                                    case'current':
                                        $ratecategory = 'isBudget';
                                        break;
                                    case'prevyear':
                                        $ratecategory = 'isYef';
                                        break;
                                    case'prevtwoyears':
                                        $ratecategory = 'isActual';
                                        break;
                                }
                                if(isset($budgetsids) && !empty($budgetsids)) {
                                    if(is_array($budgetsids)) {
                                        foreach($budgetsids as $budgetid) {
                                            $budgetobject = Budgets::get_data(array('bid' => $budgetid), array('simple' => false));
                                            if(!is_object($budgetobject)) {
                                                continue;
                                            }
                                            /* get budget line currencies */
                                            $budget_currencies = array();
                                            $budgetlines = BudgetLines::get_data(array('bid' => $budgetobject->bid), array('returnarray' => true));
                                            if(is_array($budgetlines)) {
                                                foreach($budgetlines as $budgetline) {
                                                    $budget_currencies[$budgetline->blid] = $budgetline->originalCurrency;
                                                }
                                                $budget_currencies = array_unique($budget_currencies);
                                            }

                                            /* Currency Check */
                                            $dal_config = array(
                                                    'operators' => array('fromCurrency' => 'in', 'affid' => ' = ', 'year' => ' = '),
                                                    'simple' => false,
                                                    'returnarray' => true
                                            );
                                            if(!empty($budget_currencies)) {
                                                if(!in_array($options['tocurrency'], $budget_currencies)) {
                                                    $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budget_currencies, 'toCurrency' => $options['tocurrency'], 'affid' => $budgetobject->affid, 'year' => $budgetobject->year, $ratecategory => 1), $dal_config);
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
                                                                $tocurrency = new Currencies($options['tocurrency']);
                                                                error($lang->sprint($lang->noexchangerate, $output_currname, $tocurrency->get_displayname(), $budgetobject->year), $_SERVER['HTTP_REFERER']);
                                                            }
                                                        }
                                                    }
                                                    else {
                                                        error($lang->sprint($lang->noexchangerate, implode(', ', $budget_currencies), $options['tocurrency'], $budgetobject->year), $_SERVER['HTTP_REFERER']);
                                                    }
                                                }
                                            }
                                            $fxrate_query = "(CASE WHEN budgeting_budgets_lines.originalCurrency=".intval($options['tocurrency'])." THEN 1 ELSE (SELECT rate FROM budgeting_fxrates WHERE affid=".$budgetobject->affid." AND year=".$budgetobject->year." AND fromCurrency=budgeting_budgets_lines.originalCurrency AND toCurrency=".intval($options['tocurrency'])." AND ".$ratecategory."=1) END)";
                                            $sql = "SELECT saleType, SUM((CASE WHEN localIncomeAmount=0 THEN 0 ELSE amount END)*{$fxrate_query}) AS amount, SUM(localIncomeAmount*{$fxrate_query}) AS localIncomeAmount, SUM(actualAmount*{$fxrate_query}) AS actualAmount, SUM(actualIncome*{$fxrate_query}) AS actualIncome FROM ".Tprefix."budgeting_budgets_lines WHERE bid=".$budgetid." GROUP BY saleType";
                                            $query = $db->query($sql);
                                            if($db->num_rows($query) > 0) {
                                                $amount = 'amount';
                                                $income = 'localIncomeAmount';
                                                while($budget = $db->fetch_assoc($query)) {
                                                    if($key != 'current') {
                                                        $amount = 'actualAmount';
                                                        $income = 'actualIncome';
                                                    };

                                                    $saletype = SaleTypes::get_data(array('stid' => $budget['saleType']));

                                                    if($saletype->isIntercompanyTrx == 1 && count($options['affid']) == 1) {
                                                        $saletype->isIntercompanyTrx = 0;
                                                    }
                                                    if($saletype->isIntercompanyTrx != 1) {
                                                        $combudget[$key][$budget['saleType']]['amount'] += $budget[$amount] / 1000;
                                                    }

                                                    if(($saletype->countLocally == 0 && count($options['affid']) > 1)) {
                                                        $combudget[$key][$budget['saleType']]['amount'] -= $budget[$amount] / 1000;
                                                    }
                                                    else {
                                                        $combudget[$key][$budget['saleType']]['income'] += $budget[$income] / 1000;
                                                    }
                                                    if($key == 'current') {
                                                        if($saletype->countLocally == 1 && $saletype->isIntercompanyTrx != 1) {
                                                            $totalamount[$key] += $budget[$amount] / 1000;
                                                        }
                                                    }
                                                }
                                            }
                                            unset($budget);
                                        }
                                    }
                                }

                                if($key == 'current') {
                                    if(is_array($options['affid'])) {
                                        foreach($options['affid'] as $affid) {
                                            $budgetline = new BudgetLines();
                                            $allocatedamount = $budgetline->get_invoicingentity_income($options['tocurrency'], $options['year'], $affid);
                                            if(is_array($allocatedamount[$key])) {
                                                foreach($allocatedamount[$key] as $saletype => $data) {
                                                    if(empty($data['invoicingentityincome'])) {
                                                        continue;
                                                    }
                                                    $effective_stid = $saletype;
                                                    if(isset($data['oldSaleType'])) {
                                                        $effective_stid = $data['oldSaleType'];
                                                    }
                                                    $saletype_obj = SaleTypes::get_data(array('stid' => $effective_stid));
                                                    //$allocatedamount = number_format($data['amount'] / 1000, 2);

                                                    if($saletype_obj->countLocally == 0) {
                                                        $combudget[$key][$saletype]['amount'] += $data['amount'] / 1000;
                                                        $totalamount[$key] += $data['amount'] / 1000;
                                                        if(count($options['affid']) > 1) {
                                                            $combudget[$key][$saletype]['income'] += round($data['localIncomeAmount'] / 1000, 2);
                                                        }
                                                    }

                                                    $combudget[$key][$saletype]['income'] += round($data['invoicingentityincome'] / 1000, 2);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $saletypes = SaleTypes::get_data('', array('order' => array('by' => 'sequence')));
                            foreach($saletypes as $type) {
                                /* Set yef default value for testing */
                                $combudget['prevyear'][$type->stid]['amount'] = $combudget['prevyear'][$type->stid]['income'] = 0;

                                /* calculate field values of Accounted commissions/sales category Row */
                                $commercialbudgetfields = array('prevthreeyears', 'prevtwoyears', 'prevyear', 'current');
                                foreach($commercialbudgetfields as $field) {
                                    $combudget[$field][$type->stid]['perc'] = sprintf("%.2f", 0);
                                    if($combudget[$field][$type->stid]['amount'] != 0) {
                                        $combudget[$field][$type->stid]['perc'] = sprintf("%.2f", ($combudget[$field][$type->stid]['income'] / $combudget[$field][$type->stid]['amount'] ) * 100);
                                    }
                                }
                                /* Calculate yef/ActualPrev2years and budgetCurrent/yef percentages  */
                                $commercialbudget_rows = array('amount', 'income', 'perc');
                                foreach($commercialbudget_rows as $row) {
                                    $combudget[yefactual][$type->stid][$row] = $combudget[budyef][$type->stid][$row] = '0.00 %';
                                    if($combudget[prevtwoyears][$type->stid][$row] != 0) {
                                        $combudget[yefactual][$type->stid][$row] = sprintf("%.2f", (($combudget[prevyear][$type->stid][$row] - $combudget[prevtwoyears][$type->stid][$row] ) / $combudget[prevtwoyears][$type->stid][$row] ) * 100).' %';
                                    }
                                    if($combudget[yef][$type->stid][$row] != 0) {
                                        $combudget[budyef][$type->stid][$row] = sprintf("%.2f", (($combudget[current][$type->stid][$row] - $combudget[prevyear][$type->stid][$row] ) / $combudget[prevyear][$type->stid][$row] ) * 100).' %';
                                    }
                                }

                                /* parse fields */
                                $fields = array('prevthreeyears', 'prevtwoyears', 'prevyear', 'yefactual', 'current', 'budyef');
                                $amount_output .= '<td style = "width:28%; font-weight:bold;">'.$type->title.'</td>';
                                $grossmargin_commissions = $lang->grossmargin;
                                if($type->stid == 1 || $type->stid == 2) {
                                    $grossmargin_commissions = $lang->accountedcommissions;
                                }
                                $income_output .= '<td style="width:28%">'.$grossmargin_commissions.'</td>';
                                foreach($fields as $field) {
                                    if(empty($combudget[$field][$type->stid]['amount'])) {
                                        $combudget[$field][$type->stid]['amount'] = 0;
                                    }
                                    if(empty($combudget[$field][$type->stid]['income'])) {
                                        $combudget[$field][$type->stid]['income'] = 0;
                                    }
                                    if($field == 'yefactual' || $field == 'budyef') {
                                        $amount_output .= '<td style = "width:9%" class = "border_left"><div id = "placcount_'.$category->name.'_'.$field.'_'.$type->stid.'">'.$combudget[$field][$type->stid]['amount'].'</div></td>';
                                        $income_output .= '<td style = "width:9%" class = "border_left"><div id = "placcount_'.$category->name.'_'.$field.'_'.$type->stid.'">'.$combudget[$field][$type->stid]['income'].'</div></td>';
                                    }
                                    else {
                                        $amount_output .= '<td style = "width:9%" class = "border_left">'.number_format($combudget[$field][$type->stid]['amount'], 2).'</td>';
                                        $income_output .= '<td style = "width:9%" class = "border_left">'.number_format($combudget[$field][$type->stid]['income'], 2).'</td>';
                                        $totalincome[$field] += $combudget[$field][$type->stid]['income'];
                                        if($field != 'current') {
                                            $totalamount[$field] += $combudget[$field][$type->stid]['amount'];
                                        }
                                    }
                                }
                                $rowclass = alt_row($rowclass);

                                eval("\$saletypeoutput .= \"".$template->get('budgeting_plitem')."\";");
                                $income_output = $amount_output = '';
                            }
                            /* Sales Category total amount */
                            $fields = array('actualPrevThreeYears' => 'prevthreeyears', 'actualPrevTwoYears' => 'prevtwoyears', 'yefPrevYear' => 'prevyear', 'yefactual' => 'yefactual', 'budgetCurrent' => 'current', 'budyef' => 'budyef');
                            $output .= '<tr><td style="width:28%;font-weight:bold;">'.$lang->totalsales.'</td>';
                            foreach($fields as $key => $value) {
                                switch($value) {
                                    case yefactual:
                                        $totalamount[$value] = '0.00 %';
                                        if($totalamount['prevtwoyears'] != 0) {
                                            $totalamount[$value] = sprintf("%.2f", (($totalamount['prevyear'] - $totalamount['prevtwoyears'] ) / $totalamount['prevtwoyears'] ) * 100).' %';
                                        }
                                        break;
                                    case budyef:
                                        $totalamount[$value] = '0.00 %';
                                        if($totalamount['prevyear'] != 0) {
                                            $totalamount[$value] = sprintf("%.2f", (($totalamount['current'] - $totalamount['prevyear'] ) / $totalamount['prevyear'] ) * 100).' %';
                                        }
                                        break;
                                    default:
                                        $totalamount[$value] = round($totalamount[$value], 2);
                                        break;
                                }
//                                if(empty($totalamount[$value])) {
//                                    $totalamount[$value] = 0;
//                                }
                                $output .='<td style = "width:9%;font-weight:bold;" class = "border_left">'.$totalamount[$value].'</td>';
                            }
                            $output .='</tr>';
                            $output .= $saletypeoutput;
                            /* Sales category total income */
                            $output .= '<tr><td style="width:28%"></td>';
                            foreach($fields as $key => $value) {
                                $totalincome[$key] = $totalincome[$value];  /* total of sale types category */
                                $output .= '<td style="width:9%;"><input type="hidden" id="total_'.$category->name.'_'.$key.'" value="'.$totalincome[$key].'"></td>';
                            }
                            $output .= '</tr>';
                        }
                    }

//parse Adm.Com. Expenses section
                    if($category->name == 'admcomexpenses') {
                        $rows = array('adminexpenses', 'commercialexpenses', 'totaladmcom');
                        $admcomexpenses_fields = array('actualPrevThreeYears' => 'finGenAdmExpAmtApthy', 'actualPrevTwoYears' => 'finGenAdmExpAmtApty', 'yefPrevYear' => 'finGenAdmExpAmtYpy', 'yefactual' => 'yefactual', 'budgetCurrent' => 'finGenAdmExpAmtCurrent', 'budyef' => 'budyef');
                        if(is_object($options['financialbudget'])) {
                            $options['filter'][] = $options['financialbudget']->bfbid;
                        }
                        unset($fxrate_query2);
                        if(is_array($options['filter'])) {
                            $prevyearsexp_fxrates = array('finGenAdmExpAmtApthy' => array('year' => ($options['year'] - 3), 'ratecategory' => 'isActual'),
                                    'finGenAdmExpAmtApty' => array('year' => ($options['year'] - 2), 'ratecategory' => 'isActual'),
                                    'finGenAdmExpAmtYpy' => array('year' => ($options['year'] - 1), 'ratecategory' => 'isYef'),
                                    'finGenAdmExpAmtCurrent' => array('year' => $options['year'], 'ratecategory' => 'isBudget')
                            );
                            foreach($prevyearsexp_fxrates as $attr => $fxconfig) {

                                $fxrate_query2[$attr] = '(CASE WHEN budgeting_financialbudget.currency = '.intval($options['tocurrency']).' THEN 1
                                    ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = budgeting_financialbudget.affid AND bfr.year = '.$fxconfig['year'].' AND bfr.fromCurrency = budgeting_financialbudget.currency AND bfr.toCurrency = '.intval($options['tocurrency']).'  AND bfr.'.$fxconfig['ratecategory'].' =1) END)';
                            }

                            $sql = "SELECT bfbid, sum(finGenAdmExpAmtApthy*{$fxrate_query2['finGenAdmExpAmtApthy']}) AS finGenAdmExpAmtApthy ,sum(finGenAdmExpAmtApty*{$fxrate_query2['finGenAdmExpAmtApty']}) AS finGenAdmExpAmtApty, sum(finGenAdmExpAmtYpy*{$fxrate_query2['finGenAdmExpAmtYpy']}) AS finGenAdmExpAmtYpy, sum(finGenAdmExpAmtCurrent*{$fxrate_query2['finGenAdmExpAmtCurrent']}) AS finGenAdmExpAmtCurrent
                                    FROM ".Tprefix."budgeting_financialbudget WHERE bfbid IN (".implode(', ', $options['filter']).")";

                            $query = $db->query($sql);
                            if($db->num_rows($query) > 0) {
                                while($budget = $db->fetch_assoc($query)) {
                                    $financialbudget['finGenAdmExpAmtApthy'] -= $budget['finGenAdmExpAmtApthy'];
                                    $financialbudget['finGenAdmExpAmtApty'] -= $budget['finGenAdmExpAmtApty'];
                                    $financialbudget['finGenAdmExpAmtYpy'] -= $budget['finGenAdmExpAmtYpy'];
                                    $financialbudget['finGenAdmExpAmtCurrent'] -= $budget['finGenAdmExpAmtCurrent'];
                                }
                            }
                            $prevyears_fxrates = array('actualPrevThreeYears' => array('year' => ($options['year'] - 3), 'ratecategory' => 'isActual'),
                                    'actualPrevTwoYears' => array('year' => ($options['year'] - 2), 'ratecategory' => 'isActual'),
                                    'yefPrevYear' => array('year' => ($options['year'] - 1), 'ratecategory' => 'isYef'),
                                    'budgetCurrent' => array('year' => $options['year'], 'ratecategory' => 'isBudget')
                            );
                            foreach($prevyears_fxrates as $attr => $fxconfig) {
                                $fxrate_commadminexpsquery[$attr] = '(CASE WHEN bfb.currency = '.intval($options['tocurrency']).' THEN 1
                                    ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = bfb.affid AND bfr.year =  '.$fxconfig['year'].' AND bfr.fromCurrency = bfb.currency AND bfr.toCurrency = '.intval($options['tocurrency']).' AND bfr.'.$fxconfig['ratecategory'].' =1) END)';
                            }
                            $sql = "SELECT beciid, SUM(actualPrevThreeYears*{$fxrate_commadminexpsquery['actualPrevThreeYears']}) AS actualPrevThreeYears, SUM(actualPrevTwoYears*{$fxrate_commadminexpsquery['actualPrevTwoYears']}) AS actualPrevTwoYears, sum(yefPrevYear*{$fxrate_commadminexpsquery['yefPrevYear']}) AS yefPrevYear, sum(budgetCurrent*{$fxrate_commadminexpsquery['budgetCurrent']}) AS budgetCurrent FROM ".Tprefix."budgeting_commadminexps bcade JOIN ".Tprefix." budgeting_financialbudget bfb ON (bcade.bfbid=bfb.bfbid) WHERE bcade.bfbid IN (".implode(', ', $options['filter']).")";
                            $query = $db->query($sql);
                            if($db->num_rows($query) > 0) {
                                while($item = $db->fetch_assoc($query)) {
                                    $comercialbudget['actualPrevThreeYears'] = sprintf("%.2f", 0 - $item['actualPrevThreeYears']);
                                    $comercialbudget['actualPrevTwoYears'] = sprintf("%.2f", 0 - $item['actualPrevTwoYears']);
                                    $comercialbudget['yefPrevYear'] = sprintf("%.2f", 0 - $item['yefPrevYear']);
                                    $comercialbudget['budgetCurrent'] = sprintf("%.2f", 0 - $item['budgetCurrent']);
                                }
                            }
                        }

                        foreach($rows as $row) {
                            $style = 'style = "width:28%"';
                            if($row === 'totaladmcom') {
                                $style = 'style = "width:28%;font-weight:bold;"';
                            }
                            ${"output_".$row} .= '<td '.$style.'>'.$lang->$row.'</td>';
                        }
                        foreach($admcomexpenses_fields as $key => $value) {
                            if(empty($financialbudget[$value])) {
                                $financialbudget[$value] = $options['financialbudget']->$value;
                            }
                            if(empty($comercialbudget[$key])) {
                                $comercialbudget[$key] = number_format(0, 2);
                            }

                            $financialbudget[$key] = sprintf("%.2f", $financialbudget[$value]);
                            $commercialexpenses[$key] = sprintf("%.2f", $comercialbudget[$key] - $financialbudget[$value]);
                            if($key === 'yefactual' || $key === 'budyef') {
                                $comercialbudget['yefactual'] = $comercialbudget['budyef'] = '0.00 %';
                                $commercialexpenses['yefactual'] = $commercialexpenses['budyef'] = '0.00 %';
                                $financialbudget['yefactual'] = $financialbudget['budyef'] = '0.00 %';
// code need to be optimized
                                /* Calculation of yef/actual  yef/bud and bud/yef fields */
                                if($comercialbudget['actualPrevTwoYears'] != 0) {
                                    $comercialbudget['yefactual'] = sprintf("%.2f", (($comercialbudget['yefPrevYear'] - $comercialbudget['actualPrevTwoYears'] ) / $comercialbudget['actualPrevTwoYears'] ) * 100).' %';
                                }
                                if($comercialbudget['yefPrevYear'] != 0) {
                                    $comercialbudget['budyef'] = sprintf("%.2f", (($comercialbudget['budgetCurrent'] - $comercialbudget['yefPrevYear'] ) / $comercialbudget['yefPrevYear'] ) * 100).' %';
                                }
                                if($commercialexpenses['actualPrevTwoYears'] != 0) {
                                    $commercialexpenses['yefactual'] = sprintf("%.2f", (($commercialexpenses['yefPrevYear'] - $commercialexpenses['actualPrevTwoYears'] ) / $commercialexpenses['actualPrevTwoYears'] ) * 100).' %';
                                }
                                if($commercialexpenses['yefPrevYear'] != 0) {
                                    $commercialexpenses['budyef'] = sprintf("%.2f", (($commercialexpenses['budgetCurrent'] - $commercialexpenses['yefPrevYear'] ) / $commercialexpenses['yefPrevYear'] ) * 100).' %';
                                }
                                if($financialbudget['actualPrevTwoYears'] != 0) {
                                    $financialbudget['yefactual'] = sprintf("%.2f", (($financialbudget['yefPrevYear'] - $financialbudget['actualPrevTwoYears'] ) / $financialbudget['actualPrevTwoYears'] ) * 100).' %';
                                }
                                if($financialbudget['yefPrevYear'] != 0) {
                                    $financialbudget['budyef'] = sprintf("%.2f", (($financialbudget['budgetCurrent'] - $financialbudget['yefPrevYear'] ) / $financialbudget['yefPrevYear'] ) * 100).' %';
                                }
                            }

                            $output_adminexpenses .= '<td style="width:9%" class = "border_left"><div id = "adminexpenses_'.$key.'">'.$financialbudget[$key].'</div></td>';
                            $output_commercialexpenses .= '<td style="width:9%" class = "border_left"><div  id = "commercialexpenses_'.$key.'">'.$commercialexpenses[$key].'</div></td>';
                            $output_totaladmcom .= '<td style="width:9%;font-weight:bold;" class = "border_left"><div id = "total_'.$category->name.'_'.$key.'">'.$comercialbudget[$key].'</div></td>';
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