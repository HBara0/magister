<?php

if (!$core->input['action']) {

    $recommendation_obj = new Recommendations();
    if (isset($core->input['id']) && !empty($core->input['id'])) {
        $recommendation_obj = new Recommendations(intval($core->input['id']));
        $recommendation = $recommendation_obj->get();
        $rating = $recommendation['rating'];
        $category = $recommendation['category'];
        $city = $recommendation['city'];
        $isActive = $recommendation['isActive'];
        $recommendation['city_output'] = $recommendation_obj->get_cityoutput();
    }
    $isactive_list = parse_selectlist2('recommendation[isActive]', 1, array('1' => 'Yes', '0' => 'No'), $isActive);
    $categories_list = $recommendation_obj->parse_categories();
    $rating_list = $recommendation_obj->parse_rating();

    eval("\$managerecommendation= \"" . $template->get('travel_managerecommendation') . "\";");
    output_page($managerecommendation);
}
elseif ($core->input['action'] == 'do_perform_managerecommendation') {
    $recommendation_obj = new Recommendations();
    $core->input['recommendation']['alias'] = generate_alias($core->input['recommendation']['title']);
    $recommendation_obj->set($core->input['recommendation']);
    $recommendation_obj = $recommendation_obj->save();
    switch ($recommendation_obj->get_errorcode()) {
        case 0:
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
            break;
        case 1:
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            break;
        default:
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            break;
    }
}
?>