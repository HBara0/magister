<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Travelmanager_Expenses_class.php
 * Created:        @tony.assaad    Sep 16, 2014 | 11:56:25 AM
 * Last Update:    @tony.assaad    Sep 16, 2014 | 11:56:25 AM
 */

/**
 * Description of Travelmanager_Expenses_class
 *
 * @author tony.assaad
 */
class Travelmanager_Expenses extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'tmeid';
    const TABLE_NAME = 'travelmanager_expenses';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;
    const SIMPLEQ_ATTRS = 'tmeid,tmetid,tmpsid,expectedAmt,currency,description';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

        global $db, $core;
        if(is_array($data)) {
            if($data['paidBy'] != 'anotheraff') {
                unset($data['paidById']);
            }
            if((!empty($data['expectedAmt']) && $data['expectedAmt'] != 0) && empty($data['currency'])) {
                $this->errorcode = 2;
                $errorhandler->record('requiredfields', 'Expenses Currency');
                return $this;
            }
            $data['createdOn'] = TIME_NOW;
            $query = $db->insert_query(self::TABLE_NAME, $data);
        }
    }

    public function save(array $data = array()) {
        global $core;
        if(empty($data)) {
            $data = $this->data;
        }
        if(!$this->validate_requiredfields($data)) {
            if(isset($data['tmeid']) && !empty($data['tmeid'])) {
                $expensesbypk = Travelmanager_Expenses::get_data(array('tmeid' => $data['tmeid']));
            }
            if(is_object($expensesbypk)) {
                $expensesbypk->update($data);
            }
            else {
                $expenses = Travelmanager_Expenses::get_data(array('tmpsid' => $data['tmpsid'], 'tmetid' => $data['tmetid']));
                if(is_object($expenses)) {
                    $expenses->update($data);
                }
                else {
                    $this->create($data);
                }
            }
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            if(empty($data['currency'])) {
                $this->errorcode = 2;
                return $this;
            }
            $expensestdata['expectedAmt'] = $data['expectedAmt'];
            $expensestdata['currency'] = $data['currency'];
            $expensestdata['description'] = $data['description'];
            $expensestdata['tmetid'] = $data['tmetid'];
            $expensestdata['paidBy'] = $data['paidBy'];
            if($expensestdata['paidBy'] == 'anotheraff') {
                $expensestdata['paidById'] = $data['paidById'];
            }
            $expensestdata['modifiedBy'] = $core->user['uid'];
            $expensestdata['modifiedOn'] = TIME_NOW;
            if(empty($expensestdata['currency'])) {
                $this->errorcode = 2;
                $errorhandler->record('requiredfields', 'Expenses Currency');
                return $this;
            }
            $db->update_query(self::TABLE_NAME, $expensestdata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        }
    }

    public function get_types() {
        return new Travelmanager_Expenses_Types($this->data['tmetid']);
    }

    public function get_createdBy() {
        return new Users($this->data['createdBy']);
    }

    public static function parse_expenses($sequence, $rowid, $expensestype, $destcity) {
        global $lang, $core, $template;
        if(is_array($expensestype)) {
            $segid = key($expensestype);
        }
        else {
            unset($expensestype);
        }
        $expenses_output_required_comments = '<span class=l"red_text">*</span>';
        $expenses_output_comments_requiredattr = ' required="required"';
//$expenses_output_comments_field = '<div style="display:block; padding:5px; text-align:left;  vertical-align: top;">expectedAmt'.$expenses_output_required_comments.'<textarea cols="25" rows="1" id="expenses_['.$expensestype['alteid'].'][description]" name="leaveexpenses['.$expensestype['alteid'].'][description]" '.$expenses_output_comments_requiredattr.'>'.$expensestype['description'].'</textarea></div>';
        $mainaffobj = new Affiliates($core->user['mainaffiliate']);
        $currencies[] = $destcity->get_country()->get_maincurrency();
        $currencies[] = $mainaffobj->get_country()->get_maincurrency();
        $currencies[] = new Currencies(840, true);
        $currencies[] = new Currencies(978, true);
        $currencies = array_unique($currencies);
        $currencies_list = parse_selectlist('segment['.$sequence.'][expenses]['.$rowid.'][currency]', '6', $currencies, $expensestype[$segid][$rowid][currency], '', '', array('id' => 'currency_'.$sequence.'_'.$rowid.'_list'));
        eval("\$expenses= \"".$template->get('travelmanager_expenses')."\";");
        return $expenses;
    }

    protected function validate_requiredfields(array $data = array()) {
        global $core, $db;
        if(is_array($data)) {
            $required_fields = array('expectedAmt', 'tmetid');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
            }
        }
    }

    public function get_convertedamount(Currencies $tocurrency) {
        if($this->currency == $tocurrency->numCode) {
            return $this->expectedAmt;
        }
        $fromcurrency = new Currencies($this->currency);
        $exchangerate = $tocurrency->get_latest_fxrate($tocurrency->alphaCode, array(), $fromcurrency->alphaCode);

        if(empty($exchangerate)) {
            $reverserate = $tocurrency->get_latest_fxrate($fromcurrency->alphaCode, array(), $tocurrency->alphaCode);
            if(!empty($reverserate)) {
                $exchangerate = 1 / $reverserate;
                $tocurrency->set_fx_rate($fromcurrency->numCode, $tocurrency->numCode, $exchangerate);
            }
        }
        return $this->expectedAmt * $exchangerate;
    }

    public function validate_foodandbeverage_expenses($data) {
        global $lang;
        $fromcurrency = new Currencies($data['currency']);
        $tocurrency = new Currencies('USD');
        $exchangerate = $tocurrency->get_latest_fxrate($tocurrency->alphaCode, array(), $fromcurrency->alphaCode);
        if(empty($exchangerate)) {
            $reverserate = $tocurrency->get_latest_fxrate($fromcurrency->alphaCode, array(), $tocurrency->alphaCode);
            if(!empty($reverserate)) {
                $exchangerate = 1 / $reverserate;
                $tocurrency->set_fx_rate($fromcurrency->numCode, $tocurrency->numCode, $exchangerate);
            }
        }
        if(!empty($data['numnights']) && $data['numnights'] != 0) {
            $data['amtperday'] = ($data['amount'] * $exchangerate) / $data['numnights'];
            if($data['amtperday'] > 50) {
                return '<p style="color:red">'.$lang->fandbwarning.'</p>';
            }
        }
    }

}