<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: aro_expiringpolicynotification.php
 * Created:        @rasha.aboushakra    Sep 24, 2015 | 3:36:57 PM
 * Last Update:    @rasha.aboushakra    Sep 24, 2015 | 3:36:57 PM
 */

require_once '../inc/init.php';
$lang = new Language('english');
$lang->load('aro_meta');



$tables = array('aro_policies', 'aro_wareshouses_policies', 'aro_approvalchain_policies', 'aro_documentsequences');
foreach($tables as $table) {
    $expiringpolicies_query = $db->query('SELECT * FROM '.Tprefix.$table.' WHERE effectiveTo BETWEEN ('.TIMENOW.' AND '.strtotime("+10 days").') OR (modifiedOn=0 AND createdOn='.strtotime("-1 year").')');
    if($db->num_rows($expiringpolicies_query) > 0) {

        if(!empty($policy['modifiedOn'])) {
            $uid = $policy['modifiedOn'];
        }
        else {
            $uid = $policy['createdOn'];
        }
        $recipients[] = $uid;
        $expiringpolices[$uid] .= '<div><h3>'.$lang->table.'</h3><br/>';
        $expiringpolices[$uid] .='<ul>';
        while($policy = $db->fetch_assoc($expiringpolicies_query)) {
            switch($table) {
                case 'aro_policies':
                case 'aro_approvalchain_policies':
                    $aff = new Affiliates($policy['affid']);
                    $pt = new PurchaseTypes($policy['purchaseType']);
                    $expiringpolices[$uid] .="<li>".$aff->get_displayname()." ".$pt->get_displayname()." ";
                    break;
                case 'aro_wareshouses_policies':
                    $warehouse = new Warehouses($policy['warehouse']);
                    $expiringpolices[$uid] .="<li>".$warehouse->get_displayname()." ";
                    break;
                case 'aro_documentsequences':
                    $aff = new Affiliates($policy['affid']);
                    $pt = new PurchaseTypes($policy['ptid']);
                    $expiringpolices[$uid] .="<li>".$aff->get_displayname()." ".$pt->get_displayname()." ";
                    break;
                default:
                    break;
            }
        }
        $expiringpolices[$uid] .=date('d-m-Y', $policy['effectiveTo']);
        $expiringpolices[$uid] .=" (Created On: ".date('d-m-Y', $policy['createdOn']).") </li>";

        $expiringpolices[$uid] .='</ul></div>';
    }
}
if(is_array($recipients)) {
    foreach($recipients as $recipient) {
        if(isset($expiringpolices[$recipient]) && !empty($expiringpolices[$recipient])) {
            $user = new Users($recipient);
            if(permission) {
                $email_data = '<h1> The following policies (will expire within 10 days /have been created a year ago):</h1>'.$expiringpolices[$recipient];
                $email_data = array(
                        'from_email' => $core->settings['maileremail'],
                        'from' => 'OCOS Mailer',
                        'subject' => 'Epiring ARO Policies',
                        'message' => $email_data,
                        'to' => $recipient->get_email(),
                );
                $mail = new Mailer($email_data, 'php');
            }
        }
    }
}
?>