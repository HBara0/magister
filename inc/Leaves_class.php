<?php
/*
 * Copyright � 2013 Orkila International Offshore, All Rights Reserved
 * 
 * Leaves Class
 * $id: Leave.php
 * Created:        @tony.assaad    May 29, 2013 | 2:17:27 PM
 * Last Update:    @tony.assaad    June 21, 2013 | 12:17:27 PM
 */

class Leaves {
    private $errorcode = 0; //0=No errors;1=Subject missing;2=Entry exists;3=Error saving;4=validation violation
    private $leave = array();

    public function __construct($leavedata = array(), $simple = true) {
        global $db;
        if(!is_array($leavedata) && !empty($leavedata)) {
            $this->leave = $this->read($leavedata, $simple);
        }
        else {
            if(isset($leavedata['lid']) && !empty($leavedata['lid'])) {
                $this->leave = $this->read($leavedata['lid'], $simple);
            }
        }
    }

    public function has_expenses($id = '') {
        global $db;

        if(!empty($this->leave['lid']) && empty($id)) {
            $id = $this->leave['lid'];
        }

        if(value_exists('attendance_leaves_expenses', 'lid', $db->escape_string($id))) {
            return true;
        }
        else {
            return false;
        }
    }

    public function get_expenses($id = '') {
        global $db;

        if(!empty($this->leave['lid']) && empty($id)) {
            $id = $this->leave['lid'];
        }

        $leaveexptype_query = $db->query('SELECT * FROM '.Tprefix.'attendance_leaves_expenses WHERE lid='.$db->escape_string($id));
        if($db->num_rows($leaveexptype_query) > 0) {
            while($leaveexpenses = $db->fetch_assoc($leaveexptype_query)) {
                $leaveexpense[$leaveexpenses['aleid']] = $leaveexpenses;
            }
            if(is_array($leaveexpense)) {
                return $leaveexpense;
            }
            return false;
        }
        return false;
    }

    public function get_expensesdetails($id = '') {
        global $db;

        if(!empty($this->leave['lid']) && empty($id)) {
            $id = $this->leave['lid'];
        }

        $leaveexpdetails_query = $db->query('SELECT ale.*, alte.*, alet.*
										FROM '.Tprefix.'attendance_leaves_expenses ale
										JOIN '.Tprefix.'attendance_leavetypes_expenses alte ON (alte.alteid=ale.alteid)
										JOIN '.Tprefix.'attendance_leaveexptypes alet ON (alet.aletid=alte.aletid)
										WHERE ale.lid='.$db->escape_string($id).' ORDER BY hasComments DESC');
        if($db->num_rows($leaveexpdetails_query) > 0) {
            while($expensesdetail = $db->fetch_assoc($leaveexpdetails_query)) {
                $expensesdetails[$expensesdetail['alteid']] = $expensesdetail;
            }
            if(is_array($expensesdetails)) {
                return $expensesdetails;
            }
            return false;
        }
        return false;
    }

    public function get_expensestotal($id = '', $amounttype = 'expected', $currency = '') {
        global $db;

        if(!empty($this->leave['lid']) && empty($id)) {
            $id = $this->leave['lid'];
        }

        if($this->has_expenses()) {
            $total = 0;
            $expenses_query = $db->query('SELECT *
									FROM '.Tprefix.'attendance_leaves_expenses
									WHERE lid='.$db->escape_string($id));
            while($expense = $db->fetch_assoc($expenses_query)) {
                /* To implement: convert from currency to parameter currency */
                if($amounttype == 'actual') {
                    $total += $expense['actualAmt'];
                }
                else {
                    $total += $expense['expectedAmt'];
                }
            }
            return $total;
        }
        return false;
    }

    public function create_expenses($expenses = array()) {
        global $db, $log;

        if(is_array($expenses)) {
            foreach($expenses as $alteid => $expense) {
                if(!isset($this->leave['ltid'])) {
                    $this->leave['ltid'] = $db->fetch_field($db->query("SELECT ltid FROM ".Tprefix."attendance_leavetypes_expenses WHERE alteid=".$db->escape_string($alteid)), 'ltid');
                }

                $leavetype = $this->get_leavetype();
                $expenses_types = $leavetype->get_expenses();
                /* if empty and type is required */

                if($expense['expectedAmt'] == '') {
                    $expense['expectedAmt'] = 0;
                }

                $expenses_data = array('alteid' => $alteid,
                        'lid' => $this->leave['lid'],
                        'expectedAmt' => $expense['expectedAmt'],
                        'currency' => $expense['currency'],
                        'description' => $expense['description'],
                        'usdFxrate' => '1' //Hard coded for now given USD currency
                );
                $query = $db->insert_query('attendance_leaves_expenses', $expenses_data);
                if(!$query) {
                    //Record Error
                }
            }
            $log->record($this->leave['lid'], 'addedexpenses');
            $this->errorcode = 0;
        }
        return false;
    }

    public function update_leaveexpenses(array $leaveexpenses_data) {
        global $db, $log;

        if(is_array($leaveexpenses_data)) {
            foreach($leaveexpenses_data as $alteid => $expense) {
                $alteid = $db->escape_string($alteid);
                $leavetype = $this->get_leavetype();
                $expenses_types = $leavetype->get_expenses();

                if($expense['expectedAmt'] == '') {
                    $expense['expectedAmt'] = 0;
                }
                if(value_exists('attendance_leaves_expenses', 'lid', $this->leave['lid'], 'alteid='.$alteid)) {
                    $db->update_query('attendance_leaves_expenses', $expense, 'lid='.$this->leave['lid'].' AND alteid='.$alteid);
                }
                else {
                    $expense['lid'] = $this->leave['lid'];
                    $expense['alteid'] = $alteid;
                    $db->insert_query('attendance_leaves_expenses', $expense);
                }
            }
            /* Remove unrelated expenses - in case the type has changed */
            $db->delete_query('attendance_leaves_expenses', 'lid='.$this->leave['lid'].' AND alteid NOT IN ('.implode(',', array_keys($leaveexpenses_data)).')');
            $log->record($this->leave['lid'], 'updatedexpenses');
        }
    }

    private function read($id, $simple = true) {
        global $db;

        if(empty($id)) {
            return false;
        }
        $query_select = '*';
        if($simple == true) {
            $query_select = 'lid, uid, type, fromDate, toDate';
        }

        return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."leaves WHERE lid=".$db->escape_string($id)));
    }

    public function get_approvers($isapproved = 0) {
        global $db;
        if($isapproved == 1) {
            $where_isapproved = ' WHERE isApproved=1';
        }
        else {
            $where_isapproved = ' WHERE isApproved=0';
        }
        $query = $db->query('SELECT * FROM '.Tprefix.'leavesapproval  '.$where_isapproved.' AND lid='.$this->leave['lid']);
        if($db->num_rows($query) > 0) {
            while($approver = $db->fetch_assoc($query)) {
                $approvers[$approver['uid']] = new Users($approver['uid']);
            }
            return $approvers;
        }
        return false;
    }

    public static function get_leaves_expencesdata($data_filter = array()) {
        global $db;

        if(isset($data_filter['affids']) && (!empty($data_filter['affids']))) {
            $where = " 	WHERE l.affid IN (".implode(',', $data_filter['affids']).") ";
        }
        if(isset($data_filter['employees']) && (!empty($data_filter['employees']))) {
            $where .= " AND l.uid IN (".implode(',', $data_filter['employees']).") ";
        }
        if(isset($data_filter['leavetype']) && (!empty($data_filter['leavetype']))) {
            $where .= "  AND l.type IN (".implode(',', $data_filter['leavetype']).") ";
        }
        if(isset($data_filter['leaveexptype']) && (!empty($data_filter['leaveexptype']))) {
            $where .= "  AND  lextt.aletid IN (".implode(',', $data_filter['leaveexptype']).") ";
        }
        //if(isset($data_filter['affids'], $data_filter['employees'], $data_filter['leavetype'], $data_filter['leaveexptype']) && !is_empty($data_filter['affids'], $data_filter['employees'], $data_filter['leavetype'], $data_filter['leaveexptype'])) {

        $query = $db->query("SELECT l.lid, l.uid,l.affid,l.spid,l.cid,lt.title,lt.ltid,lextt.aletid,lext.aleid,lext.alteid,lext.expectedAmt ,lext.actualAmt   FROM ".Tprefix."leaves  l
								JOIN ".Tprefix." leavetypes lt ON(l.type=lt.ltid)
								JOIN ".Tprefix." attendance_leaves_expenses lext ON(lext.lid=l.lid)
								 JOIN ".Tprefix."attendance_leavetypes_expenses letexp ON(letexp.alteid=lext.alteid)
								JOIN ".Tprefix."attendance_leaveexptypes lextt ON(lextt.aletid=letexp.aletid)
								{$where}");

        if($db->num_rows($query) > 0) {
            while($rowsdata = $db->fetch_assoc($query)) {
                $leavexpencesdata[$rowsdata['aleid']] = $rowsdata;
            }
            return $leavexpencesdata;
        }
        //}
    }

    public function get_leavemessages() {
        global $db;
        /* apply view permission */
        $query = $db->query("SELECT lmid FROM ".Tprefix."leaves_messages WHERE lid='".$this->leave['lid']."'");
        if($db->num_rows($query) > 0) {
            while($leavemessage = $db->fetch_assoc($query)) {
                $leavemessages[$leavemessage['lmid']] = new LeavesMessages($leavemessage['lmid']);
            }
            return $leavemessages;
        }
        return false;
    }

    public function get_requester() {
        return new Users($this->leave['uid']);
    }

    public function is_leaverequester() {
        global $core;
        if(value_exists('leaves', 'uid', $this->leave['uid'], ' lid='.$this->leave['lid'])) {
            return true;
        }
        return false;
    }

    public function get_type($simple = true) {
        return $this->get_leavetype($simple);
    }

    public function get_leavetype($simple = true) {
        return new Leavetypes($this->leave['type'], $simple);
    }

    public function get_workingdays() {
        return count_workingdays($this->leave['uid'], $this->leave['fromDate'], $this->leave['toDate'], $this->get_type()['isWholeDay']);
    }

    public function get() {
        return $this->leave;
    }

    public function get_errorcode() {
        $this->errorcode;
    }

}
?>
