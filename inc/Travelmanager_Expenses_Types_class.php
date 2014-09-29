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

    public function parse_expensesfield($expensesoptions, $sequence, $rowid, $expensestype = array()) {
        global $db, $template, $lang;

        if(is_array($expensesoptions)) {  // if the object coming from the update mode.
            $this->data = $expensesoptions;
        }
        if(is_array($expensestype)) {
            $segid = key($expensestype);
        }

        if(is_array($this->data)) {
            foreach($this->data as $expenses) {
                print_R($expensestype[$segid][$rowid]);
                if(is_array($expensestype[$segid][$rowid]['selectedtype'])) {
                    if(in_array($expenses->tmetid, $expensestype[$segid][$rowid]['selectedtype'])) {
                        $selected = ' selected="selected"';
                    }
                }
                $expenses_details = Travelmanager_Expenses::parse_expenses($sequence, $rowid, $expensestype);
                $expenses_details.=$this->parse_paidby($sequence, $rowid, $segid, array('selectedpaidby' => $expensestype[$segid][$expenses->tmetid]['paidby'], 'selectedpaidid' => $expensestype[$segid][$expenses->tmetid]['paidbyid']));
                $onchange_actions = '$("#"+$(this).find(":selected").attr("itemref")+"_"+'.$sequence.'+"_"+'.$rowid.').show();';
                $expenses_options.='<option value='.$expenses->tmetid.' itemref='.$expenses->name.' '.$selected.'>'.$expenses->title.'</option>';
                $selected = '';
            }
        }
        eval("\$segments_expenses_output = \"".$template->get('travelmanager_expenses_types')."\";");
        $segments_expenses_output .='<hr>';
        return $segments_expenses_output;
    }

    public function parse_paidby($sequence, $rowid, $segid, $selectedoptions = array()) {
        global $lang;

        if(!empty($selectedoptions['selectedpaidby'])) {
            $selected_paidby[$segid][$rowid] = $selectedoptions['selectedpaidby'];
        }
        $paidby_entities = array(
                'myaffiliate' => $lang->myaffiliate,
                'supplier' => $lang->supplier,
                'client' => $lang->client,
                'myself' => $lang->myself,
                'anotheraff' => $lang->anotheraff
        );
        return '<div style="display:block;padding:8px;"  id="paidby"> Paid By '.parse_selectlist('segment['.$sequence.'][expenses]['.$rowid.'][entites]', 6, $paidby_entities, $selected_paidby[$segid], '', '$("#"+$(this).find(":selected").val()+ "_"+'.$sequence.'+"_"+'.$rowid.').effect("highlight", {color: "#D6EAAC"}, 1500).find("input").first().focus();;', array('id' => 'paidby')).'</div>';
    }

}