<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AttLeavesApproval.php
 * Created:        @zaher.reda    May 2, 2014 | 5:17:24 PM
 * Last Update:    @zaher.reda    May 2, 2014 | 5:17:24 PM
 */

/**
 * Description of AttLeavesApproval
 *
 * @author zaher.reda
 */
class AttLeavesApproval {
    //put your code here
    private $approval;

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->approval = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.'leavesapproval WHERE laid='.intval($id)));
    }

    public function get_user() {
        return new Users($this->approval['uid']);
    }

    public function get_leave() {
        return new Leaves($this->approval['lid']);
    }

    public static function get_approvals($filters = '') {
        global $db;
        if(!empty($filters)) {
            $filters = ' WHERE '.$db->escape_string($filters);
        }
        $query = $db->query('SELECT laid FROM '.Tprefix.'leavesapproval'.$filters);
        $rows_counts = $db->num_rows($query);
        if($rows_counts > 0) {
            if($rows_counts > 1) {
                while($approval = $db->fetch_assoc($query)) {
                    $approvals[$approval['laid']] = new AttLeavesApproval($approval['laid']);
                }
                return $approvals;
            }
            else {
                $id = $db->fetch_field($query, 'laid');
                return new AttLeavesApproval($id);
            }
        }
        return false;
    }

    public static function get_approvals_byattr($attr, $value) {
        global $db;

        if(!empty($value) && !empty($attr)) {
            $query = $db->query('SELECT laid FROM '.Tprefix.'leavesapproval WHERE '.$db->escape_string($attr).'="'.$db->escape_string($value).'"');
            if($db->num_rows($query) > 1) {
                $approvals = array();
                while($approval = $db->fetch_assoc($query)) {
                    $approvals[$approval['laid']] = new AttLeavesApproval($approval['laid']);
                }
                $db->free_result($query);
                return $approvals;
            }
            else {
                if($db->num_rows($query) == 1) {
                    return new AttLeavesApproval($db->fetch_field($query, 'laid'));
                }
                return false;
            }
        }
        return false;
    }

    public function is_apporved() {
        if($this->approval['isApproved'] == 1) {
            return true;
        }
        return false;
    }

    public function get() {
        return $this->approval;
    }

}