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
$tables = array('apid' => 'aro_policies', 'awpid' => 'aro_wareshouses_policies', 'aapcid' => 'aro_approvalchain_policies', 'adsid' => 'aro_documentsequences');
foreach($tables as $primarykey => $table) {
    $expiringpolicies_query = $db->query('SELECT * FROM '.Tprefix.$table.' WHERE effectiveTo BETWEEN '.TIME_NOW.' AND '.strtotime("+30 days"));
    if($db->num_rows($expiringpolicies_query) > 0) {
        $expiringpolices_header = '<div><h3>'.$lang->$table.'</h3><ul>';
        while($policy = $db->fetch_assoc($expiringpolicies_query)) {
            $uid = $policy['createdBy'];
            if(!empty($policy['modifiedBy'])) {
                $uid = $policy['modifiedBy'];
            }
            $recipients[$uid] = $uid;
            switch($table) {
                case 'aro_policies':
                case 'aro_approvalchain_policies':
                    $aff = new Affiliates($policy['affid']);
                    $pt = new PurchaseTypes($policy['purchaseType']);
                    $expiringpolices[$table][$uid] .="<li>".$aff->get_displayname()." / ".$pt->get_displayname()." ";
                    break;
                case 'aro_wareshouses_policies':
                    $warehouse = new Warehouses($policy['warehouse']);
                    $expiringpolices[$table][$uid] .="<li>".$warehouse->get_displayname()." ";
                    break;
                case 'aro_documentsequences':
                    $aff = new Affiliates($policy['affid']);
                    $pt = new PurchaseTypes($policy['ptid']);
                    $expiringpolices[$table][$uid] .="<li>".$aff->get_displayname()." / ".$pt->get_displayname()." ";
                    break;
                default:
                    break;
            }
            $expiringpolices[$table][$uid].= " / ".date('d-m-Y', $policy['effectiveTo']);
            $expiringpolices[$table][$uid] .=" (Created On: ".date('d-m-Y', $policy['createdOn']).") </li>";
            $query = $db->update_query($table, array('effectiveTo' => strtotime('+30 days', $policy['effectiveTo'])), $primarykey.'='.$policy[$primarykey]);
            if($query) {
                $log->record($table, array('update'));
            }
        }
        $expiringpolices[$table][$uid] .='</ul></div>';
        $expiringpolices_data[$uid] .= $expiringpolices_header.$expiringpolices[$table][$uid];
        unset($expiringpolices_header);
    }
}
if(is_array($recipients)) {
    foreach($recipients as $recipient) {
        if(isset($expiringpolices_data[$recipient]) && !empty($expiringpolices_data[$recipient])) {
            $recipient_obj = new Users($recipient);
            if($usergroup['aro_canManagePolicies'] == 1) {
                $email_data = '<h1>'.$lang->followingexppolicies.'</h1>'.$expiringpolices_data[$recipient].$lang->expiringpolicyrenewed;
                $email_data = array(
                        'from_email' => $core->settings['maileremail'],
                        'from' => 'OCOS Mailer',
                        'subject' => 'Expiring ARO Policies',
                        'message' => $email_data,
                        'to' => $recipient_obj->get_email(),
                );
//            print_r($email_data);
//            exit;
                $mail = new Mailer($email_data, 'php');
            }
        }
    }
}
?>