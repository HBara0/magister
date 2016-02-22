<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Fill up a visit report
 * $module: CRM
 * $id: fillvisitreport.php
 * Created: 	@zaher.reda 	June 26, 2009 | 11:21 AM
 * Last Update: @zaher.reda 	July 11, 2012 | 11:32 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['crm_canFillVisitReports'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!isset($core->input['identifier'])) {
    $identifier = substr(md5(uniqid(microtime())), 1, 10); //base64_encode($core->user['uid'].'_'.$timenow);
}
else {
    $identifier = $core->input['identifier'];
}
$session->name_phpsession(COOKIE_PREFIX.'fillvisitreport'.$identifier);
$session->start_phpsession();

$lang->load('crm_visitreport');
if(!$core->input['action']) {
    /* Check if there is data in Sessions - START */
    if(isset($core->input['identifier']) && !empty($core->input['identifier'])) {
        $identifier = $db->escape_string($core->input['identifier']);

        $visitreport_obj = new CrmVisitReports();
        $visitreport = $visitreport_obj->get_visitreports(array('identifier' => $identifier));
        if(!is_object($visitreport)) {
            $visitreport = new CrmVisitReports();
            $visitreport->identifier = $identifier;
        }
        unset($visitreport_obj);
        if($core->input['stage'] == 'visitdetails') {
            $rowid = intval($core->input['value']) + 2;

            if($session->isset_phpsession('visitreportvisitdetailsdata_'.$identifier)) {
                $visitdetails = unserialize($session->get_phpsession("visitreportvisitdetailsdata_{$identifier}"));
                $visitreport_data = unserialize($session->get_phpsession("visitreportdata_{$identifier}"));

                if(is_array($visitreport_data['spid'])) {
                    foreach($visitreport_data['spid'] as $key => $val) {
                        if(empty($val) && $val != 0 || (count($visitreport_data['spid']) > 1 && $val == 0)) {
                            unset($visitreport_data['spid'][$key]);
                            continue;
                        }
                        $visitdetails['comments'][$val]['suppliername'] = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='".$db->escape_string($val)."'"), 'companyName');

                        if(empty($visitdetails['comments'][$val]['suppliername'])) {
                            $visitdetails['comments'][$val]['suppliername'] = $lang->unspecified;
                        }

                        if(is_array($visitdetails['comments'])) {
                            foreach($visitdetails['comments'][$val] as $k => $v) {
                                $visitdetails['comments'][$val][$k] = $core->sanitize_inputs($v, array('method' => 'striponly', 'removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                            }
                        }
                        eval("\$visitdetails_fields .= \"".$template->get('crm_fillvisitreport_visitdetailspage_fields')."\";");
                    }
                }
            }
            else {
                if(is_empty($core->input['cid'], $core->input['rpid'], $core->input['productLine'])) {
                    error($lang->fillallrequiredfields, 'index.php?module=crm/fillvisitreport&identifier='.$identifier);
                }

                if(!isset($core->input['spid'])) {
                    error($lang->fillallrequiredfields, 'index.php?module=crm/fillvisitreport&identifier='.$identifier);
                }

                if(is_array($core->input['spid'])) {
                    $marktask_date_output = date('F d, Y', strtotime('+5 weekdays'));
                    $marktask_date = date('d-m-Y', strtotime('+5 weekdays'));

                    foreach($core->input['spid'] as $key => $val) {
                        if(empty($val) && $val != 0 || (count($core->input['spid']) > 1 && $val == 0)) {
                            unset($core->input['spid'][$key]);
                        }
                        else {
                            $visitdetails['comments'][$val]['suppliername'] = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='".$db->escape_string($val)."'"), "companyName");

                            if(empty($visitdetails['comments'][$val]['suppliername'])) {
                                $visitdetails['comments'][$val]['suppliername'] = $lang->unspecified;
                            }

                            if(is_array($visitdetails['comments'])) {
                                foreach($visitdetails['comments'][$val] as $k => $v) {
                                    $visitdetails['comments'][$val][$k] = $core->sanitize_inputs($v, array('method' => 'striponly', 'removetags' => true, 'allowed_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                                }
                            }
                            eval("\$visitdetails_fields .= \"".$template->get('crm_fillvisitreport_visitdetailspage_fields')."\";");
                        }
                    }
                }

                $core->input['vrid'] = $visitreport->vrid;
                $visitreport_data['cid'] = $core->input['cid'];
                $session->set_phpsession(array('visitreportdata_'.$identifier => serialize($core->input)));
            }
            /* Parse MI Data Section - START */
            if($core->usergroup['profiles_canAddMkIntlData'] == 1) {

                $lang->load('profiles_meta');
                $addmarketdata_link = '<div style="float: right;" title="'.$lang->addmarket.'"><a href="#popup_profilesmarketdata" id="showpopup_profilesmarketdata" class="showpopup"><img alt="'.$lang->addmarket.'" src="'.$core->settings['rootdir'].'/images/icons/edit.gif" /></a></div>';
                $module = 'profiles';
                $elemtentid = $visitreport_data['cid'];
                $elementname = 'marketdata[cid]';
                $action = 'do_addmartkerdata';
                $modulefile = 'entityprofile';
                $css['display']['chemsubfield'] = 'none';
                $css['display']['basicingsubfield'] = 'none';
                $css['display']['product'] = 'none';

                $brandprod_rowid = 0;
                $midata = new MarketIntelligence();
                $midata->cid = $visitreport_data['cid'];

                $mkdchem_rowid = 0;
                eval("\$profiles_michemfuncproductentry_row = \"".$template->get('profiles_michemfuncsubstancentry')."\";");
                eval("\$profiles_michemfuncproductentry = \"".$template->get('profiles_michemfuncsubstancentry_rows')."\";");

                $mkdprod_rowid = 0;
                eval("\$profiles_minproductentry_row = \"".$template->get('profiles_michemfuncproductentry')."\";");
                eval("\$profiles_minproductentry = \"".$template->get('profiles_michemfuncproductentry_rows')."\";");

                $mkdbing_rowid = 0;
                eval("\$profiles_mibasicingredientsentry_row = \"".$template->get('profiles_mibasicingredientsentry')."\";");
                eval("\$profiles_mibasicingredientsentry = \"".$template->get('profiles_mibasicingredientsentry_rows')."\";");
                $packaging_list = parse_selectlist('marketdata[competitor]['.$rowid.'][packaging]', 7, Packaging::get_data('name IS NOT NULL'), '', '', '', array('blankstart' => 1));
                $saletype_list = parse_selectlist('marketdata[competitor]['.$rowid.'][saletype]', 8, SaleTypes::get_data('stid IN (1,4)'), '', '', '', array('blankstart' => 1));
                $samplacquire = parse_radiobutton('marketdata[competitor]['.$rowid.'][isSampleacquire]', array(1 => 'yes', 0 => 'no'), '', true);
                $characteristics = ProductCharacteristicValues::get_data(null, array('returnarray' => true));
                $characteristics_list = parse_selectlist('entitybrand[pcvid]', 4, $characteristics, null, 0, null, array('blankstart' => true));
                eval("\$popup_marketdata= \"".$template->get('popup_profiles_marketdata')."\";");
                eval("\$popup_createbrand = \"".$template->get('popup_createbrand')."\";");
                eval("\$mkintl_section = \"".$template->get('profiles_mktintelsection')."\";");

                // eval("\$profiles_michemfuncproductentry = \"".$template->get('profiles_michemfuncsubstancentry')."\";");
                //  eval("\$profiles_minproductentry = \"".$template->get('profiles_michemfuncproductentry')."\";");
                //get brand related to the customer
                $entity = new Entities($visitreport_data['cid']);


                $brandsproducts = $entity->get_brandsproducts();
                $output = '';
                if(is_array($brandsproducts)) {
                    foreach($brandsproducts as $brandproduct_obj) {
                        $brandproduct_brand = $brandproduct_obj->get_entitybrand();
                        $brandproduct_productype = $brandproduct_obj->get_endproduct();
                        $options[$brandproduct_obj->ebpid] = $brandproduct_brand->name;
                        if(is_object($brandproduct_productype)) {
                            $options[$brandproduct_obj->ebpid] .= ' - '.$brandproduct_productype->title;
                        }

                        eval("\$brandsendproducts .= \"".$template->get('profiles_entityprofile_brandsproducts')."\";");
                    }
                    $entitiesbrandsproducts_list = parse_selectlist('marketdata[ebpid]', 7, $options, '');
                }
                //Adding end-product type for the add brand box
                $endproducttypes = EndProducTypes::get_endproductypes();
                if(is_array($endproducttypes)) {
                    foreach($endproducttypes as $endproducttype) {
                        $value = $endproducttype->title;
                        $pplication = $endproducttype->get_application()->parse_link();
                        if($pplication !== null) {
                            $value .=' - '.$pplication;
                        }
                        $parent = $endproducttype->get_endproducttype_chain();
                        if(!empty($parent)) {
                            $values[$endproducttype->eptid] = $parent.' > '.$value;
                        }
                        else {
                            $values[$endproducttype->eptid] = $value;
                        }
                    }
                    asort($values);
                    foreach($values as $key => $value) {
                        $checked = $rowclass = '';
                        $endproducttypes_list .= ' <tr class="'.$rowclass.'">';
                        $endproducttypes_list .= '<td><input id="producttypefilter_check_'.$key.'" type="checkbox"'.$checked.' value="'.$key.'"  name="entitybrand[endproducttypes]['.$key.'][eptid]">'.$value.'<input style="float:right;" type="text" name="entitybrand[endproducttypes]['.$key.'][description]" placeholder="'.$lang->description.'"  value="'.$brandproduct[description].'"/></td><tr>';
                    }
                }
                $characteristics = ProductCharacteristicValues::get_data(null, array('returnarray' => true));
                $characteristics_list = parse_selectlist('entitybrand[pcvid]', 4, $characteristics, null, 0, null, array('blankstart' => true));
                eval("\$popup_createbrand = \"".$template->get('popup_createbrand')."\";");
                eval("\$popup_marketdata = \"".$template->get('popup_profiles_marketdata')."\";");
            }
            /* Parse MI Data Section - END */
            eval("\$fillreportpage = \"".$template->get('crm_fillvisitreport_visitdetailspage')."\";");
        }
        else {
            if($session->isset_phpsession('visitreportdata_'.$identifier)) {
                $visitreport_values = unserialize($session->get_phpsession('visitreportdata_'.$identifier));
            }
            else {
                $visitreport_values = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."visitreports WHERE identifier='".$identifier."'"));
                if(!isset($visitreport_values['spid'])) {
                    $visitreport_values['spid'][0] = 0;
                }
            }

            if(is_array($visitreport_values) && !empty($visitreport_values)) {
                foreach($visitreport_values as $key => $val) {
                    switch($key) {
                        case 'cid':
                            $company_name = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='".$db->escape_string($val)."'"), "companyName");
                            $visitreport_values['customername'] = $company_name;
                            break;
                        case 'spid':
                            $supplierrownumber = 1;
                            foreach($val as $k => $v) {
                                $company_name = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='".$db->escape_string($v)."'"), "companyName");
                                $visitreport_values['suppliername'][$k] = $company_name;
                                $visitreport_values['spid'][$k] = $v;
                                eval("\$suppliers_fields .= \"".$template->get('crm_fillvisitreport_supplierfield')."\";");
                                $supplierrownumber++;
                            }
                            break;
                        case 'rpid':
                            $visitreport_values['representativename'] = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."representatives WHERE rpid='".$db->escape_string($val)."'"), "name");
                            break;
                        case 'type':
                        case 'purpose':
                        case 'availabilityIssues':
                        case 'supplyStatus':
                            $variable_name = "{$key}_selected";
                            ${$variable_name}[$val] = " selected='selected'";
                            break;
                        case 'date':
                            if(empty($val)) {
                                $val = TIME_NOW;
                            }
                            if((string)intval($val) != $val) {
                                $val = strtotime($val);
                            }
                            $visitreport_values['date_formatted'] = date('d-m-Y', $val);
                            $visitreport_values['date_output'] = date($core->settings['dateformat'], $val);
                            break;
                    }
                }
                $productLine_selected = $visitreport_values['productLine'];
            }
            $visitreport_values['competition'] = unserialize($session->get_phpsession("visitreportdata_{$identifier}_competition"));

            /* $supplyStatus_selected[$visitreport_values['supplyStatus']] = " selected='selected'";
              $availabilityIssues_selected[$visitreport_values['availabilityIssues']] = " selected='selected'";
              $purpose_selected[$visitreport_values['purpose']] = " selected='selected'";
              $type_selected[$visitreport_values['type']] = " selected='selected'"; */
        }
    }
    else {
        $timenow = TIME_NOW;
        $supplierrownumber = 1;

        $k = 1;
        $visitreport_values['spid'][$k] = 0;

        eval("\$suppliers_fields = \"".$template->get('crm_fillvisitreport_supplierfield')."\";");
    }

    if($core->input['stage'] == 'visitdetails') {

    }
    elseif($core->input['stage'] == 'competition') {
        if(strpos(strtolower($_SERVER['HTTP_REFERER']), 'stage=visitdetails') !== false) {
            $session->set_phpsession(array('visitreportvisitdetailsdata_'.$identifier => serialize($core->input)));
        }

        $visitreport_data = unserialize($session->get_phpsession('visitreportdata_'.$identifier));
        $competition = unserialize($session->get_phpsession('visitreportcompetitiondata_'.$identifier));

        if(is_array($visitreport_data['spid'])) {
            foreach($visitreport_data['spid'] as $key => $val) {
                if(empty($val) && $val != 0 || (count($visitreport_data['spid']) > 1 && $val == 0)) {
                    unset($visitreport_data['spid'][$key]);
                    continue;
                }
                $competition['comments'][$val]['suppliername'] = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='".$db->escape_string($val)."'"), 'companyName');
                if(empty($competition['comments'][$val]['suppliername'])) {
                    $competition['comments'][$val]['suppliername'] = $lang->unspecified;
                }

                foreach($competition['comments'][$val] as $k => $v) {
                    $competition['comments'][$val][$k] = $core->sanitize_inputs($v, array('method' => 'striponly', 'removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                }

                eval("\$competition_fields .= \"".$template->get('crm_visitreport_competitionpage_fields')."\";");
            }
        }

        eval("\$fillreportpage = \"".$template->get('crm_fillvisitreport_competitionpage')."\";");
    }
    else {
        $affiliates_attributes = array('affid', 'name');
        $affiliates_order = array(
                'by' => 'name',
                'sort' => 'ASC'
        );
        if($core->usergroup['canViewAllAff'] == 0) {
            $inaffiliates = implode(',', $core->user['affiliates']);
            $affiliate_where = 'affid IN ('.$inaffiliates.')';
        }
        $affiliates = get_specificdata('affiliates', $affiliates_attributes, 'affid', 'name', $affiliates_order, 0, $affiliate_where);
        $affiliates_list = parse_selectlist("affid", 2, $affiliates, $visitreport_values['affid']);

        $productline_attributes = array('psid', 'title');
        $productline_order = array(
                'by' => 'title',
                'sort' => 'ASC'
        );

        $productlines_query = $db->query("SELECT ps.psid, title FROM ".Tprefix."productsegments ps JOIN ".Tprefix."employeessegments es ON (es.psid=ps.psid) WHERE es.uid={$core->user[uid]}");
        if($db->num_rows($productlines_query) > 0) {
            while($productline = $db->fetch_assoc($productlines_query)) {
                $productlines[$productline['psid']] = $productline['title'];
            }
        }
        else {
            error($lang->notassignedtosegments);
        }

        $productline_list = parse_selectlist('productLine[]', 3, $productlines, $productLine_selected, 1, '', array('required' => 'required'));

        /* Parse draft reports select list - START */
        $query = $db->query('SELECT vr.identifier, vr.date, companyName AS customerName
				FROM '.Tprefix.'visitreports vr
				JOIN '.Tprefix.'entities c ON (c.eid=vr.cid)
				WHERE uid='.intval($core->user['uid']).' AND isDraft=1
				ORDER BY date ASC');

        if($db->num_rows($query) > 0) {
            $draftreports[0] = '';
            while($draftreport = $db->fetch_assoc($query)) {
                $draftreports[$draftreport['identifier']] = $draftreport['customerName'].' - '.date($core->settings['dateformat'], $draftreport['date']);
            }
            $draftreports_selectlist = '<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p>'.$lang->continuefilling.': '.parse_selectlist('identifier', 1, $draftreports, '', 0, 'goToURL("index.php?module=crm/fillvisitreport&amp;identifier="+$(this).val())').'</p></div>';
        }
        $display = "  display: none;";
        if(!empty($visitreport_values['location'])) {
            $display = " display: block;";

            $locations = EntityLocations::get_data(array('eid' => $visitreport_values['cid']), array('simple' => false, 'returnarray' => true));
            $customerlocation = parse_selectlist('location', 3, $locations, $visitreport_values['location'], 6, '');
        }
        /* Parse draft reports select list - END */
        eval("\$fillreportpage = \"".$template->get('crm_fillvisitreport')."\";");
    }

    output_page($fillreportpage);
}
else {
    if($core->input['action'] == 'do_add_fillvisitreport') {
        $identifier = $db->escape_string($core->input['identifier']);
        $visitreport = unserialize($session->get_phpsession('visitreportdata_'.($identifier)));
        //$competition = unserialize($session->get_phpsession('visitreportdata_'.$db->escape_string($core->input['identifier']).'_competition'));
        $visitdetails = unserialize($session->get_phpsession('visitreportvisitdetailsdata_'.($identifier)));
        $competition = unserialize($session->get_phpsession('visitreportcompetitiondata_'.($identifier)));

        if(is_empty($visitreport['cid'], $visitreport['spid'], $visitreport['rpid'], $visitreport['productLine'])) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }

        if(is_array($visitdetails['comments'])) {
            foreach($visitdetails['comments'] as $key => $val) {
                if(is_array($competition['comments'][$key])) {
                    $visitdetails['comments'][$key] = array_merge($visitdetails['comments'][$key], $competition['comments'][$key]);
                }
            }
        }

        $fields_array = $db->show_fields_from('visitreports');
        foreach($fields_array as $field) {
            if(isset($visitreport[$field['Field']])) {
                $visitreport_main[$field['Field']] = $visitreport[$field['Field']];
            }
        }

        $visitreport_main['uid'] = $core->user['uid'];

        if(!empty($visitreport['date'])) {
            $visitreportdate = explode('-', $visitreport['date']);
            $visitreport_main['date'] = mktime(0, 0, 0, $visitreportdate[1], $visitreportdate[0], $visitreportdate[2]);
        }
        else {
            $visitreport_main['date'] = TIME_NOW;
        }

        $visitreport_main['isLocked'] = 1;
        $visitreport_main['isDraft'] = 0;
        $visitreport_main['finishDate'] = TIME_NOW;

        if(count($visitreport['spid']) <= 1) {
            if($visitreport['spid'][0] == 0) {
                $visitreport_main['hasSupplier'] = 0;
            }
        }
        $existing_report = $db->fetch_assoc($db->query('SELECT vrid, identifier FROM '.Tprefix.'visitreports WHERE identifier="'.$db->escape_string($visitreport['identifier']).'"'));
        if(!empty($existing_report)) {
            $is_new = false;
            $query = $db->update_query('visitreports', $visitreport_main, 'vrid='.$existing_report['vrid']);
        }
        else {
            $is_new = true;
            $query = $db->insert_query('visitreports', $visitreport_main);
        }

        if($query) {
            if($is_new == false) {
                $vrid = $existing_report['vrid'];
            }
            else {
                $vrid = $db->last_id();
            }
            /* update the visit report id in the  market data based on the temporary videntifer */
            $db->update_query('marketintelligence_basicdata', array('vrid' => $vrid, 'vridentifier' => null), 'vridentifier ="'.$identifier.'" ');

            if(is_array($visitreport['productLine'])) {
                if($is_new == false) {
                    $db->delete_query('visitreports_productlines', 'vrid='.$vrid);
                }
                foreach($visitreport['productLine'] as $key => $val) {
                    $db->insert_query('visitreports_productlines', array('vrid' => $vrid, 'productLine' => $val));
                }
            }

            if(is_array($visitreport['spid'])) {
                $comments_fields_array = $db->show_fields_from('visitreports_comments');
                if($is_new == false) {
                    $db->delete_query('visitreports_reportsuppliers', 'vrid='.$vrid);
                    $db->delete_query('visitreports_comments', 'vrid='.$vrid);
                }

                foreach($visitreport['spid'] as $key => $val) {
                    $visitreport_supplier['spid'] = $val;
                    $visitreport_supplier['vrid'] = $vrid;
                    if(!empty($visitreport['sprid'])) {
                        $visitreport_supplier['vrid'] = $visitreport['sprid'];
                    }
                    $db->insert_query('visitreports_reportsuppliers', $visitreport_supplier);

                    if($visitdetails['comments'][$val]['markTask'] == 1) {
                        $new_task['markTask'] = $visitdetails['comments'][$val]['markTask'];
                        $new_task['dueDate'] = $visitdetails['comments'][$val]['taskDate'];
                        $new_task['suppliername'] = $visitdetails['comments'][$val]['suppliername'];
                    }

                    foreach($comments_fields_array as $field) {
                        if(isset($visitdetails['comments'][$val][$field['Field']])) {
                            $visitreport_comments[$field['Field']] = $visitdetails['comments'][$val][$field['Field']];
                            $visitreport_comments[$field['Field']] = $core->sanitize_inputs($visitreport_comments[$field['Field']], array('method' => 'striponly', 'removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                        }
                    }

                    if(is_array($visitreport_comments)) {
                        $visitreport_comments['vrid'] = $vrid;
                        $visitreport_comments['spid'] = $val;
                        $db->insert_query('visitreports_comments', $visitreport_comments);

                        /* Create follow up task - START */
                        if($new_task['markTask'] == 1 && !empty($visitreport_comments['followUp'])) {
                            $customer_name = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='".$visitreport['cid']."'"), 'companyName');

                            $new_task = array(
                                    'uid' => $core->user['uid'],
                                    'subject' => $lang->visitfollowup.': '.$customer_name.'/'.$new_task['suppliername'],
                                    'priority' => 1,
                                    'dueDate' => $new_task['dueDate'],
                                    'description' => $visitreport_comments['followUp'],
                                    'reminderInterval' => 604800, //Every 2 days
                                    'reminderStart' => $new_task['dueDate'],
                                    'createdBy' => $core->user['uid']
                            );

                            $task = new Tasks();
                            $task->create_task($new_task);
                        }
                        /* Create follow up task - END */
                    }
                }
            }

            $log->record($vrid);

            $session->destroy_phpsession();

            output_xml("<status>true</status><message>{$lang->visitreportfinalized}<![CDATA[<script>goToUrl(\''.$core->settings['rootdir'].'/index.php?module=crm/listvisitreports\')</script>]]></message>");
        }
    }
    elseif($core->input['action'] == 'get_addnew_representative' || $core->input['action'] == 'get_addnew_supprepresentative') {
        if($core->input['action'] == 'get_addnew_supprepresentative') {
            eval("\$entity_field_row = \"".$template->get('popup_addrepresentative_supplierfield')."\";");
        }
        else {
            eval("\$entity_field_row = \"".$template->get('popup_addrepresentative_customerfield')."\";");
        }
        eval("\$addrepresentativebox = \"".$template->get('popup_addrepresentative')."\";");
        echo $addrepresentativebox;
    }
    elseif($core->input['action'] == 'do_add_representative') {
        $representative = new Entities($core->input, 'add_representative');

        if($representative->get_status() === true) {
            output_xml("<status>true</status><message>{$lang->representativecreated}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorcreatingreprentative}</message>");
        }
    }
    elseif($core->input['action'] == 'autosave') {
        //print_r($core->input);
        //$comments_fields_array = $db->show_fields_from('visitreports_comments');

        /* 		foreach($comments_fields_array as $field) {
          if(isset($core->input['comments'][$val][$field['Field']])) {
          $visitreport_comments[$field['Field']] = $visitdetails['comments'][$val][$field['Field']];
          $visitreport_comments[$field['Field']] = $core->sanitize_inputs($visitreport_comments[$field['Field']], array('method'=> 'striponly', 'removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
          }
          } */
    }
    elseif($core->input['action'] == 'parsemitimeline') {
        $visitreport = unserialize($session->get_phpsession('visitreportdata_'.$db->escape_string($core->input['identifier'])));
        $visitdetails = unserialize($session->get_phpsession('visitreportvisitdetailsdata_'.$db->escape_string($core->input['identifier'])));
        /* Add Market Inteligence Data --START */
        if($core->usergroup['profiles_canAddMkIntlData'] == 1) {
            $lang->load('profiles_meta');
            $module = 'profiles';
            $elemtentid = $visitreport['cid'];
            $elementname = 'marketdata[cid]';
            $action = 'do_addmartkerdata';
            $modulefile = 'entityprofile';
            eval("\$profiles_michemfuncproductentry = \"".$template->get('profiles_michemfuncsubstancentry')."\";");

            /* View detailed market intelligence box --START */
            $maktintl_mainobj = new MarketIntelligence();
            $miprofile = $maktintl_mainobj->get_miprofconfig_byname('latestcustomersumbyproduct');
            $miprofile['next_miprofile'] = 'allprevious';

            $maktintl_objs = $maktintl_mainobj->get_marketintelligence_timeline(array('cid' => $elemtentid), $miprofile);

            if(is_array($maktintl_objs)) {
                foreach($maktintl_objs as $mktintldata) {
                    $mktintldata['tlidentifier']['id'] = 'tlrelation-'.$elemtentid;
                    $mktintldata['tlidentifier']['value'] = array('cid' => $elemtentid);
                    $core->input[module] = 'profiles/entityprofile';  /* overwrite core input module name to pass the same module name  to the popup template  */

                    $detailmarketbox .= $maktintl_mainobj->parse_timeline_entry($mktintldata, $miprofile, '', '');
                }
                $latest_mkdataid = max($maktintl_objs)['mibdid'];

                eval("\$visitdetails_fields_mktidata = \"".$template->get('crm_fillvisitreport_visitdetailspage_marketdata')."\";");
            }

            /* View detailed market intelligence box --END */
            output($visitdetails_fields_mktidata);
        }
        /* Add Market Inteligence Data --END */
    }
    elseif($core->input['action'] == 'get_customerlocation') {
        //$cid = $db->escape_string($core->input['cid']);
        $locations = EntityLocations::get_data(array('eid' => $core->input['cid']), array('simple' => false, 'returnarray' => true));
        if(is_array($locations)) {
            $entity_locations = '<td>'.$lang->chooselocation.'</td>';
            $entity_locations .='<td>'.parse_selectlist('location', 3, $locations, '', 6, '', array('blankstart' => 1)).'</td>';
            output($entity_locations);
        }
    }
    elseif($core->input['action'] == 'ajaxaddmore_profmkdchemical') {
        $mkdchem_rowid = $db->escape_string($core->input['value']) + 1;
        eval("\$profiles_michemfuncproductentry_rows = \"".$template->get('profiles_michemfuncsubstancentry')."\";");
        echo $profiles_michemfuncproductentry_rows;
    }
    elseif($core->input['action'] == 'ajaxaddmore_profmkdbasicing') {
        $mkdbing_rowid = $db->escape_string($core->input['value']) + 1;
        eval("\$profiles_mibasicingredientsentry_rows = \"".$template->get('profiles_mibasicingredientsentry')."\";");
        echo $profiles_mibasicingredientsentry_rows;
    }
    elseif($core->input['action'] == 'ajaxaddmore_profmkdproduct') {
        $mkdprod_rowid = $db->escape_string($core->input['value']) + 1;
        eval("\$profiles_minproductentry_rows = \"".$template->get('profiles_michemfuncproductentry')."\";");
        echo $profiles_minproductentry_rows;
    }
}
?>