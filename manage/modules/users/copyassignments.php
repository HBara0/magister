<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: copyassignments.php
 * Created:        @zaher.reda    Jul 14, 2014 | 9:46:08 AM
 * Last Update:    @zaher.reda    Jul 14, 2014 | 9:46:08 AM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageUsers'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $users = Users::get_users(null, array('order' => 'displayName'));
    $fromuser_selectlist = parse_selectlist('fromUser', 2, $users, '');
    $touser_selectlist = parse_selectlist('toUser', 2, $users, '');

    $segments = ProductsSegments::get_segments();
    $segments_selectlist = parse_selectlist('segments[]', 3, $segments, '', 1);

    $affiliates = Affiliates::get_affiliates();
    $affiliate_selectlist = parse_selectlist('affid', 4, $affiliates, '');
    eval("\$page = \"".$template->get('admin_users_copyassignments')."\";");
    output_page($page);
}
else {
    if($core->input['action'] == 'do_perform_copyassignments') {
        $param = $core->input;

        $sql = "SELECT DISTINCT(ae.eid), e.companyName
	FROM ".Tprefix."assignedemployees ae
	JOIN entities e ON (e.eid=ae.eid)
	JOIN affiliatedentities afe ON (afe.eid=e.eid)
	WHERE uid=".$db->escape_string($param['fromUser'])."
	AND ae.eid IN (SELECT es.eid FROM ".Tprefix."entitiessegments es WHERE es.psid IN (".implode(', ', $param['segments'])."))
	AND ae.eid NOT IN (SELECT ae2.eid FROM ".Tprefix."assignedemployees ae2 WHERE ae2.affid=".intval($param['affid'])." AND ae2.uid=".$db->escape_string($param['toUser']).")
	AND afe.affid=".intval($param['affid'])." AND e.type IN ('c', 's') ORDER BY type ASC, companyName ASC";
        $query = $db->query($sql);

        if($db->num_rows($query) > 0) {
            while($entity = $db->fetch_assoc($query)) {
                if(!empty($entity['eid'])) {
                    $results .= $entity['companyName'].'<br />';
                    $db->insert_query('assignedemployees', array('uid' => $param['toUser'], 'eid' => $entity['eid'], 'affid' => $param['affid']));
                    $db->insert_query('users_transferedassignments', array('fromUser' => $param['fromUser'], 'toUser' => $param['toUser'], 'eid' => $entity['eid'], 'affid' => $param['affid']));
                }
            }

            output_xml("<status>true</status><message><![CDATA[{$lang->successfullysaved}<br />".$results."]]></message>");
            exit;
        }
        else {
            output_xml("<status>false</status><message>{$lang->nomatchfound}</message>");
            exit;
        }
    }
}
?>