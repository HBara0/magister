<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Create Survey
 * $module: createsurvey
 * $id: createsurvey.php
 * Created By: 		@tony.assaad		May 3, 2012 | 4:30 PM
 * Last Update: 	@zaher.reda			May 10, 2012 | 02:03 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['surveys_canCreateSurvey'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $action = 'createsurvey';

    $surveycategories = get_specificdata('surveys_categories', array('scid', 'title'), 'scid', 'title', 'title');
    $surveycategories_list = parse_selectlist('category', 5, $surveycategories, $survey['category']);
    $surveystemplates = SurveysTemplates::get_data('isPublic=1 OR createdBy='.$core->user['uid'], array('order' => array('by' => array('isQuiz', 'title'), 'sort' => array('sort' => array('isQuiz' => 'DESC', 'title' => 'ASC'))), 'returnarray' => true));
    if(is_array($surveystemplates)) {
        $onchange = '$("a[id=previewtemplate_link]").attr("href","index.php?module=surveys/preview&stid=" + $("select[id=stid]").val());';
        $surveytemplates_list = parse_selectlist('stid', 5, $surveystemplates, $survey['stid'], '', $onchange, array('id' => 'stid'));
    }

    $radiobuttons['isPublicFill'] = parse_yesno('isPublicFill', 1, $survey['isPublicFill']);
    $radiobuttons['isPublicResults'] = parse_yesno('isPublicResults', 1, $survey['isPublicResults']);
    $radiobuttons['anonymousFilling'] = parse_yesno('anonymousFilling', 1, $survey['anonymousFilling']);
    $radiobuttons['isExternal'] = parse_yesno('isExternal', 1);

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

    /* Parse Invitations Section - START */
    if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
        $invitations_query_where = ' AND aff.affid IN ('.implode(',', $core->user['affiliates']).')';
    }

    $invitations_query = $db->query("SELECT DISTINCT(ae.uid), u.*, aff.*, displayName, aff.name AS mainaffiliate, aff.affid
						FROM ".Tprefix."users u
						JOIN ".Tprefix."affiliatedemployees ae ON (u.uid=ae.uid)
						JOIN ".Tprefix."affiliates aff ON (aff.affid=ae.affid)
						WHERE gid!='7' AND isMain='1'{$invitations_query_where}
						ORDER BY displayName ASC");

    if($db->num_rows($invitations_query) > 0) {
        while($user = $db->fetch_assoc($invitations_query)) {
            $rowclass = alt_row($rowclass);

            $userpositions = $hiddenpositions = $break = '';

            $user_positions_query = $db->query("SELECT p.* FROM ".Tprefix."positions p LEFT JOIN ".Tprefix."userspositions up ON (up.posid=p.posid) WHERE up.uid='{$user[uid]}' ORDER BY p.name ASC");
            $positions_counter = 0;

            while($position = $db->fetch_assoc($user_positions_query)) {
                if(!empty($lang->{$position['name']})) {
                    $position['title'] = $lang->{$position['name']};
                }

                if(++$positions_counter > 2) {
                    $hidden_positions .= $break.$position['title'];
                }
                else {
                    $userpositions .= $break.$position['title'];
                }
                $break = '<br />';
            }

            if($positions_counter > 2) {
                $userpositions = $userpositions.", <a href='#' id='showmore_positions_{$user[uid]}'>...</a> <span style='display:none;' id='positions_{$user[uid]}'>{$hidden_positions}</span>";
            }

            /* Get User Segments - START */
            $user_segments_query = $db->query("SELECT es.*, u.uid ,u.username, ps.title, ps.psid
										FROM ".Tprefix."employeessegments es
										JOIN ".Tprefix."users u ON (es.uid=u.uid)
										JOIN ".Tprefix."productsegments ps ON (ps.psid=es.psid)
										WHERE es.uid='{$user[uid]}'
										ORDER BY title ASC");
            $usersegments = $hidden_segments = $break = '';
            $segment_counter = 0;
            while($segment = $db->fetch_assoc($user_segments_query)) {
                if(++$segment_counter > 2) {
                    $hidden_segments .= $break.$segment['title'];
                }
                else {
                    $usersegments = $break.$segment['title'];
                }
                $break = '<br />';
            }

            if($segment_counter > 2) {
                $usersegments .= ", <a href='#{$user[uid]}' id='showmore_segments_{$user[uid]}'>...</a> <span style='display:none;' id='segments_{$user[uid]}'>{$hidden_segments}</span>";
            }
            /* Get User Segments - END */

            $invitationsgroup = 1;
            eval("\$invitations_rows .= \"".$template->get('surveys_createsurvey_invitationrows')."\";");
        }
    }
    eval("\$defaultmsg = \"".$template->get('surveys_createsurvey_invitationlayout_body')."\";");
    /* Parse Invitations Section - START */
    eval("\$createsurvey = \"".$template->get('surveys_createsurvey')."\";");
    output_page($createsurvey);
}
else {
    if($core->input['action'] == 'do_perform_createsurvey') {
        $survey = new Surveys();
        $survey->create_survey($core->input);

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
            case 4:
                output_xml("<status>false</status><message>{$lang->mustinviteusers}</message>");
                break;
            case 5:
                header('Content-type: text/xml+xhtml');
                output_xml("<status>false</status><message><![CDATA[ ".$errorhandler->get_errors_inline()." ]]></message>");
                break;
        }
    }
    if($core->input['action'] == 'sendinvitations') {
        $surveyobj = new Surveys($core->input['identifier']);
        $surveyobj->send_additional_invitations($core->input);
    }
}
?>