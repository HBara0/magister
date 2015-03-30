<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: possibleproductsegmentapplication.php
 * Created:        @hussein.barakat    Mar 27, 2015 | 10:43:15 AM
 * Last Update:    @hussein.barakat    Mar 27, 2015 | 10:43:15 AM
 */

require_once '../inc/init.php';

$lang = new Language('english');
$lang->load('messages');

//fetch all cid and eptids for customers with eptids not being sold any product or chemicals
$no_midata = $db->query("SELECT cid,eptid FROM ".Tprefix."marketintelligence_basicdata WHERE cfcid=0 AND cfpid=0 AND eptid !=0");
if($db->num_rows($no_midata) > 0) {
    $row = 1;
    while($no_midata_row = $db->fetch_assoc($no_midata)) {
        foreach($no_midata_row as $key => $value) {
            $nomidata_sold[$row][$key] = $value;
        }
        $row++;
    }
}

//fetch all cid,eptid,cfcid,cfpid where customers are being sold different products or chemicals than other customersOR
$dif_midata = $db->query("SELECT DISTINCT a.cid,a.eptid,a.cfcid,a.cfpid FROM ".Tprefix."marketintelligence_basicdata AS a INNER JOIN ".Tprefix."marketintelligence_basicdata AS b ON (a.eptid=b.eptid) Where a.cid!=b.cid AND a.eptid!=0 AND (a.cfcid!=b.cfcid OR a.cfpid != b.cfpid) AND a.cfpid!=0 AND a.cfcid!=0");
if($db->num_rows($dif_midata) > 0) {
    $row = 1;
    while($diff_midata_row = $db->fetch_assoc($dif_midata)) {
        foreach($diff_midata_row as $key => $value) {
            $diff_midata_sold[$row][$key] = $value;
        }
        $row++;
    }
    unset($row);
}
//getting all products and their chemicals related to an endproducttype that arent being sold
//to customers with same endpdtype
$row = 1;
foreach($nomidata_sold as $nomidata_sold_row) {
    foreach($nomidata_sold_row as $key => $value) {
        if($key == 'cid') {
            $cid = $value;
        }
        elseif($key == 'eptid') {
            $eptid = $value;
        }
    }
    $assignedemp_objs = AssignedEmployees::get_data(array('eid' => $cid), array('returnarray' => true));
    if(is_null($assignedemp_objs) || $assignedemp_objs == false) {
        continue;
    }
    foreach($assignedemp_objs as $assignedemp_obj) {
        $userid = $assignedemp_obj->uid;
        $endprodtype_obj = new EndProducTypes($eptid, false);
        $application = $endprodtype_obj->get_application();
        if(is_null($application)) {
            continue;
        }
        $segapfunctions_objs = $application->get_segappfunctionsobjs();
        if($segapfunctions_objs != false) {
            if(!is_array($segapfunctions_objs)) {
                $segapfunctions_objs[] = $segapfunctions_objs;
            }
            foreach($segapfunctions_objs as $segapfunctions_obj) {
                $chemfuncprod_objs = ChemFunctionProducts::get_data(array('safid' => $segapfunctions_obj->safid), array('returnarray' => true));
                if($chemfuncprod_objs != false) {
                    foreach($chemfuncprod_objs as $chemfuncprod_obj) {
                        $output[$userid][$cid][$eptid]['cfpid'][] = $chemfuncprod_obj->cfpid;
                    }
                }
                $chemfuncchem_objs = ChemFunctionChemicals::get_data(array('safid' => $segapfunctions_obj->safid), array('returnarray' => true));
                if($chemfuncchem_objs != false) {
                    foreach($chemfuncchem_objs as $chemfuncchem_obj) {
                        $output[$userid][$cid][$eptid]['cfcid'][] = $chemfuncchem_obj->cfcid;
                    }
                }
            }
        }
    }
}

//getting all products and chemicals that are being sold by another employee to a customer in the same end product type
$original = $diff_midata_sold;
foreach($original as $row => $keys) {
    foreach($original as $row_2 => $key_2) {
        if($key_2['cid'] != $keys['cid'] && $key_2['eptid'] == $keys['eptid']) {
            $customer_obj = new Entities($key_2['cid']);
            $assignedemp_objs = AssignedEmployees::get_data(array('eid' => $key_2['cid']), array('returnarray' => true));
            if(is_null($assignedemp_objs)) {
                continue;
            }
            foreach($assignedemp_objs as $assignedemp_obj) {
                $userid = $assignedemp_obj->uid;
                $output_2[$userid][$keys['cid']][$keys['eptid']]['cfpid'][] = $key_2['cfpid'];
                $output_2[$userid][$keys['cid']][$keys['eptid']]['cfcid'][] = $key_2['cfcid'];
                $output_2[$userid][$keys['cid']][$keys['eptid']]['cfcid'] = array_unique($output_2[$userid][$keys['cid']][$keys['eptid']]['cfcid']);
                $output_2[$userid][$keys['cid']][$keys['eptid']]['cfpid'] = array_unique($output_2[$userid][$keys['cid']][$keys['eptid']]['cfpid']);
            }
        }
        else {
            continue;
        }
    }
}
$emails_contents = array_merge_recursive_replace($output_2, $output);
$verif = send_email($emails_contents);
function send_email($content) {
    if(is_array($content)) {
//loop through the array with the first dimension which is the customer
        foreach($content as $uid => $values) {
            $user_obj = new Users($uid);
            $email = $user_obj->email;
            $message = "<h1>These products and chemicals may concern the below customers: </h1><br>";
            foreach($values as $cid => $data) {
                $cust_obj = new Entities($cid);
                $message.='<h4>Customer :'.$cust_obj->get_displayname().'</h4>';
                foreach($data as $eptid => $ndata) {
                    $ept = new EndProducTypes($eptid);
                    $message.='<h5>'.$ept->get_displayname().' :</h5>';
                    foreach($ndata as $key => $value) {
                        if($key == 'cfcid') {
                            foreach($value as $cfcid) {
                                $chemfunchem = new ChemFunctionChemicals($cfcid);
                                $chemsubstances[] = $chemfunchem->get_chemicalsubstance()->get_displayname();
                            }
                        }
                        if($key == 'cfpid') {
                            foreach($value as $cfpid) {
                                $chemfuncprod = new ChemFunctionProducts($cfpid);
                                $prods[] = $chemfuncprod->get_produt()->get_displayname();
                            }
                        }
                    }
                    $message.='<br>Products: '.implode(',', $prods);
                    $message.='<br>Chemical Substances: '.implode(',', $chemsubstances).'<br>';
                    unset($prods);
                    unset($chemsubstances);
                }
            }
            $email_data = array(
                    'from_email' => 'admin@ocos.orkila.com',
                    'from' => 'Orkila Reminder System',
                    'to' => $email,
                    'subject' => 'Possible Products/Chemicals Clients Might Be Interested In',
                    'message' => $message,
            );
            echo($message);
            $mail = new Mailer($email_data, 'php');
            if($mail->get_status() === true) {
                return true;
            }
            else {
                continue;
            }
        }
    }
}
