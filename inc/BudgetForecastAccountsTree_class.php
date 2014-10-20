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
    const SIMPLEQ_ATTRS = 'batid, name,sequence, title,parent,accountlevel';
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
        return $this->get_data(array('parent' => $this->data[self::PRIMARY_KEY]), array('returnarray' => true));
    }

    public function get_headaccounts() {
        return $this->get_data(array('parent' => 0), array('order' => array('by' => 'sequence', 'sort' => 'ASC'), 'returnarray' => true));
    }

    public function get_parent($simple = true) {
        return new BudgetForecastAccountsTree($this->data['parent'], $simple);
    }

    public function parse_account($accountype, $options = array()) {


        $account_items = $this->get_headaccounts();

        if(!empty($account_items)) {
            foreach($account_items as $id => $item) {
                $accountitems_output.='<div style="display:inline-block;padding:5px; width:40%;">';
                $accountitems_output.='<table width="100%">';
                $accountitems_output .= $this->parse_accountsitems(array($id => $item), 0, array('financialbudget' => $options['financialbudget']->bfbid, 'mode' => $options['mode'], 'forecastbalancesheet' => $options['forecastbalancesheet']));
                $accountitems_output .= '<td><strong>Total of '.$item.': </strong> <div style="display:inline-block;font-weight:bold;font-size:14px;" id="total_'.$id.'_'.$item.'">'.$this->total[$id].'</div><input type="text" name="budgetforecastbs['.$item.'][total]" id="total_'.$id.'_'.$item.'" value="'.$this->total[$id].'"></input></td>';
                $accountitems_output .='</table>';
                $accountitems_output .= '</div>';
            }
        }

        return $accountitems_output;
    }

    private function parse_accountsitems($items, $depth, $options = array()) {
        global $template, $core;
        foreach($items as $id => $item) {
            switch($item->accountlevel) {
                case'heading':
                    $class = 'subtitle';
                    $item->name = ucwords($item->name);
            }
            if(!is_object($item)) {
                $item = self::get_data(array('batid' => $item['batid']));
            }

            $output.='<tr><td class="'.$class.'">'.$item->name.'</td>';

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
                    $output.=' <input type="hidden" name="budgetforecastbs['.$item->batid.'][bfbsid]" value="'.$forecast_expenses->bfbsid.'">';
                    $output.=' <input type="hidden" name="budgetforecastbs['.$item->batid.'][batid]" value="'.$item->batid.'">';
                    $output.='<td>'.parse_textfield('budgetforecastbs['.$item->batid.'][amount]', 'budgetforecastbs_'.$item->batid.'_'.$item->get_parent()->batid.'_'.$item->get_parent()->get_parent()->batid.'_subaccount', 'number', $budgetforecastexp[$item->batid], array('required' => 'required', 'accept' => 'numeric', 'step' => 'any')).'</td>';
                }
                else {
                    if(isset($options['forecastbalancesheet']) && !empty($options['forecastbalancesheet'])) {
                        $forecastbalancesheet = $options['forecastbalancesheet'];
                        $budgetforecastexp[$item->batid] = $forecastbalancesheet[$item->batid]['amount'];
                    }
                    $output.='<td>'.$budgetforecastexp[$item->batid].'</td>';
                }
            }
            $output .='</tr>';

            // if($depth === 1) {
            //   $output.=' <td> <div id="subtotal_'.$item->batid.'_'.$item->get_parent()->batid.'_'.$item->get_parent()->get_parent()->batid.'" style="display:block;font-weight: bold;">'.$subtotal[$item->get_parent()->batid].'</div><input type="hidden"  value=""id="subtotal_'.$item->batid.'_'.$item->get_parent()->batid.'_'.$item->get_parent()->get_parent()->batid.'"></td>';
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
                        $fields .= ' <div style = "display:inline-block; padding:7px;  border: grey solid 1px;">equity</div>';
                        break;
                }
            }

            return $fields;
        }
    }

    public function get_createdby() {
        return new Users($this->data['createdBy'
        ]);
    }

}