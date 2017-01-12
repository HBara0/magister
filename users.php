<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * User related actions
 * $id: users.php
 * Created: 	@zaher.reda
 * Last Update: @zaher.reda 	May 28, 2012 | 11:24 AM
 */
define('PASSEXPIRE_EXCLUDE', 1);
require_once './global.php';

if($core->input['action']) {
    if($core->input['action'] == 'do_login') {
        $session->name_phpsession(COOKIE_PREFIX.'login');
        $session->start_phpsession();
        $session->regenerate_id_phpsession(true);
        /* Ensure that request is genuine, not a CSRF */
        $core->input['token'] = $core->sanitize_inputs($core->input['token']);
        /* 		if(!$session->is_validtoken()) {
          output_xml("<status>false</status><message></message>");
          $session->destroy_phpsession(true);
          exit;
          } */

        $login_details = array(
                'username' => $db->escape_string($core->input['username']),
                'password' => $db->escape_string($core->input['password'])
        );

        $validation = new ValidateAccount();
        $user_details = $validation->get_user_by_username($login_details['username']);
        unset($user_details['password'], $user_details['salt']);

        if($validation->can_attemptlogin($login_details['username'])) {
            $validation = new ValidateAccount($login_details);

            if($validation->get_validation_result()) {
                $user_data = $validation->get_userdetails();

                create_cookie('uid', $user_data['uid'], (TIME_NOW + (60 * $core->settings['idletime'])));
                create_cookie('loginKey', $user_data['loginKey'], (TIME_NOW + (60 * $core->settings['idletime'])));
                $db->update_query('users', array('failedLoginAttempts' => 0), "uid='{$user_details[uid]}'");

                output_xml("<status>true</status><message>{$lang->loginsuccess}</message>");
            }
            else {
                $db->update_query('users', array('failedLoginAttempts' => $validation->get_real_failed_attempts() + 1, 'lastAttemptTime' => TIME_NOW), "uid='{$user_details[uid]}'");
                if($validation->get_real_failed_attempts() >= 3) {
                    $lang->tryresetpassword = '<![CDATA[<br />'.$lang->tryresetpassword.']]>';
                }
                else {
                    $lang->tryresetpassword = '';
                }
                $log->record($core->input['username'], 0);
                output_xml("<status>false</status><message>{$lang->invalidlogin}{$lang->tryresetpassword}</message>");
            }
        }
        else {
            if($validation->get_error_message()) {
                $fail_message = $validation->get_error_message();
            }
            else {
                $login_after = round($core->settings['failedlogintime'] - ((TIME_NOW - $user_details['lastAttemptTime']) / 60), 0);
                $fail_message = $lang->sprint($lang->reachedmaxattempts, $login_after);
            }

            output_xml("<status>false</status><message>{$fail_message}</message>");
        }
    }
    elseif($core->input['action'] == 'do_logout') {
        $uid = $core->user['uid'];

        $db->update_query('users', array('lastVisit' => TIME_NOW), "uid='$uid'");

        $db->delete_query('sessions', "uid='$uid'");

        create_cookie('sid', '', (TIME_NOW - 3600));
        create_cookie('uid', '', (TIME_NOW - 3600));
        create_cookie('loginKey', '', (TIME_NOW - 3600));

        redirect('users.php?action=login');
    }
    elseif($core->input['action'] == 'reset_password') {
        $lang->load('messages');
        $email = $db->escape_string(trim($core->input['email']));

        $new_details = $db->fetch_assoc($db->query("SELECT uid, firstName FROM ".Tprefix."users WHERE email='{$email}'"));

        if($new_details['uid']) {
            $new_details['password'] = Accounts::generate_password_string(8);

            $lang->resetemailmessage = $lang->sprint($lang->resetemailmessage, $new_details['firstName'], $new_details['password'], $core->settings['adminemail']);

            $mailer = new Mailer();
            $mailer = $mailer->get_mailerobj();
            $mailer->set_layouttype('standard');
            $mailer->set_from(array('name' => 'OCOS Mailer', 'email' => $core->settings['maileremail']));
            $mailer->set_subject($lang->yournewpassword);
            $mailer->set_message($lang->resetemailmessage);
            $mailer->set_to($email);
            $mailer->send();

            if($mailer->get_status() === true) {
                $modify = new ModifyAccount($new_details);
                output_xml("<status>true</status><message>{$lang->emailsentcontainpassword}</message>");
            }
            else {
                output_xml("<status>false</status><message>{$lang->errorsendingemail}</message>");
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->accountemailnotfound}</message>");
        }
    }
    elseif($core->input['action'] == 'do_changepassword') {
        $lang->load('profile');

        if(empty($core->input['oldpassword']) || empty($core->input['newpassword']) || empty($core->input['newpassword2'])) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }

        $validation = new ValidateAccount(array('username' => $core->user['username'], 'password' => $core->input['oldpassword']));
        if(!$validation->get_validation_result()) {
            output_xml("<status>false</status><message>{$lang->wrongoldpassword}</message>");
            exit;
        }

        if($core->input['newpassword'] == $core->input['newpassword2']) {
            $newdata = array(
                    'uid' => $core->user['uid'],
                    'password' => $db->escape_string($core->input['newpassword']),
                    'lastPasswordChange' => TIME_NOW
            );

            $modify = new ModifyAccount($newdata);
            if($modify->get_status() === true) {
                $modify->archive_password($core->input['oldpassword'], $newdata['uid']);
                output_xml("<status>true</status><message>{$lang->passwordsuccessfullychanged}</message>");
                $log->record($core->input['uid']);
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->passwordnomatch}</message>");
        }
    }
    elseif($core->input['action'] == 'do_modifyprofile') {
        if($session->uid == 0) {
            redirect('users.php?action=login');
        }

        $lang->load('profile');

        if(is_empty($core->input['email'], $core->input['firstName'], $core->input['lastName'], $core->input['skype'])) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }

        unset($core->input['action']);
        $core->input['uid'] = $core->user['uid'];

        $modify = new ModifyAccount($core->input);
        if($modify->get_status() === true) {
            output_xml("<status>true</status><message>{$lang->profilesuccessfullyupdated}</message>");
            log_action($core->input['uid']);
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorupdatingprofile}</message>");
        }
    }
    elseif($core->input['action'] == 'do_changeprofilepic') {
        if($session->uid == 0) {
            redirect('users.php?action=login');
        }


        if(!isset($core->input['uid'])) {
            $core->input['uid'] = $core->user['uid'];
        }
        $old_profilepicture = $db->fetch_field($db->query("SELECT profilePicture FROM ".Tprefix."users WHERE uid=".intval($core->input['uid'])), 'profilePicture');
        $upload = new Uploader('uploadfile', $_FILES, array('image/jpeg', 'image/png', 'image/gif'), 'putfile', 300000, 0, 1);
        $upload->set_upload_path($core->settings['profilepicdir']);
        $upload->process_file();
        $filename = $upload->get_filename();
        if(!empty($fielname)) {
            ?>
            <script language="javascript" type="text/javascript">
                $(function () {
                    window.top.$("#upload_Result").html("<?php echo ('no file found')?>");
                });
            </script>
            <?php
        }

        $upload->resize();
        $query = $db->update_query('users', array('profilePicture' => $filename), 'uid='.intval($core->input['uid']));
        eval("\$headerinc = \"".$template->get('headerinc')."\";");
        echo $headerinc;
        ?>
        <script language="javascript" type="text/javascript">
            $(function () {
                window.top.$("#upload_Result").html("<?php echo $upload->parse_status($upload->get_status());?>");
            });
        </script>
        <?php
        if($query) {
            if(!empty($old_profilepicture)) {
                unlink('./'.$core->settings['profilepicdir'].'/'.$old_profilepicture);
            }
        }
    }
    elseif($core->input['action'] == 'downloadsignature') {
        if($session->uid == 0) {
            redirect('users.php?action=login');
        }

        $signature['name'] = 'Orkila';

        $identifier = substr(md5(uniqid(microtime())), 1, 5);
        $zip = new ZipArchive();
        $filepath = './tmp/';
        $filename = $identifier.'.zip';
        if($zip->open($filepath.$filename, ZIPARCHIVE::CREATE) !== TRUE) {
            echo 'error';
            exit;
        }

        $user = new Users();
        $image_location = $user->generate_image_sign(true);
        $imagemin_location = $user->generate_image_sign(true, 180, 40, true);
        $signature['text'] = $user->generate_text_sign();

        $signature['text'] = preg_replace("/<br \/>/i", "\n", $signature['text']);
        $signature['text'] .= "\n".$lang->signaturefooter."\n".$lang->signaturefooter_2."\n";

        $signature['textmin'] = $user->generate_text_sign(true);
        $signature['textmin'] = preg_replace("/<br \/>/i", "\n", $signature['textmin']);

        $zip->addEmptyDir('Outlook');
        $zip->addFromString('Outlook/Readme.txt', $lang->signatureguideline);
        $zip->addFromString('Outlook/'.$signature['name'].'.txt', $signature['text']);
        $zip->addFromString('Outlook/'.$signature['name'].'_compact.txt', $signature['textmin']);

        $signature['alt'] = $signature['name'];

        $signature['imagepath'] = $signature['name'].'_files/'.$signature['name'].'.png';
        eval("\$signaturehtm = \"".$template->get('editprofile_downloadsignature_htm')."\";");
        $zip->addFromString('Outlook/'.$signature['name'].'.htm', $signaturehtm);

        $signature['imagepath'] = $signature['name'].'_files/'.$signature['name'].'_compact.png';
        eval("\$signatureminhtm = \"".$template->get('editprofile_downloadsignaturemin_htm')."\";");
        $zip->addFromString('Outlook/'.$signature['name'].'_compact.htm', $signatureminhtm);

        $zip->addEmptyDir('Outlook/'.$signature['name'].'_files');
        $zip->addFile($image_location, 'Outlook/'.$signature['name'].'_files/'.$signature['name'].'.png');
        $zip->addFile($imagemin_location, 'Outlook/'.$signature['name'].'_files/'.$signature['name'].'_compact.png');

        $zip->addEmptyDir('Other');
        $signature['alt'] = $signature['text'];
        $signature['imagepath'] = 'data:image/jpg;base64,'.base64_encode(file_get_contents($image_location));
        eval("\$signaturehtm = \"".$template->get('editprofile_downloadsignature_htm')."\";");
        $zip->addFromString('Other/'.$signature['name'].'.htm', $signaturehtm);

        $signature['alt'] = $signature['textmin'];
        $signature['imagepath'] = 'data:image/jpg;base64,'.base64_encode(file_get_contents($imagemin_location));
        eval("\$signatureminhtm = \"".$template->get('editprofile_downloadsignaturemin_htm')."\";");
        $zip->addFromString('Other/'.$signature['name'].'_compact.htm', $signatureminhtm);

        $zip->close();

        unlink(realpath($image_location));

        $download = new Download();
        $download->set_real_path($filepath.$filename);
        $download->stream_file(true);
    }
    elseif($core->input['action'] == 'generatesignature' || $core->input['action'] == 'generatesignaturemin') {
        if($session->uid == 0) {
            redirect('users.php?action=login');
        }

        $user = new Users();
        if($core->input['action'] == 'generatesignaturemin') {
            $user->generate_image_sign(false, 180, 40, true);
        }
        else {
            $user->generate_image_sign(false);
        }
    }
    elseif($core->input['action'] == 'profile') {
        $lang->load('profile');

        if($core->input['do'] == 'edit') {
            if($session->uid == 0) {
                redirect('users.php?action=login');
            }

            $phones_index = array('mobile', 'mobile2', 'telephone', 'telephone2');
            foreach($phones_index as $val) {
                $phone[$val] = explode('-', $core->user[$val]);

                $phones[$val]['intcode'] = $phone[$val][0];
                $phones[$val]['areacode'] = $phone[$val][1];
                $phones[$val]['number'] = $phone[$val][2];
            }

            $checkboxes_index = array('mobileIsPrivate', 'mobile2IsPrivate', 'newFilesNotification', 'birthdayIsPrivate');
            foreach($checkboxes_index as $key) {
                if($core->user[$key] == 1) {
                    $checkedboxes[$key] = ' checked="checked"';
                }
            }

            $moduleslist = parse_moduleslist($core->user['defaultModule'], 'modules', true);
            $languageslist = parse_selectlist('language', '', $lang->get_languages(), $core->user['language']);

            if(empty($core->user['profilePicture'])) {
                if(isset($core->user['gender'])) {
                    if($core->user['gender'] == 1) {
                        $core->user['profilePicture'] = 'no_photo_female.gif';
                    }
                    else {
                        $core->user['profilePicture'] = 'no_photo_male.gif';
                    }
                }
                else {
                    $core->user['profilePicture'] = 'no_photo_male.gif';
                }
            }

            $user = new Users();
            $signature['text'] = $user->generate_text_sign();
            $signature['text'] = preg_replace("/\n/i", '<br />', $signature['text']).'</p>';

            if(isset($core->input['messagecode']) && $core->input['messagecode'] == 1) {
                $notification_message = '<div class="ui-state-highlight ui-corner-all" style="padding: 5px; margin-bottom:10px; font-weight: bold;">'.$lang->passwordhasexpired.'</div>';
            }

            $profile['uid'] = $core->user['uid'];

            eval("\$editprofilepage_profilepicform = \"".$template->get('popup_changeprofilepic')."\";");
            /* Get Help Video */
            $helpvideo = HelpVideos::get_data(array('alias' => 'how-to-generate-signature'));
            if(is_object($helpvideo)) {
                $helplinks['how-to-generate-signature'] = $helpvideo->parse_link();
            }

//            $timezones = DateTimeZone::listIdentifiers();
//            $timezoneslist = parse_selectlist('timeZone', 10, array_combine($timezones, $timezones), $core->user['timeZone']);

            eval("\$editprofilepage = \"".$template->get('editprofile')."\";");
            output_page($editprofilepage);
        }
        else {
            if($session->uid == 0) {
                redirect('users.php?action=login&referer='.base64_encode(DOMAIN.'/users.php?'.$_SERVER['QUERY_STRING']));
            }

            if(!$core->input['uid']) {
                $uid = $core->user['uid'];
            }
            else {
                $uid = $db->escape_string($core->input['uid']);
            }

            $profile_user = new Users($uid, false);
            $download_card_button = '<a style="cursor:pointer;" href="'.$core->settings['rootdir'].'/users.php?action=downloadvcard&uid='.$uid.'"><img src="./images/download_vcard.png" title="'.$lang->downloadcontact.'"/></a>';
            if($profile_user->get_errorcode() == 1) {
                redirect($_SERVER['HTTP_REFERER']);
            }
            $profile = $profile_user->get();

            if($profile_user->get_reportsto() != false) {
                $profile['reportsToName'] = $profile_user->get_reportsto()->get()['displayName'];
            }

            $profile['assistantName'] = $profile_user->get_assistant()->get()['displayName'];

            unset($profile['password'], $profile['salt'], $profile['loginKey']);

            if(!empty($profile['assistant'])) {
                $assistant_details = $lang->assistant.": <a href='users.php?action=profile&amp;uid={$profile[assistant]}'>{$profile[assistantName]}</a><br />";
            }

            $profile['position'] = $profile_user->get_positions();
            if(is_array($profile['position'])) {
                $profile['position'] = implode(', ', $profile['position']);
            }
            /* 	Prepare affiliates list */
            $main_affiliate = $profile_user->get_mainaffiliate();
            $profile['mainaffiliate']['id'] = $main_affiliate->get()['affid'];
            $profile['mainaffiliate']['name'] = $main_affiliate->get()['name'];

            $query2 = $db->query("SELECT a.affid, a.name, ae.isMain
									FROM ".Tprefix."affiliates a LEFT JOIN ".Tprefix."affiliatedemployees ae ON (ae.affid=a.affid)
									WHERE ae.uid='{$uid}'
									ORDER BY a.name ASC");
            $affiliates_counter = 0;
            while($affiliate = $db->fetch_array($query2)) {
                if(++$affiliates_counter > 2) {
                    $hidden_affiliates .= $break.$affiliate['name'];
                }
                else {
                    $useraffiliates .= $break.$affiliate['name'];
                }
                $break = '<br />';
            }

            if($affiliates_counter > 2) {
                $profile['affiliatesList'] = $useraffiliates.", <a href='#affiliates' id='showmore_affiliates_{$profile[uid]}' class='smalltext'>{$lang->readmore}</a> <span style='display:none;' id='affiliates_{$profile[uid]}'>{$hidden_affiliates}</span>";
            }
            else {
                $profile['affiliatesList'] = $useraffiliates;
            }

            /* Prepared segements list */
            $segments_query = $db->query("SELECT DISTINCT(ps.psid), ps.title FROM ".Tprefix."productsegments ps JOIN ".Tprefix."employeessegments es ON (es.psid=ps.psid) WHERE uid='{$uid}' ORDER BY title ASC");
            if($db->num_rows($segments_query) > 0) {
                while($segment = $db->fetch_assoc($segments_query)) {
                    $profile['segments'][] = $segment['title'];
                }
                $profile['segmentsList'] = implode('<br />', $profile['segments']);
            }
            else {
                $profile['segmentsList'] = $lang->na;
            }
            /* Prepared segements list */

            /* Prepare entities lists */
            $query3 = $db->query("SELECT DISTINCT(e.eid), companyName, type
									FROM ".Tprefix."entities e LEFT JOIN ".Tprefix."assignedemployees aemp ON (aemp.eid=e.eid)
									WHERE aemp.uid='{$uid}'
									ORDER BY e.companyName ASC");

            $customers_counter = 0;
            $suppliers_counter = 0;

            $cbreak = $sbreak = $profile['suppliersList'] = $profile['customersList'] = '';
            while($entity = $db->fetch_assoc($query3)) {
                $entity_obj = new Entities($entity['eid']);
                if($entity['type'] == 'c') {
                    if(++$customers_counter > 2) {
                        $hidden_customers .= $cbreak.$entity_obj->parse_link();
                    }
                    else {
                        $usercustomers .= $cbreak.$entity_obj->parse_link();
                    }
                    $cbreak = '<br />';

                    if($customers_counter > 2) {
                        $profile['customersList'] = $usercustomers.", <a href='#customers' id='showmore_customers_{$profile[uid]}' class='smalltext'>{$lang->readmore}</a> <span style='display:none;' id='customers_{$profile[uid]}'>{$hidden_customers}</span>";
                    }
                }
                else {
                    if(++$suppliers_counter > 2) {
                        $hidden_suppliers .= $sbreak.$entity_obj->parse_link();
                    }
                    else {
                        $usersuppliers .= $sbreak.$entity_obj->parse_link();
                    }
                    $sbreak = '<br />';

                    if($suppliers_counter > 2) {
                        $profile['suppliersList'] = $usersuppliers.", <a href='#suppliers' id='showmore_suppliers_{$profile[uid]}' class='smalltext'>{$lang->readmore}</a> <span style='display:none;' id='suppliers_{$profile[uid]}'>{$hidden_suppliers}</span>";
                    }
                }
                unset($entity_obj);
            }

            if(!empty($profile['building'])) {
                $profile['fulladdress'] .= $profile['building'].' - ';
            }

            $profile['fulladdress'] = $profile['building'].' - ';
            if(!empty($profile['postCode'])) {
                $profile['fulladdress'] .= $profile['postCode'].', ';
            }

            if(!empty($profile['addressLine1'])) {
                $profile['fulladdress'] .= $profile['addressLine1'].', ';
            }

            if(!empty($profile['addressLine2'])) {
                $profile['fulladdress'] .= $profile['addressLine2'].', ';
            }

            if(!empty($profile['city'])) {
                $profile['fulladdress'] .= $profile['city'].' - ';
            }

            if(!empty($profile['skype'])) {
                $profile['skype_output'] = " &nbsp; <a href='skype:{$profile[skype]}'><img src='./images/icons/skype.gif' alt='{$lang->skype}' border='0' /> ".$profile['skype'].'</a><br />';
            }

            $show_private_mobiles = true;
            if(!value_exists('affiliatedemployees', 'affid', $profile['mainaffiliate']['id'], "(canHr = 1 OR canAudit = 1) AND uid={$core->user[uid]}")) {
                if($core->user['uid'] != $profile['reportsTo']) {
                    if($core->user['uid'] != $profile['uid']) {
                        $show_private_mobiles = false;
                    }
                }
            }

            if(!empty($profile['mobile']) && ($profile['mobileIsPrivate'] == 0 || $show_private_mobiles === true)) {
                $profile['mobile_output'] = '+'.$profile['mobile'];
            }

            if(!empty($profile['mobile2']) && ($profile['mobile2IsPrivate'] == 0 || $show_private_mobiles === true)) {
                $profile['mobile2_output'] = '+';
                if(!empty($profile['mobile_output'])) {
                    $profile['mobile2_output'] = '/+';
                }
                else {
                    $profile['mobile_output'] = ' ';
                }
                $profile['mobile2_output'] .= $profile['mobile2'];
            }
            else {
                if(empty($profile['mobile_output'])) {
                    $profile['mobile_output'] = '-';
                }
            }

            if(!empty($profile['telephoneExtension']) && !empty($profile['telephone'])) {
                $profile['telephone'] .= '&times;'.$profile['telephoneExtension'];
            }

            if(!empty($profile['telephone2Extension']) && !empty($profile['telephone2'])) {
                $profile['telephone2'] .= '&times;'.$profile['telephone2Extension'];
            }


            if(empty($profile['profilePicture'])) {
                if(isset($profile['gender'])) {
                    if($profile['gender'] == 1) {
                        $profile['profilePicture'] = 'no_photo_female.gif';
                    }
                    else {
                        $profile['profilePicture'] = 'no_photo_male.gif';
                    }
                }
                else {
                    $profile['profilePicture'] = 'no_photo_male.gif';
                }
            }
            if($core->usergroup['canAdminCP'] == 1) {
                eval("\$profile_profilepicform = \"".$template->get('popup_changeprofilepic')."\";");

                $profile['picture'] = '<a id="showpopup_changeprofilepic" class="showpopup"><img id="profilePicture" src="'.$core->settings[rootdir].'/'.$core->settings[profilepicdir].'/'.$profile[profilePicture].'" alt="'.$profile['username'].'" border="0" style="cursor:pointer;"/></a>';
            }
            else {
                $profile['picture'] = '<img id="profilePicture" src="'.$core->settings[rootdir].'/'.$core->settings[profilepicdir].'/'.$profile[profilePicture].'" alt="'.$profile['username'].'" border="0" />';
            }
            $profile['country'] = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."countries WHERE coid='{$profile[country]}'"), 'name');

            $profile['fulladdress'] .= $profile['country'];

            $jobdescription_permissioned = array($profile['uid'], $profile['reportsTo'], $main_affiliate->get_generalmanager(), $main_affiliate->get_hrmanager(), $main_affiliate->get_supervisor(), 65);
            if(in_array($core->user['uid'], $jobdescription_permissioned)) {
                $profile['hrinfo'] = $profile_user->get_hrinfo();
                if(!empty($profile['hrinfo']['jobDescription'])) {
                    fix_newline($profile['hrinfo']['jobDescription']);
                    $jobdescription_section = '<hr /><div><div class="subtitle">'.$lang->jobdescription.'</div>'.$profile['hrinfo']['jobDescription'].'</div>';
                }
            }
            /* Get user job description - END */
            if($core->usergroup['canViewPrivateProfile'] == '1') {
                $leaves_toshow = 3;
                $lang->lastleaves = $lang->sprint($lang->lastleaves, $leaves_toshow);
                $query = $db->query("SELECT l.*, lt.name, lt.title FROM ".Tprefix."leaves l LEFT JOIN ".Tprefix."leavetypes lt ON (lt.ltid=l.type) WHERE l.uid='{$uid}' ORDER BY l.fromDate DESC LIMIT 0, {$leaves_toshow}");
                if($db->num_rows($query) > 0) {
                    while($leave = $db->fetch_array($query)) {
                        if(!empty($lang->{$leave['name']})) {
                            $leave['title'] = $lang->{$leave['name']};
                        }
                        $leave['type_output'] = $leave['title'];
                        if(date($core->settings['dateformat'], $leave['fromDate']) == date($core->settings['dateformat'], $leave['toDate'])) {
                            $leave_dates = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']).'/'.date($core->settings['timeformat'], $leave['toDate']);
                        }
                        else {
                            $leave_dates = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']).'/'.date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['toDate']);
                        }
                        $leaves .= '<li>'.$leave_dates.': '.$leave['type_output'].'</li>';
                    }
                }
                else {
                    $leaves = '<li>'.$lang->na.'</li>';
                }

                $logs_toshow = 3;
                $lang->lastlogs = $lang->sprint($lang->lastlogs, $logs_toshow);
                $query = $db->query("SELECT * FROM ".Tprefix."logs WHERE uid='{$uid}' ORDER BY date DESC LIMIT 0, {$logs_toshow}");
                if($db->num_rows($query) > 0) {
                    while($log_entry = $db->fetch_array($query)) {
                        $logs .= '<li>'.$log->explain($log_entry).'</li>';
                    }
                }
                else {
                    $logs = '<li>'.$lang->na.'</li>';
                }

                if(!empty($profile['lastVisit'])) {
                    $profile['lastVisit'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $profile['lastVisit']);
                }
                else {
                    $profile['lastVisit'] = $lang->na;
                }

                $profile_user = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."usergroups WHERE gid=(SELECT gid FROM ".Tprefix."users_usergroups WHERE isMain=1 AND uid={$profile[uid]})"));

                if($profile_user['canUseReporting'] == 1) {
                    $additional_where = getquery_entities_viewpermissions('', '', $profile['uid']);
                    $query = $db->query("SELECT r.quarter, r.year, s.companyName, a.name AS affiliate_name
											FROM ".Tprefix."reports r JOIN ".Tprefix."entities s ON (r.spid=s.eid) JOIN ".Tprefix."affiliates a ON (r.affid=a.affid)
											WHERE r.type='q' AND r.status='0'{$additional_where[extra]}
											ORDER BY r.initDate DESC
											LIMIT 0, 3");
                    if($db->num_rows($query) > 0) {
                        while($due_report = $db->fetch_array($query)) {
                            $due_reports_list .= "<li>Q{$due_report[quarter]} {$due_report[year]} - {$due_report[companyName]} / {$due_report[affiliate_name]}</li>";
                        }
                    }
                    else {
                        $due_reports_list = '<li>'.$lang->na.'</li>';
                    }

                    $query = $db->query("SELECT r.quarter, r.year, s.companyName, a.name AS affiliate_name
										FROM ".Tprefix."reports r JOIN ".Tprefix."entities s ON (r.spid=s.eid) JOIN ".Tprefix."affiliates a ON (r.affid=a.affid)
										WHERE  r.type='q' AND r.status='1'{$additional_where[extra]}
										ORDER BY r.finishDate DESC
										LIMIT 0, 3");
                    if($db->num_rows($query) > 0) {
                        while($last_report = $db->fetch_array($query)) {
                            $last_reports_list .= "<li>Q{$last_report[quarter]} {$last_report[year]} - {$last_report[companyName]} / {$last_report[affiliate_name]}</li>";
                        }
                    }
                    else {
                        $last_reports_list = '<li>'.$lang->na.'</li>';
                    }

                    $quarter = currentquarter_info();

                    $countall_current_quarterly = $db->fetch_field($db->query("SELECT count(*) as countall FROM ".Tprefix."reports r WHERE type='q' AND year='{$quarter[year]}' AND quarter='{$quarter[quarter]}'{$additional_where[extra]}"), 'countall');
                    if($countall_current_quarterly > 0) {
                        $countall_current_quarterly_unfinalized = $db->fetch_field($db->query("SELECT count(*) as countall FROM ".Tprefix."reports r WHERE type='q' AND year='{$quarter[year]}' AND quarter='{$quarter[quarter]}' AND status='0'{$additional_where[extra]}"), 'countall');
                    }
                    else {
                        $countall_current_quarterly_unfinalized = 0;
                    }
                }
                eval("\$userprofile_private = \"".$template->get('userprofile_private')."\";");
            }

            foreach($profile as $key => $val) {
                if(empty($val)) {
                    if(!in_array($key, array('middleName', 'reportsTo', 'reportsToName', 'skype_output'))) {
                        $profile[$key] = $lang->na;
                    }
                }
            }
            eval("\$profilepage = \"".$template->get('userprofile')."\";");
            output_page($profilepage);
        }
    }
    elseif($core->input['action'] == 'userslist') {
        if($session->uid == 0) {
            redirect('users.php?action=login');
        }
        define('PASSEXPIRE_EXCLUDE', 0);
        $lang->load('profile');

        if($core->input['view'] == 'thumbnails') {
            /* Users mosaic view - START */
            $change_view_icon = 'list_view.gif';
            $change_view_url = 'users.php?action=userslist&view=list';

            $query = $db->query("SELECT uid
								FROM ".Tprefix."users
								WHERE gid!='7' and profilePicture!=''");

            if($db->num_rows($query) > 0) {
                while($user = $db->fetch_assoc($query)) {
                    $user_obj = new Users($user['uid'], false);
                    $users[$user['uid']] = $user_obj->get();
                }
            }
            shuffle($users);
            foreach($users as $user) {
                if(isset($user['profilePicture']) && !empty($user['profilePicture']) && file_exists(($core->settings['profilepicdir'].$user['profilePicture']))) {
                    eval("\$userslistmosaic_pieces .= \"".$template->get('userslist_mosaic_piece')."\";");
                }
            }
            eval("\$userslistmosaic = \"".$template->get('userslist_mosaic')."\";");
            output_page($userslistmosaic);
            /*  Users mosaic view - END */
        }
        else {
            $change_view_icon = 'thumbnail_view.gif';
            $change_view_url = 'users.php?action=userslist&view=thumbnails';

            $sort_query = 'firstName ASC';
            if(isset($core->input['sortby'], $core->input['order'])) {
                $sort_query = $core->input['sortby'].' '.$core->input['order'];
            }
            $sort_url = sort_url();

            $limit_start = 0;
            if(isset($core->input['start'])) {
                $limit_start = $db->escape_string($core->input['start']);
            }
            if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
                $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
            }

            $multipage_where = 'gid!=7';
            /* Perform inline filtering - START */
            $filters_config = array(
                    'parse' => array('filters' => array('fullName', 'displayName', 'allenabledaffiliates', 'position', 'reportsTo')
                    ),
                    'process' => array(
                            'filterKey' => 'uid',
                            'mainTable' => array(
                                    'name' => 'users',
                                    'filters' => array('displayName' => 'displayName', 'reportsTo' => array('operatorType' => 'multiple', 'name' => 'reportsTo')),
                                    'extraSelect' => 'CONCAT(firstName, \' \', lastName) AS fullName',
                                    'havingFilters' => array('fullName' => 'fullName')
                            ),
                            'secTables' => array(
                                    'userspositions' => array(
                                            'filters' => array('position' => array('operatorType' => 'multiple', 'name' => 'posid')),
                                    ),
                                    'affiliatedemployees' => array(
                                            'filters' => array('allenabledaffiliates' => array('operatorType' => 'multiple', 'name' => 'affid')),
                                            'extraWhere' => 'isMain=1'
                                    )
                            )
                    )
            );

            $filter = new Inlinefilters($filters_config);
            $filter_where_values = $filter->process_multi_filters();

            $filters_row_display = 'show';
            if(is_array($filter_where_values)) {
                $filters_row_display = 'hide';
                $filter_where = 'AND u.'.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
                $multipage_where .= ' AND u.'.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
            }

            $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
            /* Perform inline filtering - END */

            $query = $db->query("SELECT DISTINCT(u.uid), u.*, aff.*, reportsTo AS supervisor, CONCAT(firstName, ' ', lastName) AS name, aff.name AS mainaffiliate, aff.affid,reportstotable.reportsToName
							FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees ae ON (u.uid=ae.uid) JOIN ".Tprefix."affiliates aff ON (aff.affid=ae.affid)
							JOIN (SELECT CONCAT(firstName, ' ', lastName) as reportsToName, uid as rtid FROM ".Tprefix."users)reportstotable ON u.reportsTo=reportstotable.rtid
                                                        WHERE gid!='7' AND isMain='1'
							{$filter_where}
							ORDER BY {$sort_query}
							LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

            $filters_cache = array();
            if($db->num_rows($query) > 0) {
                while($user = $db->fetch_assoc($query)) {
                    $class = alt_row($class);
                    /* $user['mainaffiliate'] = $db->fetch_field($db->query("SELECT aff.name as affiliatename
                      FROM ".Tprefix."affiliates aff LEFT JOIN ".Tprefix."affiliatedemployees ae ON (ae.affid=aff.affid)
                      WHERE ae.uid='{$user[uid]}' AND isMain='1'"), 'affiliatename'); */

                    $userpositions = $hiddenpositions = $break = '';

                    $query2 = $db->query("SELECT p.* FROM ".Tprefix."positions p LEFT JOIN ".Tprefix."userspositions up ON (up.posid=p.posid) WHERE  up.uid='{$user[uid]}' ORDER BY p.name ASC");
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

                    //  list($user['reportsToName']) = get_specificdata('users', array('CONCAT(firstName, \' \', lastName) as reportsToName'), '0', 'reportsToName', '', 0, "uid='{$user[reportsTo]}'");

                    $skypelink = '';
                    if(isset($user['skype']) && !empty($user['skype'])) {
                        $skypelink = "<a href='skype:{$user[skype]}'><img src='./images/icons/skype.gif' alt='{$lang->skype}' border='0' /></a>";
                    }
                    //$tooltip = $lang->extension.':'.$user['extension'].'<br />'.$lang->mobile.':'.$user['mobile'];
                    eval("\$usersrows .= \"".$template->get('userslist_row')."\";");
                }

                $multipages = new Multipages('users u JOIN '.Tprefix.'affiliatedemployees ae ON (u.uid=ae.uid) JOIN '.Tprefix.'affiliates aff ON (aff.affid=ae.affid)', $core->settings['itemsperlist'], $multipage_where, 'u.uid');
                $usersrows .= "<tr><td colspan='6'>".$multipages->parse_multipages()."</td></tr>";
            }
            else {
                $usersrows = "<tr><td colspan='6' style='text-align:center;'>".$lang->nomatchfound."</td></tr>";
            }

            eval("\$userslist = \"".$template->get('userslist')."\";");
            output_page($userslist);
        }
    }
    elseif($core->input['action'] == 'get_popup_loginbox') {
        eval("\$loginbox = \"".$template->get('popup_loginbox')."\";");
        echo $loginbox;
    }
    elseif($core->input['action'] == 'downloadvcard') {
        $user = new Users($core->input['uid']);
        if($user->gid == 7) {
            exit;
        }
        $user_data = vCard::get_userdata($core->input['uid']);
        if(is_array($user_data) && !is_empty($user_data)) {
            $vcard = new vCard($user_data);
            if(is_object($vcard)) {
                $vcard->download();
            }
        }
    }
    elseif($core->input['action'] == 'downloadallempvcard') {
        $users = Users::get_users(array('gid' => 7), array('operators' => array('gid' => 'NOT IN'), 'returnarray' => true));
        if(is_array($users)) {
            $vcard_cont = '';
            foreach($users as $user) {
                $user_data = vCard::get_userdata($user->uid);
                if(is_array($user_data) && !is_empty($user_data)) {
                    $vcard = new vCard($user_data);
                    if(is_object($vcard)) {
                        $vcard_cont.= $vcard->get_vcard();
                    }
                }
            }
            if(!empty($vcard_cont)) {
                $glob_vcard = new vCard($vcard_cont);
                if(is_object($glob_vcard)) {
                    $glob_vcard->set_vcardname('ORKILA Employees');
                    $glob_vcard->download();
                }
            }
        }
    }
    else {
        $session->name_phpsession(COOKIE_PREFIX.'login');
        $session->start_phpsession();
        $session->regenerate_id_phpsession(true);
        $token = $session->generate_token();
        $session->set_phpsession(array('token' => $token));

        if(isset($core->input['referer']) && !empty($core->input['referer'])) {
            $lastpage = base64_decode($db->escape_string($core->input['referer']));
        }
        else {
            $lastpage = DOMAIN;
        }

        /* Get Help Video */
        $helpvideo = HelpVideos::get_data(array('alias' => 'how-to-reset-password'));
//        $helplink = $helpvideo->parse_link();

        eval("\$loginpage = \"".$template->get('loginpage')."\";");
        output_page($loginpage);
    }
}
?>