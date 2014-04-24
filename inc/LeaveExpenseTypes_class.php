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

    /*
     * Get  Users for affililates that he can  HR and he is working with
     * @param	int		$core->user['hraffids'],$core->user['affiliates']
     * @return  Array
     */
    public static function get_viewablemanagers() {
        global $core, $db;
        $business_managers = array();
        if($core->usergroup['attendance_canViewExpenses'] == 1) {

            foreach($core->user['affiliates'] as $affid) {
                $aff_obj = new Affiliates($affid);
                $employees = $aff_obj->get_users();
                if(is_array($employees)) {
                    foreach($employees as $employee) {
                        $business_managers[$employee['uid']] = $employee['displayName'];
                    }
                }
            }
        }
        elseif($core->usergroup['attendace_canViewAllAffExpenses'] == 1) {
            $user_objs = Users::get_allusers();
            foreach($user_objs as $user_obj) {
                $employees = $user_obj->get();
                $business_managers[$employees['uid']] = $employees['displayName'];
            }
        }
        else {
            foreach($core->user['hraffids'] as $hraffid) {
                $aff_obj = new Affiliates($hraffid);

                $employees = $aff_obj->get_users();
                if(is_array($employees)) {
                    foreach($employees as $employee) {
                        $business_managers[$employee['uid']] = $employee['displayName'];
                    }
                }
            }
        }

        return $business_managers;
    }

    /*
     * Get  Affiliates  that he can  HR and he is working with
     * @param	int		$core->user['affiliates']
     * @return  Array
     */
    public static function get_viewableuseraffiliates() {
        global $db, $core;

        $afffiliates_users = $core->user['affiliates'] + $core->user['hraffids'];
        if(is_array($afffiliates_users)) {
            foreach($afffiliates_users as $affid => $affiliate) {
                $selected = '';
                $affiliate_obj = new Affiliates($affiliate);
                $affiliates_data = $affiliate_obj->get();
                $affiliates[$affiliates_data['affid']] = $affiliates_data['name'];
            }
            return $affiliates;
        }
    }

}