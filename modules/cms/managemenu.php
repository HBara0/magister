<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Manage News
 * $module: CMS
 * $id: managemenu.php
 * Created By: 		@tony.assaad		Augusst 30, 2012 | 11:30 PM
 * Last Update: 	@tony.assaad		August 30, 201 | 12:13 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['cms_canAddMenu'] == 0) {
    //error($lang->sectionnopermission);
    //exit;
}

if(!$core->input['action']) {
    if($core->input['type'] == 'addmenuitem') {
        if(isset($core->input['id']) && !empty($core->input['id'])) {
            $menu_id = $db->escape_string($core->input['id']);
        }
        $query = $db->query("SELECT cmsmiid, title, parent FROM ".Tprefix." cms_menuitems WHERE cmsmid =".$menu_id." ORDER BY title ASC");
        while($parentmenu = $db->fetch_assoc($query)) {
            $parentmenus[$parentmenu['cmsmiid']] = $parentmenu;
        }
        if(isset($parentmenus)) {
            $parent_list = parse_selectlist('menuitem[parent]', 3, array(0 => '') + get_menuitmes($parentmenus, 1), $core->input['parent']);
        }

        //$parent_list =  parse_selectlist('menuid', 1, $parentmenu,  0);

        $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'));
        $list_affiliates = parse_selectlist('menuitem[configurations][affiliate][]', 1, array(0 => '') + $affiliates, array(0 => ''), 1);


        $list_brancheprofile = parse_selectlist('menuitem[configurations][branchprofile]', 1, array(0 => '') + $affiliates, $core->user['mainaffiliate'], 0);

        $robots_list = parse_selectlist('menuitem[robotsRule]', 1, array("INDEX,FOLLOW" => "INDEX,FOLLOW", "NOINDEX,FOLLOW" => "NOINDEX,FOLLOW", "INDEX,NOFOLLOW" => "INDEX,NOFOLLOW", "NOINDEX,NOFOLLOW" => "NOINDEX,NOFOLLOW"), 0);
        $segment_data = get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', array('by' => 'title', 'sort' => 'ASC'), 0, "publishOnwebsite = 1");
        $segmentcats_data = SegmentCategories::get_data(null, array('returnarray' => true));
        $list_segments = parse_selectlist('menuitem[configurations][segmentslist][]', 1, array(0 => '') + $segment_data, $core->user['segments'], 1);
        $list_segmentcats = parse_selectlist('menuitem[configurations][segmentcatslist][]', 1, $segmentcats_data, '', 1);

        $single_segment = parse_selectlist('menuitem[configurations][singlesegment]', 6, array(0 => '') + $segment_data, '');
        $single_segmentcategory = parse_selectlist('menuitem[configurations][singlesegmentcategory]', 6, array(0 => '') + $segmentcats_data, '');

        $webpages = get_specificdata('cms_pages', array('alias', 'title'), 'alias', 'title', array('by' => 'title', 'sort' => 'ASC'), 0);
        $list_webpages = parse_selectlist('menuitem[configurations][webpage]', 1, array(0 => '') + $webpages, 0);


        eval("\$createmenuitem =\"".$template->get('cms_menu_create_item')."\";");
        output_page($createmenuitem);
    }
    else {
        if(isset($core->input['mitid']) && !empty($core->input['mitid'])) {
            $menuitem_obj = new CmsMenuItems($core->input['mitid'], false);
            $menuname = $menuitem_obj->title;
            $menuitem_id = $menuitem_obj->cmsmiid;
            $menu_id = $menuitem_obj->cmsmid;

            $query = $db->query("SELECT cmsmiid, title, parent FROM ".Tprefix." cms_menuitems WHERE cmsmid =".$menuitem_obj->cmsmid." ORDER BY title ASC");
            while($parentmenu = $db->fetch_assoc($query)) {
                $parentmenus[$parentmenu['cmsmiid']] = $parentmenu;
            }

            if(isset($parentmenus)) {
                $parent_list = parse_selectlist('menuitem[parent]', 3, array(0 => '') + get_menuitmes($parentmenus, 1), $menuitem_obj->parent);
            }
            $menuitem = $menuitem_obj->get();
            if($menuitem_obj->isPublished) {
                $ispublished_check = 'checked="checked"';
            }
            $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'));
            $list_affiliates = parse_selectlist('menuitem[configurations][affiliate][]', 1, array(0 => '') + $affiliates, array(0 => ''), 1);


            $list_brancheprofile = parse_selectlist('menuitem[configurations][branchprofile]', 1, array(0 => '') + $affiliates, $core->user['mainaffiliate'], 0);

            $robots_list = parse_selectlist('menuitem[robotsRule]', 1, array("INDEX,FOLLOW" => "INDEX,FOLLOW", "NOINDEX,FOLLOW" => "NOINDEX,FOLLOW", "INDEX,NOFOLLOW" => "INDEX,NOFOLLOW", "NOINDEX,NOFOLLOW" => "NOINDEX,NOFOLLOW"), $menuitem['robotsRule']);
            $segment_data = get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', array('by' => 'title', 'sort' => 'ASC'), 0, "publishOnwebsite = 1");
            $segmentcats_data = SegmentCategories::get_data(null, array('returnarray' => true));

            $list_segmentcats = parse_selectlist('menuitem[configurations][segmentcatslist][]', 1, $segmentcats_data, '', 1);

            $list_segments = parse_selectlist('menuitem[configurations][segmentslist][]', 1, array(0 => '') + $segment_data, $core->user['segments'], 1);
            $single_segment = parse_selectlist('menuitem[configurations][singlesegment]', 6, array(0 => '') + $segment_data, '');
            $single_segmentcategory = parse_selectlist('menuitem[configurations][singlesegmentcategory]', 6, array(0 => '') + $segmentcats_data, '');
            $webpages = get_specificdata('cms_pages', array('alias', 'title'), 'alias', 'title', array('by' => 'title', 'sort' => 'ASC'), 0);
            $list_webpages = parse_selectlist('menuitem[configurations][webpage]', 1, array(0 => '') + $webpages, 0);

            if(isset($menuitem_obj->type) && !empty($menuitem_obj->type)) {
                switch($menuitem_obj->type) {
                    case "webpage":
                    case "branchprofile":
                    case "singlesegment":
                    case "singlesegmentcategory":

                        $selectedoption = unserialize(base64_decode($menuitem_obj->configurations));
                        $selectedtype = '$("input[id=\''.$menuitem_obj->type.'_type\']").prop("checked",true);';
                        $selectedtype.='$("#'.$menuitem_obj->type.'_configuration").show();';
                        $selectedtype.=' $("select[name =\'menuitem[configurations]['.$menuitem_obj->type.']\']").find("option[value = \''.$selectedoption.'\']").attr("selected",true);';
                        break;
                    case "listaffiliates":
                        $selectedoptions = unserialize(base64_decode($menuitem_obj->configurations));
                        $selectedtype = '$("input[id=\''.$menuitem_obj->type.'_type\']").prop("checked",true);';
                        $selectedtype.='$("#'.$menuitem_obj->type.'_configuration").show();';
                        if(is_array($selectedoptions)) {
                            foreach($selectedoptions as $selectedoption) {
                                $selectedtype.=' $("select[name =\'menuitem[configurations][affiliate][]\']").find("option[value = \''.$selectedoption.'\']").attr("selected",true);';
                            }
                        }
                        else {
                            $selectedtype.=' $("select[name =\'menuitem[configurations][affiliate]\']").find("option[value = \''.$selectedoptions.'\']").attr("selected",true);';
                        }
                        break;
                    case "segmentslist":
                    case "segmentcatslist":
                        $selectedoptions = unserialize(base64_decode($menuitem_obj->configurations));
                        $selectedtype = '$("input[id=\''.$menuitem_obj->type.'_type\']").prop("checked",true);';
                        $selectedtype.='$("#'.$menuitem_obj->type.'_configuration").show();';
                        if(is_array($selectedoptions)) {
                            foreach($selectedoptions as $selectedoption) {
                                $selectedtype.=' $("select[name =\'menuitem[configurations]['.$menuitem_obj->type.'][]\']").find("option[value = \''.$selectedoption.'\']").attr("selected",true);';
                            }
                        }
                        else {
                            $selectedtype.=' $("select[name =\'menuitem[configurations][affiliate]\']").find("option[value = \''.$selectedoptions.'\']").attr("selected",true);';
                        }
                        break;
                    case "externalurl":
                        $selectedtype.='$("#'.$menuitem_obj->type.'_configuration").show();';
                        $selectedtype.= '$("input[id=\''.$menuitem_obj->type.'_type\']").prop("checked",true);';
                        $selectedoptions = unserialize(base64_decode($menuitem_obj->configurations));
                        if(is_array($selectedoptions)) {
                            foreach($selectedoptions as $type => $selectedoption) {
                                $selectedtype.='$("input[name=\'menuitem[configurations][externalurl]['.$type.']\']").val("'.$selectedoption.'");';
                            }
                        }
                        break;

                    case "contact":
                    case "listnews":
                    case "events":
                    case "newsarchive":
                    case "eventsarchive":
                        $selectedtype.= '$("input[id=\''.$menuitem_obj->type.'_type\']").prop("checked",true);';
                        break;
                }
            }
        }
        eval("\$createmenuitem =\"".$template->get('cms_menu_create_item')."\";");
        output_page($createmenuitem);
    }
}
else {
    /* if the action is create menu from the popup Dialog */
    if($core->input['action'] == 'do_createmenu') {
        $cms_menu = new CmsMenu();
        $cms_menu->create($core->input['menu']);

        switch($cms_menu->get_status()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            case 2:
                output_xml("<status>false</status><message>{$lang->menuexists}</message>");
                break;
            case 3:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                break;
        }
    }
    else if($core->input['action'] == 'do_updatemenu') {
        $cms_menu = new CmsMenu();
        $cms_menu->update($core->input['menu']);

        switch($cms_menu->get_status()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            case 2:
                output_xml("<status>false</status><message>{$lang->menuexists}</message>");
                break;
            case 3:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                break;
        }
    }
    elseif($core->input['action'] == 'do_menuitem') {
        $cms_menuitem = new CmsMenuItems();
//        $core->input['menuitem']['cmsmid'] = $core->input['menuitem']['cmsmid'];
//        foreach($core->input['menuitem'] as $key => $menuitemdata) {
//            if(is_array($menuitemdata)) {
//                continue;
//            }
//            $menuitems[$key] = $menuitemdata;
//        }

        $core->input['menuitem']['alias'] = generate_alias($core->input['menuitem']['title']);
        $cms_menuitem->set($core->input['menuitem']);
        $cms_menuitem->save();

        switch($cms_menuitem->get_errorcode()) {
            case 0:
                /* we hide the dialog Box on succefull Creation */
                output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            case 2:
                output_xml("<status>false</status><message>{$lang->menuexists}</message>");
                break;
            case 3:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                break;
        }
    }
}
function get_menuitmes($parentmenus, $depth = '') {
    global $parentmenu;
    if(!empty($depth)) {
        $depth++;
    }
    if(is_array($parentmenus)) {
        foreach($parentmenus as $key => $parentmenu) {
            $parent_list[$parentmenu['cmsmiid']] = str_repeat('&hellip;', $depth).' '.$parentmenu['title'];
            if(is_array($parentmenu['title'])) {
                $parent_list += get_menuitmes($parentmenu['title'], $depth);
            }
        }
    }

    return $parent_list;
}

?>