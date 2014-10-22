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
        $sides = array('left' => array('a'), 'right' => array('l', 'o'));
        foreach($sides as $column => $accounttypes) {
            $accountitems_output .= '<div style="display:inline-block; padding:5px; width:45%; vertical-align: top;">';
            $account_items = $this->get_data(array('accountType' => $accounttypes, 'parent' => 0), array('returnarray' => true));
            if(!empty($account_items)) {
                foreach($account_items as $id => $item) {
                    $accountitems_output .= '<div>';
                    $accountitems_output .= '<table width="100%">';
                    $accountitems_output .= $this->parse_accountsitems(array($id => $item), 0, array('financialbudget' => $options['financialbudget']->bfbid, 'mode' => $options['mode'], 'forecastbalancesheet' => $options['forecastbalancesheet']));
                    $accountitems_output .= '<tr><td><strong>Total of '.$item->title.': </strong><span style="font-weight:bold;" id="total_'.$id.'_'.$item.'">'.$this->total[$id].'</span><input type="hidden" name="budgetforecastbs['.$item.'][total]" id="total_'.$id.'_'.$item.'" value="'.$this->total[$id].'"></input></td></tr>';
                    //parse net income for  Stockholders'Equity get the value from the financial budget total netIncome

                    $accountitems_output.= $accountnetincome_output;
                    $accountitems_output .= '</table>';
                    $accountitems_output .= '</div>';

                    $grandtotals[$column] += $this->total[$id];
                    $columnrelation[$column] .= '_'.$id;
                }
            }


            $accountitems_output .= '</div>';
        }

        if($options['mode'] != 'fill') {
            foreach($grandtotals as $column => $total) {
                $accountitems_output .= '<div class="subtitle" style="display:inline-block; width:45%; padding:5px;"><h3>Total: <span id="gtotal'.$columnrelation[$column].'">'.$total.'</span></h3></div>';
            }
        }
        return $accountitems_output;
    }

    private function parse_accountsitems($items, $depth, $options = array()) {
        global $template, $core;

        $finacncial_budobj = new FinancialBudget($options['financialbudget'], false);

        foreach($items as $id => $item) {
            switch($item->accountLevel) {
                case 'heading':
                    $class = 'subtitle';
                    $item->title = ucwords(strtolower($item->title));
                    break;
                case 'account':
                case 'Account';
                    break;
                default:
                    $class = '';
                    break;
            }

            if(!is_object($item)) {
                $item = self::get_data(array('batid' => $item['batid']));
            }

            $output .= '<tr><td class="'.$class.'" style="padding-left: '.(5 * $depth).'px;">'.$item->title.'</td>';

            if(method_exists($item, get_children)) {
                $account_children = $item->get_children();
            }
            if(is_array($account_children) && !empty($account_children)) { /* pass the fill type to parse the expenses for each subaccount */
                if(!empty($options['financialbudget'])) {
                    $forecast_expenses = BudgetForecastBalanceSheet::get_data(array('batid' => $item->batid, 'bfbid' => $options['financialbudget']->bfbid), array('simple' => false));
                }

                $output.= $this->parse_accountsitems($account_children, $depth + 1, array('financialbudget' => $options['financialbudget'], 'mode' => $options['mode'], 'forecastbalancesheet' => $options['forecastbalancesheet'], 'total' => $this->total));
                unset($children, $account_children);
                continue;
            }
            else {
                if(!empty($options['financialbudget'])) {
                    $forecast_expenses = BudgetForecastBalanceSheet::get_data(array('batid' => $item->batid, 'bfbid' => $options[financialbudget]), array('simple' => false));
                }
                if(is_object($forecast_expenses)) {
                    $budgetforecastexp[$item->batid] = $forecast_expenses->amount;
                    $subtotal[$item->get_parent()->batid] +=$forecast_expenses->amount;
                }
                /* total of each liablity and assets */


                $total[$item->get_parent()->get_parent()->batid] = $subtotal[$item->get_parent()->batid];
                if(isset($options['total']) && !empty($options['total'])) {
                    $total[$item->get_parent()->get_parent()->batid] += $options['total'][$item->get_parent()->get_parent()->batid];
                }
                $this->total = $total;
                if($total[$item->get_parent()->batid] == 0) {
                    unset($total[$item->get_parent()->batid]);
                }


                if(isset($options['mode']) && $options['mode'] === 'fill') {
                    /* to acquire netIncome */
                    if(!empty($item->sourceTable)) {
                        $this->total[$item->get_parent()->get_parent()->batid] +=$finacncial_budobj->netIncome;
                        $output .= '<td>'.parse_textfield(null, 'budgetforecastbs_'.$item->batid.'_'.$item->get_parent()->batid.'_'.$item->get_parent()->get_parent()->batid.'_subaccount', 'number', $finacncial_budobj->netIncome, array('readonly' => true, 'accept' => 'numeric', 'step' => 'any')).'</td>';
                    }
                    else {
                        $maxattr = null;
                        $min = 0;
                        $stepany = 'any';
                        /* Handle  negatie account signs ex depreciation */
                        if(!empty($item->accountSign) && $item->accountSign === 'negative') {
                            $maxattr = 0;
                            $stepany = $min = '';
                        }
                        $output.=' <input type = "hidden" name = "budgetforecastbs['.$item->batid.'][bfbsid]" value = "'.$forecast_expenses->bfbsid.'">';
                        $output.=' <input type = "hidden" name = "budgetforecastbs['.$item->batid.'][batid]" value = "'.$item->batid.'">';
                        $output .= '<td>'.parse_textfield('budgetforecastbs['.$item->batid.'][amount]', 'budgetforecastbs_'.$item->batid.'_'.$item->get_parent()->batid.'_'.$item->get_parent()->get_parent()->batid.'_subaccount', 'number', $budgetforecastexp[$item->batid], array('min' => $min, 'max' => $maxattr, 'required' => 'required', 'accept' => 'numeric', 'step' => $stepany)).'</td>';
                    }
                }
                else {
                    if(isset($options['forecastbalancesheet']) && !empty($options['forecastbalancesheet'])) {
                        $forecastbalancesheet = $options['forecastbalancesheet'];
                        $budgetforecastexp[$item->batid] = $forecastbalancesheet[$item->batid]['amount'];
                    }
                    $output .= '<td>'.$budgetforecastexp[$item->batid].'</td>';
                }
            }
            $output .='</tr>';
            // if($depth === 1) {
            //   $output.=' <td> <div id = "subtotal_'.$item->batid.'_'.$item->get_parent()->batid.'_'.$item->get_parent()->get_parent()->batid.'" style = "display:block;font-weight: bold;">'.$subtotal[$item->get_parent()->batid].'</div><input type = "hidden" value = ""id = "subtotal_'.$item->batid.'_'.$item->get_parent()->batid.'_'.$item->get_parent()->get_parent()->batid.'"></td>';
            //}
        }

        return $output;
    }

    public static function parse_accounts_old($accounts = array(), $options = array()) {

        global $template, $lang;

        if(is_array($accounts['type'])) {

            foreach($accounts['type'] as $acctype => $account) {
                switch($acctype) {
                    case'assets':
                        //  $account_items = self::get_account_items($account );
                        foreach($account as $acclevel => $accountitems) {
                            $head_account = $accountitems->get_headaccount();
                            if(!empty($head_account)) {

                                $items_output = self::parse_accountsitems($head_account, 0);
                            }
//                            if(method_exists($accountitems, get_children)) {
//                                $children = $accountitems->get_children();
//                            }
                        }


                        //eval("\$budgeting_forecast_balancesheet = \"".$template->get('budgeting_forecast_assets')."\";");
// $fields .= ' <div style = "display:inline-block; padding:7px;  border: grey solid 1px;">assets</div>';
                        break;
                    case'liabilities':

                        $fields .= ' <div style = "display:inline-block; padding:7px;  border: grey solid 1px;">liabilities</div>';
                        break;
                    case'equity':
                        $fields .=
                                ' <div style = "display:inline-block; padding:7px;  border: grey solid 1px;">equity</div>';
                        break;
                }
            }

            return $fields;
        }
    }

    public function get_createdby() {
        return new Users($this->data['createdBy']);
    }

}