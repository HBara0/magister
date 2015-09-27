<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetHeadCount.php
 * Created:        @rasha.aboushakra    Sep 30, 2014 | 2:58:44 PM
 * Last Update:    @rasha.aboushakra    Sep 30, 2014 | 2:58:44 PM
 */

class BudgetHeadCount extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'bhcid';
    const TABLE_NAME = 'budgeting_headcount';
    const DISPLAY_NAME = 'bhcid';
    const SIMPLEQ_ATTRS = 'bhcid, bfbid, posgid, actualPrevThreeYears,actualPrevTwoYears,yefPrevYear,budgetCurrent'; //actualPrevYear, budgetPrevYear
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $required_fields = array('bfbid', 'posgid', 'actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent'); //'actualPrevYear', 'budgetPrevYear',
            foreach($required_fields as $field) {
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
                $headcounts_data[$field] = $data[$field];
            }
            $headcounts_data['createdOn'] = TIME_NOW;
            $headcounts_data['createdBy'] = $core->user['uid'];
            $query = $db->insert_query(self::TABLE_NAME, $headcounts_data);
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $required_fields = array('bfbid', 'posgid', 'actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent'); //'actualPrevYear', 'budgetPrevYear',
            foreach($required_fields as $field) {
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
                $headcounts_data[$field] = $data[$field];
            }
            $headcounts_data['modifiedOn'] = TIME_NOW;
            $headcounts_data['modifiedBy'] = $core->user['uid'];
            $db->update_query(self::TABLE_NAME, $headcounts_data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        }
    }

    public function save(array $data = array()) {
        if(empty($data)) {
            $data = $this->data;
        }
        if(!$this->validate_requiredfields($data)) {
            $headcounts = self::get_data(array('bhcid' => $this->data[self::PRIMARY_KEY]));
            if(is_object($headcounts)) {
                $headcounts->update($data);
            }
            else {
                $headcounts = self::get_data(array('bfbid' => $data['bfbid'], 'posgid' => $data['posgid']));
                if(is_object($headcounts)) {
                    $headcounts->update($data);
                }
                else {
                    $this->create($data);
                }
            }
        }
    }

    public static function parse_headcountfields($positiongroups, $options = array()) {
        global $template, $lang;

        if(is_array($positiongroups)) {
            foreach($positiongroups as $group) {
                unset($subtotal, $headcount);
                $fields = array('actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent');
                $group_headcount = self::get_data(array('posgid' => $group->posgid, 'bfbid' => $options['financialbudget']->bfbid), array('simple' => false));
                $headcount['actualPrevThreeYears'] = $headcount['actualPrevTwoYears'] = $headcount['yefPrevYear'] = $headcount['budgetCurrent'] = 0;
                if(is_object($group_headcount)) {
                    foreach($fields as $field) {
                        $headcount[$field] = $group_headcount->$field;
                        $total[$field] +=$group_headcount->$field;
                    }
                }
                else if(is_object($options['prevfinancialbudget'])) {
                    $prevyear_headcount = self::get_data(array('posgid' => $group->posgid, 'bfbid' => $options['prevfinancialbudget']->bfbid), array('simple' => false));
                    if(isset($prevyear_headcount->budgetCurrent) && !empty($prevyear_headcount->budgetCurrent)) {
                        $disabledfield = 'readonly';
                        $headcount['budgetPrevYear'] = $prevyear_headcount->budgetCurrent;
                        $total['budgetPrevYear'] +=$headcount['budgetPrevYear'];
                    }
                }
                foreach($fields as $input) {
                    if(isset($options['mode']) && $options['mode'] == 'fill') {
                        if($input == 'budgetPrevYear') {
                            $readonly = $disabledfield;
                        }
                        $column_output .=' <td style="width:10%">'.parse_textfield('headcount['.$group->posgid.']['.$input.']', 'headcount_'.$input, 'number', $headcount[$input], array('accept' => 'numeric', 'step' => '1', 'style' => 'width:100%;', 'min' => 0)).'</td>'; //$readonly => $readonly,
                        unset($readonly);
                    }
                    else {
                        if(isset($options['headcount']) && !empty($options['headcount'])) {
                            $headcount = $options['headcount'];
                            $headcount[$input] = $headcount[$group->posgid][$input];
                        }
                        $column_output .=' <td style="width:10%">'.$headcount[$input].'</td>';
                        $total2[$input] += $headcount[$group->posgid][$input];
                        $total[$input] = $total2[$input];
                    }
                }
                eval("\$budgeting_group_headcount .= \"".$template->get('budgeting_group_headcount')."\";");
                $field_output = $column_output = $disabledfield = '';
            }
        }
        eval("\$budgeting_total_headcount  .= \"".$template->get('budgeting_total_headcount')."\";");
        return $budgeting_total_headcount;
    }

    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('bfbid', 'posgid', 'actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

}
?>