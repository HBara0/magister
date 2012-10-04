<?php
/*
* Orkila Central Online System (OCOS)
* Copyright © 2009 Orkila International Offshore, All Rights Reserved
* 
* Lists all suppliers
* $module: profiles
* $id: supplierslist.php	
* Created:	   	@najwa.kassem		October 15, 2010 | 10:28 AM
* Last Update: 	@zaher.reda		 October 27, 2010 | 11:17 AM
*/

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {	
	$sort_query = 'companyName ASC';
	
	if(isset($core->input['sortby'], $core->input['order'])) {
		$sort_query = $core->input['sortby'].' '.$core->input['order'];
	}
	
	$sort_url = sort_url();
	$limit_start = 0;
	$multipage_where = " type='s' ";
	if(isset($core->input['start'])) {
		$limit_start = $db->escape_string($core->input['start']);
	}
	
	if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
		$core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
	}
	
	if(isset($core->input['filterby'], $core->input['filtervalue'])) {
		$value_accepted = true;
		
		if($core->input['filterby'] == 'affid'){
			$table = 'affiliatedentities';
		}
		elseif($core->input['filterby'] == 'psid')
		{
			$table = 'entitiessegments';
		}
		else
		{
			$value_accepted = false;
		}
		
		if($value_accepted == true) {
			$extra_where = " AND eid IN (SELECT eid FROM ".Tprefix.$table." WHERE ".$db->escape_string($core->input['filterby']).'='.$db->escape_string($core->input['filtervalue']).")";
		}	
		else
		{
			$extra_where = '';
		}
		$multipage_where .= $extra_where;
	}
	
	//$affiliate_filters_cache = $segment_filters_cache = array();
	$filters_required = array('psid', 'affid');
	$filters_cache = array();
		
	$query = $db->query("SELECT *, companyName AS entityname
						FROM ".Tprefix."entities 
						WHERE type='s' {$extra_where}
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");
						
	if($db->num_rows($query) > 0) {
		while($supplier = $db->fetch_assoc($query)) {	
			$query2 = $db->query("SELECT ae.affid, a.name FROM ".Tprefix."affiliatedentities ae  JOIN ".Tprefix."affiliates a ON (a.affid=ae.affid) WHERE ae.eid='$supplier[eid]' GROUP BY a.name ORDER BY a.name ASC");
			
			$segments_counter = $affiliates_counter = 0;
			
			$affiliates = $hidden_affiliates = $show_affiliates = '';
			$segments = $hidden_segments = $show_segments = '';
			while($affiliate = $db->fetch_assoc($query2)) {
				if(!is_array($filters_cache['affid'])) {
					$filters_cache['affid'] = array();
				}
				
				if(!in_array($affiliate['affid'], $filters_cache['affid'])) {
					//$aff_filter_icon= "<a href='index.php?module=profiles/supplierslist&filterby=affid&filtervalue={$affiliate[affid]}'> <img src='./images/icons/search.gif' border='0' alt='{$lang->filterby}'/></a>";
					$filters['affid'][$affiliate['affid']] = "<a href='index.php?module=profiles/supplierslist&filterby=affid&filtervalue={$affiliate[affid]}'> <img src='./images/icons/search.gif' border='0' alt='{$lang->filterby}'/></a>";
				}
				else
				{
					$filters['affid'][$affiliate['affid']] = '';
				}
				
				if(++$affiliates_counter > 2) {	
					$hidden_affiliates .= '<a href="index.php?module=profiles/affiliateprofile&affid='.$affiliate['affid'].'">'.$affiliate['name'].'</a> '.$filters['affid'][$affiliate['affid']].'<br />';
						
				}
				elseif($affiliates_counter == 2)
				{	
					$show_affiliates .= '<a href="index.php?module=profiles/affiliateprofile&affid='.$affiliate['affid'].'">'.$affiliate['name'].'</a> '.$filters['affid'][$affiliate['affid']];
					$filters_cache['affid'][] = $affiliate['affid'];
				}
				else
				{	
					$show_affiliates .= '<a href="index.php?module=profiles/affiliateprofile&affid='.$affiliate['affid'].'">'.$affiliate['name'].'</a> '.$filters['affid'][$affiliate['affid']].'<br />';
					$filters_cache['affid'][] = $affiliate['affid'];				
				}
	
				if($affiliates_counter > 2) 
				{
					$affiliates = $show_affiliates.", <a href='#affiliate' id='showmore_affiliates_{$supplier[eid]}'>...</a> <br /><span style='display:none;' id='affiliates_{$supplier[eid]}'>{$hidden_affiliates}</span>";
				}
				else
				{
					$affiliates = $show_affiliates;
				}		
			}
	
			$query3 = $db->query("SELECT title,es.psid FROM ".Tprefix."productsegments p JOIN ".Tprefix."entitiessegments es ON (es.psid=p.psid) WHERE es.eid='$supplier[eid]'");
			while($segment = $db->fetch_assoc($query3)) {
				if(!is_array($filters_cache['psid'])) {
					$filters_cache['psid'] = array();
				}
					
				if(!in_array($segment['psid'], $filters_cache['psid'])) {
					//$seg_filter_icon = "<a href='index.php?module=profiles/supplierslist&filterby=psid&filtervalue={$segment[psid]}'> <img src='./images/icons/search.gif' border='0' alt='{$lang->filterby}'/></a>";
					$filters['psid'][$segment['psid']] = "<a href='index.php?module=profiles/supplierslist&filterby=psid&filtervalue={$segment[psid]}'> <img src='./images/icons/search.gif' border='0' alt='{$lang->filterby}'/></a>";
				}
				else
				{
					$filters['psid'][$segment['psid']] = '';
				}	 
				
				if(++$segments_counter > 2) {
					$hidden_segments .= $segment['title'].' '.$filters['psid'][$segment['psid']].'<br />';
				}
				elseif($segments_counter == 2)
				{
					$show_segments .= $segment['title'].' '.$filters['psid'][$segment['psid']];
					$filters_cache['psid'][] = $segment['psid'];
				}
				else
				{
					$show_segments .= $segment['title'].' '.$filters['psid'][$segment['psid']].'<br />';
					$filters_cache['psid'][] = $segment['psid'];
				}
				
				if($segments_counter > 2) {
					$segments = $show_segments.", <a href='#segment' id='showmore_segments_{$supplier[eid]}'>...</a><br /> <span style='display:none;' id='segments_{$supplier[eid]}'>{$hidden_segments}</span>";
				}
				else
				{
					$segments = $show_segments;
				}
			}
		
			$suppliers_list .= "<tr class='{$class}'><td valign='top'><a href='index.php?module=profiles/entityprofile&eid={$supplier[eid]}'>{$supplier[companyName]}</td><td valign='top'>{$affiliates}</td><td valign='top'>{$segments}</td>";
		}
		
		$multipages = new Multipages('entities', $core->settings['itemsperlist'], $multipage_where);
		$suppliers_list .= '<tr><td colspan="3">'.$multipages->parse_multipages().'</td></tr>';
	}
	else
	{
		$suppliers_list = '<tr><td colspan="3">'.$lang->no.'</td></tr>';
	}		
	
	eval("\$listpage = \"".$template->get('profiles_supplierslist')."\";");
	output_page($listpage);
}
?>