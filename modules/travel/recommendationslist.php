<?php

if (!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if ($core->usergroup['canAccessSystem'] == 0) {
    error($lang->sectionnopermission);
}
if (!isset($core->input['action'])) {

    //get active recommendations list
    $recommendation_objs = Recommendations::get_data(array('isActive' => 1), array('returnarray' => true, 'simple' => false));
    if (is_array($recommendation_objs)) {
        foreach ($recommendation_objs as $recommendation_obj) {
            $recommendation = $recommendation_obj->get();
            $recommendation_link = $recommendation_obj->parse_link();
            $recommendation['cityoutput'] = $recommendation_obj->get_cityoutput();
            $recommendation['categoryoutput'] = $recommendation_obj->get_categoryutput();
            $recommendation['ratingoutput'] = $recommendation_obj->get_ratingoutput();
            $recommendation['ratingroder'] = 'data-order="' . $recommendation['rating'] . '" data-search="' . $recommendation['rating'] . '"';
            if ($recommendation_obj->description) {
                if (strlen($recommendation_obj->description) > 150) {
                    $description = substr($recommendation_obj->description, 0, 150) . '...';
                }
                else {
                    $description = $recommendation_obj->description;
                }
            }
            else {
                $description = 'N/A';
            }

            $tool_items = ' <li><a id="openmodal_' . $recommendation_obj->get_id() . '" data-url="' . $core->settings['rootdir'] . '/index.php?module=travel/recommendationslist&action=loadrecommendation_popup&id=' . $recommendation_obj->get_id() . '"><span class="glyphicon glyphicon-eye-open"></span>&nbsp' . $lang->viewrecommendation . '</a></li>';
            if ($recommendation_obj->canManageRecommendation()) {
                $tool_items .= ' <li><a target="_blank" href="' . $recommendation_obj->get_editlink() . '"><span class="glyphicon glyphicon-pencil"></span>&nbsp' . $lang->managerecommendation . '</a></li>';
            }
            eval("\$tools = \"" . $template->get('tools_buttonselectlist') . "\";");
            eval("\$recommendations_rows .= \"" . $template->get('travel_travel_recommendationslist_row') . "\";");
            unset($tool_items, $subscribe_cell);
        }
    }
    eval("\$page= \"" . $template->get('travel_recommendationslist') . "\";");
    output_page($page, array('pagetitle' => 'travelrecommendations'));
}
else {
    if ($core->input['action'] == 'loadrecommendation_popup') {
        if (!intval($core->input['id'])) {
            echo('<span style="color:red">Error Loading Window</span>');
            exit;
        }
        $id = intval($core->input['id']);
        $recommendation_obj = new Recommendations($id);
        $recommendation = $recommendation_obj->get();
        $recommendation['displayname'] = $recommendation_obj->get_displayname();
        $recommendation['additionaloutput'] = $recommendation_obj->get_cityoutput() . ' - ' . $recommendation_obj->get_categoryutput();
        eval("\$modal = \"" . $template->get('modal_travel_recommendation') . "\";");
        echo($modal);
    }
}