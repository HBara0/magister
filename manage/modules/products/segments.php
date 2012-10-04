<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Manage Segments
 * $module: admin/products
 * $id: segments.php	
 * Last Update: @zaher.reda 	Mar 18, 2009 | 03:32 PM
 */
if(!defined("DIRECT_ACCESS"))
{
	die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageSegments'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

$lang->load("products_segments");
if(!$core->input['action']) {	
	$query = $db->query("SELECT * FROM ".Tprefix."productsegments ORDER BY title ASC");
	if($db->num_rows($query) > 0) {
		while($segment = $db->fetch_array($query)) {
			$segments_list .= "<tr><td>".$segment['psid']."</td><td>".$segment['title']."</td><td>".$segment['description']."</td></tr>";
		}
	}
	else
	{
		$segments_list = "<tr><td colspan='3' style='text-align: center;'>{$lang->nosegementsavailable}</td></tr>";
	}
	eval("\$segmentspage = \"".$template->get("admin_products_segments")."\";");
	output_page($segmentspage);
}
else
{
	if($core->input['action'] == "do_add_segments") {
		if(empty($core->input['title'])) {
			output_xml("<status>false</status><message>{$lang->pleasefillintitle}</message>");
			exit;
		}
		
		if(value_exists("productsegments", "title", $core->input['title'])) {
			output_xml("<status>false</status><message>{$lang->segmentalreadyexists}</message>");
			exit;
		}
		$new_segment = array (
		"title"	=> $core->input['title'],
		"description"	=> $core->input['description']
		);
		
		$query = $db->insert_query("productsegments", $new_segment);
		if($query) {
			output_xml("<status>true</status><message>{$lang->segmentadded}</message>");
			log_action($core->input['title']);
		}
		else
		{
			output_xml("<status>false</status><message>{$lang->segmentadderror}</message>");
		}
	}	
}
?>