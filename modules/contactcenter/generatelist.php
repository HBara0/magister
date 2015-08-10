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
ini_set(max_execution_time, 0);
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
                                'filters' => array('segment' => array('operatorType' => 'equal', 'name' => 'psid')),
                        ),
                        'assignedemployees' => array(
                                'filters' => array('entities' => array('operatorType' => 'multiple', 'name' => 'eid')),
                        ),
                )
        )
);
$userfilter = new Inlinefilters($filters_user_config);
if($core->input['action'] == 'user') {
    $userchecked = 'checked="checked"';
    $filter_whereuser_values = $userfilter->process_multi_filters();
    $filter_userwhere = null;
    if(is_array($filter_whereuser_values)) {
        $filter_userwhere = ' '.$filters_user_config['process']['filterKey'].' IN ('.implode(',', $filter_whereuser_values).')';
        $multipage_userwhere .= ' AND '.$filters_user_config['process']['filterKey'].' IN ('.implode(',', $filter_whereuser_values).')';
    }
    $users = Users::get_data($filter_userwhere, array('returnarray' => true, 'simple' => false, 'order' => $sort_query));
    if(is_array($users)) {
        $first_timeuser = 0;
        $results_head = '<th>'.$lang->name.'</th>';
        $results_head .= '<th>'.$lang->email.'</th>';

        foreach($users as $user_obj) {
            $results_body .= '<tr>';
            $results_body.='<td>'.$user_obj->parse_link().'</td>';
            $results_body.='<td>'.$user_obj->email.'</td>';

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
                                $results_body.='<td>'.$userposition->get_position()->get_displayname().'</td>';
                            }
                            elseif(is_array($userposition)) {
                                foreach($userposition as $pos) {
                                    $positions[] = $pos->get_position()->get_displayname();
                                }
                                $results_body.='<td>'.implode(',', $positions).'</td>';
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
                                $results_body.='<td>'.implode(',', $entities).'</td>';
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
                                $results_body.='<td>'.implode(',', $segments).'</td>';
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
                                $results_body.='<td>'.$assignedaff->get_affiliate()->get_displayname().'</td>';
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
                                $results_body.='<td>'.implode(',', $assgined).'</td>';
                                $assgined = '';
                            }
                            break;
                        case 'reportsTo':
                            if($first_timeuser == 0) {
                                $results_head .= '<th>'.$lang->reportsto.'</th>';
                            }
                            $reportsto = new Users($user->reportsTo);
                            if(is_object($reportsto)) {
                                $results_body.='<td>'.$reportsto->parse_link().'</td>';
                            }
                            $reportsto = '';
                            break;
                        default :
                            $results_body.='<td>-</td>';
                            break;
                    }
                }
            }
            $results_body .= '</tr>';
            $first_timeuser = 1;
        }
        eval("\$results = \"".$template->get('contacts_generatelist_results')."\";");
    }
}
$filters_user_row = $userfilter->prase_filtersrows(array('hidebutton' => true, 'tags' => 'table', 'display' => $filters_userrow_display));
//user filters--end
//rep filters-Start
$filters_reprow_display = 'show';
function get_type() {
    if($this->type == 's') {
        return 'Supplier';
    }
    if($this->type == 'c') {
        return 'Customer';
    }
    if($this->type == 'pc') {
        return 'Producer Customer';
    }
    if($this->type == 'ps') {
        return 'Producer Supplier';
    }
    if($this->type == 't') {
        return 'Trader';
    }
    if($this->type == 'p') {
        return 'Producer';
    }
    if($this->type == 'cs') {
        return 'Competitor supplier';
    }
}

$entitytype = array('s' => $lang->supplier, 'c' => $lang->customer, 'pc' => $lang->potentialcustomer, 'ps' => $lang->potentialsupplier);
$suppliertype = array('t' => $lang->trader, 'p' => $lang->producer);
$filters_rep_config = array(
        'parse' => array('filters' => array('name', 'entities', 'companytype', 'suppliertype', 'customertype', 'segment', 'allaffiliates', 'requiresQr', 'hasContract', 'coid'),
                'overwriteField' => array(
                        'requiresQr' => '<select name="extrafilters[requiresQr][]"><option></option><option value=1>Yes</option><option value=0>No</option></select>',
                        'hasContract' => '<select name="extrafilters[hasContract][]"><option></option><option value=1>Yes</option><option value=0>No</option></select>',
                        'coid' => parse_selectlist('extrafilters[coid][]', 0, Countries::get_data(), $core->input['extrafilters']['coid'], '', '', array('multiplesize' => 3, 'blankstart' => true)),
                        'companytype' => parse_selectlist('extrafilters[companytype][]', 0, $entitytype, $core->input['extrafilters']['companytype'], '', '', array('multiplesize' => 3, 'blankstart' => true)),
                        'suppliertype' => parse_selectlist('extrafilters[suppliertype][]', 0, $suppliertype, $core->input['extrafilters']['suppliertype'], '', '', array('multiplesize' => 3, 'blankstart' => true)),
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
                                'filters' => array('entities' => array('operatorType' => 'multiple', 'name' => 'eid')),
                        ),
                        'representativessegments' => array(
                                'filters' => array('segment' => array('operatorType' => 'equal', 'name' => 'psid')),
                        ),
                )
        )
);
$repfilter = new Inlinefilters($filters_rep_config);
if($core->input['action'] == 'rep') {
    $repchecked = 'checked="checked"';
    $filter_whererep_values = $repfilter->process_multi_filters();
    $filter_userwhere = null;
    if(is_array($filter_whererep_values)) {
        $filter_repwhere = ' '.$filters_rep_config['process']['filterKey'].' IN ('.implode(',', $filter_whererep_values).')';
        $multipage_repwhere .= ' AND '.$filters_rep_config['process']['filterKey'].' IN ('.implode(',', $filter_whererep_values).')';
    }
    if(is_array($core->input['extrafilters'])) {
        foreach($core->input['extrafilters'] as $filter => $val) {
            switch($filter) {
                case 'suppliertype':
                    $entties[] = '';
                    foreach($val as $type) {
                        if(empty($type)) {
                            continue;
                        }
                        $entties = array_filter(array_merge($entties, Entities::get_data(array('supplierType' => $type), array('returnarray' => true))));
                    }
                    if(is_array($entties) && !empty($entties)) {
                        foreach($entties as $entity) {
                            if(is_object($entity)) {
                                $reps = $entity->get_representatives_ids();
                                if(is_array($reps)) {
                                    $repids = array_merge($repids, array_filter($reps));
                                }
                            }
                        }
                    }
                    break;
                case 'companytype':
                    $entties[] = '';
                    foreach($val as $type) {
                        if(empty($type)) {
                            continue;
                        }
                        $entties = array_filter(array_merge($entties, Entities::get_data(array('type' => $type), array('returnarray' => true))));
                    }
                    if(is_array($entties) && !empty($entties)) {
                        foreach($entties as $entity) {
                            if(is_object($entity)) {
                                $reps = $entity->get_representatives_ids();
                                if(is_array($reps)) {
                                    $repids = array_merge($repids, array_filter($reps));
                                }
                            }
                        }
                    }
                    break;
                case 'coid':
                    $entties[] = '';
                    foreach($val as $coid) {
                        if(empty($coid)) {
                            continue;
                        }
                        $entties = array_filter(array_merge($entties, Entities::get_data(array('country' => $coid), array('returnarray' => true))));
                    }
                    if(is_array($entties) && !empty($entties)) {
                        foreach($entties as $entity) {
                            if(is_object($entity)) {
                                $reps = $entity->get_representatives_ids();
                                if(is_array($reps)) {
                                    $repids = array_merge($repids, array_filter($reps));
                                }
                            }
                        }
                    }
                    break;
                case 'requiresQr':
                    $entities[] = '';
                    foreach($val as $qr) {
                        if(is_empty($qr)) {
                            continue;
                        }
                        if($qr == 0) {
                            $entities = array_filter(array_merge($entities, Entities::get_data('noQReportReq IS NOT NULL AND noQReportReq <> 0'), array('returnarray' => true)));
                        }
                        else {
                            $entities = array_filter(array_merge($entities, Entities::get_data('noQReportReq IS NULL OR noQReportReq =0'), array('returnarray' => true)));
                        }
                    }
                    if(is_array($entities) && !empty($entities)) {
                        foreach($entities as $entity) {
                            if(is_object($entity)) {
                                $reps = $entity->get_representatives_ids();
                                if(is_array($reps)) {
                                    $repids = array_merge($repids, array_filter($reps));
                                }
                            }
                        }
                    }
                    break;
                case 'hasContract':
                    $entities[] = '';
                    foreach($val as $cont) {
                        if(is_empty($cont)) {
                            continue;
                        }
                        if($cont == 1) {
                            $entities = array_filter(array_merge($entities, Entities::get_data('contractFirstSigDate IS NOT NULL OR contractFirstSigDate !=0'), array('returnarray' => true)));
                        }
                        elseif($cont == 0) {
                            $entities = array_filter(array_merge($entities, Entities::get_data('contractFirstSigDate IS NULL OR contractFirstSigDate =0'), array('returnarray' => true)));
                        }
                    }
                    if(is_array($entities) && !empty($entities)) {
                        foreach($entities as $entity) {
                            if(is_object($entity)) {
                                $reps = $entity->get_representatives_ids();
                                if(is_array($reps)) {
                                    $repids = array_merge($repids, array_filter($reps));
                                }
                            }
                        }
                    }
                    break;
                default:
                    break;
            }
        }
        if(is_array($repids) && !empty($repids)) {
            $extrafilter_where = ' AND rpid IN ['.implode(',', $repids).']';
        }
    }

    $representatives = Representatives::get_data($filter_repwhere.$extrafilter_where, array('returnarray' => true, 'simple' => false, 'order' => array('by' => 'name', 'sort' => 'ASC')));
    if(is_array($representatives)) {
        $first_timerep == 0;
        $results_head = '<th>'.$lang->name.'</th>';
        $results_head .= '<th>'.$lang->email.'</th>';
        $results_head .= '<th>'.$lang->assignedbusinesspartner.'</th>';
        foreach($representatives as $representative) {
            $results_body .= '<tr>';
            $results_body.='<td>'.$representative->get_displayname().'</td>';
            $results_body.='<td>'.$representative->email.'</td>';
            $result_title = $lang->representativesresults;
            $assignedreps = EntitiesRepresentatives::get_data(array('rpid' => $representative->rpid), array('returnarray' => true));
            if(is_array($assignedreps)) {
                foreach($assignedreps as $assignedrep) {
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
                        case 'customertype':
                            if($first_timerep == 0) {
                                $results_head .= '<th>'.$lang->customertype.'</th>';
                            }
                            if(is_array($entities)) {
                                $entype[] = '';
                                $results_body.='<td>';
                                foreach($entities as $entity) {
                                    if(!is_object($entity)) {
                                        continue;
                                    }
                                    $results_body.=' - <br>';
                                }
                                $results_body.='</td>';
                                $entype = '';
                            }
                            break;
                        case 'coid':
                            if($first_timerep == 0) {
                                $results_head .= '<th>'.$lang->country.'</th>';
                            }
                            if(is_array($entities)) {
                                $entype[] = '';
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
                            break;
                        case 'hasContract':
                            if($first_timerep == 0) {
                                $results_head .= '<th>'.$lang->suppliertype.'</th>';
                            }
                            if(is_array($entities)) {
                                $entype[] = '';
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
                            break;
                        case 'suppliertype':
                            if($first_timerep == 0) {
                                $results_head .= '<th>'.$lang->suppliertype.'</th>';
                            }
                            if(is_array($entities)) {
                                $entype[] = '';
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
                            break;
                        case 'companytype':
                            if($first_timerep == 0) {
                                $results_head .= '<th>'.$lang->companytype.'</th>';
                            }
                            if(is_array($entities)) {
                                $entype[] = '';
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
                            break;
                        case 'requiresQr':
                            if($first_timerep == 0) {
                                $results_head .= '<th>'.$lang->requiresqr.'</th>';
                            }
                            if(is_array($entities)) {
                                $requires[] = '';
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
                            break;
                        case 'segment':
                            if($first_timerep == 0) {
                                $results_head .= '<th>'.$lang->segments.'</th>';
                            }
                            $repssegs = RepresentativesSegments::get_data(array('rpid' => $representative->rpid), array('returnarray' => true));
                            if(is_array($repssegs)) {
                                foreach($repssegs as $repsseg) {
                                    $segments[] = $repsseg->get_segment()->get_displayname();
                                }
                                $results_body.='<td>'.implode(',', $segments).'</td>';
                            }
                            else {
                                $results_body.='<td>-</td>';
                            }
                            $segments = '';
                            break;
                        case 'allaffiliates':
                            if($first_timerep == 0) {
                                $results_head .= '<th>'.$lang->assignedaffiliate.'</th>';
                            }
                            if(is_array($entities)) {
                                $affnames[] = '';
                                foreach($entities as $entity) {
                                    $affiliatedents = AffiliatedEntities::get_data(array('eid' => $entitiy->eid, array('returnarray' => true)));
                                }
                                if(is_array($affiliatedents)) {
                                    foreach($affiliatedents as $affiliatedent) {
                                        $affnames[] = $affiliatedent->get_affiliate()->get_displayname();
                                    }
                                    $results_body.='<td>'.implode(',', $affnames).'</td>';
                                    $affnames = '';
                                }
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
    }
}
$filters_repr_row = $repfilter->prase_filtersrows(array('hidebutton' => true, 'tags' => 'table', 'display' => $filters_reprow_display));
//rep filters-end
eval("\$generatelist = \"".$template->get('contacts_generatelist')."\";");
output_page($generatelist);

