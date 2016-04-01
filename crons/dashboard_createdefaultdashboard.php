<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: dashboard_createdefaultdashboard.php
 * Created:        @hussein.barakat    01-Apr-2016 | 09:41:10
 * Last Update:    @hussein.barakat    01-Apr-2016 | 09:41:10
 */

require '../inc/init.php';

if($core->input['authkey'] == 'asfasdkjj!h4k23jh4k2_3h4k23jh') {
    //if uids are manually entered
    if(isset($core->input['ids']) && !empty($core->input['ids'])) {
        $db->escape_string($core->input['ids']);
        $activeusers = Users::get_data(array('uid' => explode(',', $core->input['ids'])), array('returnarray' => true));
    }
    else {
//get all active users
        $activeusers = Users::get_data('gid !=7', array('returnarray' => true));
    }
    if(is_array($activeusers)) {
        $final_uids = array();
        foreach($activeusers as $user_obj) {
            if(!isset($core->input['ids']) || empty($core->input['ids'])) {
                //check if user already has a dashboard
                $existing_mainmenudash = SystemDashboard::get_data(array('isActive' => 1, 'moduleName' => 'portal', 'pageName' => 'dashboard', 'uid' => $user_obj->uid));
                if(is_object($existing_mainmenudash)) {
                    continue;
                }
            }
            $final_uids[] = $user_obj->uid;
        }
        //log user id to create the main dashboard
    }
    if(is_array($final_uids)) {
//create default home dashboard
        $mainhome = SystemDashboard::createdefaultdashboard_home($final_uids);
        if($mainhome) {
            echo('DONE FOR HOME DASH<br><hr>');
        }
    }
}