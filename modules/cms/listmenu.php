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

/* if($core->usergroup['cms_canAddPage'] == 0) {
  error($lang->sectionnopermission);
  exit;
  } */

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

    $menu_details = $allmenus->get_menus();  /* Call main menu */

    if(is_array($menu_details)) {
        foreach($menu_details as $menuid => $menulist) {
            $rowclass = alt_row($rowclass);
            $menu_counter = 0;
            $menulist['dateCreated_output'] = date($core->settings['dateformat'], $menulist['dateCreated']);

            if(strlen($menulist['description']) > 50) {
                $menulist['description'] = '<a href="#description" id="showmore_description_'.$menulist['cmsmid'].'">...'.substr($menulist['description'], 0, 40).'</a><span style="display:none;" id="description_'.$menulist['cmsmid'].'">'.substr($menulist['description'], 40).'</span>';
            }
            else {
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
            else {
                $filter_value = ' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
            }
            $multipage_where .= $db->escape_string($attributes_filter_options['title'][$core->input['filterby']].$core->input['filterby']).$filter_value;
        }

        $multipages = new Multipages('cms_menus cm', $core->settings['itemsperlist'], $multipage_where);

        $cms_menuitmes_list_rows .= '<tr><td colspan="7">'.$multipages->parse_multipages().'</td></tr>';
        /* Parse pagination - END */
    }
    else {
        $cms_menuitmes_list_rows = '<tr><td colspan="7">'.$lang->na.'</td></tr>';
    }

    eval("\$menulist = \"".$template->get('cms_menu_list')."\";");
    output_page($menulist);
}
elseif($core->input['action'] == 'viewmenuitem') {
    $rowclass = alt_row($rowclass);
    $newsid = $db->escape_string($core->input['newsid']);
    $menuitem = new CmsMenu();
    $menus_arrays = $menuitem->read_menus($newsid);
    if(is_array($menus_arrays)) {
        $menu_lists = $menuitem->parse_menu_list($menus_arrays);
    }
    else {
        $menu_lists = '<a href="index.php?module=cms/managemenu&type=addmenuitem&id='.$newsid.'" target="_blank"  title="'.$lang->addmenuitem.'"><img src="'.$core->settings['rootdir'].'/images/add.gif" border="0"/>'.$lang->addmenuitem.'</a>';
    }
    if(!empty($menu_lists)) {
        echo($menu_lists);
    }
}
elseif($core->input['action'] == "get_deletemenuitem") {
    $id = $core->input['id'];
    eval("\$deletemenuitem = \"".$template->get('popup_cms_deletemenuitem')."\";");
    output_page($deletemenuitem);
}
elseif($core->input['action'] == "do_deletemenuitem") {
    $id = $db->escape_string($core->input['todelete']);
    if(empty($id) || $id < 1) {
        output_xml("<status>false</status><message>".$lang->cannotdeleteentry."</message>");
    }
    $menuitem = new CmsMenuItems($id);

    $menuitem = $menuitem->delete_menuitem($id);
    if($menuitem) {
        output_xml('<status>true</status><message>'.$lang->successfullydeleted.'</message>');
    }
    else {
        output_xml("<status>false</status><message>".$lang->cannotdeleteentry."</message>");
    }
}
?>
