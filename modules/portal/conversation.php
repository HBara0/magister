<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * main page for conversations creation and replying
 * $id: conversation.php
 * Created:        @hussein.barakat    25-Feb-2016 | 09:55:37
 * Last Update:    @hussein.barakat    25-Feb-2016 | 09:55:37
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}


if(!$core->input['action']) {
    if(!empty($core->input['d$@1á'])) {
        $conv_data = unserialize(base64_decode($core->input['d$@1á']));
    }
    elseif(!empty($core->input['scid'])) {
        $conv_data = array('scid' => intval($core->input['scid']));
    }
    if(!is_array($conv_data)) {
        error($lang->couldnotloadconversation);
    }
    //check if previous conversation exists
    if(isset($conv_data['scid']) && !empty($conv_data['scid'])) {
        $conversation_obj = new SystemConversations(intval($conv_data['scid']));
    }
    else {
        $conversation_obj = SystemConversations::get_data($conv_data);
    }
    if(is_object($conversation_obj) && !empty($conversation_obj->scid)) {
        $pagecontent = $conversation_obj->parse_conversation();
    }
    else {
        //get all active users to chose participants
        $users = Users::get_data('gid != 7', array('returnarray' => true, 'order' => 'displayName'));
        if(is_array($users)) {
            foreach($users as $user_obj) {
                $user = $user_obj->get();
                $mainaffiliate = $user_obj->get_mainaffiliate();
                $user['mainaff_output'] = $mainaffiliate->get_displayname();

                //get positions
                $query2 = $db->query("SELECT p.* FROM ".Tprefix."positions p LEFT JOIN ".Tprefix."userspositions up ON (up.posid=p.posid) WHERE  up.uid='{$user['uid']}' ORDER BY p.name ASC");
                $positions_counter = 0;

                while($position = $db->fetch_assoc($query2)) {
                    if(!empty($lang->{$position['name']})) {
                        $position['title'] = $lang->{$position['name']};
                    }

                    if(++$positions_counter > 2) {
                        $hidden_positions .= $break.$position['title'];
                    }
                    else {
                        $userpositions .= $break.$position['title'];
                    }
                    $break = '<br />';
                }

                if($positions_counter > 2) {
                    $userpositions = $userpositions.", <a href='#' id='showmore_positions_{$user[uid]}'>...</a> <span style='display:none;' id='positions_{$user[uid]}'>{$hidden_positions}</span>";
                }
                eval("\$participants_list .= \"".$template->get('general_conversationpage_createconversation_participantrow')."\";");
                unset($userpositions, $hidden_positions);
            }
        }
        $inputChecksum = generate_checksum('cnv');
        $message_inputChecksum = generate_checksum('msg');
        eval("\$pagecontent = \"".$template->get('general_conversationpage_createconversation')."\";");
    }

    eval("\$convpage = \"".$template->get('general_conversationpage')."\";");
    output_page($convpage);
}
else {
    if($core->input['action'] == 'do_perform_conversation') {
        $conversation_obj = new SystemConversations();
        $conversation_obj = $conversation_obj->initiate_conversation($core->input['conversation'], $core->input['message'], $core->input['participants']);
        switch($conversation_obj->get_errorcode()) {
            case 0:
                output_xml("<status>true</status><message>Successfully Saved</message>");
                break;
            case 3:
                $error_output = $errorhandler->get_errors_inline();
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'<![CDATA[<br/>'.$error_output.']]></message>');
                break;
            case 5:
                output_xml("<status>false</status><message>{$lang->emptymessage}</message>");
                break;
            case 6:
                output_xml("<status>false</status><message>{$lang->errorsendingemail}</message>");
                break;
        }
    }
}