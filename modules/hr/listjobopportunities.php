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

//    /* Perform inline filtering - START */
//    $filters_config = array(
//            'parse' => array('filters' => array('title', 'isPublished', 'createdBy', 'lang', 'hits', 'date'),
//                    'overwriteField' => array('version' => parse_selectlist('filters[version]', 5, array_combine($version_scale, $version_scale), $core->input['filters']['version'], 1),
//                            'isPublished' => parse_selectlist('filters[isPublished]', 2, array('' => '', '1' => $lang->published, '0' => $lang->notpublished), $core->input['filters']['isPublished']),
//                            'lang' => parse_selectlist('filters[lang]', 2, array('' => '', 'en' => $lang->english, 'fr' => $lang->french), $core->input['filters']['lang'])
//                    )
//            ),
//            'process' => array(
//                    'filterKey' => 'cmsnid',
//                    'mainTable' => array(
//                            'name' => 'cms_news',
//                            'filters' => array('title' => 'title', 'version' => 'version', 'isPublished' => 'isPublished', 'lang' => 'lang', 'hits' => 'hits', 'date' => array('operatorType' => 'date', 'name' => 'publishDate')),
//                    ),
//            )
//    );
//    $filter = new Inlinefilters($filters_config);
//    $filter_where_values = $filter->process_multi_filters();
//
//    if(is_array($filter_where_values)) {
//        if($filters_config['process']['filterKey'] == 'cmsnid') {
//            $filters_config['process']['filterKey'] = 'cn.cmsnid';
//        }
//        $filter_where = 'WHERE '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
//        $multipage_where .= $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
//    }
//
//    $filters_row = $filter->prase_filtersrows(array('tags' => 'table'));
//
//    $news_details = $allnews->get_multiplenews($filter_where);
//


    $hrjobopportunities = HrJobOpportunities::get_data(array('createdBy' => $core->user['uid']), array('returnarray' => true, 'simple' => false));
    if(is_array($hrjobopportunities)) {
        foreach($hrjobopportunities as $id => $jobopportunity) {
            $jobopportunity = $jobopportunity->get();
            $createdby_obj = new Users($jobopportunity['createdBy']);
            $jobopportunity['createdBy_output'] = $createdby_obj->parse_link();
            $jobopportunity['createdOn_output'] = date($core->settings['dateformat'], $surveystemplate['createdOn']);
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
        $surveys_rows .= '<tr><td colspan="5">'.$lang->na.'</td></tr>';
    }
    eval("\$surveytemplateslist = \"".$template->get('hr_listjobopportunities')."\";");
    output_page($surveytemplateslist);
}


