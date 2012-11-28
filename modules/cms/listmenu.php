<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Create Survey
 * $module: addnews
 * $id: listwebpages.php	
 * Created By: 		@tony.assaad		Augusst 28, 2012 | 12:30 PM
 * Last Update: 	@tony.assaad		Augusst 28, 2012 | 12:30 PM
 */

/*if($core->usergroup['cms_canAddPage'] == 0) {
	error($lang->sectionnopermission);
	exit;
}*/

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['cms_canAddMenu'] == 0) {
	//error($lang->sectionnopermission);
	//exit;
}
if(!$core->input['action']) {
	$sort_url = sort_url();
	$allmenus = new CmsMenu();
	
	$menu_details = $allmenus->get_menus();  /*Call main menu*/
	
	if(is_array($menu_details)) {
		foreach($menu_details as $menuid=>$menulist) {
			$rowclass = alt_row($rowclass);
			 $menu_counter = 0;
			$menulist['dateCreated_output'] = date($core->settings['dateformat'], $menulist['dateCreated']);

			if(strlen($menulist['description']) > 50) {
				$menulist['description']  = '<a href="#description" id="showmore_description_'.$menulist['cmsmid'].'">...'.substr($menulist['description'], 0, 40).'</a><span style="display:none;" id="description_'.$menulist['cmsmid'].'">'.substr($menulist['description'], 40).'</span>';

			}
			else
			{
				$menulist['description'] = $menulist['description'];
			}
			eval("\$cms_menuitmes_list_rows .= \"".$template->get('cms_menuitmes_list_rows')."\";");			
			
			
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

		$multipages = new Multipages('cms_menus cm', $core->settings['itemsperlist'], $multipage_where);
		
		$cms_menuitmes_list_rows .= '<tr><td colspan="7">'.$multipages->parse_multipages().'</td></tr>';
		/* Parse pagination - END */
		}

	else
	{
		$cms_menuitmes_list_rows = '<tr><td colspan="7">'.$lang->na.'</td></tr>';	
	}

}

elseif($core->input['action']=='viewmenuitem') {
	$options = 'haschildren';
	$rowclass = alt_row($rowclass);
	$newsid = $db->escape_string($core->input['newsid']);
	$menuitem = new CmsMenu();
	$menu_item_details = $menuitem->get_menus($options,$newsid);  /*Call  menu items for the menuid passed */
	if(is_array($menu_item_details)) {
		foreach($menu_item_details as $menu_item) {
		echo '<div class="'.$rowclass.'" id="news_'.$menu_item['cmsmiid'].'" style="padding:3px;border: 1px solid #a9a9a9;-moz-border-radius: 6px;-webkit-border-radius: 3px;-khtml-border-radius:3px;">--'.$menu_item['title'].'</div>';		
		}
	}
	exit;	
}


	eval("\$menulist = \"".$template->get('cms_menu_list')."\";");
	output_page($menulist);	
	
	
?>
	