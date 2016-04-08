<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: GadgetFxRates_class.php
 * Created:        @hussein.barakat    11-Mar-2016 | 14:30:02
 * Last Update:    @hussein.barakat    11-Mar-2016 | 14:30:02
 */

class GadgetPendLvsYrApproval extends SystemGadget {
    protected $data = array();
    protected $widget_id = '5';

    const CLASSNAME = __CLASS__;

    public function __construct() {
        parent::__construct();
    }

    public function create(array $data) {

    }

    public function update(array $data) {

    }

    /**
     *
     * @param array $instancedata
     * @return string
     */
    public function parse(array $instancedata) {
        global $template, $lang, $core;

        $leavesapprovals = AttLeavesApproval::get_data(array('uid' => $core->user['uid'], 'isApproved' => 0), array('returnarray' => true));
        if(is_array($leavesapprovals)) {
            $output = '<ul class="list-group">';
            foreach($leavesapprovals as $leaveapproval) {
                if($leaveapproval->sequence != 1) {
                    $leaves_prevapprovals = AttLeavesApproval::get_data(array('lid' => $leaveapproval->lid, 'sequence' => $leaveapproval->sequence - 1, 'isApproved' => 0), array('returnarray' => true));
                    if(is_array($leaves_prevapprovals)) {
                        continue;
                    }
                }
                $leave = $leaveapproval->get_leave();
                if(date($core->settings['dateformat'], $leave->fromDate) != date($core->settings['dateformat'], $leave->toDate)) {
                    $todate_format = $core->settings['dateformat'].' '.$core->settings['timeformat'];
                }
                else {
                    $todate_format = $core->settings['timeformat'];
                }

                $approve_link = DOMAIN.'/index.php?module=attendance/listleaves&action=takeactionpage&requestKey='.base64_encode($leave->requestKey).'&id='.base64_encode($leave->lid);

                $output .= '<li class="list-group-item"><a href="'.$approve_link.'"><span class="glyphicon glyphicon-exclamation-sign"></span> '.$leave->get_user()->get_displayname().' - '.$leave->get_type()->get_displayname().' between '.date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave->fromDate).' and '.date($todate_format, $leave->toDate).'</a></li>';
            }
            $output .= '</ul>';
            return $output;
        }
        else {
            return $lang->na;
        }
    }

}