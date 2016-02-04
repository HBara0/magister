<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Lists related customers
 * $module: profiles
 * $id: customersslist.php
 * Created:	   	@najwa.kassem		October 18, 2010 | 10:28 AM
 * Last Update: 	@zaher.reda		 	May 04, 2012 | 11:12 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $sort_query = 'companyName ASC';

    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $core->input['sortby'].' '.$core->input['order'];
    }

    $sort_url = sort_url();
    $limit_start = 0;

    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = intval($core->input['perpage']);
    }

    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('companyName', 'affid', 'segment', 'type'),
                    'overwriteField' => array('type' => parse_selectlist('filters[type]', 4, array('' => '', 'c' => $lang->customer, 'pc' => $lang->prospect), $core->input['filters']['type']))
            ),
            'process' => array(
                    'filterKey' => 'eid',
                    'mainTable' => array(
                            'name' => 'entities',
                            'filters' => array('companyName' => 'companyName', 'type' => 'type'),
                    ),
                    'secTables' => array(
                            'affiliatedentities' => array(
                                    'filters' => array('affid' => array('operatorType' => 'multiple', 'name' => 'affid'))
                            ),
                            'entitiessegments' => array(
                                    'filters' => array('segment' => array('operatorType' => 'multiple', 'name' => 'psid'))
                            )
                    )
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();

    if(empty($core->input['filters']['type'])) {
        $filter_where .= ' e.type IN ("c", "pc") ';
    }
    if(is_array($filter_where_values)) {
        if(!empty($filter_where)) {
            $filter_where .= ' AND ';
        }
        $filter_where .= 'e.'.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table'));
    /* Perform inline filtering - END */

    $affiliate_filters_cache = $segment_filters_cache = array();

    if($core->usergroup['canViewAllCust'] == 0) {
        $filter_where .= ' AND (ae.uid='.$core->user['uid'].' AND e.eid IN ('.implode(',', $core->user['customers']).') OR e.createdBy='.$core->user['uid'].')';
    }
    $multipage_where .= $filter_where;
    $query = $db->query("SELECT DISTINCT(e.eid), e.companyName AS customername, e.companyNameAbbr, e.type
						FROM ".Tprefix."entities e
						JOIN ".Tprefix."affiliatedentities a ON (e.eid=a.eid)
						JOIN ".Tprefix."affiliatedemployees ae ON (a.affid=ae.affid)
                                                WHERE {$filter_where}
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

    if($db->num_rows($query) > 0) {
        while($customer = $db->fetch_assoc($query)) {
            $affiliates_counter = $segments_counter = 0;
            $affiliates = $hidden_affiliates = $show_affiliates = $segments = $hidden_segments = $show_segments = '';

            if(!empty($customer['companyNameAbbr'])) {
                $customer['customername'] .= ' ('.$customer['companyNameAbbr'].')';
            }

            $query2 = $db->query("SELECT ae.affid, a.name FROM ".Tprefix."affiliatedentities ae JOIN ".Tprefix."affiliates a ON (a.affid=ae.affid) WHERE ae.eid='$customer[eid]'  GROUP BY a.name ORDER BY a.name ASC");
            while($affiliate = $db->fetch_assoc($query2)) {

                if(++$affiliates_counter > 2) {
                    $hidden_affiliates .= "<a href='index.php?module=profiles/affiliateprofile&affid={$affiliate[affid]}'>{$affiliate['name']}</a> {$aff_filter_icon}<br />";
                }
                elseif($affiliates_counter == 2) {
                    $show_affiliates .= "<a href='index.php?module=profiles/affiliateprofile&affid={$affiliate[affid]}'>{$affiliate['name']}</a> {$aff_filter_icon}";
                }
                else {
                    $show_affiliates .= "<a href='index.php?module=profiles/affiliateprofile&affid={$affiliate[affid]}'>{$affiliate['name']}</a> {$aff_filter_icon}<br />";
                }

                if($affiliates_counter > 2) {
                    $affiliates = $show_affiliates.", <a href='#affiliate' id='showmore_affiliates_{$customer[eid]}' title='".$lang->showmore."'>...</a> <br /><span style='display:none;' id='affiliates_{$customer[eid]}'>{$hidden_affiliates}</span>";
                }
                else {
                    $affiliates = $show_affiliates;
                }
            }

            $query3 = $db->query("SELECT title,es.psid FROM ".Tprefix."productsegments p JOIN  ".Tprefix."entitiessegments es  ON (es.psid=p.psid) WHERE es.eid='$customer[eid]' ");
            while($segment = $db->fetch_assoc($query3)) {
                if(++$segments_counter > 2) {
                    $hidden_segments .= "<li>{$segment[title]}{$seg_filter_icon}</li>";
                }
                elseif($segments_counter == 2) {
                    $show_segments .= "<li>{$segment[title]}{$seg_filter_icon}";
                }
                else {
                    $show_segments .= "<li>{$segment[title]}{$seg_filter_icon}</li>";
                }

                if($segments_counter > 2) {
                    $segments = '<ul style="list-style:none; padding:2px;margin-top:0px;">'.$show_segments.", <a href='#segment' id='showmore_segments_{$customer[eid]}' title='".$lang->showmore."'>...</a></li> <span style='display:none;' id='segments_{$customer[eid]}'>{$hidden_segments}</span></ul>";
                }
                else {
                    $segments = '<ul style="list-style:none; padding:2px;margin-top:0px;">'.$show_segments.'</ul>';
                }
            }

            $customers_list .= "<tr class='{$class}'><td valign='top'><a href='index.php?module=profiles/entityprofile&eid={$customer[eid]}'>{$customer[customername]}</td><td valign='top'>{$affiliates}</td><td valign='top'>{$segments}</td><td>".strtoupper($customer['type'])."</td><td>";
            if($core->usergroup['canAdminCP'] == 1 || $customer['createdBy'] == $core->user['uid']) {
                $customers_list .= "<a href='{$core->settings[rootdir]}/{$config[admindir]}/index.php?module=entities/edit&amp;eid={$customer[eid]}'><img src='{$core->settings[rootdir]}/images/edit.gif' alt='{$lang->edit}' border='0' /></a>";
            }
            $customers_list .= '</td></tr>';
        }

//        $multipages = new Multipages('entities e JOIN '.Tprefix.'affiliatedentities a ON (e.eid=a.eid) JOIN '.Tprefix.'affiliatedemployees ae ON (a.affid=ae.affid)', $core->settings['itemsperlist'], $multipage_where);
//        $customers_list .= '<tr><td colspan="4">'.$multipages->parse_multipages().'</td></tr>';
    }
    else {
        $customers_list .= '<tr><td colspan="4">'.$lang->na.'</td></tr>';
    }
    $onclickactin = "$('#tabletoexport').tableExport({type:'excel',escape:'false'});";
    $toolgenerate = '<div align="right" title="'.$lang->generate.'" style="float:right;padding:10px;width:10px;"><a onClick ="'.$onclickactin.'"><img src="./images/icons/xls.gif"/>'.$lang->generateexcel.'</a></div>';

    eval("\$listpage = \"".$template->get('profiles_customerslist')."\";");
    output_page($listpage);
}
?>