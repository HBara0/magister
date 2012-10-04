<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Manage generic products
 * $module: admin/maintenance
 * $id: generics.php	
 * Last Update: @zaher.reda 	Mar 19, 2009 | 10:00 AM
 */
if(!defined("DIRECT_ACCESS"))
{
	die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageGenericProducts'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

$lang->load("products_generics");
if(!$core->input['action']) {	
	$query = $db->query("SELECT g.*, s.title AS segmenttitle FROM ".Tprefix."genericproducts g LEFT JOIN ".Tprefix."productsegments s ON (s.psid=g.psid) ORDER BY g.title ASC");
	if($db->num_rows($query) > 0) {
		while($generic = $db->fetch_array($query)) {
			$generics_list .= "<tr><td>".$generic['gpid']."</td><td>".$generic['title']."</td><td>".$generic['segmenttitle']."</td></tr>";//<td>".$generic['description']."</td> hidesshow row
		}
	}
	else
	{
		$generics_list = "<tr><td colspan='3' style='text-align: center;'>{$lang->nogenericsavailable}</td></tr>";
	}
	
	$segment_attributes = array("psid", "title");
	$segment_order = array(
		"by" => "title", 
		"sort" => "ASC"
	);
		
	$segments = get_specificdata("productsegments", $segment_attributes, "psid", "title", $segment_order, 1);
	$segments_list = parse_selectlist("psid", 2, $segments, "");
	
	eval("\$genericspage = \"".$template->get("admin_products_generics")."\";");
	output_page($genericspage);
}
else
{
	if($core->input['action'] == "do_add_generics") {
		if(empty($core->input['title'])) {
			output_xml("<status>false</status><message>{$lang->pleasefillintitle}</message>");
			exit;
		}
		
		if(value_exists("genericproducts", "title", $core->input['title'])) {
			output_xml("<status>false</status><message>{$lang->genericalreadyexists}</message>");
			exit;
		}
		
		if(empty($core->input['psid'])) {
			output_xml("<status>false</status><message>{$lang->pleasespecifysegment}</message>");
			exit;
		}
		
		$new_generic = array (
		"psid"	 	 => $core->input['psid'],
		"title"		=> $core->input['title'],
		"description"  => $core->input['description']
		);
		
		$query = $db->insert_query("genericproducts", $new_generic);
		if($query) {
			output_xml("<status>true</status><message>{$lang->genericadded}</message>");
			log_action($core->input['title']);
		}
		else
		{
			output_xml("<status>false</status><message>{$lang->genericadderror}</message>");
		}
	}	
}
?>