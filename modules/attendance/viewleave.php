<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: viewleave.php
 * Created:        @rasha.aboushakra    Aug 20, 2014 | 10:30:34 AM
 * Last Update:    @rasha.aboushakra    Aug 20, 2014 | 10:30:34 AM
 */





$leave_obj = new Leaves($core->input['id'], FALSE);



$leave_obj->get_requester()->displayName;
$leave_obj->fromDate = date($core->settings['dateformat'], $leave_obj->fromDate);
$leave_obj->toDate = date($core->settings['dateformat'], $leave_obj->toDate);


$leavetype = $leave_obj->get_type('', false);
$leave_obj->details_crumb = parse_additionaldata($leave_obj->get(), $leavetype->additionalFields);
//print_r($leave_obj->details_crumb);
$var = '';
foreach($leave_obj->details_crumb as $key => $val) {
    //echo "$key; $val <br/>\n";
    $var .= '<div class="lefttext">'.''.'</div><div class="righttext">'.$val.'</div>';
}


$toapprove_objects = $leave_obj->get_approvals();
//foreach($toapprove_objects as $key => $val) {
//    echo "$key; $value<br />\n";
//}
print_r($toapprove_objects);


eval("\$attendance_viewleave = \"".$template->get('attendance_viewleave')."\";");
output_page($attendance_viewleave);
