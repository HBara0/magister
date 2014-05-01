<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Lookup Flights
 * $module: travelmanager
 * $id: lookupflights.php	
 * Created: 	@najwa.kassem		February 3, 2010 | 9:30 AM
 * Last Update: @zaher.reda 		February 4, 2011 | 04:44 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canUseTravelManager'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    $airports = get_specificdata('travelmanager_airports ap JOIN cities c ON (ap.ciid=c.ciid)', array('apid', "CONCAT(c.name, ', ', ap.name) AS airport"), 'apid', 'airport', 1, 1);
    if(is_array($airports)) {
        $flyfrom_field = parse_selectlist('flyingFrom', 1, $airports, 0);
        $flyto_field = parse_selectlist('flyingTo', 2, $airports, 0);
    }
    else {
        $flyfrom_field = $flyto_field = $lang->na;
    }
    eval("\$lookupflight_page = \"".$template->get('travelmanager_lookupflights')."\";");
    output_page($lookupflight_page);
}
elseif($core->input['action'] == 'do_lookupflights') {
    if(is_empty($core->input['flyingFrom'], $core->input['flyingTo'])) {
        error($lang->fillallrequiredfields, $_SERVER['HTTP_REFERER']);
    }

    if($core->input['flyingFrom'] == $core->input['flyingTo']) {
        error($lang->conflicatingdestination, $_SERVER['HTTP_REFERER']);
    }

    if(!empty($core->input['maxrate'])) {
        $rate_query_string = ' AND rate <= '.$db->escape_string($core->input['maxrate']);
    }

    $query = $db->query("SELECT af.aflid, a.name, afr.*, a.contracted
						FROM ".Tprefix."travelmanager_airlinerflights af 
						JOIN ".Tprefix."travelmanager_airlines a ON (af.alid=a.alid)
						JOIN ".Tprefix."travelmanager_flightrates afr ON (afr.aflid=af.aflid)
						WHERE flyingFrom=".$db->escape_string($core->input['flyingFrom'])." 
						AND flyingTo =".$db->escape_string($core->input['flyingTo'])."{$rate_query_string}
						ORDER BY a.contracted DESC, rate ASC");

    if($db->num_rows($query) > 0) {
        while($flight = $db->fetch_assoc($query)) {
            $row_class = alt_row($row_class);

            if($flight['isOneWay'] == 1)
                $oneway = $lang->oneway;
            else {
                $oneway = $lang->roundway;
            }
            $star = '';
            if($flight['contracted']) {
                $star = '*';
            }
            $flight_row .= '<div class="'.$row_class.'">'.$flight['name'].' - $'.$flight['rate'].'-'.strtoupper($flight['class']).' ('.$oneway.')'.$star.'</div>';
        }
    }
    else {
        $flight_row = $lang->nomatchfound;
    }
    eval("\$lookupresults_page = \"".$template->get('travelmanager_lookupresults')."\";");
    output_page($lookupresults_page);
}
?>