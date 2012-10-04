<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * List Web Pages
 * $module: CMS
 * $id: listwebpages.php	
 * Created By: 		@tony.assaad		Augusst 28, 2012 | 12:30 PM
 * Last Update: 	@zaher.reda			Augusst 28, 2012 | 03:45 PM
 */

/*if($core->usergroup['cms_canAddPage'] == 0) {
	error($lang->sectionnopermission);
	exit;
}*/

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
	$sort_url = sort_url();
	$allpages = new CmsPages();
	$pages_details = $allpages->get_multiplepages();
	
	if(is_array($pages_details)) {
		foreach($pages_details as $pageid => $page) {
			$ispublished_icon = '';
			
			$rowclass = alt_row($rowclass);		
			$page['dateCreated_output'] = date($core->settings['dateformat'], $page['dateCreated']);
			if($page['isPublished'] == 1) {	
				$ispublished_icon = '<img src="./images/valid.gif" border="0" title="'.$lang->published.'"/>';
			}
			else
			{
				$ispublished_icon = '<img src="./images/false.gif" border="0" />';
			}
			
			eval("\$cms_pages_list_rows .= \"".$template->get('cms_webpages_list_row')."\";");			
		}
		
		/* Parse pagination - START */
		if(isset($core->input['filterby'], $core->input['filtervalue'])) {
			$attributes_filter_options['title'] = array('title' => 'cp.');
			
			if($attributes_filter_options['title'][$core->input['filterby']] == 'int') {
				$filter_value = ' = "'.$db->escape_string($core->input['filtervalue']).'"';
			}
			else
			{
				$filter_value = ' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
			}
			$multipage_where .= $db->escape_string($attributes_filter_options['title'][$core->input['filterby']].$core->input['filterby']).$filter_value;
		}

		$multipages = new Multipages('cms_pages cp', $core->settings['itemsperlist'], $multipage_where);
		
		$cms_pages_list_rows .= '<tr><td colspan="7">'.$multipages->parse_multipages().'</td></tr>';
		/* Parse pagination - END */
	}
	else
	{
		$cms_pages_list_rows = '<tr><td colspan="7">'.$lang->na.'</td></tr>';	
	}
	eval("\$pageslist = \"".$template->get('cms_webpages_list')."\";");
	output_page($pageslist);	
}
?>