<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managehighlight.php
 * Created:        @hussein.barakat    Jun 30, 2015 | 2:50:28 PM
 * Last Update:    @hussein.barakat    Jun 30, 2015 | 2:50:28 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $types = array(graph => 'Graph', html => 'HTML');
    if(isset($core->input['id']) && !empty($core->input['id'])) {
        $highlight_obj = new CmsHighlights(intval($core->input['id']), false);
        if(is_object($highlight_obj)) {
            $highlight = $highlight_obj->get();
            if($highlight['type'] == 'html') {
                $hide_graph = 'hidden="hidden"';
            }
            elseif($highlight['type'] == 'graph') {
                $hide_html = 'hidden="hidden"';
            }
            if($highlight['isEnabled'] == '1') {
                $enablecheck = 'checked="checked"';
            }
        }
    }
    else {
        $hide_html = 'hidden="hidden"';
        $action = 'create';
    }
    $types_list = parse_selectlist('highlight[type]', 1, $types, $highlight['type'], '', '', array('id' => 'types'));
    eval("\$type_graph = \"".$template->get('cms_managehighlights_type_graph')."\";");
    eval("\$type_html = \"".$template->get('cms_managehighlights_type_html')."\";");
    eval("\$highlightslist = \"".$template->get('cms_managehighlights')."\";");
    output_page($highlightslist);
}
else {
    if($core->input['action'] == 'do_perform_managehighlight') {
        $highlight = $core->input['highlight'];
        if(!is_array($highlight) || is_empty($highlight || empty($highlight['title']))) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }
        if($core->input['actiontype'] == 'create') {
            $highlight['name'] = generate_alias($highlight['title']);
            $existing_object = CmsHighlights::get_data(array('name' => $highlight['name']));
            if(is_object($existing_object)) {
                output_xml("<status>false</status><message>{$lang->entryalreadyexists}</message>");
                exit;
            }
        }
        $highlight_obj = new CmsHighlights();
        $highlight_obj->set($highlight);
        $highlight_obj = $highlight_obj->save();
        switch($highlight_obj->get_errorcode()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
                exit;
            case 2:
                output_xml("<status>false</status><message>{$lang->errorsaving}></message>");
                exit;
        }
    }
    elseif($core->input['action'] == 'togglepublish') {
        // if($core->usergroup['cms_canPublishNews'] == 1 && !empty($core->input['id'])) {
        $highlight = new CmsHighlights(intval($core->input['id']));
        $db->update_query(CmsHighlights::TABLE_NAME, array('isEnabled' => !$highlight->isEnabled), CmsHighlights::PRIMARY_KEY.'='.intval($core->input['id']));
        // }
        redirect('index.php?module=cms/highlightslist');
    }
}