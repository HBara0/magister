<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: customers.php
 * Created:        @hussein.barakat    02-Feb-2016 | 18:10:36
 * Last Update:    @hussein.barakat    02-Feb-2016 | 18:10:36
 */
require '../inc/init.php';
//if($_REQUEST['authkey'] == 'kia5ravb$op09dj4a!xhegalhj') {
$userstoemail = array(386, 435, 389, 1);
$activeaffiliates = Affiliates::get_affiliates(array('isActive' => 1), array('returnarray' => true));
if(is_array($activeaffiliates)) {
    $message = '<html><body><h1 style="background-color:#91b64f">Customers list by country for following segments : Construction , Paint and Coating</h1>';
    $message.='<table width="100%" cellspacing="0" cellpadding="5" style="border-left: 1px solid #CCC;" border="0"><thead><tr style="background-color:#EAEAEA"> <th style="border: 1px solid black;">Representative Name</th><th style="border: 1px solid black;">Representative Email</th><th style="border: 1px solid black;">Customer Name</th><th style="border: 1px solid black;">Company Email</th><th style="border: 1px solid black;">Country</th><th style="border: 1px solid black;">Segment</th></tr></thead><tbody>';
    foreach($activeaffiliates as $affiliate) {
        $affiliatedentities = AffiliatedEntities::get_data('eid IN (SELECT eid FROM entities WHERE isActive =1 AND type = "c" AND eid IN (SELECT eid FROM '.EntitiesSegments::TABLE_NAME.' WHERE psid IN(24,4))) AND affid = '.$affiliate->affid, array('returnarray' => true, 'operators' => array('filter' => 'CUSTOMSQLSECURE')));
        if(is_array($affiliatedentities)) {
            $message.='<tr><td colspan="6"><hr></td></tr>';
            $message.='<tr><td colspan="6" style="text-align:center"><h2>'.$affiliate->get_displayname().'</h2></td></tr>';
            $message.='<tr><td colspan="6"><hr></td></tr>';
            foreach($affiliatedentities as $affiliatedentitie) {
                $entity = $affiliatedentitie->get_entity();
                if(is_object($entity)) {
                    $representatives = $entity->get_representatives();
                    if(is_array($representatives)) {
                        $companyemail = '-';
                        if(!empty($entity->mainEmail)) {
                            $companyemail = $entity->mainEmail;
                        }
                        $country = $entity->get_country()->get_displayname();
                        $entsegments_objs = EntitiesSegments::get_data(array('eid' => $entity->eid), array('returnarray' => true));
                        if(is_array($entsegments_objs)) {
                            foreach($entsegments_objs as $entsegments_obj) {
                                $segments_obj = $entsegments_obj->get_segment();
                                $segments_list[] = $segments_obj->get_displayname();
                            }
                            $segments = implode(', ', $segments_list);
                        }
                        foreach($representatives as $representative) {
                            $message.="<tr><td style='border: 1px solid black;'>{$representative->get_displayname()}</td><td style='border: 1px solid black;'>{$representative->email}</td><td style='border: 1px solid black;'>{$entity->get_displayname()}</td>"
                                    ."<td style='border: 1px solid black;'>{$companyemail}</td><td style='border: 1px solid black;'>{$country}</td><td style='border: 1px solid black;'>{$segments}</td></tr>";
                        }
                    }
                    unset($entsegments_objs, $segments_list, $segments_obj, $segments);
                }
            }
        }
    }
    $message.='</tbody></table></body></html>';
    print($message);
    foreach($userstoemail as $uid) {
        $user = new Users($uid);
        $email_data = array(
                'from_email' => 'admin@ocos.orkila.com',
                'from' => 'OCOS Special Customer List',
                'to' => $user->email,
                'subject' => 'Customer List Ordered By Country For Two Segments',
                'message' => $message,
        );
        $mail = new Mailer($email_data, 'php');
    }
}


//}