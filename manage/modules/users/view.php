<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 *
 * View users
 * $module: admin/users
 * $id: view.php
 * Last Update: @zaher.reda 	Sep 22, 2010 | 10:25 AM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageUsers'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $sort_query = "u.username ASC";
    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $core->input['sortby']." ".$core->input['order'];
    }
    $sort_url = sort_url();
    $filters_config = array(
            'parse' => array('filters' => array('id', 'username', 'email', 'usergroup', 'affiliate', 'lastvisit'),
                    'overwriteField' => array(
                            'affiliate' => '',
                            'lastvisit' => '',
                            'usergroup' => '',
                    )
            ),
            'process' => array(
                    'filterKey' => 'uid',
                    'mainTable' => array(
                            'name' => 'users',
                            'filters' => array('id' => array('operatorType' => 'equal', 'name' => 'uid'), 'username' => array('name' => 'username'), 'email' => array('name' => 'email')),
                    ),
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filter_where = null;
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' WHERE '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));

    $limit_start = 0;
    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }

    $query = $db->query("SELECT u.*, g.title AS grouptitle
						FROM ".Tprefix."users u LEFT JOIN ".Tprefix."usergroups g ON (u.gid=g.gid){$filter_where}
                                                ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");
    if($db->num_rows($query) > 0) {
        while($user = $db->fetch_array($query)) {
            $class = alt_row($class);
            $useraffiliates = $hidden_affiliates = $break = "";

            $query2 = $db->query("SELECT aff.name as affiliatename
								  FROM ".Tprefix."affiliates aff LEFT JOIN ".Tprefix."affiliatedemployees ae ON (ae.affid=aff.affid)
								  WHERE ae.uid='{$user[uid]}'
								  ORDER BY aff.name ASC");
            $affiliates_counter = 0;
            while($affiliate = $db->fetch_array($query2)) {
                if(++$affiliates_counter > 2) {
                    $hidden_affiliates .= $break.$affiliate['affiliatename'];
                }
                else {
                    $useraffiliates .= $break.$affiliate['affiliatename'];
                }
                $break = "<br />";
            }
            if($affiliates_counter > 2) {
                $useraffiliates = $useraffiliates.", <a href='#' id='showmore_affiliates_{$user[uid]}'>...</a> <span style='display:none;' id='affiliates_{$user[uid]}'>{$hidden_affiliates}</span>";
            }

            if($user['lastVisit'] != 0) {
                $lastvisit = date($core->settings['dateformat']." ".$core->settings['timeformat'], $user['lastVisit']);
            }
            else {
                $lastvisit = $lang->never;
            }
            $userslist .= "<tr class='{$class}'>";
            $userslist .= "<td>{$user[uid]}</td><td><a href='../users.php?action=profile&uid={$user[uid]}' target='_blank'>{$user[username]}</a></td><td>{$user[email]}&nbsp;</td><td>{$user[grouptitle]}</td><td>{$useraffiliates}&nbsp;</td><td>{$lastvisit}&nbsp;</td>";
            $userslist .= "<td><a href='index.php?module=users/edit&amp;uid={$user[uid]}'><img src='{$core->settings[rootdir]}/images/edit.gif' alt='{$lang->edit}' border='0' /></a></td><tr>";
        }
        $multipages = new Multipages("users", $core->settings['itemsperlist']);
        $userslist .= "<tr><td colspan='7'>".$multipages->parse_multipages()."</td></tr>";
    }
    else {
        $userslist = "<tr><td colspan='5'>{$lang->nousers}</td></tr>";
    }

    eval("\$viewpage = \"".$template->get("admin_users_view")."\";");
    output_page($viewpage);
}
?>