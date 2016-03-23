<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Lists all affiliates
 * $module: profiles
 * $id: affiliateslist.php
 * Created:	   	@najwa.kassem		October 18, 2010 | 10:28 AM
 * Last Update: 	@zaher.reda		 	May 11, 2012 | 05:22 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    if($core->input['view'] == 'map') {
        $switchview = array('icon' => 'list_view.gif', 'link' => 'list');

        $datatable_display = 'none';
        $query = $db->query("SELECT affid, name, phone1, X(geoLocation) AS longitude, Y(geoLocation) AS latitude FROM ".Tprefix."affiliates WHERE asText(geoLocation) IS NOT NULL AND isActive=1");
        while($affiliate = $db->fetch_assoc($query)) {
            $markers[$affiliate['affid']] = array('title' => $affiliate['name'], 'otherinfo' => $affiliate['phone1'], 'geoLocation' => $affiliate['longitude'].','.$affiliate['latitude'], 'type' => 'affiliateprofile');
        }
        $map = new Maps($markers, array('infowindow' => 1, 'mapcenter' => '25.887078000731695, 40.195312999999935'));

        $map_view = $map->get_map(800);
    }
    else {
        $switchview = array('icon' => 'mapmarker.png', 'link' => 'map');
        $datatable_display = 'table';
        $sort_query = 'name ASC';

        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = $core->input['sortby'].' '.$core->input['order'];
        }

        $multipage_where = 'isActive=1';
        if(isset($core->input['filterby'], $core->input['filtervalue'])) {
            $value_accepted = true;

            if($core->input['filterby'] == 'generalManager') {
                $field = "generalManager";
            }
            elseif($core->input['filterby'] == 'supervisor') {
                $field = "supervisor";
            }
            else {
                $value_accepted = false;
            }

            if($value_accepted == true) {
                $where = $field." IN (SELECT ".$field." FROM ".Tprefix."affiliates WHERE ".$db->escape_string($core->input['filterby']).'='.$db->escape_string($core->input['filtervalue']).")";
                $extra_where = " AND ".$where;
            }
            else {
                $extra_where = '';
            }
            $multipage_where .= ' AND '.$where;
        }
        $sort_url = sort_url();
        $limit_start = 0;
        $gm_filters_cache = $supervisor_filters_cache = array();

        if(isset($core->input['start'])) {
            $limit_start = intval($core->input['start']);
        }

        if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
            $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
        }
        $query = $db->query("SELECT *
							FROM ".Tprefix."affiliates
                                                        WHERE isActive=1
							{$extra_where}
							ORDER BY {$sort_query}
							LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

        if($db->num_rows($query) > 0) {
            while($affiliate = $db->fetch_assoc($query)) {
                $countries_counter = 0;
                $countries = $hidden_countries = $show_countries = '';
                $countries_query = $db->query("SELECT name 	FROM ".Tprefix."countries WHERE affid={$affiliate[affid]} ORDER BY name");
                while($countries = $db->fetch_array($countries_query)) {
                    if(++$countries_counter > 2) {
                        $hidden_countries .= "{$countries['name']}<br />";
                    }
                    elseif($countries_counter == 2) {
                        $show_countries .= "{$countries['name']}";
                    }
                    else {
                        $show_countries .= "{$countries['name']}<br />";
                    }

                    if($countries_counter > 2) {
                        $countries_list = $show_countries.", <a href='#countries' id='showmore_countries_{$affiliate[affid]}'>...</a> <br /><span style='display:none;' id='countries_{$affiliate[affid]}'>{$hidden_countries}</span>";
                    }
                    else {
                        $countries_list = $show_countries;
                    }
                }

                $management_query = $db->query("SELECT uid,CONCAT(firstName, ' ', lastName) AS generalManager FROM ".Tprefix."users WHERE uid IN ({$affiliate['supervisor']},{$affiliate['generalManager']})");

                while($management = $db->fetch_array($management_query)) {
                    $managers[$management['uid']] = $management['generalManager'];
                }
                if($affiliate['generalManager'] == 0) {
                    $gm = $lang->na;
                }
                else {
                    if(!in_array($affiliate['generalManager'], $gm_filters_cache)) {
                        // $gm = "<a href='./users.php?action=profile&uid={$affiliate['generalManager']}' target='_blank'>".$managers[$affiliate['generalManager']]."</a><a href='index.php?module=profiles/affiliateslist&filterby=generalManager&filtervalue={$affiliate[generalManager]}'> <img src='./images/icons/search.gif' border='0' alt='{$lang->filterby}'/></a>";
                        $gm_filters_cache[] = $affiliate['generalManager'];
                    }
                    else {
                        $gm = "<a href='./users.php?action=profile&uid={$affiliate['generalManager']}' target='_blank'>".$managers[$affiliate['generalManager']]."</a>";
                    }
                }

                if($affiliate['supervisor'] == 0) {
                    $supervisor = $lang->na;
                }
                else {
                    if(!in_array($affiliate['supervisor'], $supervisor_filters_cache)) {
                        //  $supervisor = "<a href='./users.php?action=profile&uid={$affiliate['supervisor']}' target='_blank'>".$managers[$affiliate['supervisor']]."</a><a href='index.php?module=profiles/affiliateslist&filterby=supervisor&filtervalue={$affiliate[supervisor]}'> <img src='./images/icons/search.gif' border='0' alt='{$lang->filterby}'/></a>";
                        $supervisor_filters_cache[] = $affiliate['supervisor'];
                    }
                    else {
                        $supervisor = "<a href='./users.php?action=profile&uid={$affiliate['supervisor']}' target='_blank'>".$managers[$affiliate['supervisor']]."</a>";
                    }
                }
                $affiliates_list .= "<tr class='{$class}'><td valign='top'><a href='index.php?module=profiles/affiliateprofile&affid={$affiliate[affid]}'>{$affiliate[name]}</td><td valign='top'>{$countries_list}</td><td valign='top'>{$gm}</td><td valign='top'>{$supervisor}</td>";
            }

//            $multipages = new Multipages('affiliates ', $core->settings['itemsperlist'], $multipage_where);
//            $affiliates_list .= '<tr><td colspan="4">'.$multipages->parse_multipages().'</td></tr>';
        }
        else {
            $affiliates_list .= '<tr><td colspan="4">'.$lang->no.'</td></tr>';
        }
    }

    eval("\$listpage .= \"".$template->get('profiles_affiliateslist')."\";");
    output_page($listpage);
}
?>