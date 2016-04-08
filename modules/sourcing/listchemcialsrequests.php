<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * List Requests for Chemcials
 * $module: Sourcing
 * $id:  listchemcialsrequests.php
 * Created By: 		@tony.assaad		November 15, 2012 | 3:30 PM
 * Last Update: 	@tony.assaad		November 19, 2012 | 9:13 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['sourcing_canListSuppliers'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $origins = array('anyorigin' => $lang->anyorigin, 'chinese' => $lang->chinese, 'nonchinese' => $lang->nonchinese, 'indian' => $lang->indian, 'nonindian' => $lang->nonindian, 'european' => $lang->european, 'noneuropean' => $lang->noneuropean, 'american' => $lang->american, 'nonamerican' => $lang->nonamerican, 'otherasian' => $lang->otherasian, 'nootherasian' => $lang->nootherasian);

    $potential_supplier = new Sourcing();
    $sort_url = sort_url();
    $chemicalrequests = $potential_supplier->get_chemicalrequests();
    if(is_array($chemicalrequests)) {
        foreach($chemicalrequests as $chemicalrequest) {
            $comma = '';
            /* colorate Satisfied request */
            if($chemicalrequest['isClosed'] == 1) {
                $feedback_icon = 'valid.gif';
                $rowcolor = 'greenbackground';
            }
            elseif($chemicalrequest['isClosed'] == 0) {
                $feedback_icon = 'edit.gif';
                $rowcolor = 'unapproved';
            }

            if(is_array($chemicalrequest['origins'])) {
                foreach($chemicalrequest['origins'] as $origin) {
                    $chemicalrequest['origins_output'] .= $comma.$origin['title'];
                    $comma = ', ';
                }
            }

            $chemicalrequest['timeRequested_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $chemicalrequest['timeRequested']);
            eval("\$chemcialsrequests_rows .= \"".$template->get('sourcing_listchemcialsrequests_rows')."\";");
        }
    }
    else {
        $chemcialsrequests_rows = '<tr><td colspan="4">'.$lang->na.'</td></tr>';
    }
    eval("\$sourcing_listchemcialsrequests = \"".$template->get('sourcing_listchemcialsrequests')."\";");
    output_page($sourcing_listchemcialsrequests);
}
else {
    if($core->input['action'] == 'get_feedbackform') {
        $request_id = $core->input['id'];
        $potential_supplier = new Sourcing();

        $feedback = $potential_supplier->get_feedback($request_id);
        if($feedback['isClosed'] == 1) {
            $feedback['feedbackTime_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $feedback['feedbackTime']);
            eval("\$sourcingfeedback = \"".$template->get('popup_sourcing_readfeedback')."\";");
        }
        else {
            eval("\$sourcingfeedback = \"".$template->get('popup_sourcing_feedback')."\";");
        }
        output($sourcingfeedback);
        /* header('Content-type: text/html+javascript');
          '$("#popup_feedback").bind("clickoutside",function(){
          $("#popup_feedback").dialog("close");
          });';
          exit; */
    }
    elseif($core->input['action'] == 'do_feedback') {
        $potential_supplier = new Sourcing();
        $request_id = $db->escape_string($core->input['request']['rid']);
        $sourcingagent_name = $db->fetch_assoc($db->query("SELECT  u.displayName AS agentname
										FROM ".Tprefix."sourcing_chemicalrequests scr
										JOIN ".Tprefix."users u ON (u.uid = scr.feedbackBy) WHERE scr.scrid=".$request_id));
        $requests_feedback = $potential_supplier->set_feedback($core->input['feedback'], $request_id);
        $requester_details = $db->fetch_assoc($db->query("SELECT scr.*, u.displayName, u.email
										FROM ".Tprefix."sourcing_chemicalrequests scr
										JOIN ".Tprefix."users u ON (u.uid = scr.uid) WHERE scr.scrid=".$request_id));

        if($requests_feedback && $requester_details['isClosed'] == 1) {
            $email_data = array(
                    'to' => $requester_details['email'],
                    'from_email' => 'sourcing@orkila.com',
                    'from' => 'Orkila Sourcing',
                    'cc' => 'sourcing@orkila.com',
                    'subject' => $lang->sprint($lang->feedbacknotification_subject, $sourcingagent_name['agentname']),
                    'message' => $core->input['feedback']['feedback']
            );

            $mail = new Mailer($email_data, 'php');
            if($mail->get_status() === true) {
                $log->record('sourcingchemicalreqsfeedback', array('to' => $requester_details['email']));
            }
        }

        switch($potential_supplier->get_status()) {
            case 10:
                // if($requester_details['isClosed'] == 1) {
                header('Content-type: text/xml+javascript');  /* colorate each selected <tr> has applicant id  after successfull update */
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'<![CDATA[<script>$("#popup_feedback").dialog("close"); $("tr[id^='.$request_id.']").each(function() {$(this).addClass("greenbackground"); $(this).find("img").attr("src","./images/valid.gif") }); </script>]]></message>');
                break;
            //}
            case 1:
                output_xml("<status>false</status><message>{$lang->fieldrequired}</message>");
                break;
            case 2:
                output_xml("<status>false</status><message>{$lang->feedbackexsist}</message>");
                break;
        }
    }
}
?>
