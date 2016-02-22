<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Lists all suppliers
 * $module: profiles
 * $id: supplierslist.php
 * Created:	   	@najwa.kassem		October 15, 2010 | 10:28 AM
 * Last Update: 	@zaher.reda		 October 27, 2010 | 11:17 AM
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
    $type_querywhere = ' type="s"';
    if(!empty($core->input['type'])) {
        $type_querywhere = ' type="'.$db->escape_string($core->input['type']).'"';
    }
    $multipage_where = $type_querywhere;
    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }

    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('companyName', 'affid', 'segment')
            ),
            'process' => array(
                    'filterKey' => 'eid',
                    'mainTable' => array(
                            'name' => 'entities',
                            'filters' => array('companyName' => 'companyName'),
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


    if(is_array($filter_where_values)) {
        $filter_where = 'AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table'));
    /* Perform inline filtering - END */

    $query = $db->query("SELECT *, companyName AS entityname
						FROM ".Tprefix."entities
						WHERE".$type_querywhere." {$filter_where}
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

    if($db->num_rows($query) > 0) {
        while($supplier = $db->fetch_assoc($query)) {
            $query2 = $db->query("SELECT ae.affid, a.name FROM ".Tprefix."affiliatedentities ae  JOIN ".Tprefix."affiliates a ON (a.affid=ae.affid) WHERE ae.eid='$supplier[eid]' GROUP BY a.name ORDER BY a.name ASC");

            $segments_counter = $affiliates_counter = 0;

            $affiliates = $hidden_affiliates = $show_affiliates = '';
            $segments = $hidden_segments = $show_segments = '';
            while($affiliate = $db->fetch_assoc($query2)) {
                if(++$affiliates_counter > 2) {
                    $hidden_affiliates .= '<a href="index.php?module=profiles/affiliateprofile&affid='.$affiliate['affid'].'">'.$affiliate['name'].'</a><br />';
                }
                elseif($affiliates_counter == 2) {
                    $show_affiliates .= '<a href="index.php?module=profiles/affiliateprofile&affid='.$affiliate['affid'].'">'.$affiliate['name'].'</a>';
                }
                else {
                    $show_affiliates .= '<a href="index.php?module=profiles/affiliateprofile&affid='.$affiliate['affid'].'">'.$affiliate['name'].'</a><br />';
                }

                if($affiliates_counter > 2) {
                    $affiliates = $show_affiliates.", <a href='#affiliate' id='showmore_affiliates_{$supplier[eid]}' title='".$lang->showmore."'>...</a> <br /><span style='display:none;' id='affiliates_{$supplier[eid]}'>{$hidden_affiliates}</span>";
                }
                else {
                    $affiliates = $show_affiliates;
                }
            }

            $query3 = $db->query("SELECT title, es.psid FROM ".Tprefix."productsegments p JOIN ".Tprefix."entitiessegments es ON (es.psid=p.psid) WHERE es.eid='$supplier[eid]'");
            while($segment = $db->fetch_assoc($query3)) {
                if(++$segments_counter > 2) {
                    $hidden_segments .= '<a href="index.php?module=profiles/segmentprofile&id='.$segment['psid'].'" target="_blank">'.$segment['title'].'</a><br />';
                }
                elseif($segments_counter == 2) {
                    $show_segments .= '<a href="index.php?module=profiles/segmentprofile&id='.$segment['psid'].'" target="_blank">'.$segment['title'].'</a>';
                }
                else {
                    $show_segments .= '<a href="index.php?module=profiles/segmentprofile&id='.$segment['psid'].'" target="_blank">'.$segment['title'].'</a><br />';
                }

                if($segments_counter > 2) {
                    $segments = $show_segments.", <a href='#segment' id='showmore_segments_{$supplier[eid]}' title='".$lang->showmore."'>...</a><br /> <span style='display:none;' id='segments_{$supplier[eid]}'>{$hidden_segments}</span>";
                }
                else {
                    $segments = $show_segments;
                }
            }

            $suppliers_list .= "<tr class='{$class}'><td valign='top'><a href='index.php?module=profiles/entityprofile&amp;eid={$supplier[eid]}'>{$supplier[companyName]}</td><td valign='top'>{$affiliates}</td><td valign='top'>{$segments}</td>";
        }

//        $multipages = new Multipages('entities', $core->settings['itemsperlist'], $multipage_where);
//        $suppliers_list .= '<tr><td colspan="3">'.$multipages->parse_multipages().'</td></tr>';
    }
    else {
        $suppliers_list = '<tr><td colspan="3">'.$lang->no.'</td></tr>';
    }

    eval("\$listpage = \"".$template->get('profiles_supplierslist')."\";");
    output_page($listpage);
}
?>