<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Create Survey
 * $module: addnews
 * $id: listnews.php	
 * Created By: 		@tony.assaad		Augusst 17, 2012 | 5:30 PM
 * Last Update: 	@tony.assaad		Augusst 17, 2012 | 5:30 PM
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
			
			if($news['isFeatured'] == 1) {	
				$ispublished_icon = '<img src="./images/valid.gif" border="0" />';
			}
			else
			{
				$ispublished_icon = '<img src="./images/false.gif" border="0" />';
			}
			
			if($news['isFeatured'] == 1) {	
				$isfeatured_icon = '<img src="./images/valid.gif" border="0" title="'.$lang->featured.'"/>';
			}
			
			eval("\$cms_news_list_rows .= \"".$template->get('cms_news_list_row')."\";");
		}
		$multipage_where .= $db->escape_string($attributes_filter_options['prefixes'][$core->input['filterby']].$core->input['filterby']).$filter_value;
		$multipages = new Multipages('cms_news cn', $core->settings['itemsperlist'], $multipage_where);
		$cms_news_list_rows .= "<tr><td colspan='6'>".$multipages->parse_multipages()."</td></tr>";
	}		
	else
	{
		$surveys_rows .= '<tr><td colspan="5">'.$lang->na.'</td></tr>';
	}
	eval("\$newslist = \"".$template->get('cms_news_list')."\";");
	output_page($newslist);	
}
?>