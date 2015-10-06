<?php
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('title', 'place', 'fromDate', 'toDate', 'publishOnWebsite'),
                    'overwriteField' => array(
                            'publishOnWebsite' => parse_selectlist('filters[publishOnWebsite]', 2, array('' => '', '1' => $lang->published, '0' => $lang->notpublished), $core->input['filters']['publishOnWebsite']),
                    )
            ),
            'process' => array(
                    'filterKey' => 'ceid',
                    'mainTable' => array(
                            'name' => 'calendar_events',
                            'filters' => array('title' => 'title', 'place' => 'place', 'fromDate' => array('operatorType' => 'date', 'name' => 'fromDate'), 'toDate' => array('operatorType' => 'date', 'name' => 'toDate')),
                    ),
            )
    );
    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();

    if(is_array($filter_where_values)) {
        if($filters_config['process']['filterKey'] == 'ceid') {
            $filters_config['process']['filterKey'] = 'ceid';
        }
        $filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table'));
    $filter_where .= ' AND (createdBy= '.$core->user['uid'].')';

//    if(is_array($core->input[filters])) {
//        $array_fields = array('title', 'place', 'fromDate', 'toDate');
//        foreach($array_fields as $field) {
//            if(isset($core->input['filters'][$field]) && !empty($core->input['filters'][$field])) {
//                $where_filter[$field] = ($core->input['filters'][$field]);
////                    $where_filter['fromDate'] = strtotime($core->input['filters']['fromDate']);
////                    $where_filter['toDate'] = strtotime($core->input['filters']['toDate']);
//            }
//        }
//    }
    $sort_url = sort_url();
    $where_filter['publishOnWebsite'] = 1;
    $dal_config = array(
            'simple' => false,
            'returnarray' => true,
            'order' => array('by' => 'fromDate', 'sort' => 'DESC')
    );

    $event_objs = Events::get_data($filter_where, $dal_config);
    unset($where_filter);

    if(is_array($event_objs)) {
        foreach($event_objs as $event) {
            $event->fromdate = date($core->settings['dateformat'], $event->fromDate);
            $event->todate = date($core->settings['dateformat'], $event->toDate);
            $eventtitle = $event->title;
            if($event->createdBy == $core->user['uid']) {
                $eventtitle = '<a target="_blank" href="index.php?module=calendar/manageevents&amp;id='.$event->ceid.'" title="'.$event->title.'">'.$event->title.'</a>';
            }
            if($event->publishOnWebsite == 1) {
                $ispublished_icon = '<img src="./images/valid.gif" border="0" title="'.$lang->published.'"/>';
            }
            else {
                $ispublished_icon = '<img src="./images/false.gif" border="0" />';
            }

            if($core->usergroup['cms_canPublishNews'] == 1) {
                $ispublished_icon = '<a href="index.php?module=cms/manageevents&action=togglepublish&id='.$event->get_id().'">'.$ispublished_icon.'</a>';
            }

            eval("\$cms_events_list_rows .= \"".$template->get('calendar_events_list_rows')."\";");
        }
    }
    $multipages = new Multipages('calendar_events', $core->settings['itemsperlist'], $multipage_where);
    $cms_events_list_rows .= "<tr><td colspan='6'>".$multipages->parse_multipages()."</td></tr>";


    eval("\$eventslist = \"".$template->get('cms_eventlist')."\";");
    output_page($eventslist);
}
