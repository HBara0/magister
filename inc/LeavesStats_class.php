<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: LeavesStats_class.php
 * Created:        @zaher.reda    Aug 2, 2014 | 1:43:04 PM
 * Last Update:    @zaher.reda    Aug 2, 2014 | 1:43:04 PM
 */

/**
 * Description of LeavesStats_class
 *
 * @author zaher.reda
 */
class LeavesStats extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;
    private $user_hrinfo = null;

    const PRIMARY_KEY = 'lsid';
    const TABLE_NAME = 'leavesstats';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'uid,ltid,periodStart,periodEnd';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    public function get_leavetype() {
        return new LeaveTypes($this->data['ltid']);
    }

    public function generate_periodbased($data) {
        /* Get required Info */
        date_default_timezone_set('UTC');
        $user = new Users($data['uid'], false);
        $this->user_hrinfo = $user->get_hrinfo();
        $affiliate = $user->get_mainaffiliate();

        $leavetype = new LeaveTypes($data['type'], false);
        if(!isset($data['isWholeDay'])) {
            $data['isWholeDay'] = 1;
            if($leavetype->isWholeDay == 0) {
                $data['isWholeDay'] = 0.5;
            }
        }

        if(!empty($leavetype->countWith)) {
            $leavetype = new LeaveTypes($leavetype->countWith, false);
            $data['type'] = $leavetype->ltid;
        }

        if(empty($this->user_hrinfo['joinDate'])) {
            $this->user_hrinfo['joinDate'] = $user->dateAdded;
        }

        /* Count Working Days - START */
        if($data['skipWorkingDays'] == true) {
            $data['workingDays'] = 0;
        }
        else {
            if(!isset($data['workingDays']) || empty($data['workingDays'])) {
                if(isset($data['lid'])) {
                    $leave = new Leaves($data['lid']);
                    $data['workingDays'] = $leave->count_workingdays();
                }
                else {
                    $data['workingDays'] = self::count_workingdays($data);
                }
            }
        }

        if($data['negativeWorkingDays'] == true) {
            $data['workingDays'] = 0 - $data['workingDays'];
        }
        /* Count Working Days - END */

        $existing_stat = self::get_data('uid='.$user->uid.' AND ltid='.$leavetype->ltid.' AND (('.$data['fromDate'].' BETWEEN periodStart AND periodEnd) AND ('.$data['toDate'].' BETWEEN periodStart AND periodEnd))');
        if(is_object($existing_stat)) {
            $statdata['daysTaken'] = $existing_stat->daysTaken + $data['workingDays'];
            $existing_stat->update($statdata);

            return;
        }

        $existing_stats = self::get_data('uid='.$user->uid.' AND ltid='.$leavetype->ltid.' AND (('.$data['fromDate'].' BETWEEN periodStart AND periodEnd) OR ('.$data['toDate'].' BETWEEN periodStart AND periodEnd))', array('returnarray' => true));
        if(is_array($existing_stats)) {
            unset($data['lid'], $data['workingDays']); /* Remove lid to avoid calculating number of days based on it */
            if(count($existing_stats) == 1) {
                /* Case where one period exists, and one or more don't */

                $this->generate_periodbased_multiperiods(current($existing_stats), $data);
            }
            else {
                foreach($existing_stats as $stat) {
                    $recrsive_data = $data;
                    if($recrsive_data['fromDate'] < $stat->periodStart) {
                        $recrsive_data['fromDate'] = $stat->periodStart;
                    }

                    if($recrsive_data['toDate'] > $stat->periodEnd) {
                        $recrsive_data['toDate'] = $stat->periodEnd;
                    }
                    $this->generate_periodbased($recrsive_data);
                }
            }
        }
        else {
            $leavepolicy = AffiliatesLeavesPolicies::get_data(array('affid' => $affiliate->affid, 'ltid' => $leavetype->ltid));
            if(!is_object($leavepolicy)) {
                return false;
            }

            if($leavepolicy->useFirstJobDate == 1 && !empty($this->user_hrinfo['firstJobDate'])) {
                $this->user_hrinfo['joinDate'] = $this->user_hrinfo['firstJobDate'];
            }

            $oneyear_anniversary = strtotime(date('Y-m-d', $this->user_hrinfo['joinDate']).' + 1 year -1 second');
            $adjustmentperiodend = strtotime((date('Y', $oneyear_anniversary) + 1).'-01-01 -1 second');
            $leave_year = date('Y', $data['fromDate']);

            $statdata['daysTaken'] = $data['workingDays'];
            if($oneyear_anniversary > $data['fromDate']) {
                $statdata['periodStart'] = $this->user_hrinfo['joinDate'];
                $statdata['periodEnd'] = $oneyear_anniversary;
                $statdata += $this->calculate_firstperiod($leavepolicy);
            }
            elseif($adjustmentperiodend > $data['fromDate']) {
                $statdata['periodStart'] = $oneyear_anniversary + 1;
                $statdata['periodEnd'] = $adjustmentperiodend;
                if($leavepolicy->entitleAfter == 0) {
                    $statdata += $this->calculate_adjusmentperiod($oneyear_anniversary + 1, $adjustmentperiodend, $leavepolicy);
                }
                else {
                    $statdata += $this->calculate_regularperiod($statdata['periodStart'], $leavepolicy);
                }
            }
            elseif($leave_year - date('Y', $adjustmentperiodend) == 1) {
                $statdata['periodStart'] = $adjustmentperiodend + 1;
                $statdata['periodEnd'] = mktime(23, 59, 59, 12, 31, $leave_year);
                if($leavepolicy->entitleAfter == 0) {
                    $statdata += $this->calculate_regularperiod($statdata['periodStart'], $leavepolicy);
                }
                else {
                    $statdata += $this->calculate_adjusmentperiod($oneyear_anniversary + 1, $adjustmentperiodend, $leavepolicy);
                }
            }
            else {
                $statdata['periodStart'] = strtotime('1/1/'.$leave_year.' midnight UTC');
                $statdata['periodEnd'] = strtotime('1/1/'.($leave_year + 1).' midnight UTC') - 1;
                $statdata += $this->calculate_regularperiod($statdata['periodStart'], $leavepolicy);
            }

            $statdata['canTake'] = $statdata['entitledFor'];

            /* Set the new data for later use */
            $valid_attrs = array('uid', 'ltid', 'periodStart', 'periodEnd', 'entitledFor', 'canTake', 'daysTaken');
            $data['ltid'] = $data['type'];
            $statdata += $data;
            $valid_attrs = array_combine($valid_attrs, $valid_attrs);
            $statdata = array_intersect_key($statdata, $valid_attrs);
            $this->set($statdata);

            $prev_stat = $this->get_prevstat();
            if(is_object($prev_stat)) {
                $effective_prevbalance = ($prev_stat->canTake - $prev_stat->entitledFor); /* The actual accepted prev balance */
                if($prev_stat->daysTaken < $effective_prevbalance) {
                    $statdata['remainPrevYear'] = $prev_stat->entitledFor;
                }
                else {
                    /* Consume previous balance 1st */
                    $statdata['remainPrevYear'] = $prev_stat->daysTaken - $effective_prevbalance;
                    /* Consume additional days 2nd */
                    $statdata['remainPrevYear'] -= $prev_stat->additionalDays;
                    /* Consume previous period entitelment 3rd */
                    $statdata['remainPrevYear'] = $prev_stat->entitledFor - $statdata['remainPrevYear'];
                }

                $statdata['remainPrevYearActual'] = $statdata['remainPrevYear'];


                /* Check if balance was entitled less than a year ago
                 * Required for adjustment period
                 */
                if(floor(($statdata['periodStart'] - $prev_stat->periodStart) / (365 * 60 * 60 * 24)) >= 1) {
                    if($statdata['remainPrevYear'] > $leavepolicy->maxAccumulateDays) {
                        $statdata['remainPrevYearActual'] = $leavepolicy->maxAccumulateDays;
                    }
                }
            }

            $statdata['canTake'] += $statdata['remainPrevYearActual'];
            unset($statdata['remainPrevYearActual']);

            $this->create($statdata);
        }
    }

    private function generate_periodbased_multiperiods(LeavesStats $stat, array $data) {
        $periods[2] = $data;
        if($data['fromDate'] < $stat->periodStart) {
            $periods[1] = $data;
            $periods[1]['toDate'] = $stat->periodStart - 1;
            $periods[2]['fromDate'] = $stat->periodStart;
        }

        if($data['toDate'] > $stat->periodEnd) {
            $periods[3] = $data;
            $periods[3]['fromDate'] = $stat->periodEnd + 1;
            $periods[2]['toDate'] = $stat->periodEnd;
        }

        foreach($periods as $period) {
            $this->generate_periodbased($period);
        }
    }

    private function calculate_firstperiod($leavepolicy) {
        $newleavestats['entitledFor'] = 0;
        if($leavepolicy->entitleAfter == 0) {
            $newleavestats['entitledFor'] = $leavepolicy->basicEntitlement;
        }

        return $newleavestats;
    }

    private function calculate_adjusmentperiod($start, $end, $leavepolicy) {
        $newleavestats['entitledFor'] = (round((($end - $start) / 60 / 60 / 24 / 30.4)) * $leavepolicy->basicEntitlement) / 12;
        $entitlement_remainder = fmod($newleavestats['entitledFor'], 1);

        if($entitlement_remainder > 0.5) {
            $newleavestats['entitledFor'] = ceil($newleavestats['entitledFor']);
        }
        elseif($entitlement_remainder < 0.5) {
            $newleavestats['entitledFor'] = floor($newleavestats['entitledFor']);
        }

        return $newleavestats;
    }

    private function calculate_regularperiod($start, $leavepolicy) {
        $newleavestats['entitledFor'] = $leavepolicy->basicEntitlement;

        $newleavestats['entitledFor'] += $this->calculate_promotion($leavepolicy, date('Y', $start), date('Y', $this->user_hrinfo['joinDate']));

        return $newleavestats;
    }

    private static function count_workingdays(array $data) {
        require_once ROOT.INC_ROOT.'attendance_functions.php';
        return count_workingdays($data['uid'], $data['fromDate'], $data['toDate'], $data['isWholeDay']);
    }

    protected function create(array $data) {
        global $db;

        $db->insert_query(self::TABLE_NAME, $data);
    }

    protected function update(array $data) {
        global $db;

        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.$this->data[self::PRIMARY_KEY]);
    }

    public function calculate_promotion($leavepolicy, $leave_year, $employment_year) {
        $promotion = 0;

        if(!empty($leavepolicy->promotionPolicy)) {
            $new_promotion = 0;
            $working_years = $leave_year - $employment_year;
            $promotion_policy = unserialize($leavepolicy->promotionPolicy);
            ksort($promotion_policy);
            /**
             * Loop over the different available promotions and add them if working years are greater than
             * the promotion requirement.
             */
            while($val = current($promotion_policy)) {
                if($working_years == key($promotion_policy)) {
                    $new_promotion = $val;
                }

                if($working_years > key($promotion_policy) && $working_years != key($promotion_policy)) {
                    $promotion += $val;
                }
                next($promotion_policy);
            }

            /**
             * Apply new promotion based on employment date
             */
            if(array_key_exists($working_years, $promotion_policy)) {
                /* Calculate promotion based on the date of employment till end of year */
                $employment_month = date('n', $this->user_hrinfo['joinDate']);
                $promotion += ((12 - $employment_month + 1) * $new_promotion) / 12;
                //$newleavestats['entitledFor'] = ((((12 - $employment_month) + 1) * $newleavestats['entitledFor']) / 12) + (((12 - ((12 - $employment_month) + 1)) * ($leavepolicy->basicEntitlement + $prev_promotion)) / 12);
            }
        }
        return $promotion;
    }

    /*
     * Gets the previous period
     */
    public function get_prevperiod() {
        return $this->get_prevstat();
    }

    public function get_prevstat() {
        return $this->get_data('periodEnd < '.$this->data['periodStart'].' AND ltid = '.$this->data['ltid'].' AND uid = '.$this->data['uid'], array('order' => array('by' => 'periodEnd', 'sort' => 'DESC'), 'limit' => '0, 1'));
    }

}
?>