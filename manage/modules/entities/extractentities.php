<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * This Page is meant to give ability to either extract or at least generate the entity by type, filtered by affiliates and under segments
 * $id: extract_entities.php
 * Created:        @hussein.barakat    11-Feb-2016 | 10:16:35
 * Last Update:    @hussein.barakat    11-Feb-2016 | 10:16:35
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageSuppliers'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $permissions = $core->user_obj->get_businesspermissions();
    $filters = array('isActive' => 1);
    if(is_array($permissions['affid'])) {
        $afffilters['affid'] = $permissions['affid'];
    }
    //get all affiliates which are active and under the user permissions
    $active_affiliates = Affiliates::get_affiliates($filters, array('returnarray' => true));
    if(is_array($active_affiliates)) {
        foreach($active_affiliates as $affiliate) {
            $affiliates_rows.='<tr><td style="width:5%"><input id="affiliates_'.$affiliate->affid.'" type="checkbox" name="filters[affid][]" value="'.$affiliate->affid.'"></td><td>'.$affiliate->get_displayname().'</td></tr>';
        }
        $affiliates_rows.='<tr><td style="width:5%"><input id="affiliates_unspecified" type="checkbox" name="filters[affid][unspecified]" value="unspecified"></td><td>Unspecified</td></tr>';
    }
    //get all affiliates which are active and under the user permissions
    $active_segments = ProductsSegments::get_data('', array('returnarray' => true));
    if(is_array($active_segments)) {
        foreach($active_segments as $segment) {
            $segments_rows.='<tr><td style="width:5%"><input id="segments_'.$segment->psid.'" type="checkbox" name="filters[psid][]" value="'.$segment->psid.'"></td><td>'.$segment->get_displayname().'</td></tr>';
        }
        $segments_rows.='<tr><td style="width:5%"><input id="segments_unspecified" type="checkbox" name="filters[psid][unspecified]" value="unspecified"></td><td>Unspecified</td></tr>';
    }
    eval("\$customerspage = \"".$template->get("admin_entities_extractentities")."\";");
    output_page($customerspage);
}
else {
    if($core->input['action'] == 'do_perform_extractentities') {
        $filters = $core->input['filters'];
        //check if any of the filters is empty
        if(!is_array($filters) || (!isset($filters['type']) || empty($filters['type'])) || (!isset($filters['affid']) || empty($filters['affid']))) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }
        //check if the unspecified option of the segments has been chose, unset it and save the optino in another variable
        if(is_array($filters['psid'])) {
            if(in_array('unspecified', $filters['psid'])) {
                $unspecified_seg = 1;
                unset($filters['psid']['unspecified']);
            }
            $segfilters['psid'] = $filters['psid'];
        }
        //get the segments filtered by the filter choices
        $segments = ProductsSegments::get_data($segfilters, array('returnarray' => true));
        if(!is_array($segments)) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }

        $type = ' type IN ('.implode(',', $filters['type']).')';
        //check if unspecified is chosen for affiliated, unset it from the array and put it into a seperate variable
        if(in_array('unspecified', $filters['affid'])) {
            $unspecified = 1;
            unset($filters['affid']['unspecified']);
        }
        //loop through all the chosen affiliates
        foreach($filters['affid'] as $affid) {
            //loop through all the chosen segments
            foreach($segments as $segment) {
                $entities = Entities::get_data($type.' AND eid IN (SELECT eid FROM affiliatedentities WHERE affid = '.$affid.') AND eid IN (SELECT eid FROM entitiessegments WHERE psid = '.$segment->psid.')', array('returnarray' => true, 'simple' => false, 'operators' => array('filter' => 'CUSTOMSQLSECURE')));
                if(is_array($entities)) {
                    $results[$affid][$segment->psid] = $entities;
                }
                //if unspecified segments was chosen, check if current affiliate contains unspecified segment entities
                if($unspecified_seg == 1) {
                    $entities = Entities::get_data($type.' AND NOT EXISTS (SELECT eid FROM affiliatedentities WHERE affid = '.$affid.') AND eid NOT IN (SELECT eid FROM entitiessegments)', array('returnarray' => true, 'simple' => false, 'operators' => array('filter' => 'CUSTOMSQLSECURE')));
                    if(is_array($entities)) {
                        $results[$affid]['unspecified'] = $entities;
                    }
                }
            }
        }
        //if unspecified affiliate was chosen, then loop through the segments and get entities
        if($unspecified == 1) {
            foreach($segments as $segment) {
                $entities = Entities::get_data($type.' AND NOT EXISTS (SELECT eid FROM affiliatedentities) AND eid  IN (SELECT eid FROM entitiessegments  WHERE psid = '.$segment->psid.')', array('returnarray' => true, 'operators' => array('filter' => 'CUSTOMSQLSECURE')));
                if(is_array($entities)) {
                    $results['unspecified'][$segment->psid] = $entities;
                }
            }
            if($unspecified_seg == 1) {
                $entities = Entities::get_data($type.' AND NOT EXISTS (SELECT eid FROM affiliatedentities) AND NOT EXISTS (SELECT eid FROM entitiessegments)', array('returnarray' => true, 'operators' => array('filter' => 'CUSTOMSQLSECURE')));
                if(is_array($entities)) {
                    $results['unspecified']['unspecified'] = $entities;
                }
            }
        }
//if results have been found chose how to proceed by output type
        if(is_array($results)) {
            switch($core->input['extract']) {
                case 'generate':
                    foreach($results as $affid => $segmentedres) {
                        if(is_array($segmentedres)) {
                            $affiliate = new Affiliates($affid);
                            //go through all entities and parse the information as requested
                            foreach($segmentedres as $psid => $entities) {
                                if(is_numeric($psid)) {
                                    $segment = new ProductsSegments($psid);
                                }
                                elseif($psid == 'unspecified') {
                                    $segment = 'Unspecified';
                                }
                                if(is_array($entities)) {
                                    foreach($entities as $entity_obj) {
                                        if(is_object($entity_obj)) {
                                            $entity = $entity_obj->get();
                                            //check fields if empty, if so then put a dash
                                            $fields_tofetch = array('fax1', 'fax2', 'phone1', 'phone2', 'addressLine1', 'mainEmail', 'website');
                                            foreach($fields_tofetch as $field) {
                                                if(empty($entity[$field])) {
                                                    $entity[$field] = '';
                                                }
                                            }
                                            //get company type output
                                            $entity['companyType_output'] = $entity_obj->get_type();
                                            $entirty_country = $entity_obj->get_country();
                                            if(is_object($entirty_country)) {
                                                $entity['companyCountry_output'] = $entirty_country->get_displayname();
                                            }
                                            //get all representatives for the company and parse them in a single TD
                                            $representatives = $entity_obj->get_representatives();
                                            if(is_array($representatives)) {
                                                foreach($representatives as $representative) {
                                                    $rep_field['names'].= $representative->get_displayname().',  ';
                                                    $rep_field['emails'].=$representative->get_contactinfo().', ';
                                                }
                                            }
                                            else {
                                                $rep_field['names'] = '';
                                            }
                                            eval("\$entityrows.=\"".$template->get("admin_entities_extractentities_affiliate_segment_entityrow")."\";");
                                            unset($rep_field);
                                        }
                                    }
                                }
                            }
                            if(is_object($segment)) {
                                $segment_output = $segment->alias;
                            }
                            else if($segment == 'Unspecified') {
                                $segment_output = $segment;
                            }
                            else {
                                continue;
                            }
                            //parsing the excel file
                            $tbody = '<tbody>'.$entityrows.'</tbody>';
                            eval("\$thead = \"".$template->get("admin_entities_extractentities_affiliate_segment_header")."\";");
                            $result = '<table class="datatable_basic table table-bordered row-border hover order-column" cellspacing="0" width="100%">'.$thead.$tbody.'</table>';
                            output_xml('<status></status><message><![CDATA['.$result.']]></message>');
                            exit();
                        }
                    }
                    break;
                case 'export':
                    //if export, create the main file that will include all the affiliate files
//                    $export_path = $_SERVER['DOCUMENT_ROOT'].'/tmp/exctractentities';
//
//                    if(!file_exists($export_path)) {
//                        mkdir($export_path);
//                    }
//
//                    @rmdir($export_path.'/*');
//                    $motherpath = $export_path.'/exctractentities_'.uniqid();
//                    mkdir($motherpath);
                    foreach($results as $affid => $segmentedres) {
                        if(is_array($segmentedres)) {
                            $affiliate = new Affiliates($affid);
                            //create a sub-folder for affiliate
//                            $sub_path = $motherpath.'/'.$affiliate->alias;
//                            mkdir($sub_path);
                            //go through all entities and parse the information as requested
                            foreach($segmentedres as $psid => $entities) {
                                if(is_numeric($psid)) {
                                    $segment = new ProductsSegments($psid);
                                }
                                elseif($psid == 'unspecified') {
                                    $segment = 'Unspecified';
                                }
                                if(is_array($entities)) {
                                    foreach($entities as $entity_obj) {
                                        if(is_object($entity_obj)) {
                                            $entity = $entity_obj->get();
                                            //check fields if empty, if so then put a dash
                                            $fields_tofetch = array('fax1', 'fax2', 'phone1', 'phone2', 'addressLine1', 'mainEmail', 'website');
                                            foreach($fields_tofetch as $field) {
                                                if(empty($entity[$field])) {
                                                    $entity[$field] = '';
                                                }
                                            }
                                            //get company type output
                                            $entity['companyType_output'] = $entity_obj->get_type();
                                            $entirty_country = $entity_obj->get_country();
                                            if(is_object($entirty_country)) {
                                                $entity['companyCountry_output'] = $entirty_country->acronym;
                                            }
                                            //get company city
                                            $entity_city = $entity_obj->get_city();
                                            if(is_object($entity_city)) {
                                                $entity['companyCity_output'] = $entity_city->unlocode;
                                            }
                                            //get assigned employees
                                            $assignedemoployees_obj = $entity_obj->get_assignedusers();
                                            if(is_array($assignedemoployees_obj)) {
                                                $assignedemployees_array = array();
                                                foreach($assignedemoployees_obj as $assigned_obj) {
                                                    $assignedemployees_array[] = $assigned_obj->get_displayname();
                                                }
                                                $entity['asssignedemployees_output'] = implode(';', $assignedemployees_array);
                                            }
                                            //get if company is active
                                            $entity['isactive_output'] = 'n';
                                            if($entity['isActive'] == 1) {
                                                $entity['isactive_output'] = 'y';
                                            }
                                            //get all representatives for the company and parse them in a single TD
                                            $representatives = $entity_obj->get_representatives();
                                            if(is_array($representatives)) {
                                                foreach($representatives as $representative) {
                                                    $rep_field['name'] = $representative->get_displayname();
                                                    $rep_field['email'] = $representative->email;
                                                    if(empty($representative->email)) {
                                                        $rep_field['email'] = '';
                                                    }
                                                    $rep_field['phone'] = $representative->phone;
                                                    if(empty($representative->phone)) {
                                                        $rep_field['phone'] = '';
                                                    }
                                                    $rep_field['rpid'] = $representative->rpid;
                                                    $rep_field['isactive_output'] = 'n';
                                                    if($rep_field['isActive'] == 1) {
                                                        $rep_field['isactive_output'] = 'y';
                                                    }
                                                    eval("\$entityrows.=\"".$template->get("admin_entities_extractentities_affiliate_segment_entityrow")."\";");
                                                    unset($rep_field);
                                                }
                                            }
                                            else {
                                                $rep_field['names'] = $rep_field['phone'] = $rep_field['email'] = $rep_field['isactive_output'] = $rep_field['rpid'] = '';
                                                eval("\$entityrows.=\"".$template->get("admin_entities_extractentities_affiliate_segment_entityrow")."\";");
                                                unset($rep_field);
                                            }
                                        }
                                        unset($entity);
                                    }
                                }
                                if(is_object($segment)) {
                                    $segment_output = $segment->alias;
                                }
                                else if($segment == 'Unspecified') {
                                    $segment_output = $segment;
                                }
                                else {
                                    continue;
                                }
                                //parsing the excel file
                                $tbody = '<tbody>'.$entityrows.'</tbody>';
                                eval("\$thead = \"".$template->get("admin_entities_extractentities_affiliate_segment_header")."\";");
                                $result = '<table>'.$thead.$tbody.'</table>';
                                $page = '<html xmlns:v = "urn:schemas-microsoft-com:vml" xmlns:o = "urn:schemas-microsoft-com:office:office" xmlns:x = "urn:schemas-microsoft-com:office:excel"
            xmlns = "http://www.w3.org/TR/REC-html40">
            <head>
            <meta http-equiv = Content-Type content = "text/html; charset=windows-1252">
            <meta name = ProgId content = Excel.Sheet>
            <meta name = Generator content = "Microsoft Excel 11">
            <!--[if gte mso 9]><xml>
            <x:ExcelWorkbook>
            <x:ExcelWorksheets>
            <x:ExcelWorksheet>
            <x:Name>none</x:Name>
            <x:WorksheetOptions>
            <x:ProtectContents>False</x:ProtectContents>
            <x:ProtectObjects>False</x:ProtectObjects>
            <x:ProtectScenarios>False</x:ProtectScenarios>
            </x:WorksheetOptions>
            </x:ExcelWorksheet>
            </x:ExcelWorksheets>
            <x:WindowHeight>9210</x:WindowHeight>
            <x:WindowWidth>19035</x:WindowWidth>
            <x:WindowTopX>0</x:WindowTopX>
            <x:WindowTopY>75</x:WindowTopY>
            <x:ProtectStructure>False</x:ProtectStructure>
            <x:ProtectWindows>False</x:ProtectWindows>
            </x:ExcelWorkbook>
            </xml><![endif] -->
            </head>
            <body>'.$result.'</body></html>';
                                //write and create the file
                                $path = $sub_path.'/'.$segment_output.'.xls';
                                $handle = fopen($path, 'w') or die('Cannot open file: '.$allpaths);
                                $writefile = file_put_contents($path, $page);
                                unset($result, $entityrows);
                            }
                        }
                    }


                    if(file_exists($motherpath)) {
                        $zipname = $motherpath.'/../extractentities'.uniqid().'.zip';
                        $zip = Zip($motherpath, $zipname);

                        $link = '<a target="_blank" href="'.$core->settings['rootdir'].'/manage/index.php?module=entities/extractentities&action=download&dasource='.base64_encode($zipname).'">Cick Here To Download</a>';
                        output_xml("<status>true</status><message>{$lang->success}! <![CDATA[<br />{$link}]]></message>");
                    }
                    break;
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->nomatchfound}</message>");
            exit;
        }
    }
    else if($core->input['action'] == 'download') {
        $file = base64_decode($core->input['dasource']);
        if(file_exists($file)) {
            $download = new Download();
            $download->set_real_path($file);
            $download->stream_file(true);
        }
        else {
            redirect(DOMAIN);
        }
    }
}
function Zip($source, $destination) {
    if(!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if(!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if(is_dir($source) === true) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach($files as $file) {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if(in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
                continue;

            $file = realpath($file);

            if(is_dir($file) === true) {
                $zip->addEmptyDir(str_replace($source.'/', '', $file.'/'));
            }
            else if(is_file($file) === true) {
                $zip->addFromString(str_replace($source.'/', '', $file), file_get_contents($file));
            }
        }
    }
    else if(is_file($source) === true) {
        $zip->addFromString(basename($source), file_get_contents($source));
    }


    return $zip->close();
}
