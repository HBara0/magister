<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: directq.php
 * Created:        @rasha.aboushakra    Mar 7, 2016 | 11:51:51 AM
 * Last Update:    @rasha.aboushakra    Mar 7, 2016 | 11:51:51 AM
 */

require '../inc/init.php';

global $db;
//$query = $db->update_query('aro_requests', array('isRejected' => 0, 'RejectedOn' => 0, 'RejectedBy' => 0), 'aorid=430');
//$query = $db->update_query('reportssendlog', array('readyForSending' => '1'), 'rslid=166');
//
//$query = $db->fetch_assoc($db->query('SELECT toApprove FROM `leavetypes` WHERE name="businesstravel"'));
//print_R($query);
//$aroapprovals = AroRequestsApprovals::get_data(array('aorid' => '423', 'position' => 'regionalSupervisor'), array('returnarray' => true));
//$query = $db->delete_query('aro_requests_approvals', 'araid=2005');
//
//print_R($aroapprovals);
//
// $query = $db->insert_query('incoterms', array('titleAbbr' => 'DDU', 'name' => 'delivery-duty-unpaid', 'title' => 'Delivery Duty Unpaid'));
//$query = $db->insert_query('paymentterms', array('name' => 'payment-term-40', 'title' => '1350Kg IBC'));
//$aff = Affiliates::get_affiliates(array('affid' => 12), array('simple' => false));
//print_R($aff);
//$query = $db->update_query('visitreports', array('isDraft' => '0'), 'identifier IN (\'3b4c16d357\',\'8f7f71df2e\')');
//$db->update_query('aro_requests', array('createdBy' => 434), 'aorid=454');
//$query = $db->insert_query('packaging', array('name' => '500g-jar', 'title' => '500g Jar'));
//$packaging = Packaging::get_data(array('title' => '1010Kg IBC'), array('returnarray' => true));
//print_R($packaging);
$query = $db->delete_query('packaging', 'packid=78');
