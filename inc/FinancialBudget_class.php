<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: FinancialBudget_Class.php
 * Created:        @rasha.aboushakra    Sep 25, 2014 | 9:48:20 AM
 * Last Update:    @rasha.aboushakra    Sep 25, 2014 | 9:48:20 AM
 */

Class FinancialBudget extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'bfbid';
    const TABLE_NAME = 'budgeting_financialbudget';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'bfbid, affid, year';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $financialdata['bfbid'] = self::PRIMARY_KEY;
            $fields = array('finGenAdmExpAmtApthy', 'finGenAdmExpAmtApty', 'finGenAdmExpAmtYpy', 'finGenAdmExpAmtCurrent'); //'finGenAdmExpAmtApy', 'finGenAdmExpAmtBpy'
            $financialdata['affid'] = $data['financialbudget']['affid'];
            $financialdata['year'] = $data['financialbudget']['year'];
            $financialdata['netIncome'] = $data['financialbudget']['income'];
            $affiliate = new Affiliates($financialdata['affid']);
            $financialdata['currency'] = $affiliate->get_country()->get_maincurrency()->get()[numCode];
            foreach($fields as $field) {
                $max = 'max'.$field;
                if($data['financialbudget'][$field] > $data['financialbudget'][$max]) {
                    $this->errorcode = 3;
                    return;
                }
                $data['financialbudget'][$field] = $core->sanitize_inputs($data['financialbudget'][$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data['financialbudget'][$field] = $db->escape_string($data['financialbudget'][$field]);
                $financialdata[$field] = $data['financialbudget'][$field];
            }
            $affiliate = new Affiliates($financialdata['affid']);
            $financialdata['currency'] = $affiliate->get_country()->get_maincurrency()->get()[numCode];
            $financialdata['createdOn'] = TIME_NOW;
            $financialdata['createdBy'] = $core->user['uid'];
            $query = $db->insert_query(self::TABLE_NAME, $financialdata);
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        if(!query) {
            return;
        }
        $financialexpenses = $data['budgetexps'];
        if(is_array($financialexpenses)) {
            foreach($financialexpenses as $expense) {
                $expense['bfbid'] = $this->data[self::PRIMARY_KEY];
                $comadminexpense = new BudgetComAdminExpenses();
                $comadminexpense->set($expense);
                $comadminexpense->save();
                $this->errorcode = $comadminexpense->errorcode;
                switch($this->get_errorcode()) {
                    case 0:
                        continue;
                    case 2:
                        return;
                }
            }
        }

        $financialinvest = $data['budgetinvst'];
        if(is_array($financialinvest)) {
            foreach($financialinvest as $invest) {
                $invest['bfbid'] = $this->data[self::PRIMARY_KEY];
                $investfollowup = new BudgetInvestExpenses();
                $investfollowup->set($invest);
                $investfollowup->save();
                $this->errorcode = $investfollowup->errorcode;
                switch($this->get_errorcode()) {
                    case 0:
                        continue;
                    case 2:
                        return;
                }
            }
        }

        $headcount = $data['headcount'];
        if(is_array($headcount)) {
            foreach($headcount as $count) {
                $count[bfbid] = $this->data[self::PRIMARY_KEY];
                $budgetheadcount = new BudgetHeadCount();
                $budgetheadcount->set($count);
                $budgetheadcount->save();
                $this->errorcode = $budgetheadcount->errorcode;
                switch($this->get_errorcode()) {
                    case 0:
                        continue;
                    case 2:
                        return;
                }
            }
        }
        $placcounts = $data['placcount'];
        if(is_array($placcounts)) {
            foreach($placcounts as $account) {
                $account['bfbid'] = $this->data[self::PRIMARY_KEY];
                $placcount_obj = new BudgetPlExpenses();
                $placcount_obj->set($account);
                $placcount_obj->save();
                $this->errorcode = $placcount_obj->errorcode;
                switch($this->get_errorcode()) {
                    case 0:
                        continue;
                    case 2:
                        return;
                }
            }
        }
        $budgetforecastbs = $data['budgetforecastbs'];
        if(is_array($budgetforecastbs)) {
            unset($budgetforecastbs['liabilities'], $budgetforecastbs['Assets']);
            foreach($budgetforecastbs as $forecast) {
                $forecasts['bfbid'] = $this->data[self::PRIMARY_KEY];
                $budgetforecast_obj = new BudgetForecastBalanceSheet();
                $budgetforecast_obj->set($forecasts);
                $budgetforecast_obj->save();
                $this->errorcode = $budgetforecast_obj->errorcode;
                switch($this->get_errorcode()) {
                    case 0:
                        continue;
                    case 2:
                        return;
                }
            }
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $fields = array('finGenAdmExpAmtApthy', 'finGenAdmExpAmtApty', 'finGenAdmExpAmtYpy', 'finGenAdmExpAmtCurrent'); //'finGenAdmExpAmtApy', 'finGenAdmExpAmtBpy'
            $financialdata['affid'] = $data['financialbudget']['affid'];
            $financialdata['year'] = $data['financialbudget']['year'];
            if(isset($data['financialbudget']['income'])) {
                $financialdata['netIncome'] = $data['financialbudget']['income'];
            }
            foreach($fields as $field) {
                $max = 'max'.$field;
                if($data['financialbudget'][$field] > $data['financialbudget'][$max]) {
                    $this->errorcode = 3;
                    return;
                }
                $data['financialbudget'][$field] = $core->sanitize_inputs($data['financialbudget'][$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data['financialbudget'][$field] = $db->escape_string($data['financialbudget'][$field]);
                $financialdata[$field] = $data['financialbudget'][$field];
            }
            $affiliate = new Affiliates($financialdata['affid']);
            $financialdata['currency'] = $affiliate->get_country()->get_maincurrency()->get()[numCode];
            $financialdata['modifiedOn'] = TIME_NOW;
            $financialdata['modifiedBy'] = $core->user['uid'];
            $query = $db->update_query(self::TABLE_NAME, $financialdata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            if(!query) {
                $this->errorcode = 601;
                return;
            }
            $financialexpenses = $data['budgetexps'];
            if(is_array($financialexpenses)) {
                foreach($financialexpenses as $expense) {
                    $expense['bfbid'] = $this->data[self::PRIMARY_KEY];
                    $comadminexpense = new BudgetComAdminExpenses();
                    $comadminexpense->set($expense);
                    $comadminexpense->save();
                    $this->errorcode = $comadminexpense->errorcode;
                    switch($this->get_errorcode()) {
                        case 0:
                            continue;
                        case 2:
                            return;
                    }
                }
            }
            $financialinvest = $data['budgetinvst'];
            if(is_array($financialinvest)) {
                foreach($financialinvest as $invest) {
                    $invest['bfbid'] = $this->data[self::PRIMARY_KEY];
                    $investfollowup = new BudgetInvestExpenses();
                    $investfollowup->set($invest);
                    $investfollowup->save();
                    $this->errorcode = $investfollowup->errorcode;
                    switch($this->get_errorcode()) {
                        case 0:
                            continue;
                        case 2:
                            return;
                    }
                }
            }

            $headcount = $data['headcount'];
            if(is_array($headcount)) {
                foreach($headcount as $count) {
                    $count['bfbid'] = $this->data[self::PRIMARY_KEY];
                    $budgetheadcount = new BudgetHeadCount();
                    $budgetheadcount->set($count);
                    $budgetheadcount->save();
                    $this->errorcode = $budgetheadcount->errorcode;
                    switch($this->get_errorcode()) {
                        case 0:
                            continue;
                        case 2:
                            return;
                    }
                }
            }
            $budgetforecastbs = $data['budgetforecastbs'];
            if(is_array($budgetforecastbs)) {
                unset($budgetforecastbs[liabilities], $budgetforecastbs[Assets]);

                foreach($budgetforecastbs as $forecast) {
                    $forecast['bfbid'] = $this->data[self::PRIMARY_KEY];
                    $budgetforecast_obj = new BudgetForecastBalanceSheet();
                    $budgetforecast_obj->set($forecast);
                    $budgetforecast_obj->save();
                    $this->errorcode = $budgetforecast_obj->errorcode;
                    switch($this->get_errorcode()) {
                        case 0:
                            continue;
                        case 2:
                            return;
                    }
                }
            }
            $placcounts = $data['placcount'];
            if(is_array($placcounts)) {
                foreach($placcounts as $account) {
                    $account['bfbid'] = $this->data[self::PRIMARY_KEY];
                    $placcount_obj = new BudgetPlExpenses();
                    $placcount_obj->set($account);
                    $placcount_obj->save();
                    $this->errorcode = $placcount_obj->errorcode;
                    switch($this->get_errorcode()) {
                        case 0:
                            continue;
                        case 2:
                            return;
                    }
                }
            }
            $this->errorcode = 1;
        }
    }

    public function save(array $data = array()) {
        if(empty($data)) {
            $data = $this->data;
        }

        if(!$this->validate_requiredfields($data)) {
            $financialbudget = FinancialBudget::get_data(array('bfbid' => $this->data[self::PRIMARY_KEY]));
            if(is_object($financialbudget)) {
                $financialbudget->update($data);
                $this->errorcode = $financialbudget->errorcode;
            }
            else {
                $financialbudget = FinancialBudget::get_data(array('affid' => $data['financialbudget']['affid'], 'year' => $data['financialbudget']['year']));
                if(is_object($financialbudget)) {
                    $financialbudget->update($data);
                    $this->errorcode = $financialbudget->errorcode;
                }
                else {
                    $this->create($data);
                }
            }
        }
    }

    public static function get_availableyears() {
        global $db;
        $query = $db->query('SELECT DISTINCT(year) FROM '.Tprefix.'budgeting_financialbudget ORDER BY year DESC');
        if($db->num_rows($query) > 0) {
            while($year = $db->fetch_assoc($query)) {
                $years[$year['year']] = $year['year'];
            }
            return $years;
        }
        return false;
    }

    public static function parse_financialbudget($options = array()) {
        global $db, $template, $lang;
        if(isset($options['budgettypes']) && !empty($options['budgettypes'])) {

            /* get currenceis by consolidated budgetfinamce id */
            $financial_obj = FinancialBudget::get_data(array('bfbid' => $options['filter']), array('simple' => false, 'returnarray' => true));

            if(is_array($financial_obj)) {
                foreach($financial_obj as $finbudget) {
                    $budget_currencies[$finbudget->bfbid] = $finbudget->currency;
                }
            }
            $dal_config = array(
                    'operators' => array('fromCurrency' => 'in', 'affid' => 'in', 'year' => '='),
                    'simple' => false,
                    'returnarray' => true
            );

            $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budget_currencies, 'toCurrency' => $options['tocurrency'], 'affid' => $options['affid'], 'year' => $options['year'],), $dal_config);
            if(is_array($fxrates_obj)) {

                if(count($budget_currencies) != count($fxrates_obj)) {
                    foreach($fxrates_obj as $budgetrate) {
                        $budget_currency[] = $budgetrate->fromCurrency;
                    }
                    $currencies_diff = array_diff($budget_currencies, $budget_currency);
                    if(is_array($currencies_diff)) {
                        foreach($currencies_diff as $currencyid) {
                            $currency = new Currencies($currencyid);
                            $output_currname.=$comma.$currency->get_displayname();
                            $comma = ', ';
                        }
                    }
                    error($lang->sprint($lang->currencynotexistvar, $output_currname), $_SERVER['HTTP_REFERER']);
                }
            }
            else {
                error($lang->currencynotexist, $_SERVER['HTTP_REFERER']);
            }
            $output['currfxrates'] = '<strong>'.$lang->exchangerates.'</strong><br>';
            foreach($fxrates_obj as $budgetrate) {
                $currency = new Currencies($budgetrate->fromCurrency);
                $currencyto = new Currencies($options['tocurrency']);
                $output['currfxrates'] .= $currency->get()['alphaCode'].' to '.$currencyto->get()['alphaCode'].' > '.$budgetrate->rate.'<br>';
            }

            foreach($options['budgettypes'] as $type) {
                switch($type) {
                    case'headcount':
                        $positiongroups = PositionGroups::get_data('', array('returnarray' => true));
                        $sql = "SELECT posgid, sum(actualPrevThreeYears) AS actualPrevThreeYears,sum(actualPrevTwoYears) AS actualPrevTwoYears, sum(yefPrevYear) AS yefPrevYear, sum(budgetCurrent) AS budgetCurrent FROM ".Tprefix."budgeting_headcount WHERE bfbid IN (".implode(',', $options['filter']).") GROUP By posgid";
                        $query = $db->query($sql);
                        $fields = array('actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent');
                        if($db->num_rows($query) > 0) {
                            while($item = $db->fetch_assoc($query)) {
                                foreach($fields as $field) {
                                    $headcount[$item['posgid']][$field] = $item[$field];
                                }
                            }
                        }
                        if(is_empty($headcount)) {
                            break;
                        }
                        $output['headcount']['data'] = BudgetHeadCount::parse_headcountfields($positiongroups, array('mode' => 'display', 'financialbudget' => $financialbudget, 'prevfinancialbudget' => $prevfinancialbudget, 'headcount' => $headcount));

                        break;

                    case'investmentfollowup':
                        $investcategories = BudgetInvestCategories::get_data('', array('returnarray' => true));
                        /* Converting amount into the affiliates existing currency */
                        $fxrate_query = '(SELECT rate from budgeting_fxrates bfr JOIN  budgeting_financialbudget bfb ON(bfb.affid=bfr.affid AND bfb.year=bfr.year)  WHERE bfr.fromCurrency=bfb.currency AND bfr.toCurrency='.intval($options['tocurrency']).' AND bfb.bfbid= budgeting_investexpenses.bfbid)';
                        $sql = "SELECT biiid, sum(actualPrevThreeYears*{$fxrate_query}) AS actualPrevThreeYears, sum(actualPrevTwoYears*{$fxrate_query}) AS actualPrevTwoYears, sum(yefPrevYear*{$fxrate_query}) AS yefPrevYear, sum(budgetCurrent*{$fxrate_query}) AS budgetCurrent, sum(percVariation) AS percVariation FROM ".Tprefix."budgeting_investexpenses WHERE bfbid IN (".implode(',', $options['filter']).") GROUP By biiid";
                        $query = $db->query($sql);

                        $fields = array('actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent');
                        if($db->num_rows($query) > 0) {
                            while($item = $db->fetch_assoc($query)) {
                                foreach($fields as $field) {
                                    $investmentfollowup[$item['biiid']][$field] = sprintf("%.2f", $item[$field]);
                                }
                                //$investmentfollowup[$item['biiid']]['percVariation'] = sprintf("%.2f", $item['percVariation']);
                            }
                        }
                        if(is_empty($investmentfollowup)) {
                            break;
                        }
                        $output['investmentfollowup']['data'] = BudgetInvestCategories::parse_expensesfields($investcategories, array('mode' => 'display', 'financialbudget' => $financialbudget, 'prevfinancialbudget' => $prevfinancialbudget, 'investmentfollowup' => $investmentfollowup));
                        break;

                    case'financialadminexpenses':
                        $expensescategories = BudgetExpenseCategories::get_data('', array('returnarray' => true));
                        /* Converting amount into the affiliates existing currency */
                        $fxrate_query = '(SELECT rate from budgeting_fxrates bfr JOIN  budgeting_financialbudget bfb ON(bfb.affid=bfr.affid AND bfb.year=bfr.year)  WHERE bfr.fromCurrency=bfb.currency AND bfr.toCurrency='.intval($options['tocurrency']).' AND bfb.bfbid=budgeting_commadminexps.bfbid)';
                        $sql = "SELECT beciid,sum(actualPrevThreeYears*{$fxrate_query}) AS actualPrevThreeYears ,sum(actualPrevTwoYears*{$fxrate_query}) AS actualPrevTwoYears, sum(yefPrevYear*{$fxrate_query}) AS yefPrevYear, sum(budgetCurrent*{$fxrate_query}) AS budgetCurrent,sum(budYefPerc) AS budYefPerc FROM ".Tprefix."budgeting_commadminexps WHERE bfbid IN (".implode(',', $options['filter']).") GROUP By beciid";
                        $query = $db->query($sql);
                        $fields = array('actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent'); //'budgetPrevYear', 'actualPrevYear'
                        if($db->num_rows($query) > 0) {
                            while($item = $db->fetch_assoc($query)) {
                                foreach($fields as $field) {
                                    $financialadminexpenses[$item['beciid']][$field] = sprintf("%.2f", $item[$field]);
                                }
                                if($financialadminexpenses [$item['beciid']]['yefPrevYear'] != 0) {
                                    $financialadminexpenses[$item['beciid']]['budYefPerc'] = sprintf("%.2f", (($financialadminexpenses[$item['beciid']]['budgetCurrent'] - $financialadminexpenses [$item['beciid']]['yefPrevYear']) / $financialadminexpenses [$item['beciid']]['yefPrevYear']) * 100).'%';
                                }
                            }
                        }

                        $finbudgetquery = $db->query("SELECT bfbid,sum(finGenAdmExpAmtApthy) AS finGenAdmExpAmtApthy ,sum(finGenAdmExpAmtApty) AS finGenAdmExpAmtApty, sum(finGenAdmExpAmtYpy) AS finGenAdmExpAmtYpy, sum(finGenAdmExpAmtCurrent) AS finGenAdmExpAmtCurrent FROM ".Tprefix."budgeting_financialbudget WHERE bfbid IN (".implode(',', $options['filter']).")");
                        if($db->num_rows($finbudgetquery) > 0) {
                            while($finbudget = $db->fetch_assoc($finbudgetquery)) {
                                $financialbudget = $finbudget;
                            }
                        }
                        if(is_empty($financialadminexpenses)) {
                            break;
                        }
                        $output['financialadminexpenses']['data'] = BudgetExpenseCategories::parse_financialadminfields($expensescategories, array('mode' => 'display', 'financialbudget' => $financialbudget, 'prevfinancialbudget' => $prevfinancialbudget, 'financialadminexpenses' => $financialadminexpenses));
                        $output['financialadminexpenses']['budyef'] = '<td style="width:10%">% '.$lang->budyef.'</td>';
                        break;

                    case'forecastbalancesheet':
                        $budforecastobj = new BudgetForecastAccountsTree();
                        $fxrate_query = '(SELECT rate from budgeting_fxrates bfr JOIN  budgeting_financialbudget bfb ON(bfb.affid=bfr.affid AND bfb.year=bfr.year)  WHERE bfr.fromCurrency=bfb.currency AND bfr.toCurrency='.intval($options['tocurrency']).' AND bfb.bfbid=budgeting_forecastbs.bfbid)';
                        $sql = "SELECT batid,sum(amount*{$fxrate_query}) AS amount FROM ".Tprefix."budgeting_forecastbs WHERE bfbid IN (".implode(',', $options['filter']).") GROUP By batid";
                        $query = $db->query($sql);
                        if($db->num_rows($query) > 0) {
                            while($item = $db->fetch_assoc($query)) {
                                $forecastbalancesheet[$item['batid']]['amount'] = $item['amount'];
                            }
                        }
                        if(is_empty($forecastbalancesheet)) {
                            break;
                        }
                        $output['forecastbalancesheet']['data'] .= $budforecastobj->parse_account(array('financialbudget' => $financialbudget, 'forecastbalancesheet' => $forecastbalancesheet, 'mode' => 'display'));
                        break;

                    case'profitlossaccount':
                        $plcategories = BudgetPlCategories::get_data('', array('returnarray' => true));
                        $fxrate_query = '(SELECT rate from budgeting_fxrates bfr JOIN  budgeting_financialbudget bfb ON(bfb.affid=bfr.affid AND bfb.year=bfr.year)  WHERE bfr.fromCurrency=bfb.currency AND bfr.toCurrency='.intval($options['tocurrency']).' AND bfb.bfbid=budgeting_plexpenses.bfbid)';
                        $sql = "SELECT bpliid,sum(actualPrevTwoYears*{$fxrate_query}) AS actualPrevTwoYears,sum(budgetPrevYear*{$fxrate_query}) AS budgetPrevYear, sum(yefPrevYear*{$fxrate_query}) AS yefPrevYear, sum(budgetCurrent*{$fxrate_query}) AS budgetCurrens FROM ".Tprefix."budgeting_plexpenses WHERE bfbid IN (".implode(', ', $options['filter']).") GROUP By bpliid";
                        $query = $db->query($sql);
                        $fields = array('actualPrevTwoYears', 'budgetPrevYear', 'yefPrevYear', 'budgetCurrent');
                        if($db->num_rows($query) > 0) {
                            while($item = $db->fetch_assoc($query)) {
                                foreach($fields as $field) {
                                    $placcount[$item['bpliid']][$field] = sprintf("%.2f", $item[$field]);
                                }
                            }
                        }
                        if(is_empty($placcount)) {
                            break;
                        }
                        $commericalbudget = Budgets::get_data(array('affid' => $options['affid'], 'year' => $options['year']), array('simple' => false, 'operators' => array('affid' => IN)));
                        $prevcommericalbudget = Budgets::get_data(array('affid' => $options['affid'], 'year' => ($options['year'] - 1)), array('simple' => false, 'operators' => array('affid' => IN)));
                        $prevtwocommericalbudget = Budgets::get_data(array('affid' => $options['affid'], 'year' => ($options['year'] - 2)), array('simple' => false, 'operators' => array('affid' => IN)));

                        $current[$commericalbudget->bid] = $commericalbudget->bid;
                        $prevtwoyears[$prevtwocommericalbudget->bid] = $prevtwocommericalbudget->bid;
                        $prevyear[$prevcommericalbudget->bid] = $prevcommericalbudget->bid;
                        if(is_array($commericalbudget)) {
                            unset($current[$commericalbudget->bid]);
                            foreach($commericalbudget as $budget) {
                                $current[$budget->bid] = $budget->bid;
                            }
                        }
                        if(is_array($prevcommericalbudget)) {
                            unset($prevyear[$prevcommericalbudget->bid]);
                            foreach($prevcommericalbudget as $budget) {
                                $prevyear[$budget->bid] = $budget->bid;
                            }
                        }
                        if(is_array($prevtwocommericalbudget)) {
                            unset($prevtwoyears[$prevtwocommericalbudget->bid]);
                            foreach($prevtwocommericalbudget as $budget) {
                                $prevtwoyears[$budget->bid] = $budget->bid;
                            }
                        }
                        $bid = array('prevtwoyears' => $prevtwoyears, 'prevyear' => $prevyear, 'current' => $current);
                        $output['profitlossaccount']['data'] = BudgetPlCategories::parse_plfields($plcategories, array('mode' => 'display', 'financialbudget' => $financialbudget, 'placcount' => $placcount, 'bid' => $bid, 'filter' => $options['filter'], 'tocurrency' => $options['tocurrency']));
                        $output['profitlossaccount']['variations'] = '<td style="width:10%">% '.$lang->yefactual.'</td><td style="width:10%">% '.$lang->yefbud.'</td>';
                        $output['profitlossaccount']['budyef'] = '<td style="width:10%">% '.$lang->budyef.'</td>';
                        $output[$type]['years'] = ' <td style="width:10%"><span>'.$options['year'].' / '.($options['year'] - 2).'</span></td> <td style="width:10%"><span>'.$options['year'].' / '.$options['year'].'</span></td>';
                        break;
                }
            }
        }
        return $output;
    }

    public function isFinalized() {
        if($this->isFinalized == 1) {
            return true;
        }
        return false;
    }

    private function validate_requiredfields(array $data = array()) {
        global $core, $db;
        if(is_array($data)) {
            $required_fields = array('affid', 'year');
            foreach($required_fields as $field) {
                if(empty($data['financialbudget'][$field]) && $data['financialbudget'][$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
                $data['financialbudget'][$field] = $core->sanitize_inputs($data['financialbudget'][$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data['financialbudget'][$field] = $db->escape_string($data['financialbudget'][$field]);
            }
        }
    }

}
?>