<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: listjobopportunities.php
 * Created:        @rasha.aboushakra    Nov 3, 2015 | 10:30:12 AM
 * Last Update:    @rasha.aboushakra    Nov 3, 2015 | 10:30:12 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['hr_canCreateJobOpport'] == 10) {
    // error($lang->sectionnopermission);
    exit;
}

$lang->load('hr_jobopportunities');

if(!$core->input['action']) {
    $sort_url = sort_url();

    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('title', 'isPublished', 'applicants', 'views', 'createdOn'),
                    'overwriteField' => array('isPublished' => parse_selectlist('filters[isPublished]', 2, array('' => '', '1' => 'yes', '0' => 'no'), $core->input['filters']['isPublished']),
                    )
            ),
            'process' => array(
                    'filterKey' => 'joid',
                    'mainTable' => array(
                            'name' => 'hr_jobopprtunities',
                            'filters' => array('title' => 'title', 'isPublished' => 'isPublished', 'countApplicants' => 'applicants', 'countViews' => 'views', 'createdOn' => array('operatorType' => 'date', 'name' => 'createdOn')),
                    ),
            )
    );
    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filters_row = $filter->prase_filtersrows(array('tags' => 'table'));


    $where = 'createdBy='.$core->user[uid];
    if(!empty($filter_where_values) && is_array($filter_where_values)) {
        $filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(', ', $filter_where_values).')';
    }
}
$hrjobopportunities = HrJobOpportunities::get_data($where, array('returnarray' => true, 'simple' => false));
if(is_array($hrjobopportunities)) {
    foreach($hrjobopportunities as $id => $jobopportunity) {
        $jobopportunity = $jobopportunity->get();
        $createdby_obj = new Users($jobopportunity['createdBy']);
        $jobopportunity['createdBy_output'] = $createdby_obj->parse_link();
        $jobopportunity['createdOn_output'] = date($core->settings['dateformat'], $jobopportunity['createdOn']);
        $rowclass = alt_row($rowclass);
        $jobopportunity['isPublished_output'] = "<img src='{$core->settings[rootdir]}/images/invalid.gif' border='0' alt='{$lang->edit}' />";

        if($jobopportunity['isPublished'] == 1) {
            $jobopportunity['isPublished_output'] = " <img src='{$core->settings[rootdir]}/images/valid.gif' border='0' alt='{$lang->edit}' />";
        }
        $jobopportunity['edit_link'] = "<a href='index.php?module=hr/managejobopportunity&amp;id=".$id."'><img src='{$core->settings[rootdir]}/images/icons/edit.gif' border='0' alt='{$lang->edit}' /></a>";
//             $jobopportunity['applicants'] = 0;
//            $applicants = HrJobApplicants::get_data(array('joid' => $jobopportunity['joid']), array('returnarray' => true));
//            if(is_array($applicants)) {
//                $jobopportunity['applicants'] = count($applicants);
//            }
        eval("\$hr_listjobopportunities_rows .= \"".$template->get('hr_listjobopportunities_row')."\";");
        unset($applicants, $createdby_obj);
    }
}
else {
    $hr_listjobopportunities_rows .= '<tr><td colspan="5">'.$lang->na.'</td></tr>';
}
eval("\$listjobopportunities = \"".$template->get('hr_listjobopportunities')."\";");
output_page($listjobopportunities);
}


