<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: segmentlist.php
 * Created:        @tony.assaad    Jun 19, 2014 | 10:10:32 AM
 * Last Update:    @tony.assaad    Jun 19, 2014 | 10:10:32 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!($core->input['action'])) {
    $segment_obs = ProductsSegments::get_segment($filters);
    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }
    if(is_array($segment_obs)) {
        foreach($segment_obs as $segment_ob) {
            $segcoord_objs = $segment_ob->get_coordinators();
            $segment_coordinator = '';
            if(is_array($segcoord_objs)) {
                foreach($segcoord_objs as $segcoord_obj) {
                    $segment_coordinator .= $segcoord_obj->get_coordinator()->get_displayname();
                }
            }

            $segment_list.='<tr><td><a href="index.php?module=profiles/segmentprofile&id='.$segment_ob->psid.'" target="_blank">'.$segment_ob->get_displayname().'</a></td><td><a href="users.php?action=profile&uid='.$segcoord_obj->get_coordinator()->uid.'" target="_blank">'.$segment_coordinator.'</a></td></tr>';
        }
    }
    eval("\$segmentslist = \"".$template->get('profiles_segmentslist')."\";");
    output_page($segmentslist);
}
