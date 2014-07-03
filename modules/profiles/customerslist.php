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
    $multipage_where = ' type IN ("c", "pc") ';
    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = intval($core->input['perpage']);
    }

    if(isset($core->input['filterby'], $core->input['filtervalue'])) {
        $value_accepted = true;

        if($core->input['filterby'] == 'affid') {
            $table = 'affiliatedentities';
        }
        elseif($core->input['filterby'] == 'psid') {
            $table = 'entitiessegments';
        }
        else {
            $value_accepted = false;
        }

        if($value_accepted == true) {
            $extra_where = " AND e.eid IN (SELECT eid FROM ".Tprefix.$table." WHERE ".$db->escape_string($core->input['filterby']).'='.$db->escape_string($core->input['filtervalue']).")";
        }
        else {
            if($core->input['filterby'] == 'name') {
                $extra_where = ' AND (e.companyName LIKE "%'.$db->escape_string($core->input['filtervalue']).'%" OR e.companyNameAbbr LIKE "%'.$db->escape_string($core->input['filtervalue']).'%")';
            }
            else {
                $extra_where = '';
            }
        }
        $multipage_where .= $extra_where;
    }

    $affiliate_filters_cache = $segment_filters_cache = array();

    if($core->usergroup['canViewAllCust'] == 0) {
        $query_string = ' AND ase.uid='.$core->user['uid'].' AND e.eid IN ('.implode(',', $core->user['customers']).')';
    }

    $query = $db->query("SELECT DISTINCT(e.eid), e.companyName AS customername, e.companyNameAbbr, e.type
						FROM ".Tprefix."entities e
						JOIN ".Tprefix."affiliatedentities a ON (e.eid=a.eid)
						JOIN ".Tprefix."affiliatedemployees ae ON (a.affid=ae.affid)
						WHERE e.type IN ('c', 'pc'){$extra_where}
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

    if($db->num_rows($query) > 0) {
        while($customer = $db->fetch_assoc($query)) {
            $affiliates_counter = $segments_counter = 0;
            $affiliates = $hidden_affiliates = $show_affiliates = $segments = $hidden_segments = $show_segments = '';

            if(!empty($customer['companyNameAbbr'])) {
                $customer['customername'] .= ' ('.$customer['companyNameAbbr'].')';
            }

            $query2 = $db->query("SELECT ae.affid, a.name FROM ".Tprefix."affiliatedentities ae  JOIN ".Tprefix."affiliates a ON (a.affid=ae.affid) WHERE ae.eid='$customer[eid]'  GROUP BY a.name ORDER BY a.name ASC");

            while($affiliate = $db->fetch_assoc($query2)) {
                if(!in_array($affiliate['affid'], $affiliate_filters_cache)) {
                    $aff_filter_icon = "<a href='index.php?module=profiles/customerslist&filterby=affid&filtervalue={$affiliate[affid]}'> <img src='./images/icons/search.gif' border='0' alt='{$lang->filterby}'/></a>";
                }
                else {
                    $aff_filter_icon = '';
                }

                if(++$affiliates_counter > 2) {
                    $hidden_affiliates .= "<a href='index.php?module=profiles/affiliateprofile&affid={$affiliate[affid]}'>{$affiliate['name']}</a> {$aff_filter_icon}<br />";
                }
                elseif($affiliates_counter == 2) {
                    $show_affiliates .= "<a href='index.php?module=profiles/affiliateprofile&affid={$affiliate[affid]}'>{$affiliate['name']}</a> {$aff_filter_icon}";
                    $affiliate_filters_cache[] = $affiliate['affid'];
                }
                else {
                    $show_affiliates .= "<a href='index.php?module=profiles/affiliateprofile&affid={$affiliate[affid]}'>{$affiliate['name']}</a> {$aff_filter_icon}<br />";
                    $affiliate_filters_cache[] = $affiliate['affid'];
                }

                if($affiliates_counter > 2) {
                    $affiliates = $show_affiliates.", <a href='#affiliate' id='showmore_affiliates_{$customer[eid]}'>...</a> <br /><span style='display:none;' id='affiliates_{$customer[eid]}'>{$hidden_affiliates}</span>";
                }
                else {
                    $affiliates = $show_affiliates;
                }
            }

            $query3 = $db->query("SELECT title,es.psid FROM ".Tprefix."productsegments p JOIN  ".Tprefix."entitiessegments es  ON (es.psid=p.psid) WHERE es.eid='$customer[eid]' ");

            while($segment = $db->fetch_assoc($query3)) {
                if(!in_array($segment['psid'], $segment_filters_cache)) {
                    $seg_filter_icon = "<a href='index.php?module=profiles/customerslist&filterby=psid&filtervalue={$segment[psid]}'><img src='./images/icons/search.gif' border='0' alt='{$lang->filterby}'/></a>";
                }
                else {
                    $seg_filter_icon = '';
                }

                if(++$segments_counter > 2) {
                    $hidden_segments .= "<li>{$segment[title]}{$seg_filter_icon}</li>";
                }
                elseif($segments_counter == 2) {
                    $show_segments .= "<li>{$segment[title]}{$seg_filter_icon}";
                    $segment_filters_cache[] = $segment['psid'];
                }
                else {
                    $show_segments .= "<li>{$segment[title]}{$seg_filter_icon}</li>";
                    $segment_filters_cache[] = $segment['psid'];
                }

                if($segments_counter > 2) {
                    $segments = '<ul style="list-style:none; padding:2px;margin-top:0px;">'.$show_segments.", <a href='#segment' id='showmore_segments_{$customer[eid]}'>...</a></li> <span style='display:none;' id='segments_{$customer[eid]}'>{$hidden_segments}</span></ul>";
                }
                else {
                    $segments = '<ul style="list-style:none; padding:2px;margin-top:0px;">'.$show_segments.'</ul>';
                }
            }

            $customers_list .= "<tr class='{$class}'><td valign='top'><a href='index.php?module=profiles/entityprofile&eid={$customer[eid]}'>{$customer[customername]}</td><td valign='top'>{$affiliates}</td><td valign='top'>{$segments}</td><td>".strtoupper($customer['type'])."</td><td>";
            if($core->usergroup['canAdminCP'] == 1) {
                $customers_list .= "<a href='{$core->settings[rootdir]}/{$config[admindir]}/index.php?module=entities/edit&amp;eid={$customer[eid]}'><img src='{$core->settings[rootdir]}/images/edit.gif' alt='{$lang->edit}' border='0' /></a>";
            }
            $customers_list .= '</td></tr>';
        }

        $multipages = new Multipages('entities e', $core->settings['itemsperlist'], $multipage_where);
        $customers_list .= '<tr><td colspan="4">'.$multipages->parse_multipages().'</td></tr>';
    }
    else {
        $customers_list .= '<tr><td colspan="4">'.$lang->na.'</td></tr>';
    }

    eval("\$listpage = \"".$template->get('profiles_customerslist')."\";");
    output_page($listpage);
}
?>