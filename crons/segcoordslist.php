<?php
require '../inc/init.php';
$lang = new Language('english');
$lang->load('profiles_meta');

//define('DIRECT_ACCESS', true);
//$core->input['referrer'] = 'cron';
//include '../modules/profiles/segmentslist.php';
//$message = 'Below is the list of the current segment coordinators:';
//$message .= $segmentslist;

$css_styles['altrow'] = 'background-color: #f7fafd;';
$segment_obs = ProductsSegments::get_segments($filters, array('order' => 'title'));
if(is_array($segment_obs)) {
    $segments_rows .='<table><tr><td style="width:50%;background-color: #92D050;">'.$lang->segment.'</td><td style="width:50%;background-color: #92D050;">'.$lang->coordinators.'</td></tr>';
    foreach($segment_obs as $segment_ob) {
        $segcoord_objs = $segment_ob->get_coordinators();
        $seg_coordinators_output = '';
        if(is_array($segcoord_objs)) {
            foreach($segcoord_objs as $segcoord_obj) {
                $segment_coordinators[] = '<a href="'.$core->settings['rootdir'].'/users.php?action=profile&amp;uid='.$segcoord_obj->get_coordinator()->uid.'" target="_blank" style=" text-decoration: none;color:black">'.$segcoord_obj->get_coordinator()->get_displayname().'</a>';
            }
            $seg_coordinators_output = implode(', ', $segment_coordinators);
            unset($segment_coordinators, $segcoord_objs);
        }
        if(empty($css_styles['altrow'])) {
            $css_styles['altrow'] = 'background-color: #f7fafd;';
        }
        else {
            $css_styles['altrow'] = '';
        }
        $segments_rows .= '<tr style="'.$css_styles['altrow'].'"><td><a href="'.$core->settings['rootdir'].'/index.php?module=profiles/segmentprofile&id='.$segment_ob->psid.'" target="_blank" style=" text-decoration: none;color:black;">'.$segment_ob->get_displayname().'</a></td><td>'.$seg_coordinators_output.'</td></tr>';
    }
    $segments_rows .='</table>';
}
else {
    $segments_rows .= '<tr><td colspan="2">'.$lang->na.'</tr>';
}
$message = 'Below is the list of the current segment coordinators: <br/>'.$lang->segmentslist.'<br/>'.$segments_rows;
$email_data = array(
        'from' => 'ocos@orkila.com',
        'to' => 'christophe.sacy@orkila.com',
        'subject' => "Segment Coordinators List",
        'message' => $message,
);

$mailer = new Mailer();
$mailer = $mailer->get_mailerobj();
$mailer->set_type();
$mailer->set_from(array('email' => $email_data['from']));
$mailer->set_subject($email_data['subject']);
$mailer->set_message($email_data['message']);
$mailer->set_to($email_data['to']);
$mailer->send();
//$x = $mailer->debug_info();
//print_R($x);
//exit;
?>