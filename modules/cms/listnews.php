<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * List Website News
 * $module: CMS
 * $id: listnews.php	
 * Created By: 		@tony.assaad		Augusst 17, 2012 | 05:30 PM
 * Last Update: 	@zaher.reda			Augusst 22, 2012 | 02:20 PM
 */

/*if($core->usergroup['cms_canAddNews'] == 0) {
	error($lang->sectionnopermission);
	exit;
}
	*/
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
	$sort_url = sort_url();
	$allnews = new CmsNews();	
	$news_details = $allnews->get_multiplenews();
		
	if(is_array($news_details)) {
		foreach($news_details as $newsid=>$news) {
			$isfeatured_icon = $ispublished_icon = '';
			$rowclass = alt_row($rowclass);	
			
			$news['dateCreated_output'] = date($core->settings['dateformat'], $news['createDate']);
			//$editnews_link = 'index.php?module=cms/managenews&type=edit&amp;newsid='.$news['cmsnid'];
			
			if($news['isPublished'] == 1) {	
				$ispublished_icon = '<img src="./images/valid.gif" border="0" title="'.$lang->published.'"/>';
			}
			else
			{
				$ispublished_icon = '<img src="./images/false.gif" border="0" title="'.$lang->published.'"/>';
			}
			
			if($news['isFeatured'] == 1) {	
				$isfeatured_icon = '<img src="./images/valid.gif" border="0" title="'.$lang->featured.'"/>';
			}
			
			eval("\$cms_news_list_rows .= \"".$template->get('cms_news_list_row')."\";");
		}
		
		if(isset($core->input['filterby'], $core->input['filtervalue'])) {
			$attributes_filter_options['title'] = array('title' => 'cn.');
			
			if($attributes_filter_options['title'][$core->input['filterby']] == 'int') {
				$filter_value = ' = "'.$db->escape_string($core->input['filtervalue']).'"';
			}
			else
			{
				$filter_value = ' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
			}
			$multipage_where = $db->escape_string($attributes_filter_options['prefixes'][$core->input['filterby']].$core->input['filterby']).$filter_value;
		}
		
		$multipage_where = $db->escape_string($attributes_filter_options['prefixes'][$core->input['filterby']].$core->input['filterby']).$filter_value;
		$multipages = new Multipages('cms_news cn ', $core->settings['itemsperlist'], $multipage_where);
		$cms_news_list_rows .= '<tr><td colspan="8">'.$multipages->parse_multipages().'</td></tr>';
	}		
	else
	{
		$cms_news_list_rows = '<tr><td colspan="8">'.$lang->na.'</td></tr>';
	}
	eval("\$newslist = \"".$template->get('cms_news_list')."\";");
	output_page($newslist);	
}
?>