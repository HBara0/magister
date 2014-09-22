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
    protected $errorcode = 0;

    const PRIMARY_KEY = 'tmeid';
    const TABLE_NAME = 'travelmanager_expenses';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;
    const SIMPLEQ_ATTRS = 'tmeid, description';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

        global $db, $core;
        if(is_array($data)) {
            $data['createdOn'] = TIME_NOW;
            $query = $db->insert_query(self::TABLE_NAME, $data);
        }
    }

    public function save(array $data = array()) {
        global $core;
        if(empty($data)) {
            $data = $this->data;
        }
        $expenses = Travelmanager_Expenses::get_data(array('tmpsid' => $data['tmpsid'], 'tmetid' => $data['tmetid']));
        if(is_object($expenses)) {
            $expenses->update($data);
        }
        else {
            $this->create($data);
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $expensestdata['expectedAmt'] = $data['expectedAmt'];
            $expensestdata['currency'] = $data['currency'];
            $expensestdata['description'] = $data['description'];
            $expensestdata['paidBy'] = $data['paidBy'];
            $expensestdata['paidByEntity'] = $data['paidByEntity'];
            $expensestdata['modifiedBy'] = $core->user['uid'];
            $expensestdata['modifiedOn'] = TIME_NOW;
            $db->update_query(self::TABLE_NAME, $expensestdata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        }
    }

    public function get_types() {
        return new Travelmanager_Expenses_Types($this->data['tmetid']);
    }

    public function get_createdBy() {
        return new Users($this->data['createdBy']);
    }

    public static function parse_expenses($sequence, $rowid) {
        global $lang, $template;

        $expenses_output_required_comments = '<span class=l"red_text">*</span>';
        $expenses_output_comments_requiredattr = ' required="required"';
//$expenses_output_comments_field = '<div style="display:block; padding:5px; text-align:left;  vertical-align: top;">expectedAmt'.$expenses_output_required_comments.'<textarea cols="25" rows="1" id="expenses_['.$expensestype['alteid'].'][description]" name="leaveexpenses['.$expensestype['alteid'].'][description]" '.$expenses_output_comments_requiredattr.'>'.$expensestype['description'].'</textarea></div>';

        eval("\$expenses= \"".$template->get('travelmanager_expenses')."\";");
        return $expenses;
    }

}