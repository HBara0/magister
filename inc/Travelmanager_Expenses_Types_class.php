<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Travelmanager_Expenses_Types_class.php
 * Created:        @tony.assaad    Sep 16, 2014 | 11:54:24 AM
 * Last Update:    @tony.assaad    Sep 16, 2014 | 11:54:24 AM
 */

/**
 * Description of Travelmanager_Expenses_Types_class
 *
 * @author tony.assaad
 */
class Travelmanager_Expenses_Types extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'tmetid';
    const TABLE_NAME = 'travelmanager_expenses_types';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;
    const SIMPLEQ_ATTRS = 'tmetid, title, name';

    public function __construct($id = '', $simple = true) {
        global $db;
        if(empty($id)) {
            $exp_query = $db->query("SELECT tmetid FROM ".Tprefix.self::TABLE_NAME);
            if($db->num_rows($exp_query) > 0) {
                while($expense = $db->fetch_assoc($exp_query)) {
                    $this->data[$expense[self::PRIMARY_KEY]] = new Travelmanager_Expenses_Types($expense[self::PRIMARY_KEY]);
                }
                if(is_array($this->data)) {
                    return $this->data;
                }
                return false;
            }
            return false;
        }
        else {
            parent::__construct($id, $simple);
        }
    }

    protected function create(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

    public function get_createdBy() {
        return new Users($this->data['createdBy']);
    }

    public function parse_expensesfield($sequence, $rowid) {
        global $db, $template, $lang;
        //$rowid++;
        foreach($this->data as $expenses) {
            $expenses_details = Travelmanager_Expenses::parse_expenses($sequence, $rowid);
            $expenses_options.='<option value='.$expenses->tmetid.'>'.$expenses->title.'</option>';
        }
        eval("\$segments_expenses_output = \"".$template->get('travelmanager_expenses_types')."\";");
        return $segments_expenses_output;
    }

}