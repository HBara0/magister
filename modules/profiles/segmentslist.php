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
    $segment_obs = ProductsSegments::get_segments($filters);
    if(is_array($segment_obs)) {
        foreach($segment_obs as $segment_ob) {
            $segcoord_objs = $segment_ob->get_coordinators();
            $seg_coordinators_output = '';
            if(is_array($segcoord_objs)) {
                foreach($segcoord_objs as $segcoord_obj) {
                    $segment_coordinators[] = $segcoord_obj->get_coordinator()->parse_link();
                }
                $seg_coordinators_output = implode(', ', $segment_coordinators);
                unset($segment_coordinators, $segcoord_objs);
            }

            $segments_rows .= '<tr><td><a href="index.php?module=profiles/segmentprofile&id='.$segment_ob->psid.'" target="_blank">'.$segment_ob->get_displayname().'</a></td><td>'.$seg_coordinators_output.'</td></tr>';
        }
    }
    else {
        $segments_rows .= '<tr><td colspan="2">'.$lang->na.'</tr>';
    }
    eval("\$segmentslist = \"".$template->get('profiles_segmentslist')."\";");
    output_page($segmentslist);
}
