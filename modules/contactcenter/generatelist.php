<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: generatelist.php
 * Created:        @hussein.barakat    Aug 5, 2015 | 1:50:57 PM
 * Last Update:    @hussein.barakat    Aug 5, 2015 | 1:50:57 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if(!$core->input['action']) {
    $filters_userrow_display = 'show';
    $filters_user_config = array(
            'parse' => array('filters' => array('name', 'position', 'entities', 'segment', 'allenabledaffiliates', 'allaffiliates', 'reportsTo'),
                    'overwriteField' => array()
            ),
            'process' => array(
                    'filterKey' => 'uid',
                    'mainTable' => array(
                            'name' => 'users',
                            'filters' => array('reportsTo' => array('operatorType' => 'multiple', 'name' => 'reportsTo')),
                            'extraSelect' => 'CONCAT(firstName, \' \', lastName) AS fullName',
                            'havingFilters' => array('name' => 'fullName'),
                    ),
                    'secTables' => array(
                            'userspositions' => array(
                                    'filters' => array('position' => array('operatorType' => 'multiple', 'name' => 'posid')),
                            ),
                            'affiliatedemployees' => array(
                                    'filters' => array('allenabledaffiliates' => array('operatorType' => 'multiple', 'name' => 'affid')),
                                    'extraWhere' => 'isMain=1'
                            ),
                            'affiliatedemployees2' => array(
                                    'filters' => array('allaffiliates' => array('operatorType' => 'multiple', 'name' => 'affid')),
                                    'tablename' => 'affiliatedemployees',
                            ),
                            'employeessegments' => array(
                                    'filters' => array('segment' => array('operatorType' => 'multiple', 'name' => 'psid')),
                            ),
                            'assignedemployees' => array(
                                    'filters' => array('entities' => array('operatorType' => 'equal', 'name' => 'eid')),
                            ),
                    )
            )
    );
    $userfilter = new Inlinefilters($filters_user_config);
    $filters_user_row = $userfilter->prase_filtersrows(array('hidebutton' => true, 'tags' => 'table', 'display' => $filters_userrow_display));
    $filters_reprow_display = 'show';

    $entitytype = array('s' => $lang->supplier, 'c' => $lang->customer, 'pc' => $lang->potentialcustomer, 'ps' => $lang->potentialsupplier);
    $suppliertype = array('t' => $lang->trader, 'p' => $lang->producer);
    $filters_rep_config = array(
            'parse' => array('filters' => array('name', 'userpermentities', 'companytype', 'suppliertype', 'usersegments', 'assignedaff', 'requiresQr', 'hasContract', 'coid'),
                    'overwriteField' => array(
                            'requiresQr' => '<select name="extrafilters[requiresQr][]"><option></option><option value=0>Yes</option><option value=1>No</option></select>',
                            'hasContract' => '<select name="extrafilters[hasContract][]"><option></option><option value=1>Yes</option><option value=0>No</option></select>',
                            'coid' => parse_selectlist('extrafilters[coid][]', 0, Countries::get_data(), $core->input['extrafilters']['coid'], '1', '', array('multiplesize' => 3, 'blankstart' => true)),
                            'companytype' => parse_selectlist('extrafilters[companytype][]', 0, $entitytype, $core->input['extrafilters']['companytype'], '1', '', array('multiplesize' => 3, 'blankstart' => true)),
                            'suppliertype' => parse_selectlist('extrafilters[suppliertype][]', 0, $suppliertype, $core->input['extrafilters']['suppliertype'], '1', '', array('multiplesize' => 3, 'blankstart' => true)),
                            'assignedaff' => parse_selectlist('extrafilters[assignedaff][]', 0, Affiliates::get_affiliates('isActive=1'), $core->input['extrafilters']['assignedaff'], '1', '', array('multiplesize' => 3, 'blankstart' => true)),
                    )
            ),
            'process' => array(
                    'filterKey' => 'rpid',
                    'mainTable' => array(
                            'name' => 'representatives',
                            'filters' => array('name' => array('operatorType' => 'equal', 'name' => 'name')),
                    ),
                    'secTables' => array(
                            'entitiesrepresentatives' => array(
                                    'filters' => array('userpermentities' => array('operatorType' => 'equal', 'name' => 'eid')),
                            ),
                            'representativessegments' => array(
                                    'filters' => array('usersegments' => array('operatorType' => 'multiple', 'name' => 'psid')),
                            ),
                    )
            )
    );
    $repfilter = new Inlinefilters($filters_rep_config);
    $filters_repr_row = $repfilter->prase_filtersrows(array('hidebutton' => true, 'tags' => 'table', 'display' => $filters_reprow_display));

    $userchecked = ' checked="checked"';
    eval("\$generatelist = \"".$template->get('contacts_generatelist')."\";");
    output_page($generatelist);
}
else {
    if($core->input['action'] == 'user') {
        $sort_query['sort'] = 'ASC';
        $sort_query['by'] = 'displayName';
//user filters-start
        $filters_userrow_display = 'show';

        $filters_user_config = array(
                'parse' => array('filters' => array('name', 'position', 'entities', 'segment', 'allenabledaffiliates', 'allaffiliates', 'reportsTo'),
                        'overwriteField' => array()
                ),
                'process' => array(
                        'filterKey' => 'uid',
                        'mainTable' => array(
                                'name' => 'users',
                                'filters' => array('reportsTo' => array('operatorType' => 'multiple', 'name' => 'reportsTo')),
                                'extraSelect' => 'CONCAT(firstName, \' \', lastName) AS fullName',
                                'havingFilters' => array('name' => 'fullName'),
                        ),
                        'secTables' => array(
                                'userspositions' => array(
                                        'filters' => array('position' => array('operatorType' => 'multiple', 'name' => 'posid')),
                                ),
                                'affiliatedemployees' => array(
                                        'filters' => array('allenabledaffiliates' => array('operatorType' => 'multiple', 'name' => 'affid')),
                                        'extraWhere' => 'isMain=1'
                                ),
                                'affiliatedemployees2' => array(
                                        'filters' => array('allaffiliates' => array('operatorType' => 'multiple', 'name' => 'affid')),
                                        'tablename' => 'affiliatedemployees',
                                ),
                                'employeessegments' => array(
                                        'filters' => array('segment' => array('operatorType' => 'multiple', 'name' => 'psid')),
                                ),
                                'assignedemployees' => array(
                                        'filters' => array('entities' => array('operatorType' => 'equal', 'name' => 'eid')),
                                ),
                        )
                )
        );
        $userfilter = new Inlinefilters($filters_user_config);
        $userchecked = 'checked="checked"';
        $filter_whereuser_values = $userfilter->process_multi_filters();
        // $filter_userwhere = null;
        $filter_userwhere = 'gid!=7';
        if(is_array($filter_whereuser_values)) {
            $filter_userwhere .= ' AND '.$filters_user_config['process']['filterKey'].' IN ('.implode(',', $filter_whereuser_values).')';
            $multipage_userwhere .= ' AND '.$filters_user_config['process']['filterKey'].' IN ('.implode(',', $filter_whereuser_values).')';
        }


        $users = Users::get_data($filter_userwhere, array('returnarray' => true, 'simple' => false, 'order' => $sort_query));
        if(is_array($users)) {
            $first_timeuser = 0;
            $results_head = '<th>'.$lang->name.'</th>';
            $results_head .= '<th>'.$lang->email.'</th>';

            foreach($users as $user_obj) {
                $results_body .= '<tr>';
                $results_body .= '<td>'.$user_obj->parse_link().'</td>';
                $results_body .= '<td>'.$user_obj->email.'</td>';

                $result_title = $lang->employeeresults;
                if(is_array($core->input['user'])) {
                    foreach($core->input['user']as $field) {
                        switch($field) {
                            case 'position':
                                if($first_timeuser == 0) {
                                    $results_head .= '<th>'.$lang->position.'</th>';
                                }
                                $userposition = UserPositions::get_data(array('uid' => $user_obj->uid), array('returnarray' => false));
                                if(is_object($userposition)) {
                                    $results_body .= '<td>'.$userposition->get_position()->get_displayname().'</td>';
                                }
                                elseif(is_array($userposition)) {
                                    foreach($userposition as $pos) {
                                        $positions[] = $pos->get_position()->get_displayname();
                                    }
                                    $results_body .= '<td>'.implode(', ', $positions).'</td>';
                                    $positions = '';
                                }

                                break;
                            case 'entities':
                                if($first_timeuser == 0) {
                                    $results_head .= '<th>'.$lang->assignedbusinesspartner.'</th>';
                                }
                                $assignedemps = AssignedEmployees::get_data(array('uid' => $user_obj->uid), array('returnarray' => true));
                                if(is_array($assignedemps)) {
                                    foreach($assignedemps as $assignedemp) {
                                        $entities[] = $assignedemp->get_entity()->get_displayname();
                                    }
                                    $results_body .= '<td>'.implode(', ', $entities).'</td>';
                                }
                                else {
                                    $results_body .= '<td>-</td>';
                                }
                                $entities = '';
                                break;
                            case 'segment':
                                if($first_timeuser == 0) {
                                    $results_head .= '<th>'.$lang->segments.'</th>';
                                }
                                $employeesegs = EmployeeSegments::get_data(array('uid' => $user_obj->uid), array('returnarray' => true));
                                if(is_array($employeesegs)) {
                                    foreach($employeesegs as $employeeseg) {
                                        $segments[] = $employeeseg->get_segment()->get_displayname();
                                    }
                                    $results_body.='<td>'.implode(', ', $segments).'</td>';
                                }
                                else {
                                    $results_body.='<td>-</td>';
                                }
                                $segments = '';
                                break;
                            case 'allenabledaffiliates':
                                if($first_timeuser == 0) {
                                    $results_head .= '<th>'.$lang->mainaffiliate.'</th>';
                                }
                                $assignedaff = AffiliatedEmployees::get_data(array('uid' => $user_obj->uid, 'isMain' => 1), array('returnarray' => false));
                                if(is_object($assignedaff)) {
                                    $results_body .= '<td>'.$assignedaff->get_affiliate()->get_displayname().'</td>';
                                }
                                break;
                            case 'allaffiliates':
                                if($first_timeuser == 0) {
                                    $results_head .= '<th>'.$lang->assignedaffiliate.'</th>';
                                }
                                $assignedaff = AffiliatedEmployees::get_data(array('uid' => $user_obj->uid), array('returnarray' => true));
                                if(is_array($assignedaff)) {
                                    foreach($assignedaff as $assign) {
                                        $assgined[] = $assign->get_affiliate()->get_displayname();
                                    }
                                    $results_body .= '<td>'.implode(', ', $assgined).'</td>';
                                    $assgined = '';
                                }
                                break;
                            case 'reportsTo':
                                if($first_timeuser == 0) {
                                    $results_head .= '<th>'.$lang->reportsto.'</th>';
                                }
                                $reportsto = new Users($user_obj->reportsTo);
                                if(is_object($reportsto)) {
                                    $results_body .= '<td>'.$reportsto->parse_link().'</td>';
                                }
                                $reportsto = '';
                                break;
                            default :
                                $results_body .= '<td>-</td>';
                                break;
                        }
                    }
                }
                $results_body .= '</tr>';
                $first_timeuser = 1;
            }
            eval("\$results = \"".$template->get('contacts_generatelist_results')."\";");
            output_xml('<message><![CDATA['.$results.']]></message>');
            exit;
        }
        else {
            output_xml("<status>false</status><message>{$lang->noresultsfound}</message>");
            exit;
        }
    }
    if($core->input['action'] == 'rep') {
        $permissions = $core->user_obj->get_businesspermissions();
//        if(is_array($permissions['psid'])) {
//            $extrawhere['psid'] = 'psid IN ('.implode(',', array_filter($permissions['psid'])).')';
//        }
        if(is_array($permissions['eid'])) {
            $extrawhere['eid'] = 'eid IN ('.implode(',', array_filter($permissions['eid'])).')';
        }
        $filters_rep_config = array(
                'parse' => array('filters' => array('name', 'userpermentities', 'companytype', 'suppliertype', 'usersegments', 'assignedaff', 'requiresQr', 'hasContract', 'coid'),
                        'overwriteField' => array(
                                'requiresQr' => '<select name="extrafilters[requiresQr][]"><option></option><option value=1>Yes</option><option value=0>No</option></select>',
                                'hasContract' => '<select name="extrafilters[hasContract][]"><option></option><option value=1>Yes</option><option value=0>No</option></select>',
                                'coid' => parse_selectlist('extrafilters[coid][]', 0, Countries::get_data(), $core->input['extrafilters']['coid'], '1', '', array('multiplesize' => 3, 'blankstart' => true)),
                                'companytype' => parse_selectlist('extrafilters[companytype][]', 0, $entitytype, $core->input['extrafilters']['companytype'], '1', '', array('multiplesize' => 3, 'blankstart' => true)),
                                'suppliertype' => parse_selectlist('extrafilters[suppliertype][]', 0, $suppliertype, $core->input['extrafilters']['suppliertype'], '1', '', array('multiplesize' => 3, 'blankstart' => true)),
                                'assignedaff' => parse_selectlist('extrafilters[assignedaff][]', 0, Affiliates::get_affiliates('isActive=1'), $core->input['extrafilters']['assignedaff'], '1', '', array('multiplesize' => 3, 'blankstart' => true)),
                        )
                ),
                'process' => array(
                        'filterKey' => 'rpid',
                        'mainTable' => array(
                                'name' => 'representatives',
                                'filters' => array('name' => array('name' => 'name')),
                        ),
                        'secTables' => array(
                                'entitiesrepresentatives' => array(
                                        'filters' => array('userpermentities' => array('operatorType' => 'equal', 'name' => 'eid')),
                                        'extraWhere' => $extrawhere['eid']
                                ),
                                'representativessegments' => array(
                                        'filters' => array('usersegments' => array('operatorType' => 'multiple', 'name' => 'psid')),
                                // 'extraWhere' => $extrawhere['psid']
                                ),
                        )
                )
        );
        $repfilter = new Inlinefilters($filters_rep_config);
        $filter_whererep_values = $repfilter->process_multi_filters();
        $filter_userwhere = null;

        if(is_array($core->input['extrafilters'])) {
            $repids = array();
            foreach($core->input['extrafilters'] as $filter => $val) {
                switch($filter) {
                    case 'assignedaff':
                        $val = array_filter($val);
                        if(!is_array($val) || empty($val)) {
                            break;
                        }
                        $extrafilters[AffiliatedEntities]['affid'] = $val;
//                    $entities[] = $affiliatedents[] = '';
//                    foreach($val as $type) {
//                        if(empty($type)) {
//                            continue;
//                        }
//                        $affiliatedents = AffiliatedEntities::get_data(array('affid' => $val), array('returnarray' => true));
//                        if(is_array($affiliatedents)) {
//                            foreach($affiliatedents as $affiliatedent) {
//                                $entities[] = $affiliatedent->get_entity();
//                            }
//                        }
//                    }
//                    if(is_array($entities) && !empty($entities)) {
//                        $entrepids['assignedaff'] = get_repids($entities);
//                    }
                        break;
                    case 'suppliertype':
                        $val = array_filter($val);
                        if(empty($val)) {
                            break;
                        }
                        $extrafilters[Entities]['supplierType'] = $val;
//                    $entities[] = '';
//                    foreach($val as $type) {
//                        if(empty($type)) {
//                            continue;
//                        }
//                        $ents = Entities::get_data(array('supplierType' => $type), array('returnarray' => true));
//                        if(is_array($ents)) {
//                            $entities = array_filter(array_merge($entities, $ents));
//                        }
//                        $ents = '';
//                    }
//                    if(is_array($entities) && !empty($entities)) {
//                        $entrepids['suppliertype'] = get_repids($entities);
//                    }
                        break;
                    case 'companytype':
                        $val = array_filter($val);
                        if(empty($val)) {
                            break;
                        }
                        $extrafilters[Entities]['type'] = $val;
//                    $entities[] = '';
//                    foreach($val as $type) {
//                        if(empty($type) && $type != 0) {
//                            continue;
//                        }
//                        $ents = Entities::get_data(array('type' => $type), array('returnarray' => true));
//                        if(is_array($ents)) {
//                            $entities = array_filter(array_merge($entities, $ents));
//                        }
//                        $ents = '';
//                    }
//                    if(is_array($entities) && !empty($entities)) {
//                        $entrepids['companytype'] = get_repids($entities);
//                    }
                        break;
                    case 'coid':
                        $val = array_filter($val);
                        if(empty($val)) {
                            break;
                        }
                        $extrafilters[Entities]['country'] = $val;
//                    $entities[] = '';
//                    foreach($val as $coid) {
//                        if(empty($coid) && $coid != 0) {
//                            continue;
//                        }
//                        $ents = Entities::get_data(array('country' => $coid), array('returnarray' => true));
//                        if(is_array($ents)) {
//                            $entities = array_filter(array_merge($entities, $ents));
//                        }
//                        $ents = '';
//                    }
//                    if(is_array($entities) && !empty($entities)) {
//                        $entrepids['coid'] = get_repids($entities);
//                    }
                        break;
                    case 'requiresQr':
                        if(empty($val[0])) {
                            break;
                        }
                        $extrafilters[Entities]['noQReportReq'] = $val;
//                    $entities[] = '';
//                    foreach($val as $qr) {
//                        if(is_empty($qr) && $qr != 0) {
//                            continue;
//                        }
//                        if($qr == 0) {
//                            $entities = array_filter(array_merge($entities, Entities::get_data('noQReportReq IS NOT NULL AND noQReportReq <> 0'), array('returnarray' => true)));
//                        }
//                        else {
//                            $entities = array_filter(array_merge($entities, Entities::get_data('noQReportReq IS NULL OR noQReportReq =0'), array('returnarray' => true)));
//                        }
//                    }
//                    if(is_array($entities) && !empty($entities)) {
//                        $entrepids['requiresQr'] = get_repids($entities);
//                    }
                        break;
                    case 'hasContract':
                        if(empty($val[0])) {
                            break;
                        }
                        $extrafilters['operators']['contractExpiryDate'] = 'CUSTOMSQLSECURE';

                        if($val[0] == 1) {
                            $extrafilters[Entities]['contractExpiryDate'] = ' contractExpiryDate > '.TIME_NOW;
                        }
                        else {
                            $extrafilters[Entities]['contractExpiryDate'] = 'contractExpiryDate  < '.TIME_NOW;
                        }

//                    $entities[] = '';
//                    foreach($val as $cont) {
//                        if(is_empty($cont) && $cont != 0) {
//                            continue;
//                        }
//                        if($cont == 1) {
//                            $entities = array_filter(array_merge($entities, Entities::get_data('contractFirstSigDate IS NOT NULL OR contractFirstSigDate !=0'), array('returnarray' => true)));
//                        }
//                        elseif($cont == 0) {
//                            $entities = array_filter(array_merge($entities, Entities::get_data('contractFirstSigDate IS NULL OR contractFirstSigDate = 0'), array('returnarray' => true)));
//                        }
//                    }
//                    if(is_array($entities) && !empty($entities)) {
//                        $entrepids['hasContract'] = get_repids($entities);
//                    }
                        break;
                    default:
                        break;
                }
            }
        }
        if(isset($extrafilters)) {
            if(is_array($permissions['eid'])) {
                if(isset($extrafilters[Entities]['eid'])) {
                    $extrafilters[Entities]['eid'] = array_intersect($extrafilters[Entities]['eid'], $permissions['eid']);
                }
                else {
                    $extrafilters[Entities]['eid'] = $permissions['eid'];
                }
            }
            $ents = Entities::get_data($extrafilters[Entities], array('returnarray' => true, 'operators' => array('contractExpiryDate' => $extrafilters['operators']['contractExpiryDate'])));
            if(isset($extrafilters[AffiliatedEntities]) && !empty($extrafilters[AffiliatedEntities])) {
                $extrafilters[AffiliatedEntities]['eid'] = array_keys($ents);
                $affiliatedents = AffiliatedEntities::get_data($extrafilters[AffiliatedEntities], array('returnarray' => true));
                $eids = array_map(
                        function($e) {
                    return $e->eid;
                }, $affiliatedents);
                $eidsdup = $eids;
                $eids = array_combine($eidsdup, $eidsdup);
                $ents = array_intersect_key($ents, $eids);
            }
            if(is_array($ents)) {
                $repids = get_repids($ents);
            }
        }
//    if(is_array($entrepids)) {
//        foreach($entrepids as $entrepid) {
//            if(is_array($entrepid)) {
//                $interarrs[] = array_filter($entrepid);
//            }
//        }
//        if(is_array($interarrs)) {
//            if(count($interarrs) > 1) {
//                $repids = call_user_func_array('array_intersect', $interarrs);
//            }
//            else {
//                $repids = $interarrs;
//            }
//        }
//    }
        $filter_whererep = array();
        if(is_array($filter_whererep_values) && !empty($filter_whererep_values)) {
            $filter_whererep = $filter_whererep_values;
        }
        if(is_array($repids) && !empty($repids)) {
            if(is_array($filter_whererep_values) && !empty($filter_whererep_values)) {
                $filter_wherereps = $filter_whererep;
                $filtered = 1;
                $filter_whererep = array_intersect(array_filter(array_unique($repids)), $filter_wherereps);
            }
            else {
                $filter_whererep = array_filter(array_unique($repids));
            }
        }
        if(is_array($filter_whererep) && !empty($filter_whererep)) {
            $filter_repwhere = ' '.$filters_rep_config['process']['filterKey'].' IN ('.implode(', ', $filter_whererep).')';
            $multipage_repwhere .= ' AND '.$filters_rep_config['process']['filterKey'].' IN ('.implode(', ', $filter_whererep).')';
        }
        if($filtered == 1 && empty($filter_repwhere)) {
            output_xml("<status>false</status><message>{$lang->noresultsfound}</message>");
            exit;
        }
        $representatives = Representatives::get_data($filter_repwhere, array('returnarray' => true, 'simple' => false, 'order' => array('by' => 'name', 'sort' => 'ASC')));
        if(is_array($representatives)) {
            $first_timerep == 0;
            $result_title = $lang->representativesresults;
            $results_head = '<th>'.$lang->name.'</th>';
            $results_head .= '<th>'.$lang->email.'</th>';
            $results_head .= '<th>'.$lang->assignedbusinesspartner.'</th>';
            foreach($representatives as $representative) {
                $results_body.= '<tr>';
                $results_body.='<td>'.$representative->get_displayname().'</td>';
                $results_body.='<td>'.$representative->email.'</td>';
                $assignedreps = EntitiesRepresentatives::get_data(array('rpid' => $representative->rpid), array('returnarray' => true));
                if(is_array($assignedreps)) {
                    $entities = array();
                    foreach($assignedreps as $assignedrep) {
                        if($assignedrep->rpid == 0) {
                            continue;
                        }
                        $entities[] = $assignedrep->get_entity();
                        $entitienames[] = $assignedrep->get_entity()->get_displayname();
                    }
                }
                if(is_array($entitienames)) {
                    $results_body.='<td>'.implode('<br>', $entitienames).'</td>';
                }
                else {
                    $results_body.='<td> - </td>';
                }
                if(is_array($core->input['representative'])) {
                    foreach($core->input['representative']as $field) {
                        switch($field) {
                            case 'coid':
                                if($first_timerep == 0) {
                                    $results_head .= '<th>'.$lang->country.'</th>';
                                }
                                if(is_array($entities)) {
                                    $entype = array();
                                    $results_body.='<td>';
                                    foreach($entities as $entity) {
                                        if(!is_object($entity)) {
                                            continue;
                                        }
                                        $results_body.=$entity->get_country()->get_displayname().'<br>';
                                    }
                                    $results_body.='</td>';
                                    $entype = '';
                                }
                                else {
                                    $results_body.='<td>-</td>';
                                }
                                break;
                            case 'hasContract':
                                if($first_timerep == 0) {
                                    $results_head .= '<th>'.$lang->suppliertype.'</th>';
                                }
                                if(is_array($entities)) {
                                    $entype = array();
                                    $results_body.='<td>';
                                    foreach($entities as $entity) {
                                        if(!is_object($entity)) {
                                            continue;
                                        }
                                        if(!empty($entity->contractFirstSigDate) && strlen($entity->contractFirstSigDate)) {
                                            $results_body.=$lang->yes.'<br>';
                                        }
                                        else {
                                            $results_body.=$lang->no.'<br>';
                                        }
                                    }
                                    $results_body.='</td>';
                                    $entype = '';
                                }
                                else {
                                    $results_body.='<td>-</td>';
                                }
                                break;
                            case 'suppliertype':
                                if($first_timerep == 0) {
                                    $results_head .= '<th>'.$lang->suppliertype.'</th>';
                                }
                                if(is_array($entities)) {
                                    $entype = array();
                                    $results_body.='<td>';

                                    foreach($entities as $entity) {
                                        if(!is_object($entity)) {
                                            continue;
                                        }
                                        $results_body.=$entity->get_suptype().'<br>';
                                    }
                                    $results_body.='</td>';
                                    $entype = '';
                                }
                                else {
                                    $results_body.='<td>-</td>';
                                }
                                break;
                            case 'companytype':
                                if($first_timerep == 0) {
                                    $results_head .= '<th>'.$lang->companytype.'</th>';
                                }
                                if(is_array($entities)) {
                                    $entype = array();
                                    $results_body.='<td>';
                                    foreach($entities as $entity) {
                                        if(!is_object($entity)) {
                                            continue;
                                        }
                                        $results_body.=$entity->get_type().'<br>';
                                    }
                                    $results_body.='</td>';
                                    $entype = '';
                                }
                                else {
                                    $results_body.='<td>-</td>';
                                }
                                break;
                            case 'requiresQr':
                                if($first_timerep == 0) {
                                    $results_head .= '<th>'.$lang->requiresqr.'</th>';
                                }
                                if(is_array($entities)) {
                                    $requires = array();
                                    $results_body.='<td>';
                                    foreach($entities as $entity) {
                                        if(!is_object($entity)) {
                                            continue;
                                        }
                                        if($entity->noQReportReq == 1) {
                                            $results_body.=$lang->no.'<br>';
                                        }
                                        elseif($entity->noQReportReq == 0) {
                                            $results_body.=$lang->yes.'<br>';
                                        }
                                        else {
                                            $results_body.=' - ';
                                        }
                                    }
                                    $results_body.='</td>';
                                    $requires = '';
                                }
                                else {
                                    $results_body.='<td>-</td>';
                                }
                                break;
                            case 'segment':
                                if($first_timerep == 0) {
                                    $results_head .= '<th>'.$lang->segments.'</th>';
                                }
                                $segments = array();
                                $repssegs = RepresentativesSegments::get_data(array('rpid' => $representative->rpid), array('returnarray' => true));
                                if(is_array($repssegs)) {
                                    foreach($repssegs as $repsseg) {
                                        $segments[] = $repsseg->get_segment()->get_displayname();
                                    }
                                    $results_body.='<td>'.implode(', ', $segments).'</td>';
                                }
                                else {
                                    $results_body.='<td>-</td>';
                                }
                                $segments = '';
                                break;
                            case 'assignedaff':
                                if($first_timerep == 0) {
                                    $results_head .= '<th>'.$lang->assignedaffiliate.'</th>';
                                }
                                if(is_array($entities)) {
                                    $affnames = array();
                                    foreach($entities as $entity) {
                                        $affiliatedents = AffiliatedEntities::get_data(array('eid' => $entity->eid), array('returnarray' => true));
                                    }
                                    if(is_array($affiliatedents)) {
                                        foreach($affiliatedents as $affiliatedent) {
                                            $affnames[] = $affiliatedent->get_affiliate()->get_displayname();
                                        }
                                        $results_body.='<td>'.implode(', ', $affnames).'</td>';
                                        $affnames = '';
                                    }
                                    else {
                                        $results_body.='<td>-</td>';
                                    }
                                }
                                else {
                                    $results_body.='<td>-</td>';
                                }
                                break;
                            default :
                                $results_body.='<td>-</td>';
                                break;
                        }
                    }
                }
                $results_body .= '</tr>';
                $first_timerep = 1;
                $assignedreps = $entities = $entitienames = '';
            }
            eval("\$results = \"".$template->get('contacts_generatelist_results')."\";");
            output_xml('<message><![CDATA['.$results.']]></message>
                        ');
            exit;
        }
        else {
            output_xml("<status>false</status><message>{$lang->noresultsfound}</message>");
            exit;
        }
    }
}
function get_repids(array $entities) {
    foreach($entities as $entity) {
        if(is_object($entity)) {
            $reps = $entity->get_representatives_ids();
            if(is_array($reps) && !empty($reps)) {
                if(is_array($entrepids) && !empty($entrepids)) {
                    $entrepids = array_merge($entrepids, array_filter(array_unique($reps)));
                }
                else {
                    $entrepids = array_filter(array_unique($reps));
                }
            }
        }
    }
    return $entrepids;
}
