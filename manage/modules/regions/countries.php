<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Manage Countries
 * $module: admin/regions
 * $id: countries.php	
 * Last Update: @zaher.reda 	Mar 18, 2009 | 03:45 PM
 */
if(!defined("DIRECT_ACCESS"))
{
	die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageCountries'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {		
	$query = $db->query("SELECT c.*, a.name AS affname FROM ".Tprefix."countries c LEFT JOIN ".Tprefix."affiliates a ON (c.affid=a.affid) ORDER BY c.name ASC");
	if($db->num_rows($query) > 0) {
		while($country = $db->fetch_array($query)) {
			$class = alt_row($class);
			
			extract($country);
			if(empty($affname)) {
				$affname = "N/A";
			}
			$countries_list .= "<tr class='{$class}'><td>{$coid}</td><td>{$name} ({$acronym})</td><td>{$affname}</td></tr>";
		}
	}
	else
	{
		$countries_list = "<tr><td colspan='4' style='text-align: center;'>{$lang->nocountriesavailable}</td></tr>";
	}
	
	$affiliates_attributes = array("affid", "name");
	$countries_order = array(
		"by" => "name", 
		"sort" => "ASC"
	);
	
	$affiliates = get_specificdata("affiliates", $affiliates_attributes, "affid", "name", $affiliates_order, 1);
	if(!empty($affiliates)) {
		$affiliates_list = parse_selectlist("affid", 2, $affiliates, "");
	}
	else
	{
		$affiliates_list = $lang->noaffiliatesavailable;
	}
	
	eval("\$countriespage = \"".$template->get("admin_regions_countries")."\";");
	output_page($countriespage);
}
else
{
	if($core->input['action'] == "do_add_countries") {
		if(empty($core->input['name']) || empty($core->input['acronym'])) {
			output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>"); 
			exit;
		}
		
		if(value_exists("countries", "name", $core->input['name'])) {
			output_xml("<status>false</status><message>{$lang->countryalreadyexists}</message>"); 
			exit;
		}
		
		log_action($core->input['name']);
		unset($core->input['module'], $core->input['action']);
		
		$core->input['name'] = ucfirst($core->input['name']);
		$core->input['acronym'] = strtoupper($core->input['acronym']);
		$query = $db->insert_query("countries", $core->input);
		if($query) {
			$lang->countryadded = $lang->sprint($lang->countryadded, "<strong>".$core->input['name']."</strong>");
			output_xml("<status>true</status><message>{$lang->countryadded}</message>");
		}
		else
		{
			output_xml("<status>false</status><message>{$lang->erroraddingcountry}</message>");
		}
	}
}
?>