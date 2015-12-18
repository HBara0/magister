<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: listplan.php
 * Created:        @tony.assaad    Aug 4, 2014 | 5:12:22 PM
 * Last Update:    @tony.assaad    Aug 4, 2014 | 5:12:22 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $travelplan_objs = TravelManagerPlan::get_plan(array('uid' => $core->user['uid']), array('returnarray' => true));

    $sort_url = sort_url();

    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('uid', 'title', 'fromDate', 'toDate', 'createdOn', 'finalized'),
                    'overwriteField' => array('title' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->title.'"/>',
                            'finalized' => '',
                    )
            ),
            'process' => array(
                    'filterKey' => 'tmpid',
                    'mainTable' => array(
                            'name' => 'travelmanager_plan',
                            'filters' => array('createdOn' => array('operatorType' => 'date', 'name' => 'createdOn')),
                    ),
                    'secTables' => array(
                            'leaves' => array(
                                    'filters' => array('fromDate' => array('operatorType' => 'date', 'name' => 'fromDate'), 'toDate' => array('operatorType' => 'date', 'name' => 'toDate')),
                                    'keyAttr' => 'lid', 'joinKeyAttr' => 'lid', 'joinWith' => 'travelmanager_plan'
                            ),
                            'users' => array(
                                    'filters' => array('uid' => array('operatorType' => 'multiple', 'name' => 'travelmanager_plan.uid')),
                                    'extraSelect' => 'CONCAT(firstName, \' \', lastName) AS fullName',
                                    'havingFilters' => array('fullName' => 'fullName'),
                                    'keyAttr' => 'uid', 'joinKeyAttr' => 'uid', 'joinWith' => 'travelmanager_plan'
                            )
                    )
            )
    );



    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filter_where = null;
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
    if(!empty($filter_where)) {
        $travelplan_objs = TravelManagerPlan::get_plan($filter_where, array('returnarray' => true));
    }
    if(is_array($travelplan_objs)) {
        foreach($travelplan_objs as $plan) {
            $leave = $plan->get_leave();
            $plan->displayName = $plan->get_leave()->get_type()->name.' - '.$plan->get_leave()->get_country()->get()['name'];
            $fromdate = date($core->settings['dateformat'], $leave->fromDate);
            $todate = date($core->settings['dateformat'], $leave->toDate);

            $employee = $plan->get_createdBy()->get()['displayName'];
            if(strlen($plan->createdOn) > 0) {
                $createdon = date($core->settings['dateformat'], $plan->createdOn);
            }

            $plan->link = 'index.php?module=travelmanager/viewplan&id='.$plan->tmpid;
            $finalized = '<img src="'.$core->settings['rootdir'].'/images/valid.gif" alt="'.$lang->finalized.'" border="0">';
            if($plan->isFinalized != 1) {
                $finalized = '<img src="'.$core->settings['rootdir'].'/images/invalid.gif" border="0">';
                $plan->link = 'index.php?module=travelmanager/plantrip&id='.$plan->tmpid;
            }
            eval("\$plan_rows .= \"".$template->get('travelmanager_listlpans_rows')."\";");
        }
    }
    eval("\$travelmanager_listlpant = \"".$template->get('travelmanager_listlpans')."\";");
    output_page($travelmanager_listlpant);
}