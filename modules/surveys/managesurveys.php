<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managesurveys.php
 * Created:        @rasha.aboushakra    Oct 14, 2015 | 8:46:37 AM
 * Last Update:    @rasha.aboushakra    Oct 14, 2015 | 8:46:37 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['surveys_canCreateSurvey'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    if(isset($core->input['identifier']) && !empty($core->input['identifier'])) {
        $survey_obj = new Surveys($core->input['identifier']);
        $survey = $survey_obj->get();
        if(empty($survey['sid'])) {
            redirect('index.php?module=surveys/list');
        }
        $associations = SurveyAssociations::get_data(array('sid' => $survey['sid']), array('returnatarray' => true));
        if(is_array($associations)) {
            foreach($associations as $association) {
                $association = $association->get();
                $survey['associations'][$association['attr']] = $association['id'];
            }
        }
        if(!empty($survey['associations']['spid'])) {
            $supplier = Entities::get_data(array('eid' => $survey['associations']['spid']));
            if(is_object($supplier)) {
                $survey['associations']['suppliername'] = $supplier->companyName;
            }
        }
        if(!empty($survey['associations']['pid'])) {
            $product = Products::get_data(array('pid' => $survey['associations']['pid']));
            if(is_object($product)) {
                $survey['associations']['productname'] = $product->name;
            }
        }
        $surveycategories = get_specificdata('surveys_categories', array('scid', 'title'), 'scid', 'title', 'title');
        $surveycategories_list = parse_selectlist('category', 5, $surveycategories, $survey['category']);
        $radiobuttons['isPublicResults'] = parse_yesno('isPublicResults', 1, $survey['isPublicResults']);

        // $asscoiations=Su
        /* Parse Associations Fields - START */
        $query = $db->query("SELECT u.uid, u.displayName
						FROM ".Tprefix."affiliatedemployees ae
						JOIN ".Tprefix."affiliates aff ON (aff.affid = ae.affid)
						JOIN ".Tprefix."users u ON (u.uid = ae.uid)
						WHERE u.gid!=7 AND (u.uid IN (SELECT uid FROM ".Tprefix."users WHERE reportsTo={$core->user[uid]})
						OR ae.affid IN (SELECT affid FROM ".Tprefix."affiliatedemployees WHERE (canAudit=1 OR canHR=1) AND uid={$core->user[uid]}))
						ORDER BY displayName ASC");
        $employees_affiliate[0] = '';
        while($employee_affiliate = $db->fetch_assoc($query)) {
            $employees_affiliate[$employee_affiliate['uid']] = $employee_affiliate['displayName'];
        }
        $employees_list = parse_selectlist('associations[uid]', 5, $employees_affiliate, $survey['associations']['uid']);

        $afiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, 'affid IN ('.implode(',', $core->user['affiliates']).')');
        $afiliates[0] = '';
        asort($afiliates);
        $affiliates_list = parse_selectlist('associations[affid]', 5, $afiliates, $survey['associations']['affid']);

        $segments_query = $db->query("SELECT ps.psid, title FROM ".Tprefix."productsegments ps JOIN ".Tprefix."employeessegments es ON (es.psid=ps.psid) WHERE es.uid={$core->user[uid]} ORDER BY title ASC");
        $segments[0] = '';
        while($segment = $db->fetch_assoc($segments_query)) {
            $segments[$segment['psid']] = $segment['title'];
        }
        $segments_list = parse_selectlist('associations[psid]', 5, $segments, $survey['associations']['psid']);

        $contries = get_specificdata('countries', array('coid', 'name'), 'coid', 'name', 'name', 1);
        $countries_list = parse_selectlist('associations[coid]', 5, $contries, $survey['associations']['coid']);
        /* Parse Associations Fields - END */


        if(!empty($survey['closingDate'])) {
            $survey['closingDate_output'] = date($core->settings['dateformat'], $survey['closingDate']);
            $survey['closingDate_value'] = $survey['closingDate'];
        }
        eval("\$managesurveys .= \"".$template->get('surveys_modifysurvey')."\";");
        output_page($managesurveys);
    }
    else {
        redirect('index.php?module=surveys/list');
    }
}
else {
    if($core->input['action'] == 'do_perform_managesurveys') {
        $survey = new Surveys();
        $survey->update_survey($core->input);
        switch($survey->get_status()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            case 2:
                output_xml("<status>false</status><message>{$lang->surveyexists}</message>");
                break;
            case 3:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                break;
        }
    }
}
?>