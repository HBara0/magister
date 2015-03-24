<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2011 Orkila International Offshore, All Rights Reserved
 *
 * List Of reputations
 * $module: reputation
 * $id: reputationslist.php
 * Created:	    @najwa.kassem 	September 09, 2011 | 12:22 PM
 * Last Update: @najwa.kassem 	September 09, 2011 | 12:22 PM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $sort_query = 'timeLine DESC';
    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $core->input['sortby'].' '.$core->input['order'];
    }

    $sort_url = sort_url();
    $limit_start = 0;

    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }
    $query = $db->query("SELECT * 
						FROM ".Tprefix."reputation
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");
    if($db->num_rows($query) > 0) {
        while($reputation = $db->fetch_assoc($query)) {
            $rowclass = alt_row($rowclass);
            eval("\$reputations_list .= \"".$template->get('reputation_list_entryrow')."\";");
        }
        $multipages = new Multipages('reputation', $core->settings['itemsperlist'], $multipage_where);
        $reputations_list .= '<tr><td colspan="5">'.$multipages->parse_multipages().'</td></tr>';
    }
    else {
        $reputations_list .= '<tr><td colspan="5" style="text-align:center;">'.$lang->nomatchfound.'</td></tr>';
    }
    eval("\$reputations = \"".$template->get('reputation_list')."\";");
    output_page($reputations);
}
?>