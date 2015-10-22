<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: rep.php
 * Created:        @hussein.barakat    Oct 21, 2015 | 9:40:16 AM
 * Last Update:    @hussein.barakat    Oct 21, 2015 | 9:40:16 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $sort_query = 'isActive DESC';

    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query .= $db->escape_string(', '.$core->input['sortby']).' '.$db->escape_string($core->input['order']);
    }

    $sort_url = sort_url();
    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('name', 'email', 'telephone', 'userpermentities', 'position'),
                    'overwriteField' => array(
                            'position' => '',
                            'telephone' => '',
                    )
            ),
            'process' => array(
                    'filterKey' => 'rpid',
                    'mainTable' => array(
                            'name' => 'representatives',
                            'filters' => array('name' => array('name' => 'name'), 'email' => array('name' => 'email')),
                    ),
                    'secTables' => array(
                            'entitiesrepresentatives' => array(
                                    'filters' => array('userpermentities' => array('operatorType' => 'equal', 'name' => 'eid')),
                            ),
                    )
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filter_where = null;
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));

    /* filter end */
    $user = new Users($core->user['uid']);
    $permissions = $user->get_businesspermissions();
    if(is_array($permissions['eid'])) {
        $entityrepresentatives = EntitiesRepresentatives::get_column('rpid', array('eid' => $permissions['eid']), array('returnarray' => true));
        if(is_array($entityrepresentatives)) {
            $permissionsfilter = 'rpid IN ('.implode(',', array_unique($entityrepresentatives)).') ';
        }
    }
    if(empty($permissionsfilter)) {
        error($lang->noentitiesassigned);
    }
    $representatives = Representatives::get_data($permissionsfilter.$filter_where, array('returnarray' => true, 'order' => $sort_query));
    if(is_array($representatives)) {
        foreach($representatives as $representative_obj) {
            if($representative_obj->isActive == 1) {
                $rowclass = 'greenbackground';
            }
            $representative = $representative_obj->get();
            $representativeassignedents = $representative_obj->get_entities_names();
            if(is_array($representativeassignedents)) {
                $representative['companyName'] = implode(', ', $representativeassignedents);
            }
            if($core->usergroup['canManageRepresentatives'] == 1 || $representative['createdBy'] == $core->user['uid']) {
                $edit_link = "<a title=".$lang->editrepresentative." id='editrepresentative_".$representative['rpid']."_profiles/representativeslist_loadpopupbyid'><img src='".$core->settings[rootdir]."/images/icons/edit.gif' border='0'/></a>";
                $edit_link .= "<a title='".$lang->deleterepresentative."' id='deleterepresentative_".$representative['rpid']."_profiles/representativeslist_loadpopupbyid'><img src='".$core->settings[rootdir]."/images/invalid.gif' border='0'/></a>";
            }
//            if($representative['isActive'] == 1) {
//                $rowclass = 'greenbackground';
//            }
            $representative['positions'] = '-';
            $rep_positions = RepresentativePositions::get_column('posid', array('rpid' => $representative['rpid']), array('returnarray' => true));
            if(is_array($rep_positions)) {
                $positions = Positions::get_column('title', array('posid' => $rep_positions), array('returnarray' => true));
                if(is_array($positions)) {
                    $representative['positions'] = implode(', ', $positions);
                }
            }
            eval("\$representatives_list .= \"".$template->get('profiles_representativeslist_representativerow')."\";");
            unset($edit_link, $rowclass, $rep_positions);
        }
    }
    else {
        $representatives_list = '<tr><td colspan="6">'.$lang->nomatchfound.'</td></tr>';
    }
    $sequence = $rownum = 1;
    $repid = 0;
    $inputname = 'entities[]';
    eval("\$companieslist = \"".$template->get('autocomplete_representativentity')."\";");
    unset($representative);
    $positions = Positions::get_data('', array('returnarray' => true, 'order' => 'name'));
    $representative['positions'] = parse_selectlist('positions[]', 1, $positions, $representative_positions, 1);
    $segments = get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', array('by' => 'title', 'sort' => 'ASC'), 0, '');
    $representative['segments'] = parse_selectlist('segments[]', 1, $segments, $representative_segments, 1);
    eval("\$create = \"".$template->get("popup_profiles_representativeslist_edit")."\";");
    eval("\$listpage = \"".$template->get('profiles_representativeslist')."\";");
    output_page($listpage);
}
else {
    if($core->input['action'] == 'get_deleterepresentative') {
        $rpid = intval($core->input['id']);
        $representative = new Representatives($rpid);
        $deletemessage = $lang->deleterep.$representative->get_displayname();
        eval("\$deleterep = \"".$template->get("popup_profiles_representativeslist_delete")."\";");
        echo $deleterep;
    }
    elseif($core->input['action'] == 'do_delete') {
        $rpid = intval($core->input['rpid']);
        $representative = new Representatives($rpid);
        $deleted = $representative->delete_representative();
        if($deleted) {
            output_xml("<status>true</status><message>{$lang->successfullydeleted}</message>");
            exit;
        }
        else {
            output_xml("<status>false</status><message>{$lang->representativeisused}</message>");
            exit;
        }
    }
    elseif($core->input['action'] == 'get_editrepresentative') {
        $rpid = intval($core->input['id']);
        $representative_obj = new Representatives($rpid);
        $representative = $representative_obj->get();
        $entities = $representative_obj->get_entities();
        if(is_array($entities)) {
            $rownum = 0;
            foreach($entities as $entity_obj) {
                $valuename = $entity_obj->get_displayname();
                $valueid = $entity_obj->eid;
                $inputname = 'entities['.$valueid.']';
                $rownum++;
                eval("\$companieslist .= \"".$template->get('autocomplete_representativentity')."\";");
            }
        }
        $phones_index = array('phone');
        foreach($phones_index as $val) {
            $phone[$val] = explode('-', $representative[$val]);
            $representative[$val] = array();
            $representative[$val]['intcode'] = $phone[$val][0];
            $representative[$val]['areacode'] = $phone[$val][1];
            $representative[$val]['number'] = $phone[$val][2];
        }
        $representative_positions = RepresentativePositions::get_column('posid', array('rpid' => $rpid), array('returnarray' => true));
        $positions = Positions::get_data('', array('returnarray' => true, 'order' => 'name'));
        $representative['positions'] = parse_selectlist('positions[]', 1, $positions, $representative_positions, 1);

        $representative_segments = RepresentativesSegments::get_column('psid', array('rpid' => $rpid), array('returnarray' => true));
        $segments = get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', array('by' => 'title', 'sort' => 'ASC'), 0, '');
        $representative['segments'] = parse_selectlist('segments[]', 1, $segments, $representative_segments, 1);

        eval("\$editbox = \"".$template->get("popup_profiles_representativeslist_edit")."\";");
        echo $editbox;
    }
    elseif($core->input['action'] == 'do_edit') {
        if(is_empty($core->input['name'], $core->input['email'], $core->input['entities'])) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }

        if(!isvalid_email($core->input['email'])) {
            output_xml("<status>false</status><message>{$lang->invalidemailaddress}</message>");
            exit;
        }

        $phones_index = array('phone');
        foreach($phones_index as $val) {
            if(isset($core->input[$val.'_intcode'], $core->input[$val.'_areacode'], $core->input[$val.'_number'])) {
                if(!empty($core->input[$val.'_intcode']) || !empty($core->input[$val.'_areacode']) || !empty($core->input[$val.'_number'])) {
                    $core->input[$val] = $core->input[$val.'_intcode'].'-'.$core->input[$val.'_areacode'].'-'.$core->input[$val.'_number'];
                }
                else {
                    $core->input[$val] = '';
                }
                unset($core->input[$val.'_intcode'], $core->input[$val.'_areacode'], $core->input[$val.'_number']);
            }
        }

        $representative = array(
                'name' => $core->input['name'],
                'email' => $core->input['email'],
                'phone' => $core->input['phone']
        );
        if(!empty($core->input['rpid'])) {
            $representative['rpid'] = $db->escape_string($core->input['rpid']);
        }
        $repobj = new Representatives();
        $repobj->set($representative);
        $repobj = $repobj->save();

        if($repobj->get_errorcode() == 0) {
            $rpid = $repobj->rpid;

            /* Clean up - Start */
            $db->delete_query('entitiesrepresentatives', "rpid={$rpid}");
            /* Clean up - End */
            if(is_array($core->input['entities'])) {
                foreach($core->input['entities'] as $eid) {
                    $entrep = new EntitiesRepresentatives();
                    $entrep->set(array('rpid' => $rpid, 'eid' => $eid));
                    $entrep->save();
                }
            }
            //$query = $db->update_query('entitiesrepresentatives', array('eid' => $core->input['eid']), "rpid={$rpid}");

            if(is_array($core->input['positions'])) {
                $db->delete_query('representativespositions', "rpid='{$rpid}'");
                foreach($core->input['positions'] as $key => $val) {
                    $position = $db->insert_query('representativespositions', array('posid' => $val, 'rpid' => $rpid));
                }
            }

            if(is_array($core->input['segments'])) {
                $db->delete_query('representativessegments', "rpid='{$rpid}'");
                foreach($core->input['segments'] as $key => $val) {
                    $db->insert_query('representativessegments', array('psid' => $val, 'rpid' => $rpid));
                }
            }
            output_xml("<status>true</status><message>{$lang->updatedsuccessfully}</message>");
            exit;
        }
        else {
            output_xml("<status>false</status><message>{$lang->updateerror}</message>");
            exit;
        }
    }
    elseif($core->input['action'] == 'ajaxaddmore_entity') {
        $rownum = $sequence = intval($core->input['value']) + 1;
        $inputname = 'entities[]';
        eval("\$companieslist = \"".$template->get('autocomplete_representativentity')."\";");
        output($companieslist);
    }
}
