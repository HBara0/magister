<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * Leaves Expenses Class
 * $id: LeavesExpenses_class.php
 * Created:        @zaher.reda    Apr 30, 2014 | 10:55:08 AM
 * Last Update:    @zaher.reda    Apr 30, 2014 | 10:55:08 AM
 */

class LeavesExpenses {
    private $expense = array();

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->expense = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.'attendance_leaves_expenses WHERE aleid='.intval($id)));
    }

    public function get_leave() {
        return new Leaves($this->expense['lid']);
    }

    public function get_expensetype() {
        return new LeaveExpenseTypes($this->expense['alteid']);
    }

    public function get_currency() {
        return new Currencies($this->expense['currency']);
    }

    /*
     * Get  Users for affililates that he can  HR and he is working with
     * @return  Array
     */
    public static function get_viewableusers() {
        global $core;

        $users = array();
        if($core->usergroup['attendance_canViewExpenses'] == 1) {
            foreach($core->user['affiliates'] as $affid) {
                $aff_obj = new Affiliates($affid);
                $employees = $aff_obj->get_users(array('allusers' => true));
                if(is_array($employees)) {
                    foreach($employees as $employee) {
                        $users[$employee['uid']] = $employee['displayName'];
                    }
                }
            }
        }
        elseif($core->usergroup['attendace_canViewAllAffExpenses'] == 1) {
            $user_objs = Users::get_data(null, array('order' => 'displayName'));
            foreach($user_objs as $user_obj) {
                $employees = $user_obj->get();
                $users[$employees['uid']] = $employees['displayName'];
            }
        }
        else {
            if(is_array($core->user['hraffids'])) { /* if the user  HR any affiliate */
                foreach($core->user['hraffids'] as $hraffid) {
                    $aff_obj = new Affiliates($hraffid);

                    $employees = $aff_obj->get_users(array('allusers' => true));
                    if(is_array($employees)) {
                        foreach($employees as $employee) {
                            $users[$employee['uid']] = $employee['displayName'];
                        }
                    }
                }
            }
            else {
                $users[$core->user['uid']] = $core->user['displayName'];
            }
        }
        return $users;
    }

    public function get() {
        return $this->expense;
    }

}