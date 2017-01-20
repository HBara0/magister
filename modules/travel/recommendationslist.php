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

            if ($recommendation_obj->canManageRecommendation()) {
                $editlink = '<button type="button" class="btn btn-warning" onclick="window.open(\'' . $recommendation_obj->get_editlink() . '\', \'_blank\')">' . $lang->manage . '</button>';
            }
            eval("\$recommendations_rows .= \"" . $template->get('travel_travel_recommendationslist_row') . "\";");
            unset($subscribe_cell);
        }
    }
    eval("\$page= \"" . $template->get('travel_recommendationslist') . "\";");
    output_page($page, array('pagetitle' => 'travelrecommendations'));
}
else {
    if ($core->input['action'] == 'loadrecommendation_popup') {
        if (!intval($core->input['id'])) {
            echo ('<span style="color:red">Error Loading Window</span>');
            exit;
        }
        $id = intval($core->input['id']);
        $recommendation_obj = new Recommendations($id);
        $recommendation = $recommendation_obj->get();
        $url = $core->settings['rootdir'] . '/index.php?module=travel/recommendationslist&action=showpopup_createtravelevent&id=' . intval($id);
        if ($recommendation > 0) {
            $recommendation['ratingoutput'] = $recommendation_obj->get_ratingoutput();
        }
        $addbutton = '<button data-targetdiv="recommendations_modal" data-url="' . $url . '" type="button" class="btn btn-success" id="openmodal_' . $id . '"><span class="glyphicon glyphicon-plus"></span>' . $lang->createevent . '</button>';
        $recommendation['displayname'] = $recommendation_obj->get_displayname();
        $recommendation['additionaloutput'] = $recommendation_obj->get_cityoutput() . ' - ' . $recommendation_obj->get_categoryutput();
        eval("\$modal = \"" . $template->get('modal_travel_recommendation') . "\";");
        echo ($modal);
    }
    elseif ($core->input['action'] == 'showpopup_createtravelevent') {
        $id = intval($core->input['id']);
        $recommendation_obj = new Recommendations($id);
        $recommendation = $recommendation_obj->get();
        $recommendation['displayname'] = $recommendation_obj->get_displayname();
        $recommendation['additionaloutput'] = $recommendation_obj->get_cityoutput() . ' - ' . $recommendation_obj->get_categoryutput();
        $recommendation['description_output'] = $recommendation['additionaloutput'] . '<br><br>' . $recommendation['description'];
        eval("\$modal = \"" . $template->get('modal_travel_createventfromrecommendation') . "\";");
        echo ($modal);
    }
}