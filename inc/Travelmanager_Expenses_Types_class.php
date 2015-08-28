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
class TravelManager_Expenses_Types extends AbstractClass {
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

    public function parse_expensesfield($expensesoptions, $sequence, $rowid, $expensestype = array(), $options = array(), $segmentid = '0') {
        global $db, $template, $lang;
        $options['mode'] = 're';
        if(is_array($expensesoptions)) {  // if the object coming from the update mode.
            $this->data = $expensesoptions;
        }
        if(isset($options['mode']) && $options['mode'] != 'addrows') {
            if(is_array($expensestype) && !empty($expensestype)) {
                $segid = key($expensestype);
                $segmentexptype = key($expensestype[$segid][$rowid]);
            }
        }

        if(is_array($this->data)) {
            foreach($this->data as $expenses) {
                if(is_array($expensestype) && $options['mode'] != 'addrows') {
                    if(is_array($expensestype[$segid][$rowid]['selectedtype'])) {
                        if(in_array($expenses->tmetid, $expensestype[$segid][$rowid]['selectedtype'])) {
                            $selected = ' selected="selected"';
                        }
                    }
                    else {
                        if($expenses->title == 'Food & Beverage') {
                            $selected = ' selected="selected"';
                        }
                    }
                    $expenses_details = Travelmanager_Expenses::parse_expenses($sequence, $rowid, $expensestype, $options['destcity']);

                    // $expenses_details.=$this->parse_paidby($sequence, $rowid, $segid, array('selectedpaidby' => $expensestype[$segid][$segmentexptype]['paidby'], 'selectedpaidid' => $expensestype[$segid][$segmentexptype]['paidbyid']));
                    $expenses_details.=$this->parse_paidby($sequence, $rowid, $segid, array('selectedtype' => $expensestype[$segid][$rowid]['selectedtype'], 'expenses' => $expensestype));
                }
                else {
                    $expenses_details = Travelmanager_Expenses::parse_expenses($sequence, $rowid, '', $options['destcity']);

                    $expenses_details.=$this->parse_paidby($sequence, $rowid, $segid, array());
                }
                $expensestype[$segid][$segmentexptype]['paidby'] = '';
                $onchangepaidby = '$("#"+$(this).find(":selected").val()+ "_"+'.$sequence.'+"_"+'.$rowid.').effect("highlight", {color: "#D6EAAC"}, 1500).find("input").first().focus()';
                $onchange_actions = '$("#"+$(this).find(":selected").attr("itemref")+"_"+'.$sequence.'+"_"+'.$rowid.').show();';
                $expenses_options.='<option value='.$expenses->tmetid.' itemref='.$expenses->name.' '.$selected.'>'.$expenses->title.'</option>';
                $selected = $segmentexptype = '';
            }
        }
        $altrow = alt_row($altrow);
        /* hide Another affiliate input field */
        $expensestype[$sequence][$rowid]['display'] = $expensestype[$segid][$rowid]['display'];
        if(empty($expensestype[$sequence][$rowid]['display'])) {
            $expensestype[$sequence][$rowid]['display'] = "display:none;";
        }
        $display_exp = 'display:none';
        if($expensestype[$segmentid][$rowid][selectedtype][0] == '4') {
            $display_exp = '';
        }
        eval("\$segments_expenses_output = \"".$template->get('travelmanager_expenses_types')."\";");
        $expenses_detailspaidby = '';
        return $segments_expenses_output;
    }

    public function parse_paidby($sequence = '', $rowid = '', $segid = '', $expenses_options = array()) {
        global $lang;
//        if(!empty($selectedoptions['selectedpaidby'])) {
//            $selected_paidby[$segid][$rowid] = $selectedoptions['selectedpaidby'];
//        }

        $paidby_entities = array(
                'myaffiliate' => $lang->myaffiliate,
                'supplier' => $lang->supplier,
                'client' => $lang->client,
                'myself' => $lang->myself,
                'anotheraff' => $lang->anotheraff
        );

        foreach($paidby_entities as $val => $paidby) {
            if(!empty($expenses_options['expenses'][$segid][$rowid]['paidby'])) {
                $selected = '';
                if($expenses_options['expenses'][$segid][$rowid]['paidby'] === $val) {
                    $selected = ' selected="selected"';
                }
            }
            $paid_options.="<option value=".$val." {$selected}> {$paidby} </option>";
        }
        $onchange_actions = 'if($(this).find(":selected").val()=="anotheraff"){$("#"+$(this).find(":selected").val()+"_"+'.$sequence.'+"_"+'.$rowid.').effect("highlight", {color: "#D6EAAC"}, 1500).find("input").first().focus().val("");}else{$("#anotheraff_'.$sequence.'_'.$rowid.'").hide();}';
        // $onchange_actions = 'onchange="$("#"+$(this).find(":selected").val()+ "_"+'.$sequence.'+"_"+'.$rowid.').effect("highlight", {color: "#D6EAAC"}, 1500).find("input").first().focus();"';

        return "<div><div style='display:inline-block;width:20%;padding:5px;'>Paid By</div> <div style='display:inline-block;width:70%;'> <select id=segment_paidby_".$sequence."_".$rowid." name=segment[".$sequence."][expenses][".$rowid."][paidBy] onchange='".$onchange_actions."'>".$paid_options."</select></div></div>";
    }

}