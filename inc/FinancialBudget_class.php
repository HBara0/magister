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
            // $financialdata['netIncome'] = $data['financialbudget']['income'];
            $affiliate = new Affiliates($financialdata['affid']);
            $financialdata['currency'] = $affiliate->get_country()->get_maincurrency()->get()[numCode];
            foreach($fields as $field) {
                if(isset($data['financialbudget'][$field])) {
                    $max = 'max'.$field;
                    if($data['financialbudget'][$field] > $data['financialbudget'][$max]) {
                        $this->errorcode = 3;
                        return;
                    }
                    $data['financialbudget'][$field] = $core->sanitize_inputs($data['financialbudget'][$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                    $data['financialbudget'][$field] = $db->escape_string($data['financialbudget'][$field]);
                    $financialdata[$field] = $data['financialbudget'][$field];
                }
            }
            $affiliate = new Affiliates($financialdata['affid']);
            $financialdata['currency'] = $affiliate->mainCurrency;
            if($financialdata['currency'] == NULL) {
                $financialdata['currency'] = $affiliate->get_country()->get_maincurrency()->get()[numCode];
            }
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
            $financialbudgetdata['netIncome'] = $data['financialbudget']['income'];
            $query = $db->update_query(self::TABLE_NAME, $financialbudgetdata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        }

        $budgetforecastbs = $data['budgetforecastbs'];

        if(is_array($budgetforecastbs)) {
            unset($budgetforecastbs['liabilities'], $budgetforecastbs['assets']);
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
        $budget_trainingandvisits = $data['budgetrainingvisit'];
        if(is_array($budget_trainingandvisits)) {
            unset($budget_trainingandvisits['local']['classification']);
            foreach($budget_trainingandvisits as $visitype => $budgetvisit) {

                foreach($budgetvisit as $visitid => $visitdata) {
                    $visitdata['bfbid'] = $this->data[self::PRIMARY_KEY];
                    $btvid++;
                    $visitdata['btvid'] = $btvid;
                    $BudgetTrainingVisits_obj = new BudgetTrainingVisits();
                    $BudgetTrainingVisits_obj->set($visitdata);
                    $BudgetTrainingVisits_obj->save();
                    $this->errorcode = $BudgetTrainingVisits_obj->errorcode;
                    switch($this->get_errorcode()) {
                        case 0:
                            continue;
                        case 2:
                            return;
                    }
                }
            }
        }
        $banks = $data['bank'];
        if(is_array($banks)) {
            foreach($banks as $bank) {
                $bank['bfbid'] = $this->data[self::PRIMARY_KEY];
                $bankfacilities = new BudgetBankFacilities();
                $bankfacilities->set($bank);
                if($bank['bnkid'] == 0) {
                    $bankfacilities->delete_bankfacility();
                    continue;
                }
                $bankfacilities->save();
                $this->errorcode = $bankfacilities->errorcode;
                switch($this->get_errorcode()) {
                    case 0:
                        continue;
                    case 2:
                        return;
                }
            }
        }
        $clientoverdues = $data['clientoverdue'];
        if(is_array($clientoverdues)) {
            foreach($clientoverdues as $clientoverdue) {
                $clientoverdue['bfbid'] = $this->data[self::PRIMARY_KEY];
                $overduereceivables_obj = new BudgetOverdueReceivables();
                $overduereceivables_obj->set($clientoverdue);
                if(empty($clientoverdue['cid'])) {
                    $overduereceivables_obj->delete_clientoverdues();
                    continue;
                }
                $overduereceivables_obj->save();
                $this->errorcode = $overduereceivables_obj->errorcode;
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
            foreach($fields as $field) {
                if(isset($data['financialbudget'][$field])) {
                    $max = 'max'.$field;
                    if($data['financialbudget'][$field] > $data['financialbudget'][$max]) {
                        $this->errorcode = 3;
                        return;
                    }
                    $data['financialbudget'][$field] = $core->sanitize_inputs($data['financialbudget'][$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                    $data['financialbudget'][$field] = $db->escape_string($data['financialbudget'][$field]);
                    $financialdata[$field] = $data['financialbudget'][$field];
                }
            }
            $affiliate = new Affiliates($financialdata['affid']);
            $financialdata['currency'] = $affiliate->mainCurrency;
            if($financialdata['currency'] == NULL) {
                $financialdata['currency'] = $affiliate->get_country()->get_maincurrency()->get()[numCode];
            }
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
                unset($budgetforecastbs[liabilities], $budgetforecastbs['assets'], $budgetforecastbs['ownersequity']);

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
            $banks = $data['bank'];
            if(is_array($banks)) {
                foreach($banks as $bank) {
                    $bank['bfbid'] = $this->data[self::PRIMARY_KEY];
                    $bankfacilities = new BudgetBankFacilities();
                    $bankfacilities->set($bank);
                    if($bank['bnkid'] == 0) {
                        $bankfacilities->delete_bankfacility();
                        continue;
                    }
                    $bankfacilities->save();
                    $this->errorcode = $bankfacilities->errorcode;
                    switch($this->get_errorcode()) {
                        case 0:
                            continue;
                        case 2:
                            return;
                    }
                }
            }
            $clientoverdues = $data['clientoverdue'];
            if(is_array($clientoverdues)) {
                foreach($clientoverdues as $clientoverdue) {
                    $clientoverdue['bfbid'] = $this->data[self::PRIMARY_KEY];
                    $overduereceivables_obj = new BudgetOverdueReceivables();
                    $overduereceivables_obj->set($clientoverdue);
                    if(empty($clientoverdue['cid'])) {
                        $overduereceivables_obj->delete_clientoverdues();
                        continue;
                    }
                    $overduereceivables_obj->save();
                    $this->errorcode = $overduereceivables_obj->errorcode;
                    switch($this->get_errorcode()) {
                        case 0:
                            continue;
                        case 2:
                            return;
                    }
                }
            }
            $budget_trainingandvisits = $data['budgetrainingvisit'];
            if(is_array($budget_trainingandvisits)) {
                unset($budget_trainingandvisits['local']['classification']);
                // $btvid = 0;
                foreach($budget_trainingandvisits as $visitype => $budgetvisit) {
                    foreach($budgetvisit as $visitid => $visitdata) {
                        $visitdata['bfbid'] = $this->data[self::PRIMARY_KEY];

                        $BudgetTrainingVisits_obj = new BudgetTrainingVisits();

                        $BudgetTrainingVisits_obj->set($visitdata);
                        if(empty($visitdata['event'])) {
                            $delobj = BudgetTrainingVisits::get_data(array('inputChecksum' => $visitdata['inputChecksum']));
                            if(is_object($delobj)) {
                                $delobj->delete();
                            }
                        }
                        else {
                            $BudgetTrainingVisits_obj->save();
                        }
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
                if(isset($data['financialbudget']['income'])) {
                    $financialbudgetdata['netIncome'] = $data['financialbudget']['income'];
                }
                $query = $db->update_query(self::TABLE_NAME, $financialbudgetdata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
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
        global $db, $template, $core, $lang;
        if(isset($options['budgettypes']) && !empty($options['budgettypes'])) {
            /* get currenceis by consolidated budgetfinamce id */
            $financial_obj = FinancialBudget::get_data(array('bfbid' => $options['filter']), array('simple' => false, 'returnarray' => true));
            $prev_financial_obj = FinancialBudget::get_data(array('affid' => $options['affid'], 'year' => array(($options['year'] - 1), ($options['year'] - 2), ($options['year'] - 3))), array('simple' => false, 'returnarray' => true));

            if(is_array($financial_obj)) {
                foreach($financial_obj as $finbudget) {
                    $budget_currencies[$finbudget->year][$finbudget->bfbid] = $finbudget->currency;
                }
            }
            if(is_array($prev_financial_obj)) {
                foreach($prev_financial_obj as $finbudget) {
                    $budget_currencies[$finbudget->year][$finbudget->bfbid] = $finbudget->currency;
                }
            }
            $dal_config = array(
                    'operators' => array('fromCurrency' => 'in', 'affid' => 'in', 'year' => '='),
                    'simple' => false,
                    'order' => array('by' => 'year', 'sort' => DESC),
                    'returnarray' => true
            );
            $budgetcurrency = new Currencies($options['tocurrency']);
            $output['currfxratesdesc'] = $lang->currfxratedesc.$budgetcurrency->alphaCode.'</br>';
            $output['currfxrates'].='<span style="margin-top:3px;"><strong>'.$output['currfxratesdesc'].'</strong></span>';
            ksort($budget_currencies);
            foreach($budget_currencies as $budgetyear => $budget_currency) {
                $ratecategory = 'isBudget';
                if($budgetyear == ($options['year'] - 1)) {
                    $ratecategory = 'isYef';
                }
                else if($budgetyear == ($options['year'] - 2) || $budgetyear == ($options['year'] - 3)) {
                    $ratecategory = 'isActual';
                }
                $fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budget_currency, 'toCurrency' => $options['tocurrency'], 'affid' => $options['affid'], 'year' => $budgetyear, $ratecategory => 1), $dal_config);
                $currencyto = new Currencies($options['tocurrency']);
                if(is_array($fxrates_obj)) {
                    foreach($budget_currency as $currency) {
                        if($currency == $options['tocurrency']) {
                            continue;
                        }
                        $callback = function($val) use ($currency) {
                            return $val->fromCurrency == $currency;
                        };
                        $budgetfx = array_filter($fxrates_obj, $callback);
                        $budgetfx = current($budgetfx);
                        if(empty($budgetfx)) {
                            $currency = new Currencies($currency);
                            error($lang->sprint($lang->noexchangerate, $currency->alphaCode, $currencyto->alphaCode, $budgetyear), $_SERVER['HTTP_REFERER']);
                        }
                        $currency = $budgetfx->get_formCurrency();
                        $outputfxrates[$budgetfx->affid][$budgetyear][$currency->alphaCode][$currencyto->alphaCode] = $currency->alphaCode.' to '.$currencyto->alphaCode.' > '.$budgetfx->rate.'<br>';
                    }
                }
                else {
                    foreach($budget_currency as $currency) {
                        if($options['tocurrency'] != $currency) {
                            error($lang->sprint($lang->noexchangerate, implode(', ', $budget_currencies[$budgetyear]), $currencyto->alphaCode, $budgetyear), $_SERVER['HTTP_REFERER']);
                        }
                    }
                }
                /* Exchange rates to EUR */
                $budget_currency['USD'] = 840;
                $eur_fxrates_obj = BudgetFxRates::get_data(array('fromCurrency' => $budget_currency, 'toCurrency' => 978, 'affid' => $options['affid'], 'year' => $budgetyear, $ratecategory => 1), $dal_config);
                $eur = new Currencies(978);
                if(is_array($eur_fxrates_obj)) {
                    foreach($eur_fxrates_obj as $fxrate) {
                        $fromcurrency = new Currencies($fxrate->fromCurrency);
                        $outputfxrates[$fxrate->affid][$fxrate->year][$fromcurrency->alphaCode][978] = $fromcurrency->alphaCode.' to '.EUR.' > '.$fxrate->rate.'<br>';
                    }
                }
            }

            /* Displaying used exchange rates */
            if(is_array($outputfxrates)) {
                $output['currfxrates'] .='<br/><strong>'.$lang->exchangerates.'</strong><br/>';
                foreach($outputfxrates as $affiliateid => $fxrates_data) {
                    $affiliate = new Affiliates($affiliateid);
                    $output['currfxrates'] .= '<div style = "display:inline-block; vertical-align:top;"><ul style = "list-style-type: none;"><li>'.$affiliate->get_displayname();
                    if(is_array($fxrates_data)) {
                        $output['currfxrates'] .= '<ul style = "list-style-type: none;">';
                        foreach($fxrates_data as $year => $fxrates) {
                            if(is_array($fxrates)) {
                                foreach($fxrates as $rates) {
                                    foreach($rates as $rate) {
                                        $output['currfxrates'] .='<li>'.$year.' : '.$rate.'</li>';
                                    }
                                }
                            }
                        }
                        $output['currfxrates'] .= '</ul>';
                    }
                    $output['currfxrates'] .= '</li></ul></div>';
                }
            }

            if(count($options['budgettypes']) === 1) {
                if($options['budgettypes'][0] === 'headcount') {
                    $output['currfxrates'] = '';
                }
            }

            $output['note'] = '<p><strong>'.$lang->copytospreadsheets.'</strong></p>';
            foreach($options['budgettypes'] as $type) {
                /* specify for each year of budget  the specific rate from the fxrate */
                $prevyears_fxrates = array('actualPrevThreeYears' => array('year' => ($options['year'] - 3), 'ratecategory' => 'isActual'),
                        'actualPrevTwoYears' => array('year' => ($options['year'] - 2), 'ratecategory' => 'isActual'),
                        'yefPrevYear' => array('year' => ($options['year'] - 1), 'ratecategory' => 'isYef'),
                        'budgetCurrent' => array('year' => $options['year'], 'ratecategory' => 'isBudget')
                );

                switch($type) {
                    case 'headcount':
                        $positiongroups = PositionGroups::get_data('', array('returnarray' => true));
                        $sql = "SELECT posgid, sum(actualPrevThreeYears) AS actualPrevThreeYears,sum(actualPrevTwoYears) AS actualPrevTwoYears, sum(yefPrevYear) AS yefPrevYear, sum(budgetCurrent) AS budgetCurrent FROM ".Tprefix."budgeting_headcount WHERE bfbid IN (".implode(', ', $options['filter']).") GROUP By posgid";
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

                    /* ------------------------------------------------------------------------------------------------------------------- */

                    case 'investmentfollowup':
                        $investcategories = BudgetInvestCategories::get_data('', array('returnarray' => true));
                        /* Converting amount into the affiliates existing currency */
                        // $fxrate_query = '(SELECT rate from budgeting_fxrates bfr JOIN budgeting_financialbudget bfb ON(bfb.affid = bfr.affid AND bfb.year = bfr.year) WHERE bfr.fromCurrency = bfb.currency AND bfr.toCurrency = '.intval($options['tocurrency']).' AND bfb.bfbid = budgeting_investexpenses.bfbid)';

                        /* make the fxrate query dynamic based on actual(year) and year */
                        foreach($prevyears_fxrates as $attr => $fxconfig) {
                            $fxrate_query[$attr] = '(CASE WHEN bfb.currency = '.intval($options['tocurrency']).' THEN 1
                                                     ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = bfb.affid AND bfr.year ='.$fxconfig['year'].' AND bfr.fromCurrency = bfb.currency AND bfr.toCurrency = '.intval($options['tocurrency']).' AND bfr.'.$fxconfig['ratecategory'].' =1) END)';
                        }
                        $sql = "SELECT biiid, sum(actualPrevThreeYears*{$fxrate_query['actualPrevThreeYears']}) AS actualPrevThreeYears, sum(actualPrevTwoYears*{$fxrate_query['actualPrevTwoYears']}) AS actualPrevTwoYears, sum(yefPrevYear*{$fxrate_query['yefPrevYear']}) AS yefPrevYear, sum(budgetCurrent*{$fxrate_query['budgetCurrent']}) AS budgetCurrent, sum(percVariation) AS percVariation FROM ".Tprefix."budgeting_investexpenses binf JOIN  budgeting_financialbudget bfb ON(bfb.bfbid=binf.bfbid ) WHERE  binf.bfbid IN (".implode(', ', $options['filter']).") GROUP By biiid";
                        $query = $db->query($sql);
                        $fields = array('actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent');
                        if($db->num_rows($query) > 0) {
                            while($item = $db->fetch_assoc($query)) {
                                foreach($fields as $field) {
                                    $investmentfollowup[$item['biiid']][$field] = sprintf("%.2f", $item[$field]);
                                }
                            }
                        }
                        if(is_empty($investmentfollowup)) {
                            break;
                        }
                        $output['investmentfollowup']['data'] = BudgetInvestCategories::parse_expensesfields($investcategories, array('mode' => 'display', 'financialbudget' => $financialbudget, 'prevfinancialbudget' => $prevfinancialbudget, 'investmentfollowup' => $investmentfollowup));
                        break;

                    /* ------------------------------------------------------------------------------------------------------------------- */

                    case 'financialadminexpenses':
                        $expensescategories = BudgetExpenseCategories::get_data('', array('returnarray' => true));
                        /* Converting amount into the affiliates existing currency */
                        foreach($prevyears_fxrates as $attr => $fxconfig) {
                            $fxrate_query[$attr] = '(CASE WHEN bfb.currency = '.intval($options['tocurrency']).' THEN 1
                                        ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = bfb.affid AND bfr.year = '.$fxconfig['year'].' AND bfr.fromCurrency = bfb.currency AND bfr.toCurrency = '.intval($options['tocurrency']).' AND bfr.'.$fxconfig['ratecategory'].' =1) END)';
                        }

                        $sql = "SELECT beciid,sum(actualPrevThreeYears*{$fxrate_query['actualPrevThreeYears']}) AS actualPrevThreeYears ,sum(actualPrevTwoYears*{$fxrate_query['actualPrevTwoYears']}) AS actualPrevTwoYears, sum(yefPrevYear*{$fxrate_query['yefPrevYear']}) AS yefPrevYear, sum(budgetCurrent*{$fxrate_query['budgetCurrent']}) AS budgetCurrent,sum(budYefPerc) AS budYefPerc "
                                ."FROM ".Tprefix."budgeting_commadminexps bcex JOIN  budgeting_financialbudget bfb ON(bfb.bfbid=bcex.bfbid ) WHERE bcex.bfbid IN (".implode(', ', $options['filter']).") GROUP By beciid";
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

                        $prevyearsexp_fxrates = array('finGenAdmExpAmtApthy' => array('year' => ($options['year'] - 3), 'ratecategory' => 'isActual'),
                                'finGenAdmExpAmtApty' => array('year' => ($options['year'] - 2), 'ratecategory' => 'isActual'),
                                'finGenAdmExpAmtYpy' => array('year' => ($options['year'] - 1), 'ratecategory' => 'isYef'),
                                'finGenAdmExpAmtCurrent' => array('year' => $options['year'], 'ratecategory' => 'isBudget')
                        );
                        foreach($prevyearsexp_fxrates as $attr => $fxconfig) {
                            $fxrate_query2[$attr] = '(CASE WHEN bfb.currency = '.intval($options['tocurrency']).' THEN 1
                            ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = bfb.affid AND bfr.year = '.$fxconfig['year'].' AND bfr.fromCurrency = bfb.currency AND bfr.toCurrency = '.intval($options['tocurrency']).' AND bfr.'.$fxconfig['ratecategory'].' =1) END)';
                        }
                        $sql = "SELECT bfbid,sum(finGenAdmExpAmtApthy*{$fxrate_query2['finGenAdmExpAmtApthy']}) AS finGenAdmExpAmtApthy ,sum(finGenAdmExpAmtApty*{$fxrate_query2['finGenAdmExpAmtApty']}) AS finGenAdmExpAmtApty, sum(finGenAdmExpAmtYpy*{$fxrate_query2['finGenAdmExpAmtYpy']}) AS finGenAdmExpAmtYpy, sum(finGenAdmExpAmtCurrent*{$fxrate_query2['finGenAdmExpAmtCurrent']}) AS finGenAdmExpAmtCurrent FROM ".Tprefix."budgeting_financialbudget bfb WHERE bfb.bfbid IN (".implode(', ', $options['filter']).")";
                        $query = $db->query($sql);
                        $fields = array('bfbid', 'finGenAdmExpAmtApthy', 'finGenAdmExpAmtApty', 'finGenAdmExpAmtYpy', 'finGenAdmExpAmtCurrent');
                        if($db->num_rows($query) > 0) {
                            while($finbudget = $db->fetch_assoc($query)) {
                                foreach($fields as $field) {
                                    $financialbudget[$field] = sprintf("%.2f", $finbudget[$field]);
                                }
                            }
                        }
                        if(is_empty($financialadminexpenses)) {
                            break;
                        }
                        $output['financialadminexpenses']['data'] = BudgetExpenseCategories::parse_financialadminfields($expensescategories, array('mode' => 'display', 'financialbudget' => $financialbudget, 'prevfinancialbudget' => $prevfinancialbudget, 'financialadminexpenses' => $financialadminexpenses));
                        $output['financialadminexpenses']['budyef'] = '<td style = "width:10%">% '.$lang->budyef.'</td>';
                        break;

                    /* ------------------------------------------------------------------------------------------------------------------- */

                    case 'forecastbalancesheet':
                        $budforecastobj = new BudgetForecastAccountsTree();

                        $fxrate_query = '(CASE WHEN bfb.currency = '.intval($options['tocurrency']).' THEN 1
                            ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = bfb.affid AND bfr.year = bfb.year AND bfr.fromCurrency = bfb.currency AND bfr.toCurrency = '.intval($options['tocurrency']).' AND bfr.isBudget=1) END)';
                        $sql = "SELECT batid, SUM(amount*{$fxrate_query}) AS amount  FROM ".Tprefix."budgeting_forecastbs bfr JOIN  budgeting_financialbudget bfb ON(bfb.bfbid=bfr.bfbid ) WHERE bfr.bfbid IN (".implode(', ', $options['filter']).") GROUP By batid";

//$fxrate_query = '(SELECT rate from budgeting_fxrates bfr JOIN budgeting_financialbudget bfb ON(bfb.affid = bfr.affid AND bfb.year = bfr.year) WHERE bfr.fromCurrency = bfb.currency AND bfr.toCurrency = '.intval($options['tocurrency']).' AND bfb.bfbid = budgeting_forecastbs.bfbid)';
// $sql = "SELECT batid, SUM(amount*{$fxrate_query}) AS amount  FROM ".Tprefix."budgeting_forecastbs WHERE bfbid IN (".implode(', ', $options['filter']).") GROUP By batid";

                        $query = $db->query($sql);
                        if($db->num_rows($query) > 0) {
                            while($item = $db->fetch_assoc($query)) {

                                $forecastbalancesheet[$item['batid']]['amount'] = $item['amount'];
                            }
                        }
                        if(is_empty($forecastbalancesheet)) {
                            break;
                            /* @var $forecastbalancesheet type */
                        }
                        $financialbudgets = FinancialBudget::get_data(array('bfbid' => $options['filter']), array('simple' => false, 'returnarray' => true));
                        $output['forecastbalancesheet']['data'] .= $budforecastobj->parse_account(array('financialbudgets' => $financialbudgets, 'forecastbalancesheet' => $forecastbalancesheet, 'fxrates' => $fxrates_obj, 'toCurrency' => $options['tocurrency'], 'mode' => 'display'));
                        break;

                    /* ------------------------------------------------------------------------------------------------------------------- */

                    case 'trainingvisits':
                        $budgetrainingvisit_obj = BudgetTrainingVisits::get_data(array('bfbid' => $financialbudget), array('simple' => false));
                        $line_style = 'width="16.6%"';
                        $table_style = ' style = "width:100%;table-layout:fixed;"';
                        $tbody_style = 'style="width:100%;';
                        $local_linestyle = 'width="20%"';

                        if(is_array($financial_obj)) {
                            foreach($financial_obj as $financialbudget) {
                                $affiliate = new Affiliates($financialbudget->affid);
                                $budgeting_tainingvisitpreview .='<h2> '.$affiliate->get_displayname().'</small> </h2> ';
                                $rate = 1;
                                $ratequery = BudgetFxRates::get_data(array('affid' => $financialbudget->affid, 'year' => $financialbudget->year, 'fromCurrency' => $financialbudget->currency, 'toCurrency' => $options['tocurrency'], 'isBudget' => 1));
                                if(is_object($ratequery)) {
                                    $rates[$financialbudget->affid] = $ratequery->rate;
                                }
                                $budgetraininglocalvisit_objs = BudgetTrainingVisits::get_data(array('bfbid' => $financialbudget->bfbid, 'classification' => 'local'), array('returnarray' => true, 'simple' => false, 'order' => array('by' => 'totalCostAffiliate', 'sort' => 'DESC')));
                                if(is_array($budgetraininglocalvisit_objs)) {
                                    $budgeting_tainingvisitpreview .='<div class = "subtitle" style = "padding:8px;"> '.$lang->localvisit.'</div>';
                                    eval("\$budgeting_localtainingvisitpreviewinheader = \"".$template->get('budgeting_localtraininvisitpreview_header')."\";");
                                    foreach($budgetraininglocalvisit_objs as $budgetrainingvisit_ob) {
                                        $inputfields = array('company', 'name', 'date', 'purpose', 'costAffiliate', 'event', 'bm', 'planeCost', 'otherCosts', 'totalCostAffiliate');
                                        if(!empty($budgetrainingvisit_ob->date)) {
                                            $budgetrainingvisit_ob->date = date($core->settings['dateformat'], $budgetrainingvisit_ob->date);
                                        }
                                        // $entityobj = new Entities($budgetrainingvisit_ob->company);
                                        // $budgetrainingvisit_ob->company = $entityobj->name;
                                        $budgetrainingvisit_ob->costAffiliate = $budgetrainingvisit_ob->costAffiliate * $rates[$financialbudget->affid];

                                        $totallocalcostamount[$budgetrainingvisit_ob->btvid] += $budgetrainingvisit_ob->costAffiliate;
                                        $total_localamount += ($totallocalcostamount[$budgetrainingvisit_ob->btvid] );
                                        eval("\$budgeting_local_traininvisitpreview  = \"".$template->get('budgeting_local_traininvisitpreview')."\";");

                                        $budgeting_tainingvisitpreview.=$budgeting_local_traininvisitpreview;
                                        unset($budgeting_localtainingvisitpreviewinheader, $totallocalcostamount);
                                    }
                                    $budgeting_taininglocalvisitgrand_total = '<div style = "font-size:14px;font-weight:bold;float:right;margin-right:120px;">'.$lang->total.' '.$total_localamount.' </div>';
                                    $budgeting_tainingvisitpreview.=$budgeting_taininglocalvisitgrand_total;
                                }

                                $budgetrainingintvisit_objs = BudgetTrainingVisits::get_data(array('bfbid' => $financialbudget->bfbid, 'classification' => 'International'), array('returnarray' => true, 'simple' => false));
                                if(is_array($budgetrainingintvisit_objs)) {
                                    $budgeting_tainingvisitpreview .='<div class = "subtitle" style = "padding:8px;"> '.$lang->intvisit.' </div>';
                                    eval("\$budgeting_tainingvisitpreviewinheader  = \"".$template->get('budgeting_traininvisitpreview_header')."\";");
                                    foreach($budgetrainingintvisit_objs as $budgetrainingintvisit_ob) {
                                        $userob = new Users($budgetrainingintvisit_ob->bm);
                                        $budgetrainingintvisit_ob->bm = $userob->get_displayname();
                                        if(!empty($budgetrainingintvisit_ob->date)) {
                                            $budgetrainingintvisit_ob->date = date($core->settings['dateformat'], $budgetrainingintvisit_ob->date);
                                        }
                                        $budgetrainingintvisit_ob->planeCost = $budgetrainingintvisit_ob->planeCost * $rates[$financialbudget->affid];
                                        $budgetrainingintvisit_ob->otherCosts = $budgetrainingintvisit_ob->otherCosts * $rates[$financialbudget->affid];

                                        $totalinternvisit[$budgetrainingintvisit_ob->btvid] = number_format($budgetrainingintvisit_ob->planeCost + $budgetrainingintvisit_ob->otherCosts, 2);
                                        $totalamount += ($totalinternvisit[$budgetrainingintvisit_ob->btvid] );
                                        eval("\$budgeting_trainingvisitrows  .= \"".$template->get('budgeting_traininvisitpreview_row')."\";");
                                        unset($totalinternvisit);
                                    }
                                    eval("\$budgeting_int_tainingvisitpreview  = \"".$template->get('budgeting_int_traininvisitpreview')."\";");
                                    $budgeting_tainingvisitpreview.=$budgeting_int_tainingvisitpreview;
                                    $budgeting_tainingvisitgrand_total = '<div style = "font-size:14px;font-weight:bold;float:right;margin-right:120px;">'.$lang->total.' '.$totalamount.' </div>';
                                }
                                $budgeting_tainingvisitpreview.=$budgeting_tainingvisitgrand_total;
                                $budgeting_tainingvisitpreview.='<br>';
                                unset($totalamount, $total_localamount);
                            }
                        }
                        $output['trainingvisits']['data'] = $budgeting_tainingvisitpreview;
                        unset($budgeting_tainingvisitpreview);
                        break;
                    /* ------------------------------------------------------------------------------------------------------------------- */

                    case 'domestictrainingvisits':
                        $budgetrainingvisit_obj = BudgetTrainingVisits::get_data(array('bfbid' => $financialbudget), array('simple' => false));
                        if(is_array($financial_obj)) {
                            foreach($financial_obj as $financialbudget) {
                                $affiliate = new Affiliates($financialbudget->affid);
                                $rate = 1;
                                $ratequery = BudgetFxRates::get_data(array('affid' => $financialbudget->affid, 'year' => $financialbudget->year, 'fromCurrency' => $financialbudget->currency, 'toCurrency' => $options['tocurrency'], 'isBudget' => 1));
                                if(is_object($ratequery)) {
                                    $rates[$financialbudget->affid] = $ratequery->rate;
                                }
                                $budgetraininglocalvisit_objs = BudgetTrainingVisits::get_data(array('bfbid' => $financialbudget->bfbid, 'classification' => 'local'), array('returnarray' => true, 'simple' => false, 'order' => array('by' => 'totalCostAffiliate', 'sort' => 'DESC')));
                                if(is_array($budgetraininglocalvisit_objs)) {
//                                    $budgeting_tainingvisitpreview .='<div class = "subtitle" style = "padding:8px;"> '.$lang->localvisit.'</div>';
                                    eval("\$budgeting_localtainingvisitpreviewinheader = \"".$template->get('budgeting_localtraininvisitpreview_header_presentation')."\";");
                                    foreach($budgetraininglocalvisit_objs as $budgetrainingvisit_ob) {
                                        $inputfields = array('company', 'name', 'date', 'purpose', 'costAffiliate', 'event', 'bm', 'planeCost', 'otherCosts', 'totalCostAffiliate');
                                        if(!empty($budgetrainingvisit_ob->date)) {
                                            $budgetrainingvisit_ob->date = date($core->settings['dateformat'], $budgetrainingvisit_ob->date);
                                        }
                                        // $entityobj = new Entities($budgetrainingvisit_ob->company);
                                        // $budgetrainingvisit_ob->company = $entityobj->name;
                                        $budgetrainingvisit_ob->costAffiliate = $budgetrainingvisit_ob->costAffiliate * $rates[$financialbudget->affid];

                                        $totallocalcostamount[$budgetrainingvisit_ob->btvid] += $budgetrainingvisit_ob->costAffiliate;
                                        $total_localamount += ($totallocalcostamount[$budgetrainingvisit_ob->btvid] );
                                        eval("\$budgeting_local_traininvisitpreview  = \"".$template->get('budgeting_local_traininvisitpreview')."\";");

                                        $budgeting_tainingvisitpreview.=$budgeting_local_traininvisitpreview;
                                        unset($budgeting_localtainingvisitpreviewinheader, $totallocalcostamount);
                                    }
//                                    $budgeting_taininglocalvisitgrand_total = '<div>'.$lang->total.' '.$total_localamount.' </div>';
//                                    $budgeting_tainingvisitpreview.=$budgeting_taininglocalvisitgrand_total;
                                }
//                                $budgeting_tainingvisitpreview = $budgeting_tainingvisitgrand_total;
                                unset($totalamount, $total_localamount);
                            }
                        }
                        $output['domestictrainingvisits']['data'] = $budgeting_tainingvisitpreview;
                        unset($budgeting_tainingvisitpreview);
                        break;
                    /* ------------------------------------------------------------------------------------------------------------------- */

                    case 'internationaltrainingvisits':
                        $budgetrainingvisit_obj = BudgetTrainingVisits::get_data(array('bfbid' => $financialbudget), array('simple' => false));
                        if(is_array($financial_obj)) {
                            foreach($financial_obj as $financialbudget) {
                                $affiliate = new Affiliates($financialbudget->affid);
//                                $budgeting_tainingvisitpreview .='<h2> '.$affiliate->get_displayname().'</small> </h2> ';
                                $rate = 1;
                                $ratequery = BudgetFxRates::get_data(array('affid' => $financialbudget->affid, 'year' => $financialbudget->year, 'fromCurrency' => $financialbudget->currency, 'toCurrency' => $options['tocurrency'], 'isBudget' => 1));
                                if(is_object($ratequery)) {
                                    $rates[$financialbudget->affid] = $ratequery->rate;
                                }

                                $budgetrainingintvisit_objs = BudgetTrainingVisits::get_data(array('bfbid' => $financialbudget->bfbid, 'classification' => 'International'), array('returnarray' => true, 'simple' => false));
                                if(is_array($budgetrainingintvisit_objs)) {
//                                    $budgeting_tainingvisitpreview .='<div class = "subtitle" style = "padding:8px;"> '.$lang->intvisit.' </div>';
                                    eval("\$budgeting_tainingvisitpreviewinheader  = \"".$template->get('budgeting_traininvisitpreview_header_presentation')."\";");
                                    foreach($budgetrainingintvisit_objs as $budgetrainingintvisit_ob) {
                                        $userob = new Users($budgetrainingintvisit_ob->bm);
                                        $budgetrainingintvisit_ob->bm = $userob->get_displayname();
                                        if(!empty($budgetrainingintvisit_ob->date)) {
                                            $budgetrainingintvisit_ob->date = date($core->settings['dateformat'], $budgetrainingintvisit_ob->date);
                                        }
                                        $budgetrainingintvisit_ob->planeCost = $budgetrainingintvisit_ob->planeCost * $rates[$financialbudget->affid];
                                        $budgetrainingintvisit_ob->otherCosts = $budgetrainingintvisit_ob->otherCosts * $rates[$financialbudget->affid];

                                        $totalinternvisit[$budgetrainingintvisit_ob->btvid] = number_format($budgetrainingintvisit_ob->planeCost + $budgetrainingintvisit_ob->otherCosts, 2);
                                        $totalamount += ($totalinternvisit[$budgetrainingintvisit_ob->btvid] );
                                        eval("\$budgeting_trainingvisitrows  .= \"".$template->get('budgeting_traininvisitpreview_row')."\";");
                                        unset($totalinternvisit);
                                    }
                                    eval("\$budgeting_int_tainingvisitpreview  = \"".$template->get('budgeting_int_traininvisitpreview')."\";");
                                    $budgeting_tainingvisitpreview.=$budgeting_int_tainingvisitpreview;
//                                    $budgeting_tainingvisitgrand_total = '<div style = "font-size:14px;font-weight:bold;float:right;margin-right:120px;">'.$lang->total.' '.$totalamount.' </div>';
                                }
//                                $budgeting_tainingvisitpreview = $budgeting_tainingvisitgrand_total;
//                                $budgeting_tainingvisitpreview.='<br>';
                                unset($totalamount, $total_localamount);
                            }
                        }
                        $output['internationaltrainingvisits']['data'] = $budgeting_tainingvisitpreview;
                        unset($budgeting_tainingvisitpreview);
                        break;
                    /* ------------------------------------------------------------------------------------------------------------------- */

                    case 'overduereceivables':
                        global $core;
                        foreach($financial_obj as $financialbudget) {
                            $affiliate = new Affiliates($financialbudget->affid);
                            $outputclientsoverdues .='<h2><small>'.$affiliate->get_displayname().'</small></h2><table>';
                            $clientsoverdues = BudgetOverdueReceivables::get_data(array('bfbid' => $financialbudget->bfbid), array('returnarray' => true, 'order' => array('by' => 'totalAmount', 'sort' => 'DESC')));
                            if(is_array($clientsoverdues)) {
                                foreach($clientsoverdues as $clientoverdue) {
                                    $client = new Entities($clientoverdue->cid);
                                    $overduereceivables_row .='<tr><td style = "width:20%;">'.$client->get_displayname().'</td>';
                                    $fields = array('legalAction', 'oldestUnpaidInvoiceDate', 'totalAmount', 'convertedtotalAmount', 'reason', 'action');
                                    foreach($fields as $field) {
                                        switch($field) {
                                            case 'oldestUnpaidInvoiceDate':
                                                if($clientoverdue->$field == 0) {
                                                    $clientoverdue->$field = '';
                                                }
                                                else {
                                                    $clientoverdue->$field = date($core->settings['dateformat'], $clientoverdue->$field);
                                                }
                                                $overduereceivables_row .='<td style = "width:10%;">'.$clientoverdue->$field.'</td>';
                                                break;
                                            case 'convertedtotalAmount':
                                                $rate = 1;
                                                $ratequery = BudgetFxRates::get_data(array('affid' => $financialbudget->affid, 'year' => $financialbudget->year, 'fromCurrency' => $financialbudget->currency, 'toCurrency' => $options['tocurrency'], 'isBudget' => 1));
                                                if(is_object($ratequery)) {
                                                    $rate = $ratequery->rate;
                                                }
                                                $clientoverdue->$field = $clientoverdue->totalAmount * $rate;
                                                $totalamount +=$clientoverdue->totalAmount;
                                                $totalconvertedamount +=$clientoverdue->$field;
                                                $overduereceivables_row .='<td style = "width:10%;">'.$clientoverdue->$field.'</td>';
                                                break;
                                            default:
                                                $overduereceivables_row .='<td style = "width:10%;">'.$clientoverdue->$field.'</td>';
                                                break;
                                        }
                                    }
                                    $overduereceivables_row .='</tr>';
                                }
                            }
                            $currencyto = new Currencies($options['tocurrency']);
                            $total = '<td style = "width:10%;">Total ('.$currencyto->get()['alphaCode'].') </td>';
                            eval("\$outputclientsoverdues .= \"".$template->get('budgeting_overduereceivables_header')."\";");
                            $outputclientsoverdues .=$overduereceivables_row;
                            $outputclientsoverdues .='<tr><td style = "width:20%;font-weight:bold;">'.$lang->total.'</td><td colspan = "2"></td><td style = "width:10%;font-weight:bold;">'.$totalamount.'</td>';
                            $outputclientsoverdues .='<td style = "width:10%;font-weight:bold;">'.$totalconvertedamount.'</td><td colspan = "2"></td></tr></table></br>';
                            unset($overduereceivables_row, $totalamount, $totalconvertedamount);
                        }
                        $output['overduereceivables']['data'] = $outputclientsoverdues;
                        break;
                    /* ------------------------------------------------------------------------------------------------------------------- */


                    case 'bank':
                        global $core;
                        foreach($financial_obj as $financialbudget) {
                            $banksfacilities = BudgetBankFacilities::get_data(array('bfbid' => $financialbudget->bfbid), array('returnarray' => true, 'simple' => false));
                            if(is_array($banksfacilities)) {
                                $affiliate = new Affiliates($financialbudget->affid);
                                $bank_output .='<table style = "border-bottom:1px dashed #BFBFBF;;"><tr><td style = "font-weight:bold;">'.$affiliate->get_displayname().'</td></tr>';
                                $fields = array('bnkid', 'bankfacilities', 'overDraft', 'loan', 'forexForward', 'billsDiscount', 'othersGuarantees', 'facilitiesSubtotal', 'facilityCurrency', 'interestRate', 'premiumCommission', 'totalAmount', 'endquarterAmount', 'comfortLetter', 'LastIssuanceDate', 'LastRenewalDate');
                                foreach($fields as $field) {
                                    $fieldtitle = strtolower($field);
                                    $rowclass = alt_row($rowclass);
                                    if($fieldtitle == 'bnkid') {
                                        $rowclass = "thead";
                                        $fieldtitle = 'tangibleassests';
                                    }
                                    if($fieldtitle == 'facilitiessubtotal') {
                                        $rowclass = "thead";
                                    }
                                    $row_output = '<tr class = '.$rowclass.'><td style = "width:15%;">'.$lang->$fieldtitle.'</td>';
                                    foreach($banksfacilities as $bankfacilitiy) {
                                        $rate = 1;
                                        $ratequery = BudgetFxRates::get_data(array('affid' => $financialbudget->affid, 'year' => $financialbudget->year, 'fromCurrency' => $bankfacilitiy->facilityCurrency, 'toCurrency' => $options['tocurrency'], 'isBudget' => 1));
                                        if(is_object($ratequery)) {
                                            $rate = $ratequery->rate;
                                        }
                                        switch($field) {
                                            case 'bnkid':
                                                $bank_obj = new Banks($bankfacilitiy->bnkid);
                                                $row_output .= '<td>'.$bank_obj->get_displayname().'</td>';
                                                break;
                                            case 'bankfacilities':
                                                $hasfacilities = 'No';
                                                if($bankfacilitiy->hasFacilities == 1) {
                                                    $hasfacilities = 'Yes';
                                                }
                                                $row_output .= '<td>'.$hasfacilities.'</td>';
                                                break;
                                            case 'overDraft':
                                            case 'loan':
                                            case 'forexForward':
                                            case 'billsDiscount':
                                            case 'othersGuarantees':
                                            case 'endquarterAmount':
                                            case 'totalAmount':
                                                $bankfacilitiy->$field = $bankfacilitiy->$field * $rate;
                                                $row_output .= '<td>'.$bankfacilitiy->$field.'</td>';
                                                $facilities_total[$bankfacilitiy->bbfid] += $bankfacilitiy->$field;
                                                break;
                                            case 'LastIssuanceDate':
                                            case 'LastRenewalDate':
                                                if($bankfacilitiy->$field != 0) {
                                                    $bankfacilitiy->$field = date($core->settings['dateformat'], $bankfacilitiy->$field);
                                                }
                                                else {
                                                    $bankfacilitiy->$field = '';
                                                }
                                                $row_output .= '<td>'.$bankfacilitiy->$field.'</td>';
                                                break;
                                            case 'facilitiesSubtotal':
                                                $row_output .= '<td>'.$facilities_total[$bankfacilitiy->bbfid].'</td>';
                                                break;
                                            case 'facilityCurrency':
                                                $currency = Currencies::get_data(array('numCode' => $bankfacilitiy->$field));
                                                $row_output .= '<td>'.$currency->alphaCode.'<small> Exchange Rate to '.$currencyto->alphaCode.': '.$rate.'</small></td>';
                                                break;
                                                break;
                                            default:
                                                $row_output .= '<td>'.$bankfacilitiy->$field.'</td>';
                                                break;
                                        }
                                    }
                                    $banksoutput_rows .=$row_output.'</tr>';
                                    unset($row_output);
                                }
                                $bank_output .=$banksoutput_rows.'</table><br/><br/>';
                                unset($banksoutput_rows);
                            }
                        }
                        if(empty($bank_output)) {
                            break;
                        }
                        $output[$type]['data'] = $bank_output;
                        break;
                    /* ------------------------------------------------------------------------------------------------------------------- */

                    case'profitlossaccount':
                        $plcategories = BudgetPlCategories::get_data('', array('returnarray' => true));
                        /* make the fxrate query dynamic based on actual(year) and year */
//                        $prevyears_fxrates = array('actualPrevThreeYears' => ($options['year'] - 3),
//                                'actualPrevTwoYears' => ($options['year'] - 2),
//                                'yefPrevYear' => ($options['year'] - 1),
//                                'budgetCurrent' => $options['year']
//                        );
                        $fxrate_query = array();
                        foreach($prevyears_fxrates as $attr => $fxconfig) {
                            $fxrate_query[$attr] = '(CASE WHEN bfb.currency = '.intval($options['tocurrency']).' THEN 1
                        ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = bfb.affid AND bfr.year = '.$fxconfig['year'].' AND bfr.fromCurrency = bfb.currency AND bfr.toCurrency = '.intval($options['tocurrency']).'  AND bfr.'.$fxconfig['ratecategory'].' =1) END)';
                        }
                        $sql = "SELECT bpliid, sum(actualPrevThreeYears*{$fxrate_query['actualPrevThreeYears']}) AS actualPrevThreeYears, sum(actualPrevTwoYears*{$fxrate_query['actualPrevTwoYears']}) AS actualPrevTwoYears,sum(yefPrevYear*{$fxrate_query['yefPrevYear']}) AS yefPrevYear, sum(budgetCurrent*{$fxrate_query['budgetCurrent']}) AS budgetCurrent FROM ".Tprefix."budgeting_plexpenses bple"
                                ." JOIN  budgeting_financialbudget bfb ON(bfb.bfbid=bple.bfbid )".""
                                ." WHERE bple.bfbid IN (".implode(', ', $options['filter']).") GROUP By bpliid";
                        $query = $db->query($sql);
                        $fields = array('actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent');
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
                        $prevcommericalbudget = Budgets::get_data(array('affid' => $options['affid'], 'year' => ( $options['year'] - 1)), array('simple' => false, 'operators' => array('affid' => IN)));
                        $prevtwocommericalbudget = Budgets::get_data(array('affid' => $options['affid'], 'year' => ( $options['year'] - 2)), array('simple' => false, 'operators' => array('affid' => IN)));

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
                        $output['profitlossaccount']['data'] = BudgetPlCategories::parse_plfields($plcategories, array('mode' => 'display', 'financialbudget' => $financialbudget, 'placcount' => $placcount, 'bid' => $bid, 'filter' => $options['filter'], 'year' => $options['year'], 'affid' => $options['affid'], 'tocurrency' => $options['tocurrency']));
                        $output['profitlossaccount']['variations'] = '<td style = "width:10%">% '.$lang->yefactual.'</td>';
                        $output['profitlossaccount']['budyef'] = '<td style = "width:10%">% '.$lang->budyef.'</td>';
                        $output[$type]['variations_years'] = ' <td style = "width:10%"><span>'.$options['year'].' / '.($options['year'] - 2).'</span></td>';
                        break;


                    /* ------------------------------------------------------------------------------------------------------------------- */
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

    protected function validate_requiredfields(array $data = array()) {
        global $core, $db;
        if(is_array($data)) {
            $required_fields = array('affid', 'year');
            foreach($required_fields as $field) {
                if(empty($data['financialbudget'][$field]) && $data['financialbudget'] [$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
                $data['financialbudget'][$field] = $core->sanitize_inputs($data['financialbudget'][$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data['financialbudget'][$field] = $db->escape_string($data['financialbudget'][$field]);
            }
        }
    }

    public
    static function generate_filters(array $inputdata) {
        global $core;

        if(is_array($inputdata['affiliates'])) {
            if($core->usergroup['canViewAllAff'] == 0) {
                if(is_array($core->user['auditedaffids'])) {
                    if(!in_array($inputdata['affiliates'], $core->user['auditedaffids'])) {
                        $filter = array('filters' => array('affiliates' => array($core->user['affiliates'])));
                    }
                    else {
                        $filter = array('filters' => array($inputdata['affiliates']));
                    }
                }
                else {
                    $filter = array('filters' => array($core->user['affiliates']));
                }
            }
        }

        return $filter;
    }

}
?>