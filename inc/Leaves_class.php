<?php
/*
 * Copyright � 2013 Orkila International Offshore, All Rights Reserved
 *
 * Leaves Class
 * $id: Leave.php
 * Created:        @tony.assaad    May 29, 2013 | 2:17:27 PM
 * Last Update:    @tony.assaad    June 21, 2013 | 12:17:27 PM
 */

class Leaves extends AbstractClass {
    protected $errorcode = 0; //0=No errors;1=Subject missing;2=Entry exists;3=Error saving;4=validation violation
    protected $data = array();

    const PRIMARY_KEY = 'lid';
    const TABLE_NAME = 'leaves';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'lid, uid, type, requestKey, fromDate, toDate';
    const CLASSNAME = __CLASS__;

    public function __construct($id = array(), $simple = true) {
        if(!is_array($id) && !empty($id)) {
            parent::__construct($id, $simple);
        }
        else {
            if(isset($id['lid']) && !empty($id['lid'])) {
                parent::__construct($id['lid'], $simple);
            }
        }
    }

    public function get_segment() {
        if(!empty($this->data['psid'])) {
            return new ProductsSegments($this->data['psid']);
        }
        return false;
    }

    public function has_expenses($id = '') {
        global $db;

        if(!empty($this->data['lid']) && empty($id)) {
            $id = $this->data['lid'];
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

        if(!empty($this->data['lid']) && empty($id)) {
            $id = $this->data['lid'];
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

        if(!empty($this->data['lid']) && empty($id)) {
            $id = $this->data['lid'];
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

        if(!empty($this->data['lid']) && empty($id)) {
            $id = $this->data['lid'];
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
                if(!isset($this->data['ltid'])) {
                    $this->data['ltid'] = $db->fetch_field($db->query("SELECT ltid FROM ".Tprefix."attendance_leavetypes_expenses WHERE alteid=".$db->escape_string($alteid)), 'ltid');
                }

                $leavetype = $this->get_leavetype();
                $expenses_types = $leavetype->get_expenses();
                /* if empty and type is required */

                if($expense['expectedAmt'] == '') {
                    $expense['expectedAmt'] = 0;
                }

                $expenses_data = array('alteid' => $alteid,
                        'lid' => $this->data['lid'],
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
            $log->record($this->data['lid'], 'addedexpenses');
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
                if(value_exists('attendance_leaves_expenses', 'lid', $this->data['lid'], 'alteid='.$alteid)) {
                    $db->update_query('attendance_leaves_expenses', $expense, 'lid='.$this->data['lid'].' AND alteid='.$alteid);
                }
                else {
                    $expense['lid'] = $this->data['lid'];
                    $expense['alteid'] = $alteid;
                    $db->insert_query('attendance_leaves_expenses', $expense);
                }
            }
            /* Remove unrelated expenses - in case the type has changed */
            $db->delete_query('attendance_leaves_expenses', 'lid='.$this->data['lid'].' AND alteid NOT IN ('.implode(',', array_keys($leaveexpenses_data)).')');
            $log->record($this->data['lid'], 'updatedexpenses');
        }
    }

    public function is_approved() {
        $approvals = $this->get_approvals(0);
        if(count($approvals) == 0 || $approvals == false) {
            return true;
        }
        return false;
    }

    public function get_approval_byappover($approver) {
        return AttLeavesApproval::get_approvals('lid='.$this->data['lid'].' AND uid='.intval($approver));
    }

    public function get_toapprove() {
        return $this->get_approvers();
    }

    public function get_approvers(array $config = array()) {
        return AttLeavesApproval::get_data(array('lid' => $this->data['lid']), $config);
    }

    public function get_approvals($isapproved = 1) {
        global $db;
        if($isapproved == 1) {
            $where_isapproved = ' WHERE isApproved=1';
        }
        else {
            $where_isapproved = ' WHERE isApproved=0';
        }
        $query = $db->query('SELECT * FROM '.Tprefix.'leavesapproval '.$where_isapproved.' AND lid='.$this->data['lid']);
        if($db->num_rows($query) > 0) {
            while($approver = $db->fetch_assoc($query)) {
                $approvers[$approver['uid']] = new Users($approver['uid']);
            }
            return $approvers;
        }
        return false;
    }

    public function generate_approvalchain() {
        $type = $this->get_leavetype(false);
        $toapprove = unserialize($type->toApprove);

        if(is_array($toapprove)) {
            $requester = $this->get_requester();
            $affiliate = $requester->get_mainaffiliate(false);
            foreach($toapprove as $key => $val) {
                switch($val) {
                    case 'reportsTo':
                        $approvers['reportsTo'] = $requester->reportsTo;
                        break;
                    case 'generalManager':
                        $approvers['generalManager'] = $affiliate->get_generalmanager()->uid;
                        break;
                    case 'hrManager':
                        $approvers['hrManager'] = $affiliate->get_hrmanager()->uid;
                        break;
                    case 'supervisor':
                        $approvers['supervisor'] = $affiliate->get_supervisor()->uid;
                        break;
                    case 'segmentCoordinator':
                        /* If leave has segment selected */
                        if(is_object($this->get_segment())) {
                            $leave_segmobjs = $this->get_segment();
                            $leave_segment_coordinatorobjs = $leave_segmobjs->get_coordinators();
                            if(is_array($leave_segment_coordinatorobjs)) {
                                $leave_segment_coordinatorobj = $leave_segment_coordinatorobjs[array_rand($leave_segment_coordinatorobjs, 1)];
                                $approvers['segmentCoordinator'] = $leave_segment_coordinatorobj->get_coordinator()->get()['uid'];
                            }
                        }
                        break;
                    case 'financialManager':
                        $approvers['financialManager'] = $affiliate->get_financialemanager()->uid;
                        break;
                    default:
                        if(is_int($val)) {
                            $approvers[$val] = $val;
                        }
                        break;
                }
            }
            /* Make list of approvers unique */
            $approvers = array_unique($approvers);

            /* Remove the user himself from the approval chain */
            unset($approvers[array_search($requester->uid, $approvers)]);

            return $approvers;
        }
        return null;
    }

    public function create_approvalchain($approvers = null) {
        global $core;

        if(empty($approvers)) {
            $approvers = $this->generate_approvalchain();
        }
        $approve_immediately = $this->should_approveimmediately();
        foreach($approvers as $key => $val) {
            if($key != 'reportsTo' && $val == $approvers['reportsTo']) {
                continue;
            }

            $approve_status = $timeapproved = 0;
            if(($val == $core->user['uid'] && $approve_immediately == true) || ($approve_immediately == true && $key == 'reportsTo' && $core->user['uid'] == $this->get_requester()->get_reportsto()->uid)) {
                if($val == $core->user['uid']) {
                    $approve_immediately = true;
                }
                $approve_status = 1;
                $timeapproved = TIME_NOW;
            }

            $sequence = 1;
            if(is_array($toapprove)) {
                $sequence = array_search($key, $toapprove);
            }

            $approver = new AttLeavesApproval();
            $approver->set(array('lid' => $this->lid, 'uid' => $val, 'isApproved' => $approve_status, 'timeApproved' => $timeapproved, 'sequence' => $sequence));
            $approver->save();
        }
        return true;
    }

    public function should_approveimmediately() {
        global $core;

        $requester = $this->get_requester();
        $reportsto = $requester->get_reportsto();
        $leavetype = $this->get_type(false);
        $approve_immediately = false;

        if($core->user['uid'] == $this->uid) {
            return $approve_immediately;
        }

        $is_onbehalf = false;
        if($core->user['uid'] != $this->uid) {
            $is_onbehalf = true;
        }

        if($is_onbehalf == true) {
            if($core->user['uid'] == $reportsto->uid || $core->usergroup['attenance_canApproveAllLeaves'] == 1 || !is_object($requester->get_reportsto())) {
                $approve_immediately = true; //To be fully implemented at second stage
            }
        }
        else {
            if(empty($reportsto->uid)) {
                $approve_immediately = true;
            }
        }

        if($leavetype->isBusiness == 1) {
            $approve_immediately = false;
        }

        if(!isset($leavetype->toApprove) || empty($leavetype->toApprove)) {
            $approve_immediately = true;
        }

        return $approve_immediately;
    }

    public function get_firstapprover() {
        return $this->get_approvers(array('order' => array('sort' => 'ASC', 'by' => 'sequence'), 'limit' => '0, 1'));
    }

    public static function get_leaves_expencesdata($data_filter = array(), array $config = array()) {
        global $db;

        $tables_alias = array('aletid' => 'lextt');

        $fromDate = strtotime($data_filter['fromDate']);
        $toDate = strtotime($data_filter['toDate']);
        if($fromDate > $toDate) {
            return false;
        }

        $allowed_filters = array('uid', 'type', 'aletid', 'useraffid', 'fromDate', 'toDate', 'requestTime');
        $allowed_filters_configs = array('requestTime' => array('convertTo' => 'timestamp', 'operator' => '>='));
        foreach($data_filter as $filter => $data) {
            if(in_array($filter, $allowed_filters)) {
                unset($data_filter['filter']);
            }
        }
        /*
         * Empowering  the security filter  for the query to only  get the filtererd IDs
         * whose values exists in  the IDs  returned from the viewbale functions
         */
        $allowed_filtersdata['uid'] = array_keys(LeavesExpenses::get_viewableusers());
        $allowed_filtersdata['useraffids'] = array_keys(Leaves::get_viewableuseraffiliates());

        /* Only Get the ids in data_filter Var whose values exists in the allowed_filters  */
        if(isset($data_filter['useraffids'])) {
            $allowed_filtersdata['useraffids'] = array_intersect($data_filter['useraffids'], $allowed_filtersdata['useraffids']);
        }

        if(isset($allowed_filtersdata['useraffids']) && (!empty($allowed_filtersdata['useraffids']))) {
            $having_querystring = ' HAVING useraffid IN ('.$db->escape_string(implode(',', $allowed_filtersdata['useraffids'])).') ';
        }
        unset($data_filter['toDate'], $data_filter['fromDate'], $allowed_filtersdata['useraffids']);

        $querysting_where = ' WHERE ';
        foreach($allowed_filters as $filterkey) {
            $filter = array();
            if(!isset($data_filter[$filterkey])) {
                if(isset($allowed_filtersdata[$filterkey])) {
                    $filter = $allowed_filtersdata[$filterkey];
                }
                else {
                    continue;
                }
            }
            else {
                /* Only Get the ids  in data_filter Var whose values exists in the allowed_filters  */
                if(isset($allowed_filtersdata[$filterkey])) {
                    $filter = array_intersect($data_filter[$filterkey], $allowed_filtersdata[$filterkey]);
                }
                else {
                    $filter = $data_filter[$filterkey];
                }
            }

            if(!isset($tables_alias[$filterkey])) {
                $tables_alias[$filterkey] = 'l';
            }

            if(is_array($filter)) {
                $querysting .= $querysting_where.$tables_alias[$filterkey].'.'.$db->escape_string($filterkey).' IN ('.$db->escape_string(implode(',', $filter)).')';
            }
            else {
                if(isset($allowed_filters_configs[$filterkey]['convertTo'])) {
                    if($allowed_filters_configs[$filterkey]['convertTo'] == 'timestamp') {
                        $filter = strtotime($filter.' midnight');
                    }
                }

                $operator = '=';
                if(!empty($allowed_filters_configs[$filterkey]['operator'])) {
                    $operator = $db->escape_string($allowed_filters_configs[$filterkey]['operator']);
                }

                if(is_string($filter)) {
                    $filter = '"'.$filter.'"';
                }
                $querysting .= $querysting_where.$tables_alias[$filterkey].'.'.$db->escape_string($filterkey).' '.$operator.' '.$db->escape_string($filter).'';
            }
            $querysting_where = ' AND ';
        }

        $query = $db->query("SELECT DISTINCT(l.lid), l.uid, l.affid, a.affid as useraffid, l.spid, l.cid, lt.title, lt.ltid, lextt.aletid, lext.aleid, lext.alteid, lext.expectedAmt, lext.actualAmt
                            FROM ".Tprefix."leaves l
                            JOIN ".Tprefix."leavetypes lt ON (l.type=lt.ltid)
                            JOIN ".Tprefix."attendance_leaves_expenses lext ON (lext.lid=l.lid)
                            JOIN ".Tprefix."attendance_leavetypes_expenses letexp ON (letexp.alteid=lext.alteid)
                            JOIN ".Tprefix."attendance_leaveexptypes lextt ON (lextt.aletid=letexp.aletid)
                            JOIN ".Tprefix."affiliatedemployees a ON (a.uid=l.uid)
                            {$querysting} AND NOT EXISTS (SELECT la.lid FROM ".Tprefix."leavesapproval la WHERE la.isApproved=0 AND la.lid=l.lid) AND ((l.fromDate BETWEEN ".$fromDate." AND ".$toDate.") OR (l.toDate BETWEEN ".$fromDate." AND ".$toDate."))".$having_querystring);

        if($db->num_rows($query) > 0) {
            while($rowsdata = $db->fetch_assoc($query)) {
                $leavexpencesdata[$rowsdata['aleid']] = $rowsdata;
            }
            $db->free_result($query);
            unset($rowsdata);
            return $leavexpencesdata;
        }
    }

    public function get_conversation() {
        /* apply view permission */
        $messages = LeavesMessages::get_messages('lid='.$this->data['lid'], array('simple' => false));
        if(is_array($messages)) {
            return $messages;
        }
        return false;
    }

    public function get_initalmessage() {
        global $db;
        $initalmessage['lmid'] = $db->fetch_field($db->query("SELECT lmid, uid FROM ".Tprefix."leaves_messages WHERE lid='".$this->data['lid']."' AND inReplyTo=0 ORDER BY lmid ASC LIMIT 0, 1"), 'lmid');
        if(isset($initalmessage['lmid']) && !empty($initalmessage['lmid'])) {
            return new LeavesMessages($initalmessage['lmid'], false);
        }
    }

    public function get_initalvisiblemessage() {
        global $db;

        $initalmessage['lmid'] = $db->fetch_field($db->query("SELECT lmid, uid FROM ".Tprefix."leaves_messages WHERE lid='".$this->data['lid']."' AND viewPermission='public' AND inReplyTo=0 ORDER BY createdOn DESC"), 'lmid');
        if(isset($initalmessage['lmid']) && !empty($initalmessage['lmid'])) {
            return new LeavesMessages($initalmessage['lmid'], false);
        }
    }

    public function get_latestmsg() {
        return LeavesMessages::get_messages('lid='.$this->data['lid'], array('simple' => false, 'limit' => '0, 1', 'order' => array('by' => 'createdOn', 'sort' => 'DESC')));
    }

    public function parse_messages(array $options = array()) {
        global $template, $core;
        $takeactionpage_conversation = null;

        $initialmsgs = LeavesMessages::get_messages('lid='.$this->data['lid'].' AND inReplyTo=0', array('simple' => false, 'returnarray' => true));
        if(!is_array($initialmsgs)) {
            return false;
        }

        $show_replyicon = 'display:block;';
        if(isset($options['viewsource']) && ($options['viewsource'] == 'viewleave')) {
            $show_replyicon = 'display:none;';
        }

        if(empty($options['uid'])) {
            $options['uid'] = $core->user['uid'];
        }

        foreach($initialmsgs as $initialmsg) {
            if(!is_object($initialmsg)) {
                continue;
            }

            /*  Check if user is allowed to see the message */
            if(!$initialmsg->can_seemessage($options['uid'])) {
                continue;
            }

            $message = $initialmsg->get();
            $message['user'] = $initialmsg->get_user($message['uid'])->get();  /* Get the user of  who set the message conversation */
            $message['message_date'] = date($core->settings['dateformat'], $message['createdOn']);

            if(isset($options['viewmode']) && ($options['viewmode'] == 'textonly')) {
                $takeactionpage_conversation .= '<span style="font-weight: bold;"> '.$message['user']['displayName'].'</span> <span style="font-size: 9px;">'.date($core->settings['dateformat'].' '.$core->settings['timeformat'], $message['createdOn']).'</span>:';
                $takeactionpage_conversation .= '<div>'.$message['message'].'</div><br />';
            }
            else {
                eval("\$takeactionpage_conversation .= \"".$template->get('attendance_listleaves_takeaction_convmsg')."\";");
            }

            $replies_objs = $initialmsg->get_replies();
            if(is_array($replies_objs)) {
                $takeactionpage_conversation .= $this->parse_replies($replies_objs, 1, $options);
            }
        }
        return $takeactionpage_conversation;
    }

    private function parse_replies($replies, $depth = 1, array $options = array()) {
        global $template, $core;

        $show_replyicon = 'display:block;';
        if(isset($options['viewsource']) && ($options['viewsource'] == 'viewleave')) {
            $show_replyicon = 'display:none;';
        }

        if(is_array($replies)) {
            foreach($replies as $reply) {
                if(!$reply->can_seemessage($options['uid'])) {
                    continue;
                }
                $bgcolor = alt_row($bgcolor);
                $inline_style = 'margin-left:'.($depth * 8).'px;';
                $message = $reply->get();
                $message['user'] = $reply->get_user($message['uid'])->get();
                $message['message_date'] = date($core->settings['dateformat'], $message['createdOn']);

                if(isset($options['viewmode']) && ($options['viewmode'] == 'textonly')) {
                    $takeactionpage_conversation .= '<span style="font-weight: bold;"> '.$message['user']['displayName'].'</span> <span style="font-size: 9px;">'.date($core->settings['dateformat'].' '.$core->settings['timeformat'], $message['createdOn']).'</span>:';
                    $takeactionpage_conversation .= '<div>'.$message['message'].'</div><br />';
                }
                else {
                    eval("\$takeactionpage_conversation .= \"".$template->get('attendance_listleaves_takeaction_convmsg')."\";");
                }
                $reply_replies = $reply->get_replies();
                if(is_array($reply_replies)) {
                    $takeactionpage_conversation .= $this->parse_replies($reply_replies, $depth + 1, $options);
                }
            }
            return $takeactionpage_conversation;
        }
    }

    public function parse_expenses() {
        global $lang;

        if($this->has_expenses()) {
            $expenses_data = $this->get_expensesdetails();
            $total = 0;
            $expenses_message = '';
            foreach($expenses_data as $expense) {
                if(!empty($lang->{$expense['name']})) {
                    $expense['title'] = $lang->{$expense['name']};
                }
                $total += $expense['expectedAmt'];

                $exptype_obj = LeaveExpenseTypes::get_exptype_byattr('title', $expense['title'], false);
                if(is_object($exptype_obj)) {
                    if(method_exists($exptype_obj, 'parse_agencylink')) {
                        $agency_link = $exptype_obj->parse_agencylink($this);
                    }
                }
                $expenses_message .= $expense['title'].': '.$expense['expectedAmt'].$expense['currency'].' '.$agency_link.'<br>';
                unset($agency_link);
            }
            return '<br /><p>'.$lang->associatedexpenses.'<br />'.$expenses_message.'<br />Total: '.$total.'USD</p>';
        }
        return false;
    }

    public function parse_approvalsapprovers($configs = array()) {
        global $lang;

        $approvers = $this->get_approvals();
        if(is_array($approvers)) {
            foreach($approvers as $approver) {
                $leave['approvers'][] = $approver->get()['displayName'];
            }
            $leave['approvers'] = implode(', ', $leave['approvers']);
            unset($approvers);
            if($configs['parselabel'] == true) {
                return '<span style="font-weight:bold;">'.$lang->approvedby.': '.$leave['approvers'].'</span>';
            }
            else {
                return $leave['approvers'];
            }
        }
        return false;
    }

    public function get_requester($simple = true) {
        return new Users($this->data['uid'], $simple);
    }

    public function is_leaverequester() {
        if(value_exists('leaves', 'uid', $this->data['uid'], 'lid='.intval($this->data['lid']))) {
            return true;
        }
        return false;
    }

    public function get_purpose() {
        return new LeaveTypesPurposes($this->data['ltpid']);
    }

    public function get_type($simple = true) {
        return $this->get_leavetype($simple);
    }

    public function get_country() {
        if(empty($this->data['coid']) && !empty($this->data['destinationCity'])) {
            $city = new Cities($this->data['destinationCity']);
            return $city->get_country();
        }
        return new Countries($this->data['coid']);
    }

    public function get_leavetype($simple = true) {
        return new LeaveTypes($this->data['type'], $simple);
    }

    public function count_workingdays() {
        if(!function_exists('count_workingdays')) {
            require ROOT.INC_ROOT.'attendance_functions.php';
        }
        return count_workingdays($this->data['uid'], $this->data['fromDate'], $this->data['toDate'], $this->get_leavetype(false)->isWholeDay);
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        global $core;
        /* Late there will be a page for each leave
         * For now the function returns a info that identify a leave
         */

        return '<a href="index.php?module=attendance/viewleave&amp;id='.$this->data['lid'].'">'.date($core->settings['dateformat'], $this->data['fromDate']).' - '.date($core->settings['dateformat'], $this->data['toDate']).'</a>';
    }

    /*
     * Get  Affiliates  that he can  HR and he is working with
     * @param	int		$core->user['affiliates']
     * @return  Array
     */
    public static function get_viewableuseraffiliates() {
        global $db, $core;

        $afffiliates_users = $core->user['affiliates'];
        if(is_array($core->user['hraffids'])) {
            $afffiliates_users += $core->user['hraffids'];
        }
        if(is_array($afffiliates_users)) {
            foreach($afffiliates_users as $affiliate) {
                $affiliate_obj = new Affiliates($affiliate);
                $affiliates_data = $affiliate_obj->get();
                $affiliates[$affiliates_data['affid']] = $affiliates_data['name'];
            }
            return $affiliates;
        }
    }

    public function get_sourcecity($simple = true) {
        /* To be expanded later depending on
         * 1. User selection
         * 2. Current location
         */
        return $this->get_requester()->get_mainaffiliate()->get_city($simple);
    }

    public function get_destinationcity($simple = true) {
        $attributes = array('coid', 'affid', 'spid', 'cid');
        $alt_functions = array('coid' => 'get_capitalcity');

        foreach($attributes as $attribute) {
            if(!empty($this->data[$attribute])) {
                $destination['type'] = $attribute;
                $destination['id'] = $this->data[$attribute];
                break;
            }
        }

        $object = get_object_bytype($destination['type'], $destination['id']);
        if(is_object($object)) {
            if(array_key_exists($destination['type'], $alt_functions)) {
                return $object->$alt_functions[$destination['type']]();
            }
            else {
                if(method_exists($object, 'get_city')) {
                    return $object->get_city($simple);
                }
            }
            return false;
        }
        return false;
    }

    public static function get_leave_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_leaves($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_displayname() {
        global $core;
        return $this->get_type()->title.' | '.date($core->settings['dateformat'], $this->fromDate).' - '.date($core->settings['dateformat'], $this->toDate);
    }

    public function get_errorcode() {
        $this->errorcode;
    }

    public function get_contactperson($simple = false) {
        return new Users($this->data['contactPerson'], $simple);
    }

    protected function create(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

    public function check_leavedestination() {
        global $core;
        if(!empty($this->data['coid'])) {
            $requester_mainaff = new Affiliates($core->user['mainaffiliate']);
            $aff_country = $requester_mainaff->country; //|| $this->data['affid'] == $requester_mainaff->affid
            if(($this->data['coid'] == $aff_country)) {
                $visit_type = 'domestic';
            }
            else {
                $visit_type = 'international';
            }
        }
        else if(!empty($this->data['affid'])) {
            if(($this->data['affid'] == $requester_mainaff->affid)) {
                $visit_type = 'domestic';
            }
            else {
                $visit_type = 'international';
            }
        }
        return $visit_type;
    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    /**
     * Creates an auto-responder in the mail server
     * @global Language $lang
     * @global Core $core
     * @return \Exception
     */
    public function create_autoresponder() {
        global $lang, $core;
        if(class_exists('CpanelAPIConnect')) {
            $apiconnect = new CpanelAPIConnect();
            $xmlapi = $apiconnect->get_xmlapi();
            try {
                $user = $this->get_user();
                $main_aff = $user->get_mainaffiliate();
                if(!is_object($main_aff) || empty($main_aff->affid)) {
                    return false;
                }
                if(empty($main_aff->cpAccount)) {
                    return false;
                }
                $cpaccount = $main_aff->cpAccount;
                $subject = 'Auto Responder: %subject%';
                if(!is_empty($this->autoRespSubject)) {
                    $subject = 'Auto Responder: '.$this->autoRespSubject;
                }
                $message = '';
                if(!is_empty($this->autoRespBody)) {
                    $message = $this->autoRespBody;
                }
                else {
                    $message = $lang->sprint($lang->autorespondermessage, date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->fromDate), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->toDate));
                    if($this->data['limitedEmail']) {
                        $message .= "\n".$lang->autorespondermessagelimitedemail;
                    }
                    if(!empty($this->data['contactPerson'])) {
                        $contactperson = $this->get_contactperson();
                        if(is_object($contactperson)) {
                            $message .= "\n".$lang->sprint($lang->autorespondermessagecontact, $contactperson->displayName, $contactperson->email);
                        }
                    }
                }
                $dateTimeZoneLocal = new DateTimeZone(date_default_timezone_get());
                $dateTimeLocal = new DateTime("now", $dateTimeZoneLocal);
                $differencefromgmt = timezone_offset_get($dateTimeZoneLocal, $dateTimeLocal);
                $args = array($user->email, $user->displayName, $subject, $message, explode('@', $user->email)[1], true, "utf-8", 8, $this->fromDate - $differencefromgmt, $this->toDate - $differencefromgmt);
                return $xmlapi->api1_query($cpaccount, 'Email', 'addautoresponder', $args);
            }
            catch(Exception $ex) {
                return $ex;
            }
        }
    }

    /**
     *
     * @global Language $lang
     * @global Core $core
     * @return boolean|\Exception
     */
    public function delete_autoresponder() {
        global $lang, $core;
        if(class_exists('CpanelAPIConnect')) {
            $apiconnect = new CpanelAPIConnect();
            $xmlapi = $apiconnect->get_xmlapi();
            try {
                $user = $this->get_user();
                $main_aff = $user->get_mainaffiliate();
                if(!is_object($main_aff) || empty($main_aff->affid)) {
                    return false;
                }
                if(empty($main_aff->cpAccount)) {
                    return false;
                }
                $args = array($user->email);
                return $xmlapi->api1_query($cpaccount, 'Email', 'delautoresponder', $args);
            }
            catch(Exception $ex) {
                return $ex;
            }
        }
    }

}
?>