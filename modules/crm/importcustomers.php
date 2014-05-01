<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2010 Orkila International Offshore, All Rights Reserved
 * Import Customers
 * $module: CRM
 * Created		@zaher.reda 		November 12, 2010 | 10:53 AM
 * Last Update: 	@najwa.kassem 		June 20, 2011 | 04:20 AM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

/* if($core->usergroup['crm_canImportCustomers'] == 0) {
  error($lang->sectionnopermission);
  } */

$session->start_phpsession();

if(!$core->input['action']) {
    eval("\$importpage = \"".$template->get('crm_importcustomers')."\";");
    output_page($importpage);
}
else {
    if($core->input['action'] == 'preview') {
        $uploadcustomers = new Uploader('uploadcustomers', $_FILES, array('application/csv', 'application/excel', 'application/x-excel', 'text/csv', 'text/comma-separated-values', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'), 'readonly', 5242880, 0, 1);
        $uploadcustomers->process_file();
        $filedata = $uploadcustomers->get_filedata();

        $csv_file = new CSV($filedata, 2, true, $core->input['delimiter']);
        $csv_file->readdata_string();
        $data['customer'] = $csv_file->get_data();
        $csv_header['customer'] = $csv_file->get_header();

        if(!empty($_FILES['uploadrepresentatives']['name'])) {
            $uploadrepresentatives = new Uploader('uploadrepresentatives', $_FILES, array('application/csv', 'application/excel', 'application/x-excel', 'text/csv', 'text/comma-separated-values', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'), 'readonly', 5242880, 0, 1);
            $uploadrepresentatives->process_file();
            $filedata_representative = $uploadrepresentatives->get_filedata();

            $csv_file_representative = new CSV($filedata_representative, 2, true, $core->input['delimiter']);
            $csv_file_representative->readdata_string();
            $data['representative'] = $csv_file_representative->get_data();
            $csv_header['representative'] = $csv_file_representative->get_header();
        }

        eval("\$headerinc = \"".$template->get('headerinc')."\";");
        echo $headerinc;
        ?>
        <script language="javascript" type="text/javascript">
            $(function()
            {
                return window.top.$("#upload_Result").html("<?php echo addslashes(parse_datapreview($csv_header, $data));?>");
            });
        </script>   
        <?php
    }
    elseif($core->input['action'] == 'do_perform_importcustomers') {
        $headers_cache_representative = $cache['countries'] = $cache['cities'] = $cache['affiliates'] = $cache['segments'] = $cache['position'] = array();
        $all_data = unserialize($session->get_phpsession('crmimportcustomers_'.$core->input['identifier']));

        /* Check Representatives - START */
        $allowed_headers_representative = array('name' => 'name', 'email' => 'email', 'entity' => 'entity', 'phone' => 'phone', 'position' => 'position', 'segment' => 'segment'); //Make language; CLEAR NAMES
        $required_headers_check_representative = $required_headers_representative = array('name', 'email', 'entity', 'phone', 'position', 'segment');
        $alllowercase_representative = array('name', 'email', 'entity', 'phone', 'position', 'segment');

        for($i = 0; $i < count($allowed_headers_representative); $i++) {
            if(in_array($core->input['representativeselectheader_'.$i], $headers_cache_representative)) {
                $errors['representative'][] = $lang->errorheader.$lang->fieldrepeated;
                unset($all_data['representative']);
            }
            else {
                if(in_array($core->input['representativeselectheader_'.$i], $required_headers_check_representative)) {
                    unset($required_headers_check_representative[array_search($core->input['representativeselectheader_'.$i], $required_headers_check_representative)]);
                }
                $headers_cache_representative[] = $core->input['representativeselectheader_'.$i];
            }
        }

        if(count($required_headers_check_representative) > 0) {
            $errors['representative'][] = $lang->errorheader.$lang->fillallrequiredfields;
            unset($all_data['representative']);
        }
        unset($headers_cache_representative);

        if(is_array($all_data['representative'])) {
            foreach($all_data['representative'] as $key => $row) {
                $count_input = 0;
                foreach($row as $header_representative => $value) {
                    if(in_array($header_representative, $required_headers_representative) && empty($value)) {
                        $errors['representative'][] = $lang->ignoredcustomers.$lang->row.$key;
                        break;
                    }

                    $data_row_representative[$key][$core->input['representativeselectheader_'.$count_input]] = $db->escape_string(utf8_encode(trim(strtolower($value))));

                    if(!in_array($core->input['representativeselectheader_'.$count_input], $alllowercase_representative)) {
                        $data_row_representative[$key][$core->input['representativeselectheader_'.$count_input]] = ucfirst($data_row_representative[$key][$core->input['representativeselectheader_'.$count_input]]);
                    }
                    $count_input++;
                }

                if($core->validate_email($core->sanitize_email($data_row_representative[$key]['email']))) {
                    $data_row_representative[$key]['email'] = $core->sanitize_email($data_row_representative[$key]['email']);
                }
                else {
                    $errors['representative'][] = $lang->mailnotvalid.$data_row_representative[$key]['name'];
                    unset($data_row[$key]);
                }

                if(empty($data_row_representative[$key]['entity'])) {
                    $errors['representative'][] = $lang->noentitymentioned.$data_row_representative[$key]['name'];
                    unset($data_row_representative[$key]);
                }
                else {
                    $data_row_representative[$key]['entity'] = trim(strtolower($data_row_representative[$key]['entity']));
                }


                if(isset($data_row_representative[$key]['position'])) {
                    $positions_array = explode($core->input['multivalueseperator'], $data_row_representative[$key]['position']);
                    foreach($positions_array as $key2 => $val) {
                        if(!in_array($val, $cache['position'])) {
                            $posid = $db->fetch_field($db->query("SELECT posid FROM ".Tprefix."positions WHERE name='".str_replace(' ', '', strtolower($val))."'"), 'posid');
                            if(!empty($posid)) {
                                $cache['position'][$posid] = $val;
                                $representative_positions[$key][] = $posid;
                            }
                            else {
                                $errors['representative'][] = $lang->ignoredpositions.$data_row_representative[$key]['position'];
                            }
                        }
                        else {
                            $representative_positions[$key][] = array_search($val, $cache['position']);
                        }
                    }
                    unset($data_row_representative[$key]['position']);
                }
            }
        }
        /* Check Representatives - END */

        /* Check Customers - START */
        $allowed_headers = array('companyName' => 'Company Name', 'country' => 'Country', 'city' => 'City', 'addressLine1' => 'addressLine1', 'addressLine2' => 'Address Line 2', 'building' => 'Building', 'floor' => 'Floor', 'email' => 'Email', 'poBox' => 'PO Box', 'postCode' => 'Post Code', 'phone1' => 'Phone 1', 'phone2' => 'Phone 2', 'fax1' => 'Fax 1', 'fax2' => 'Fax 2', 'segments' => 'Segments', 'affiliates' => 'Affiliates'); //Make language; CLEAR NAMES
        $required_headers_check = $required_headers = array('companyName');
        $alllowercase = array('email');

        $headers_cache = array();

        for($i = 0; $i < count($allowed_headers); $i++) {
            if(empty($core->input['selectheader_'.$i])) {
                continue;
            }
            if(in_array($core->input['selectheader_'.$i], $headers_cache)) {
                output_xml("<status>false</status><message>".$core->input['selectheader_'.$i]."{$lang->fieldrepeated}</message>");
                exit;
            }
            else {
                if(in_array($core->input['selectheader_'.$i], $required_headers_check)) {
                    unset($required_headers_check[array_search($core->input['selectheader_'.$i], $required_headers_check)]);
                }
                $headers_cache[] = $core->input['selectheader_'.$i];
            }
        }
        print_r($headers_cache);
        exit;
        if(count($required_headers_check) > 0) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }
        unset($headers_cache);



        $diff = $all_data['representative'];

        foreach($all_data['customer'] as $key => $row) {
            $count_input = 0;
            if(is_array($row['representative'])) {
                foreach($row['representative'] as $ind1 => $val1) {
                    foreach($diff as $ind2 => $val2) {
                        if($ind1 == $ind2) {
                            unset($diff[$ind2]);
                        }
                    }
                }
            }

            if(is_array($diff)) {
                foreach($diff as $ind2 => $val2) {
                    $errors['representative'][] = $lang->entitynotrecognized.$val2['name'];
                }
            }

            foreach($row as $header => $value) {
                if(in_array($header, $required_headers) && empty($value)) {
                    $errors['customer'][] = $lang->ignoredcustomers.$key;
                    break;
                }
                if(empty($value)) {
                    $errors['customer'][] = $lang->ignoredcustomers.$row['companyName'];
                    break;
                }
                if(!empty($core->input['selectheader_'.$count_input])) {
                    $data_row[$key][$core->input['selectheader_'.$count_input]] = $db->escape_string(utf8_encode(trim(strtolower($value))));

                    if(!in_array($core->input['selectheader_'.$count_input], $alllowercase)) {
                        $data_row[$key][$core->input['selectheader_'.$count_input]] = ucfirst($data_row[$key][$core->input['selectheader_'.$count_input]]);
                    }
                }
                $count_input++;
            }

            $existing_eid = $db->fetch_field($db->query("SELECT eid FROM ".Tprefix."entities WHERE companyName='{$data_row[$key][companyName]}'"), 'eid');
            if(!empty($existing_eid)) {
                $action_required = 'update';
                $data_row[$key]['eid'] = $existing_eid;
            }
            else {
                $query = $db->query("SELECT eid FROM ".Tprefix."entities WHERE SOUNDEX(companyName) = SOUNDEX('{$data_row[$key][companyName]}')");
                if($db->num_rows($query) > 0) {
                    $errors['customer'][] = $lang->customeralreadyexists.$data_row[$key]['companyName'];
                    continue;
                }

                $action_required = 'create';
            }
            if(isset($data_row[$key]['email'])) {
                if($core->validate_email($core->sanitize_email($data_row[$key]['email']))) {
                    $data_row[$key]['email'] = $core->sanitize_email($data_row[$key]['email']);
                }
                else {
                    $errors['customer'][] = $lang->mailnotvalid.$data_row[$key]['name'];
                    unset($data_row[$key]['email']);
                }
            }
            if(isset($data_row[$key]['country'])) {
                if(!in_array($data_row[$key]['country'], $cache['countries'])) {
                    $coid = $db->fetch_field($db->query("SELECT coid FROM ".Tprefix."countries WHERE name='{$data_row[$key][country]}'"), 'coid');
                    if(!empty($coid)) {
                        $cache['countries'][$coid] = $data_row[$key]['country'];
                        $data_row[$key]['country'] = $coid;
                    }
                    else {
                        $errors['customer'][] = $lang->ignoredcountry.$data_row[$key]['country'];
                        unset($data_row[$key]['country']);
                    }
                }
                else {
                    $data_row[$key]['country'] = array_search($data_row[$key]['country'], $cache['countries']);
                }
            }
            if(isset($data_row[$key]['city'])) {
                if(!in_array($data_row[$key]['city'], $cache['cities'])) {
                    $ciid = $db->fetch_field($db->query("SELECT ciid FROM ".Tprefix."cities WHERE LOWER(name)='".trim(strtolower($data_row[$key]['city']))."'"), 'ciid');
                    if(!empty($ciid)) {
                        $cache['cities'][$ciid] = $data_row[$key]['city'];
                        $data_row[$key]['city'] = $ciid;
                    }
                    else {
                        $errors['customer'][] = $lang->ignoredcity.$data_row[$key]['city'];
                        unset($data_row[$key]['city']);
                    }
                }
                else {
                    $data_row[$key]['city'] = array_search($data_row[$key]['city'], $cache['cities']);
                }
            }
            if(strstr($data_row[$key]['segments'], $core->input['multivalueseperator'])) {
                $data_row[$key]['segments'] = explode($core->input['multivalueseperator'], $data_row[$key]['segments']);
            }


            if(is_array($data_row[$key]['segments'])) {
                foreach($data_row[$key]['segments'] as $key => $seg) {
                    if(in_array($seg, $cache['segments'])) {
                        $segments[$key][array_search($seg, $cache['segments'])] = array_search($seg, $cache['segments']);
                        unset($data_row[$key]['segments']);
                    }
                }
                if(is_array($data_row[$key]['segments'])) {
                    $query = $db->query("SELECT psid, title FROM ".Tprefix."productsegments WHERE title IN ('".implode('\',\'', $data_row[$key]['segments'])."')");
                    if($db->num_rows($query) > 0) {
                        $segments = array();
                        while($segment = $db->fetch_assoc($query)) {
                            $segments[$key][$segment['psid']] = $segment['psid'];
                            $cache['segments'][$segment['psid']] = $segment['title'];
                        }
                    }
                    else {
                        $errors['customer'][] = $lang->ignoredsegments.implode('<br />', $data_row[$key]['segments']);
                    }
                }
            }
            else {
                if(!in_array($data_row[$key]['segments'], $cache['segments'])) {
                    $query = $db->query("SELECT psid FROM ".Tprefix."productsegments WHERE title = '{$data_row[$key][segments]}'");
                    if($db->num_rows($query) > 0) {
                        $segment = $db->fetch_assoc($query);
                        $segments[$key][$segment['psid']] = $segment['psid'];
                        $cache['segments'][$segment['psid']] = $data_row[$key]['segments'];
                    }
                    else {
                        $errors['customer'][] = $lang->ignoredsegments.$data_row[$key]['segments'];
                    }
                }
                else {
                    $psid = array_search($data_row[$key]['segments'], $cache['segments']);
                    $segments[$key][$psid] = $psid;
                }
            }


            if(strstr($data_row[$key]['affiliates'], $core->input['multivalueseperator'])) {
                $data_row[$key]['affiliates'] = explode($core->input['multivalueseperator'], $data_row[$key]['affiliates']);
            }

            if(is_array($data_row[$key]['affiliates'])) {
                foreach($data_row[$key]['affiliates'] as $aff) {
                    if(in_array($aff, $cache['affiliates'])) {
                        $affiliates[$key][array_search($aff, $cache['affiliates'])] = array_search($aff, $cache['affiliates']);
                        unset($data_row[$key]['affiliates']);
                    }
                }
                if(is_array($data_row[$key]['affiliates'])) {
                    $query = $db->query("SELECT affid, name FROM ".Tprefix."affiliates WHERE name IN ('".implode('\',\'', $data_row[$key]['affiliates'])."')");
                    if($db->num_rows($query) > 0) {
                        $affiliates = array();
                        while($affiliate = $db->fetch_assoc($query)) {
                            $affiliates[$key][$affiliate['affid']] = $affiliate['affid'];
                            $cache['affiliates'][$affiliate['affid']] = $affiliate['name'];
                        }
                    }
                    else {
                        $errors['customer'][] = $lang->ignoredaffiliates.implode('<br />', $data_row[$key]['affiliates']);
                    }
                }
            }
            else {
                if(!in_array($data_row[$key]['affiliates'], $cache['affiliates'])) {
                    $query = $db->query("SELECT affid FROM ".Tprefix."affiliates WHERE LOWER(name) ='".trim(strtolower($data_row[$key]['affiliates']))."'");
                    if($db->num_rows($query) > 0) {
                        $affiliate = $db->fetch_assoc($query);
                        $cache['affiliates'][$affiliate['affid']] = $data_row[$key]['affiliates'];
                        $affiliates[$key][$affiliate['affid']] = $affiliate['affid'];
                    }
                    else {
                        $errors['customer'][] = $lang->ignoredaffiliates.$data_row[$key]['affiliates'];
                    }
                }
                else {
                    $affid = array_search($data_row[$key]['affiliates'], $cache['affiliates']);
                    $affiliates[$key][$affid] = $affid;
                }
            }

            unset($data_row[$key]['segments'], $data_row[$key]['affiliates']);


            if($action_required == 'create') {

                $data_row[$key]['type'] = 'c';
                $data_row[$key]['approved'] = 1;
                $data_row[$key]['dateAdded'] = TIME_NOW;

                $query = $db->insert_query('entities', $data_row[$key]);
                if($query) {

                    $eid = $db->last_id();
                    if(is_array($segments[$key])) {
                        foreach($segments[$key] as $val) {
                            $db->insert_query('entitiessegments', array('eid' => $eid, 'psid' => $val));
                        }
                    }
                    if(is_array($affiliates[$key])) {
                        foreach($affiliates[$key] as $val) {
                            $db->insert_query('affiliatedentities', array('eid' => $eid, 'affid' => $val));
                        }
                    }
                    if(isset($all_data['customer'][$key]['representative'])) {
                        foreach($all_data['customer'][$key]['representative'] as $key => $representative) {
                            if(isset($representative['segment'])) {
                                $segments_array = explode($core->input['multivalueseperator'], $representative['segment']);
                                foreach($segments_array as $key2 => $val) {
                                    if(!in_array($val, $cache['segments'])) {
                                        $psid = $db->fetch_field($db->query("SELECT psid FROM ".Tprefix."productsegments WHERE LOWER(title)='".trim(strtolower($val))."'"), 'psid');
                                        if(!empty($psid)) {
                                            $cache['segments'][$psid] = $val;
                                            $representative_segments[$key][] = $psid;
                                        }
                                        else {
                                            $errors['representative'][] = $lang->ignoredsegments.$representative['segment'];
                                        }
                                    }
                                    else {
                                        $representative_segments[$key][] = array_search($val, $cache['segments']);
                                    }
                                }
                                unset($data_row_representative[$key]['segments']);
                            }

                            unset($representative['entity'], $representative['position'], $representative['segment']);

                            $query2 = $db->insert_query('representatives', $representative);
                            $rpid = $db->last_id();
                            if($query2) {

                                $db->insert_query('entitiesrepresentatives', array('rpid' => $rpid, 'eid' => $eid));

                                if(isset($representative_positions[$key])) {
                                    foreach($representative_positions[$key] as $key2 => $posid) {
                                        $position_query = $db->insert_query('representativespositions', array('rpid' => $rpid, 'posid' => $posid));
                                    }
                                }

                                if(isset($representative_segments[$key])) {
                                    foreach($representative_segments[$key] as $key2 => $psid) {
                                        $segment_query = $db->insert_query('representativessegments', array('rpid' => $rpid, 'psid' => $psid));
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else {
                $query = $db->update_query('entities', $data_row[$key], 'eid="'.$data_row[$key]['eid'].'"');

                if($query) {
                    if(is_array($segments[$key])) {
                        $db->delete_query('entitiessegments', 'eid="'.$data_row[$key]['eid'].'"');
                        foreach($segments[$key] as $val) {
                            $db->insert_query('entitiessegments', array('eid' => $data_row[$key]['eid'], 'psid' => $val));
                        }
                    }

                    if(is_array($affiliates[$key])) {
                        $db->delete_query('affiliatedentities', 'eid="'.$data_row[$key]['eid'].'"');
                        foreach($affiliates[$key] as $val) {
                            $db->insert_query('affiliatedentities', array('eid' => $data_row[$key]['eid'], 'affid' => $val));
                        }
                    }

                    if(isset($all_data['customer'][$key]['representative'])) {
                        $rpidquery = $db->query("SELECT rpid FROM ".Tprefix."entitiesrepresentatives WHERE eid='".$data_row[$key]['eid']."'");
                        $db->delete_query('entitiesrepresentatives', 'eid="'.$data_row[$key]['eid'].'"');
                        while($rpid = $db->fetch_assoc($rpidquery)) {
                            $db->delete_query('representativespositions', 'rpid="'.$rpid['rpid'].'"');
                            $db->delete_query('representativessegments', 'rpid="'.$rpid['rpid'].'"');
                            $db->delete_query('representatives', 'rpid="'.$rpid['rpid'].'"');
                        }

                        $eid = $data_row[$key]['eid'];
                        foreach($all_data['customer'][$key]['representative'] as $key => $representative) {
                            if(isset($representative['segment'])) {
                                $segments_array = explode($core->input['multivalueseperator'], $representative['segment']);
                                foreach($segments_array as $key2 => $val) {
                                    if(!in_array($val, $cache['segments'])) {
                                        $psid = $db->fetch_field($db->query("SELECT psid FROM ".Tprefix."productsegments WHERE LOWER(title)='".trim(strtolower($val))."'"), 'psid');
                                        if(!empty($psid)) {
                                            $cache['segments'][$psid] = $val;
                                            $representative_segments[$key][] = $psid;
                                        }
                                        else {
                                            $errors['representative'][] = $lang->ignoredsegments.$representative['segment'];
                                        }
                                    }
                                    else {
                                        $representative_segments[$key][] = array_search($val, $cache['segments']);
                                    }
                                }
                                unset($data_row_representative[$key]['segments']);
                            }

                            unset($representative['entity'], $representative['position'], $representative['segment']);

                            $query2 = $db->insert_query('representatives', $representative);
                            $rpid = $db->last_id();
                            if($query2) {

                                $db->insert_query('entitiesrepresentatives', array('rpid' => $rpid, 'eid' => $eid));

                                if(isset($representative_positions[$key])) {
                                    foreach($representative_positions[$key] as $key2 => $posid) {
                                        $position_query = $db->insert_query('representativespositions', array('rpid' => $rpid, 'posid' => $posid));
                                    }
                                }

                                if(isset($representative_segments[$key])) {
                                    foreach($representative_segments[$key] as $key2 => $psid) {
                                        $segment_query = $db->insert_query('representativessegments', array('rpid' => $rpid, 'psid' => $psid));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $log->record();
        /* if(is_array($errors)) { 
          if(is_array($errors['customer'])){
          $importerrors .= '<br /><strong>Customer</strong><ol>';
          foreach($errors['customer'] as $key => $details) {
          $importerrors .= '<li>'.$details.'</li>';
          }
          $importerrors .= '</ol>';
          }

          if(is_array($errors['representative'])){
          $importerrors .= '<br /><strong>Representative</strong><ol>';
          foreach($errors['representative'] as $key => $details) {
          $importerrors .= '<li>'.$details.'</li>';
          }
          $importerrors .= '</ol>';
          }

          output_xml("<status>false</status><message>{$lang->resulterror}<![CDATA[{$importerrors}]]></message>");
          }
          else
          {
          output_xml("<status>true</status><message>{$lang->successfullyimported}</message>");
          } */
    }
}
function parse_datapreview($csv_header, $data) {
    global $session, $lang, $core;

    $output = "<span class='subtitle'>{$lang->importpreview}</span><br /><form id='perform_crm/importcustomers_Form'><table class='datatable'><tr><td colspan='16' style='text-align:center'>{$lang->customer}</td><td colspan='6' style='text-align:center'>{$lang->representatives}</td></tr><tr>";
    $cutomer_allowed_headers = array('companyName' => 'Company Name', 'country' => 'Country', 'city' => 'City', 'addressLine1' => 'addressLine1', 'addressLine2' => 'Address Line 2', 'building' => 'Building', 'floor' => 'Floor', 'email' => 'Email', 'poBox' => 'PO Box', 'postCode' => 'Post Code', 'phone1' => 'Phone 1', 'phone2' => 'Phone 2', 'fax1' => 'Fax 1', 'fax2' => 'Fax 2', 'segments' => 'Segments', 'affiliates' => 'Affiliates'); //Make language; CLEAR NAMES
    $representative_allowed_headers = array('name' => 'name', 'email' => 'email', 'entity' => 'entity', 'phone' => 'phone', 'position' => 'position', 'segment' => 'segment');
    $abbreviation = array('Ltd.', 'Ltd', 'Llc.', 'Llc', 'Sal.', 'Co.,', 'Co.', 'Co');

    foreach($csv_header['customer'] as $header_key => $header_val) {
        $output .= '<td style="width:20px;"><select name="selectheader_'.$header_key.'" id="selectheader_'.$header_key.'">';
        $output .= '<option value="">&nbsp;</option>';
        foreach($cutomer_allowed_headers as $allowed_header_key => $allowed_header_val) {
            if($header_val == $allowed_header_key) {
                $selected_header = ' selected="selected"';
            }
            else {
                $selected_header = '';
            }

            $output .= '<option value="'.$allowed_header_key.'"'.$selected_header.'>'.$allowed_header_val.'</option>';
            $selected_header = '';
        }
        $output .= '</select></td>';
    }

    if(isset($csv_header['representative'])) {
        //$output .= '<td><table width="100%"><tr>';

        foreach($csv_header['representative'] as $header_key => $header_val) {
            $output .= '<td style="width:20px;background-color:#66FFFF"><select  style="color:#CC3366" name="representativeselectheader_'.$header_key.'" id="representativeselectheader_'.$header_key.'">';
            $output .= '<option value="">&nbsp;</option>';
            foreach($representative_allowed_headers as $allowed_header_key => $allowed_header_val) {
                if($header_val == $allowed_header_key) {
                    $selected_header = 'selected="selected"';
                }
                else {
                    $selected_header = '';
                }

                $output .= '<option value="'.$allowed_header_key.'"'.$selected_header.' ><b>'.$allowed_header_val.'</b></option>';
                $selected_header = '';
            }
            $output .= '</select></td>';
        }
        //	$output .= '</tr></table></td>';
    }
    if(isset($data['representative'])) {
        foreach($data['representative'] as $repkey => $representative) {
            $representatives[trim(strtolower($representative['entity']))][$repkey] = $representative;
        }
    }
    foreach($data['customer'] as $key => $val) {
        $output .= '<tr>';

        $val['companyName'] = ucwords($val['companyName']);
        $name = explode(' ', $val['companyName']);

        foreach($abbreviation as $abb) {
            $search = array_search($abb, $name);
            if($search)
                $name[$search] = strtoupper($name[$search]);
        }
        $val['companyName'] = implode(' ', $name);

        foreach($val as $value) {
            $output .= '<td valign="top" style="width:20px;">'.utf8_encode($value).'</td>';
        }

        if(isset($representatives[trim(strtolower($val['companyName']))])) {
            $output .= '<td colspan="6" style="padding:0px;width:20px;" valign="top"><table width="100%" style="padding:0px; margin:0px;">';
            foreach($representatives[trim(strtolower($val['companyName']))] as $repkey => $repname) {
                $data['customer'][$key]['representative'][$repkey] = $repname;

                $row_class = alt_row(2);
                $output .= "<tr class='{$row_class}'>";
                //$output .= utf8_encode($repname['name']).'<br />';
                foreach($repname as $value) {
                    $output .= '<td style="width:20px;">'.utf8_encode($value).'</td>';
                }
                $output .= '</tr>';
            }
            $output .= '</table></td>';
        }
        $output .= '</tr>';
    }

    $identifier = md5(uniqid(microtime()));
    $session->set_phpsession(array('crmimportcustomers_'.$identifier => serialize($data)));
    $output .= '<tr><input type="button" value="'.$lang->savecaps.'" class="button" id="perform_crm/importcustomers_Button" name="perform_crm/importcustomers_Button"/><input type="hidden" name="identifier" id="identifier" value="'.$identifier.'"/><input type="hidden" name="multivalueseperator" id="multivalueseperator" value="'.$core->input['multivalueseperator'].'"/></table></form><div id="perform_crm/importcustomers_Results"></div>';
    return $output;
}

function custom_sort($a, $b) {
    if($a == $b) {
        return 0;
    }
    if($a > $b) {
        return 1;
    }
    else {
        return -1;
    }
}

function custom_sort_reverse($a, $b) {
    if($a == $b) {
        return 0;
    }
    if($a > $b) {
        return -1;
    }
    else {
        return 1;
    }
}
?>