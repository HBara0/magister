<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Admin CP homepage
 * $module: admin/home
 * $id: index.php	
 * Last Update: @zaher.reda 	Mar 25, 2009 | 11:35 AM
 */
if(!$core->input['action']) {
	$query = $db->query("SELECT eid, companyName FROM ".Tprefix."entities WHERE approved='0' AND type='s' AND dateAdded>'{$core->user[lastVisit]}' LIMIT 0,3");
	if($db->num_rows($query) > 0) {
		while($supplier = $db->fetch_array($query)) {
			$suggestions_list .= "<li>{$supplier[companyName]} - <a href='#' id='approve_entities/edit_approved_1_{$supplier[eid]}' class='green_text'>{$lang->approve}</a></li>";
		}
		
		$lang->followingbeensuggested = $lang->sprint($lang->followingbeensuggested, strtolower($lang->suppliers));
		eval("\$newsuggestions = \"".$template->get("admin_suggestnotification_box")."\";");
	}
	
	$lang->usersstats = $lang->sprint($lang->usersstats, $db->fetch_field($db->query("SELECT COUNT(*) as countusers FROM ".Tprefix."users"), "countusers"));
	
	$entities = $db->query("SELECT COUNT(type) AS count, type FROM ".Tprefix."entities GROUP BY type");
	while($entity_count = $db->fetch_array($entities)) {
		if($entity_count['type'] == "c") {
			$customers_count = $entity_count['count'];
		}
		elseif($entity_count['type'] == "s") {
			$suppliers_count = $entity_count['count'];
		}
		$entities_count += $entity_count['count'];
	}
	
	$lang->entitiesstats = $lang->sprint($lang->entitiesstats, $entities_count);
	$lang->customersstats = $lang->sprint($lang->customersstats, $customers_count);
	$lang->suppliersstats = $lang->sprint($lang->suppliersstats, $suppliers_count);
	
	$count_products =  $db->fetch_field($db->query("SELECT COUNT(*) as countproducts FROM ".Tprefix."products"), "countproducts");
	$count_generics =  $db->fetch_field($db->query("SELECT COUNT(*) as countgenerics FROM ".Tprefix."genericproducts"), "countgenerics");
	$count_segments =  $db->fetch_field($db->query("SELECT COUNT(*) as countsegments FROM ".Tprefix."productsegments"), "countsegments");
	
	$lang->productsstats = $lang->sprint($lang->productsstats, $count_products, $count_generics, $count_segments);
	
	$searchtime = TIME_NOW-900;
	$online = $db->query("SELECT DISTINCT(u.username), u.uid FROM ".Tprefix."sessions s LEFT JOIN ".Tprefix."users u ON (u.uid=s.uid) WHERE s.uid!=0 AND s.time > {$searchtime} ORDER BY s.time DESC");
	$count_online = 0;
	while($onlineuser = $db->fetch_array($online)) {
		$onlineusers .= "{$onlineusers_comma}<a href='../users.php?action=profile&amp;uid={$onlineuser[uid]}'>{$onlineuser[username]}";
		$onlineusers_comma = ", ";
		$count_online++;
	}
	$lang->numusersonline = $lang->sprint($lang->numusersonline, $count_online);
	
	eval("\$index = \"".$template->get("admin_index")."\";");
	output_page($index);
}
?>