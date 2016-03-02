<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: list.php
 * Created:        @tony.assaad    Nov 8, 2013 | 4:54:21 PM
 * Last Update:    @tony.assaad    Nov 8, 2013 | 4:54:21 PM
 */


if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['meetings_canCreateMeeting'] == 0) {
    error($lang->sectionnopermission);
}
if(!$core->input['action']) {
    $sort_url = sort_url();

    /* Advanced filter search */
    $filters_config = array(
            'parse' => array('filters' => array('title', 'description', 'fromDate', 'toDate', 'location'),
            ),
            'process' => array(
                    'filterKey' => 'mtid',
                    'mainTable' => array(
                            'name' => 'meetings',
                            'filters' => array('title' => 'title', 'description' => 'description', 'location' => 'location', 'fromDate' => array('operatorType' => 'date', 'name' => 'fromDate'), 'toDate' => array('operatorType' => 'date', 'name' => 'toDate', 'fielddisplay' => 'display:"none"')),
                    ),
            )
    );
    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    if(is_array($filter_where_values)) {
        if($filters_config['process']['filterKey'] == 'mtid') {
            $filters_config['process']['filterKey'] = 'mtid';
        }
        $filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }
    $filters_row = $filter->prase_filtersrows(array('tags' => 'table'));
    $additional_filters_row = $filter->parse_hidden_filters(array('title', 'test2'));
    if(!empty($additional_filters_row)) {
        $dditionalfiltericon = '<th><a href="#" id="showpopup_additionalfilters" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0"></a></th>';
    }
    $multiple_meetings = Meetings::get_multiplemeetings(array('filter_where' => $filter_where, 'order' => array('sortby' => $core->input['sortby'], 'order' => $core->input['order'])));
    if(is_array($multiple_meetings)) {
        foreach($multiple_meetings as $mid => $meeting) {
            $meeting_obj = new Meetings($mid);
            $sharedwith_icon = '';
            if($meeting_obj->is_sharedwithuser()) {
                $sharedwith_icon = '<img src="./images/icons/shared.png" border=0 alt="'.$lang->sharedwithyou.'" title="'.$lang->sharedwithyou.'"/>';
            }
            $row_tools = '';
            if($meeting['createdBy'] == $core->user['uid']) {
                if($meeting['hasMoM'] == 1) {
                    $action = '&do=edit';
                }

                $row_tools = '<a href=index.php?module=meetings/create&mtid='.$meeting['mtid'].' title="'.$lang->edit.'"><img src=./images/icons/edit.gif border=0 alt='.$lang->edit.'/></a>';
                $row_tools .= ' <a href=index.php?module=meetings/minutesmeeting'.$action.'&referrer=list&mtid='.$meeting['mtid'].' title="'.$lang->setmof.'" rel="setmof_'.$meeting['mtid'].'"><img src="'.$core->settings['rootdir'].'/images/icons/boundreport.gif" alt="'.$lang->data.'" border="0"></a>';
                $row_tools .= ' <a href="#'.$meeting['mtid'].'" id="sharemeeting_'.$meeting['mtid'].'_meetings/list_loadpopupbyid" rel="share_'.$meeting['mtid'].'" title="'.$lang->sharewith.'"><img src="'.$core->settings['rootdir'].'/images/icons/sharedoc.png" alt="'.$lang->sharewith.'" border="0"></a>';
            }

            $meeting['fromDate_output'] = date($core->settings['dateformat'], $meeting['fromDate']);
            $meeting['toDate_output'] = date($core->settings['dateformat'], $meeting['toDate']);

            $meeting['locationoutput'] = $meeting_obj->get_location();

            if(strlen($meeting['description']) > 50) {
                $meeting['description'] = $core->sanitize_inputs(substr($meeting['description'], 0, 50), array('removetags' => true)).'...';
            }
            eval("\$meeting_list_row .= \"".$template->get('meeting_list_row')."\";");
        }
    }

    eval("\$meeting_list = \"".$template->get('meeting_list')."\";");
    output_page($meeting_list);
}
else {
    if($core->input['action'] == 'get_sharemeeting') {
        $mtid = $db->escape_string($core->input['id']);

        $affiliates_users = Users::get_allusers();
        $meeting_obj = new Meetings(intval($mtid));
        if(!is_object($meeting_obj)) {
            exit;
        }
        $meeting_objarray = $meeting_obj->get();
        if(empty($meeting_objarray['mtid'])) {
            exit;
        }
        $shared_users = $meeting_obj->get_shared_users();
        if(is_array($shared_users)) {
            foreach($shared_users as $uid => $user) {
                $user = $user->get();
                $checked = ' checked="checked"';
                $rowclass = 'selected';

                eval("\$sharewith_rows .= \"".$template->get('popup_meetings_sharewith_rows')."\";");
            }
        }

        foreach($affiliates_users as $uid => $user) {
            $user = $user->get();
            $checked = $rowclass = '';
            if($uid == $core->user['uid']) {
                continue;
            }

            if(is_array($shared_users)) {
                if(array_key_exists($uid, $shared_users)) {
                    continue;
                }
            }

            eval("\$sharewith_rows .= \"".$template->get('popup_meetings_sharewith_rows')."\";");
        }
        $file = 'list';
        eval("\$share_meeting = \"".$template->get('popup_meetings_share')."\";");
        output($share_meeting);
    }
    elseif($core->input['action'] == 'do_share') {
        $mtid = $db->escape_string($core->input['mtid']);
        if(is_array($core->input['sharemeeting'])) {
            $meeting_obj = new Meetings($mtid);
            $meeting_obj->share($core->input['sharemeeting']);

            switch($meeting_obj->get_errorcode()) {
                case 0:
                    output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                    break;
            }
        }
        else {
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
        }
    }
}
?>