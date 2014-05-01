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
            $query_select = 'aletid, name, title, title AS name';
        }
        return $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'attendance_leaveexptypes WHERE aletid='.intval($id)));
    }

    public static function get_leaveexpensetypes($filters = array()) {
        global $db;

        $query = $db->query('SELECT * FROM '.Tprefix.'attendance_leaveexptypes');
        if($db->num_rows($query) > 0) {
            while($expensetype = $db->fetch_assoc($query)) {
                $expensetypes[$expensetype['aletid']] = $expensetype;
            }

            return $expensetypes;
        }
        return false;
    }

    public function get() {
        return $this->expencetype;
    }

}