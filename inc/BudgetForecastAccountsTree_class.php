<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetForecastAccountsTree.php
 * Created:        @tony.assaad    Oct 1, 2014 | 2:40:51 PM
 * Last Update:    @tony.assaad    Oct 1, 2014 | 2:40:51 PM
 */

/**
 * Description of BudgetForecastAccountsTree
 *
 * @author tony.assaad
 */
class BudgetForecastAccountsTree extends AbstractClass {
    protected $data = array();
    public $total = array();

    const PRIMARY_KEY = 'batid';
    const TABLE_NAME = 'budgeting_accountstrees';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = 'batid, name, sequence, title, parent, accountLevel, accountType';
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

    public function get_children() {
        return $this->get_data(array('parent' => $this->data[self::PRIMARY_KEY]), array('simple' => false, 'returnarray' => true));
    }

    public function get_headaccounts($filters = array()) {
        $filters['parent'] = 0;
        return $this->get_data($filters, array('order' => array('by' => 'sequence', 'sort' => 'ASC'), 'simple' => false, 'returnarray' => true));
    }

    public function get_parent($simple = true) {
        return new BudgetForecastAccountsTree($this->data['parent'], $simple);
    }

    public function parse_account($options = array()) {
        global $lang, $numfmt;

        $numfmt = new NumberFormatter($lang->settings['locale'], NumberFormatter::DECIMAL);

        $numfmt->setPattern("#0.###");
        $sides = array('left' => array('a'), 'right' => array('l', 'o'));
        foreach($sides as $column => $accounttypes) {
            $accountitems_output .= '<div style="display:inline-block; padding:5px; width:45%; vertical-align: top;">';
            $account_items = $this->get_data(array('accountType' => $accounttypes, 'parent' => 0), array('returnarray' => true));
            if(!empty($account_items)) {
                foreach($account_items as $id => $item) {
                    $accountitems_output .= '<div>';

                    $accountitems_output .= '<table width="100%">';
                    $accountitems_output .= $this->parse_accountsitems(array($id => $item), 0, array('financialbudget' => $options['financialbudget']->bfbid, 'mode' => $options['mode'], 'forecastbalancesheet' => $options['forecastbalancesheet']));
                    //$this->total[$id] = number_format($this->total[$id], 2);
                    $accountitems_output .= '<tr><td><strong>Total of '.$item->title.': </strong><span style="font-weight:bold;" id="total_'.$id.'_'.$item.'">'.$numfmt->format($this->total[$id]).'</span><input type="hidden" name="budgetforecastbs['.$item.'][total]" id="total_'.$id.'_'.$item.'" value="'.$this->total[$id].'"></input></td></tr>';
                    //parse net income for  Stockholders'Equity get the value from the financial budget total netIncome
                    $accountitems_output .= '</table>';
                    $accountitems_output .= '</div>';
                    //$this->total[$id] = number_format($this->total[$id], 2);
                    $grandtotals[$column] += $this->total[$id];
                    $columnrelation[$column] .= '_'.$id;
                }
            }


            $accountitems_output .= '</div>';
        }

        if($options['mode'] != 'fill') {
            foreach($grandtotals as $column => $total) {
                $accountitems_output .= '<div class="subtitle" style="display:inline-block; width:45%; padding:5px;"><h3>Total: <span id="gtotal'.$columnrelation[$column].'">'.$numfmt->format($total).'</span></h3></div>';
            }
        }
        return $accountitems_output;
    }

    private function parse_accountsitems($items, $depth, $options = array()) {
        global $numfmt;

        $finacncial_budobj = new FinancialBudget($options['financialbudget'], false);

        foreach($items as $id => $item) {
            $parent = $item->get_parent();
            switch($item->accountLevel) {
                case 'heading':
                    $class = 'thead';
                    $item->title = ucwords(strtolower($item->title));
                    $colspan = 2;
                    break;
                case 'account':
                case 'Account';
                    $class = 'subtitle';
                    $item->title = ucwords(strtolower($item->title));
                    $colspan = 2;
                    break;
                default:
                    $class = '';
                    $colspan = 1;
                    break;
            }
            if(!empty($item->ophrand)) {
                $item->title = null;
            }
            if(!is_object($item)) {
                $item = self::get_data(array('batid' => $item['batid']));
            }

            $output .= '<tr><td colspan="'.$colspan.'" class="'.$class.'" style="padding-left: '.(5 * $depth).'px;">'.$item->title.'</td>';

            if(method_exists($item, get_children)) {
                $account_children = $item->get_children();
            }
            if(is_array($account_children) && !empty($account_children)) { /* pass the fill type to parse the expenses for each subaccount */
                if(!empty($options['financialbudget'])) {
                    $forecast_expenses = BudgetForecastBalanceSheet::get_data(array('batid' => $item->batid, 'bfbid' => $options['financialbudget']), array('simple' => false));
                }

                $output.= $this->parse_accountsitems($account_children, $depth + 1, array('financialbudget' => $options['financialbudget'], 'mode' => $options['mode'], 'forecastbalancesheet' => $options['forecastbalancesheet'], 'total' => $this->total));

                unset($children, $account_children);
                continue;
            }
            else {
                if(!empty($options['financialbudget'])) {
                    $forecast_expenses = BudgetForecastBalanceSheet::get_data(array('batid' => $item->batid, 'bfbid' => $options['financialbudget']), array('simple' => false));
                }

                if(is_object($forecast_expenses)) {
                    if($forecast_expenses->amount != 0) {
                        //echo $forecast_expenses->amount.'  is amout of '.$item->name.' <br>';
                        $budgetforecastexp[$item->batid] = $forecast_expenses->amount;
                        //$subtotal[$parent->batid] +=$forecast_expenses->amount;
                        $subtotal[$parent->batid] = array_sum($budgetforecastexp);
                    }
                }
                /* total of each liablity and assets */
                $total[$parent->get_parent()->batid] = $subtotal[$parent->batid];

                if(isset($options['total']) && !empty($options['total'])) {
                    $total[$parent->get_parent()->batid] += $options['total'][$parent->get_parent()->batid];
                }
                $this->total = $total;
                if($total[$parent->batid] == 0) {
                    unset($total[$parent->batid]);
                }

                if(isset($options['mode']) && $options['mode'] === 'fill') {
                    /* to acquire netIncome */
                    if(!empty($item->sourceTable)) {
                        $this->total[$parent->get_parent()->batid] += $finacncial_budobj->netIncome;
                        $output .= '<td style="background-color:lightblue;"> '.parse_textfield(null, 'budgetforecastbs_'.$item->batid.'_'.$parent->batid.'_'.$parent->get_parent()->batid.'_subaccount', 'number', $finacncial_budobj->netIncome, array('readonly' => 'true', 'step' => 'any')).'</td>';
                    }
                    else if(empty($item->ophrand)) { /* hide fields for oprhand items */
                        $maxattr = null;
                        $min = 0;
                        $stepany = 'any';
                        /* Handle  negatie account signs ex depreciation */
                        if(!empty($item->accountSign) && $item->accountSign === 'negative') {
                            $maxattr = 0;
                            $stepany = $min = '';
                        }
                        $output .= '<input type = "hidden" name = "budgetforecastbs['.$item->batid.'][bfbsid]" value = "'.$forecast_expenses->bfbsid.'">';
                        $output .= '<input type = "hidden" name = "budgetforecastbs['.$item->batid.'][batid]" value = "'.$item->batid.'">';
                        $output .= '<td>'.parse_textfield('budgetforecastbs['.$item->batid.'][amount]', 'budgetforecastbs_'.$item->batid.'_'.$parent->batid.'_'.$parent->get_parent()->batid.'_subaccount', 'number', $budgetforecastexp[$item->batid], array('min' => $min, 'max' => $maxattr, 'required' => 'required', 'step' => $stepany)).'</td>';
                    }
                }
                else {
                    /* mode display */
                    if(isset($options['forecastbalancesheet']) && !empty($options['forecastbalancesheet'])) {
                        $forecastbalancesheet = $options['forecastbalancesheet'];
                        if(!empty($item->sourceTable)) {
                            $amount = $finacncial_budobj->{$item->sourceAttr};
                        }
                        else {
                            $amount = $forecastbalancesheet[$item->batid]['amount'];
                        }

                        $subtotal[$parent->batid] += $amount;
                        $total[$parent->get_parent()->batid] = array_sum($subtotal);
                        //   $this->total = $total;
                        if($total[$parent->batid] == 0) {
                            //  unset($total[$parent->batid]);
                        }
                        if(!empty($item->ophrand)) {
                            $ophrand_itmes = explode('+', $item->ophrand);
                            unset($forecastbalancesheet[$item->batid]['amount'], $amount);
                            foreach($ophrand_itmes as $ophrandval) {
                                $amount += ($forecastbalancesheet[$ophrandval]['amount']);
                            }
                        }

                        $budgetforecastexp[$item->batid] = $amount;
                    }
                    $output .= '<td>'.$numfmt->format($budgetforecastexp[$item->batid]).'</td>';
                }
            }
            $output .= '</tr>';
        }

        return $output;
    }

    public function get_createdby() {
        return new Users($this->data['createdBy']);
    }

}