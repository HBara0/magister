<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * List userd assigned to assets
 * $id: listuser.php
 * Created:        @tony.assaad    Jul 4, 2013 | 12:06:47 PM
 * Last Update:    @tony.assaad    Jul 4, 2013 | 12:06:47 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['assets_canManageAssets'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $sort_url = sort_url();

    $asset = new Assets();
    $affiliate = new Affiliates($core->user['mainaffiliate']);
    $affiliate_users = $affiliate->get_users(array('ismain' => 1, 'displaynameonly' => 1));

    $assets_data = get_specificdata('assets', array('asid', 'title'), $core->input['filters']['asid'], 'title', 'affid IN ('.implode(',', $core->user['affiliates']).')');
    if(!is_array($assets_data) && empty($assets_data)) {
        $assets_data = array();
    }
    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('uid', 'asid', 'fromDate', 'toDate', 'toDate'),
                    'overwriteField' => array(
                            'uid' => parse_selectlist('filters[uid]', 1, $affiliate_users, $core->input['filters']['uid'], '', '', array('blankstart' => true)),
                            'asid' => parse_selectlist('filters[asid]', 2, $assets_data, '', '', '', array('blankstart' => true))
                    ),
                    'fieldsSequence' => array('uid' => 1, 'asid' => 2, 'fromDate' => 3, 'toDate' => 4)
            /* get the busieness potential and parse them in select list to pass to the filter array */
            ),
            'process' => array(
                    'filterKey' => 'auid',
                    'mainTable' => array(
                            'name' => 'assets_users',
                            'filters' => array('uid' => 'uid', 'asid' => 'asid', 'fromDate' => array('operatorType' => 'date', 'name' => 'fromDate'), 'toDate' => array('operatorType' => 'date', 'name' => 'toDate')),
                    )
            ),
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filters_row_display = 'hide';

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }

    if(true) {/* Later to be, if has permission to view multiple affiliates */
        $multipage_where = 'affid = '.$core->user['mainaffiliate'];
    }
    else {
        $multipage_where = 'affid IN ('.$db->escape_string(implode(',', $core->user['affiliates'])).')';
    }

    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
    /* Perform inline filtering - END */

    $assignee = $asset->get_allassignee($filter_where);

    if(is_array($assignee)) {
        foreach($assignee as $auid => $assigneduser) {
            $rowclass = alt_row($rowclass);
            $assigneduser['fromDate_output'] = date($core->settings['dateformat'], $assigneduser['fromDate']);
            $assigneduser['toDate_output'] = date($core->settings['dateformat'], $assigneduser['toDate']);
            $auid = $assigneduser['auid'];
            /* Get assigned assets by assets object */
            $asset = new Assets($assigneduser['asid']);
            $assigneduser['asset'] = $asset->get()['title'];

            /* Get assigned USER by user object */
            $user = new Users($assigneduser['uid']);
            $employee = $user->get();

            $tools = ' <a href="#'.$assigneduser['auid'].'" id="deleteuser_'.$assigneduser['auid'].'_assets/listusers_loadpopupbyid" rel="delete_'.$assigneduser['auid'].'"><img src="'.$core->settings['rootdir'].'/images/invalid.gif" alt="'.$lang->delete.'" border="0"></a>   ';
            if(TIME_NOW > ($assigneduser['assignedon'] + ($core->settings['assets_preventeditasgnafter']))) {
                $tools = '<a href="#'.$assigneduser['auid'].'" id="deleteuser_'.$assigneduser['auid'].'_assets/listusers_loadpopupbyid" rel="delete_'.$assigneduser['auid'].'"><img src="'.$core->settings['rootdir'].'/images/invalid.gif" alt="'.$lang->delete.'" border="0"></a>   ';
            }

            eval("\$assignee_list .= \"".$template->get('assets_assignlist_row')."\";");
        }

        $multipages = new Multipages('assets_users au JOIN '.Tprefix.'assets a ON (a.asid=au.asid)', $core->settings['itemsperlist'], $multipage_where);
        $assignee_list .= '<tr><td colspan="5">'.$multipages->parse_multipages().'</td></tr>';
    }
    else {
        $assignee_list = '<tr><td colspan="5">'.$lang->na.'</td></tr>';
    }

    eval("\$assetsassignlist = \"".$template->get('assets_assignlist')."\";");
    output_page($assetsassignlist);
}
else {
    if($core->input['action'] == 'get_deleteuser') {
        eval("\$deleteassignee = \"".$template->get('popup_assets_listuserdelete')."\";");
        output($deleteassignee);
    }
    elseif($core->input['action'] == 'perform_delete') {
        $auid = $db->escape_string($core->input['todelete']);

        $asset = new Assets();
        $assignee = $asset->get_assigneduser($auid);
        if(TIME_NOW > ($assignee['assignedOn'] + ($core->settings['assets_preventeditasgnafter']))) {
            output_xml("<status>false</status><message>{$lang->notpossibledelete}</message>");
            exit;
        }

        $asset->delete_userassets($auid);
        switch($asset->get_errorcode()) {
            case 3:
                output_xml("<status>true</status><message>{$lang->successfullydeleted}</message>");
                break;
            case 604:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                break;
        }
    }
    elseif($core->input['action'] == 'get_edituser') {
        $asset = new Assets();
        $auid = $db->escape_string($core->input['id']);
        $assignee = $asset->get_assigneduser($auid);
        $assetslist = $asset->get_affiliateassets(array('titleonly' => 1, 'mainaffidonly' => 1));

        if(TIME_NOW > ($assignee['assignedon'] + ($core->settings['assets_preventeditasgnafter']))) {
            $disabled_fields['asid'] = $disabled_fields['uid'] = array('disabled' => 'disabled');
            $fields_todisable = array('conditionOnHandover', 'fromDate', 'toDate', 'fromTime', 'toTime');
            foreach($fields_todisable as $item) {
                $disabled_fields[$item] = ' disabled="disabled"';
            }
        }
        if(TIME_NOW > ($assignee['assignedon'] + ($core->settings['assets_preventconditionupdtafter']))) {
            $disabled_fields['conditionOnReturn'] = ' disabled="disabled"';
        }
        $assets_selectlist = parse_selectlist('assignee[asid]', 1, $assetslist, $assignee['asid'], '', '', $disabled_fields['asid']);

        $affiliate = new Affiliates($core->user['mainaffiliate']);
        $affiliate_users = $affiliate->get_users(array('ismain' => 1, 'displaynameonly' => 1));
        $employees_selectlist = parse_selectlist('assignee[uid]', 1, $affiliate_users, $assignee['uid'], '', '', $disabled_fields['asid']);
        $actiontype = 'edit';

        eval("\$editassignee = \"".$template->get('popup_assets_listuseredit')."\";");
        ouput($editassignee);
    }
}
?>
