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
            else if($highlight['type'] == 'graph') {
                $hide_html = 'hidden="hidden"';
            }
            if($highlight['isEnabled'] == '1') {
                $enablecheck = 'checked="checked"';
            }
        }
    }
    else {
        $hide_html = 'hidden="hidden"';
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
        if(!is_array($highlight) || is_empty($highlight)) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
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
}