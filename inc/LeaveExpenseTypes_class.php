<?php
/*
 * Copyright � 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: LeaveExpenseTypes_class.php
 * Created:        @tony.assaad    Apr 9, 2014 | 2:38:39 PM
 * Last Update:    @tony.assaad    Apr 9, 2014 | 2:38:39 PM
 */

/**
 * Description of Leaves_expenses
 *
 * @author tony.assaad
 */
class LeaveExpenseTypes {
    private $expencetype = array();

    const PRIMARY_KEY = 'aletid';
    const TABLE_NAME = 'attendance_leaveexptypes';

    public function __construct($id = '', $simple = true) {
        if(isset($id) && !empty($id)) {
            $this->expencetype = $this->read($id, $simple);
        }
    }

    private function read($id, $simple = true) {
        global $db;
        if(empty($id)) {
            return false;
        }
        $query_select = '*';
        if($simple == true) {
            $query_select = 'aletid, name, title';
        }
        return $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public static function get_leaveexpensetypes($filters = array()) {
        global $db;

        $query = $db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME);
        if($db->num_rows($query) > 0) {
            while($expensetype = $db->fetch_assoc($query)) {
                $expensetypes[$expensetype[self::PRIMARY_KEY]] = $expensetype;
            }

            return $expensetypes;
        }
        return false;
    }

    public static function get_exptype_byattr($attr, $value, $simple = true) {
        global $db;

        if(!empty($value) && !empty($attr)) {
            $query = $db->query('SELECT '.self::PRIMARY_KEY.' FROM '.Tprefix.self::TABLE_NAME.' WHERE '.$db->escape_string($attr).'="'.$db->escape_string($value).'"');
            if($db->num_rows($query) > 1) {
                $items = array();
                while($item = $db->fetch_assoc($query)) {
                    $items[$item[self::PRIMARY_KEY]] = new self($item[self::PRIMARY_KEY], $simple);
                }
                $db->free_result($query);
                return $items;
            }
            else {
                if($db->num_rows($query) == 1) {
                    return new self($db->fetch_field($query, self::PRIMARY_KEY), $simple);
                }
                return false;
            }
        }
        return false;
    }
    public function get() {
        return $this->expencetype;
    }

}