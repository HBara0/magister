<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: GadgetLeavesCounts_class.php
 * Created:        @rasha.aboushakra    Apr 1, 2016 | 4:37:27 PM
 * Last Update:    @rasha.aboushakra    Apr 1, 2016 | 4:37:27 PM
 */

/**
 * Description of GadgetLeavesCounts_class
 *
 * @author rasha.aboushakra
 */
class GadgetLeavesCounts extends SystemGadget {
    protected $data = array();
    protected $widget_id = '8';

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
//        if(!empty($instancedata['serializedConfig'])) {
//            $serialized_config = $instancedata['serializedConfig'];
//            $configs = unserialize($serialized_config);
//            if(is_array($configs)) {
//                $timezones_array = explode(',', $configs['options']['timezones']);
//            }
//        }
        return $this->parse_leavescountsgadget($instancedata);
    }

    /**
     * @global type $lang
     * @global type $core
     * @global type $template
     * @param type $instancedata
     * @return string html
     */
    public function parse_leavescountsgadget($instancedata = '') {
        global $db, $core, $template;
        $leaves_count['approved'] = $leaves_count['rejected'] = $leaves_count['pending'] = 0;
        $leaves = Leaves::get_data(array('uid' => $core->user['uid']), array('returnarray' => true, 'simple' => false));
        if(is_array($leaves)) {
            foreach($leaves as $leave) {
                $leave = $leave->get();
                $status = array();
                $query = $db->query("SELECT isApproved, COUNT(isApproved) AS approvecount FROM ".Tprefix."leavesapproval WHERE lid='{$leave[lid]}' GROUP BY isApproved");
                while($approve = $db->fetch_assoc($query)) {
                    if($approve['isApproved'] == 1) {
                        $status['approved'] = $approve['approvecount'];
                    }
                    else {
                        $status['notapproved'] = $approve['approvecount'];
                    }
                }

                if($status['approved'] == array_sum($status)) {
                    $leaves_count['approved'] ++;
                }
                elseif($status['approved'] < array_sum($status) && !empty($status['approved'])) {
                    $leaves_count['pending'] ++;
                }
                else {
                    $leaves_count['rejected'] ++;
                }
            }
        }
        eval("\$output = \"".$template->get('gadget_leavescount')."\";");
        return $output;
    }

}