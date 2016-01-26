<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Entity profile
 * $module: profiles
 * $id: entityprofile.php
 * Created:		@zaher.reda		September 28, 2010 | 11:08 AM
 * Last Update: @tony.assaad	April 21, 2011 | 03:14 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if(!$core->input['action']) {
    if(!isset($core->input['eid']) || empty($core->input['eid'])) {
        redirect($_SERVER['HTTP_REFERER']);
    }

    $lang->load('contents_meta');
    $lang->load('contents_addentities');

    $eid = $db->escape_string($core->input['eid']);
    $entity = new Entities($eid, '', false);
    $profile = $entity->get();

    /* Market Data --START */
    $filter_where = 'eid IN ('.$eid.')';
    if($core->usergroup['profiles_canAddMkIntlData'] == 1) {
        $customer_obj = new Customers($eid, '', false);
        $customer_type = $customer_obj->get_customertype();

        $addmarketdata_link = '<div style="float: right;" title="'.$lang->addmarket.'"><a href="#popup_profilesmarketdata" id="showpopup_profilesmarketdata" class="showpopup"><img alt="'.$lang->addmarket.'" src="'.$core->settings['rootdir'].'/images/icons/edit.gif" /></a></div>';
        $array_data = array('module' => 'profiles', 'elemtentid' => $eid, 'fieldlabel' => $lang->product, 'action' => 'do_addmartkerdata', 'modulefile' => 'entityprofile');

        /* to be replacing the below variables */
        $module = 'profiles';
        $elemtentid = $eid;
        $elementname = 'marketdata[cid]';
        $action = 'do_addmartkerdata';
        $modulefile = 'entityprofile';

        /* View detailed market intelligence box --START */
        $maktintl_mainobj = new MarketIntelligence();

        $miprofile = $maktintl_mainobj->get_miprofconfig_byname('latestcustomersumbyproduct');
        $miprofile['next_miprofile'] = 'allprevious';

        $maktintl_objs = $maktintl_mainobj->get_marketintelligence_timeline(array('cid' => $eid), $miprofile);
        if(is_array($maktintl_objs)) {
            foreach($maktintl_objs as $mktintldata) {
                $mktintldata['tlidentifier']['id'] = 'tlrelation-'.$eid;
                $mktintldata['tlidentifier']['value'] = array('cid' => $eid);

                $detailmarketbox .= $maktintl_mainobj->parse_timeline_entry($mktintldata, $miprofile, '', '');
            }
        }
        /* View detailed market intelligence box --END */
    }
    $rowid = intval($core->input['value']) + 2;

    /* Market Data--END */
    //$profile = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."entities WHERE eid={$eid}"));
    if(!empty($profile['building'])) {
        $profile['fulladdress'] .= $profile['building'].' - ';
    }

    //$profile['fulladdress'] = $profile['building'];
    if(!empty($profile['postCode'])) {
        $profile['fulladdress'] .= $profile['postCode'].', ';
    }

    if(!empty($profile['addressLine1'])) {
        $profile['fulladdress'] .= $profile['addressLine1'].' ';
    }

    if(!empty($profile['addressLine2'])) {
        $profile['fulladdress'] .= $profile['addressLine2'].', ';
    }

    if(!empty($profile['city'])) {
        $profile['fulladdress'] .= $profile['city'].' - ';
    }

    if(!empty($profile['floor'])) {
        $profile['fulladdress'] .= 'F '.$profile['floor'].' - ';
    }

    if((empty($profile['phone1'])) && (empty($profile['phone2']))) {
        $phone = $lang->na;
    }
    elseif(empty($profile['phone2'])) {
        $phone = $profile['phone1'];
    }
    else {
        $phone = $profile['phone1'].'/'.$profile['phone2'];
    }

    if((empty($profile['fax1'])) && (empty($profile['fax2']))) {
        $fax = $lang->na;
    }
    elseif(empty($profile['fax2'])) {
        $fax = $profile['fax1'];
    }
    else {
        $fax = $profile['fax1'].'/'.$profile['fax2'];
    }

    if(empty($profile['logo'])) {
        $profile['logo'] = './images/no_logo_entity.gif';
    }
    else {
        $profile['logo'] = $core->settings['rootdir'].'/'.$core->settings['entitylogodir'].'/'.$profile['logo'];
    }

    $profile['country'] = $entity->get_country()->name;

    $profile['fulladdress'] .= $profile['country'];

    foreach($profile as $key => $val) {
        if(empty($val)) {
            $profile[$key] = $lang->na;
        }
    }

    if(!empty($profile['website'])) {
        $profile['website'] = '<a href="'.fix_url($profile['website']).'" target="_blank">'.$profile['website'].'</a>';
    }
    else {
        $profile['website'] = '';
    }

    $representative_query = $db->query("SELECT *
                                        FROM ".Tprefix."representatives r JOIN ".Tprefix."entitiesrepresentatives er ON (er.rpid=r.rpid)
                                        WHERE er.eid={$eid}");
    while($representative = $db->fetch_assoc($representative_query)) {
        $representativelist .= '<a href="#" id="contactpersoninformation_'.base64_encode($representative['rpid']).'_profiles/entityprofile_loadpopupbyid">'.$representative['name'].'</a> - <a href = "mailto:'.$representative['email'].'">'.$representative['email'].'</a><br />';
    }

    $segment_query = $db->query("SELECT * FROM ".Tprefix."entitiessegments es JOIN ".Tprefix." productsegments ps ON (es.psid=ps.psid) WHERE es.eid={$eid}");
    while($segment = $db->fetch_assoc($segment_query)) {
        $segmentlist .= '<a href="index.php?module=profiles/segmentprofile&id='.$segment['psid'].'" target="_blank">'.$segment['title'].'<br />';
    }

    $affiliate_query = $db->query("SELECT *
                                    FROM ".Tprefix."affiliates a LEFT JOIN ".Tprefix."affiliatedentities ae ON (ae.affid=a.affid)
                                    WHERE ae.eid={$eid}
                                    ORDER BY a.name ASC");
    while($affiliate = $db->fetch_array($affiliate_query)) {
        $listitem['link'] = 'index.php?module=profiles/affiliateprofile&affid='.$affiliate['affid'];
        $listitem['title'] = $affiliate['name'];
        $listitem['divhref'] = 'affiliate';
        $listitem['loadiconid'] = 'loadentityusers_'.$affiliate['affid'].'_'.$eid;

        eval("\$affiliateslist .= \"".$template->get('profiles_affliatesentities_inlinelistitem')."\";");
    }

    $entityusers_query = $db->query("SELECT *, CONCAT(firstName, ' ', lastName) AS fullname
						FROM ".Tprefix."assignedemployees ae JOIN ".Tprefix."users u ON (ae.uid=u.uid)
						WHERE ae.eid={$eid} AND u.gid!=7
						GROUP BY u.username
						ORDER BY u.username ASC");
    $entityusers_counter = 0;

    while($entityusers = $db->fetch_array($entityusers_query)) {
        if(++$entityusers_counter > $core->settings['itemsperlist']) {
            $hidden_entityusers .= "<li><a href='./users.php?action=profile&uid={$entityusers[uid]}' target='_blank'>{$entityusers['fullname']}</a></li>";
        }
        elseif($entityusers_counter == $core->settings['itemsperlist']) {
            $shown_entityallusers .= "<li><a href='./users.php?action=profile&uid={$entityusers[uid]}' target='_blank'>{$entityusers['fullname']}</a>";
        }
        else {
            $shown_entityallusers .= "<li><a href='./users.php?action=profile&uid={$entityusers[uid]}' target='_blank'>{$entityusers['fullname']}</a></li>";
        }
    }

    if($entityusers_counter > $core->settings['itemsperlist']) {
        $entityallusers = "<ul style='list-style:none; padding:2px; margin-top:0px;'>".$shown_entityallusers.", <a href='#entityusers' id='showmore_entityusers_{$entityusers[uid]}' class='smalltext'>read more</a></li> <span style='display:none;' id='entityusers_{$entityusers[uid]}'>{$hidden_entityusers}</span></ul>";
    }
    else {
        $entityallusers = '<ul style="list-style:none; padding:2px;margin-top:0px;">'.$shown_entityallusers.'</ul>';
    }

    if($profile['type'] == 's') {
        /* Load supplier's quarterly reports - Start */
        $report_lang = $lang->lastfinalized;
        $report_query = $db->query("SELECT *, a.name AS affiliate_name FROM ".Tprefix."reports r
                                    LEFT JOIN ".Tprefix."affiliates a ON (a.affid=r.affid)
                                    WHERE r.spid={$eid} AND r.type='q' AND status = 1
                                    ORDER BY r.finishDate DESC
                                    LIMIT 0, 4");

        $reports_counter = 0;
        $finalized_reports = '';
        if($db->num_rows($report_query)) {
            while($report = $db->fetch_assoc($report_query)) {
                if($report['status'] == 1) {
                    $icon_locked = '';
                    if($report['isLocked'] == 1) {
                        $icon_locked = '_locked';
                    }
                    $report_icon = '<a href="index.php?module=reporting/preview&referrer=list&amp;affid='.$report['affid'].'&amp;spid='.$report['spid'].'&amp;quarter='.$report['quarter'].'&amp;year='.$report['year'].'"><img src="images/icons/report'.$icon_locked.'.gif" alt="'.$report['status'].'" border="0"/></a>';
                }

                $finalized_reports .= '<tr><td>'.$report['affiliate_name'].'</td><td>Q'.$report['quarter'].'</td><td>'.$report['year'].'</td><td style="width:1%; text-align:right;">'.$report_icon.'</td></tr>';
            }
            $finalized_reports .= '<tr><td colspan="4"><a href="index.php?module=reporting/list&filterby=spid&filtervalue='.$eid.'">Read more</a></tr></td>';
            eval("\$reports_section = \"".$template->get('profiles_entityprofile_reports')."\";");
        }

        /* ++$reports_counter;

          if($reports_counter < 3) {
          $shown_reports .= '<li><a href="index.php?module=reporting/preview&referrer=list&rid='.$reports['rid'].'" target="_blank">Q'.$reports['quarter'].'/'.$reports['year'].' - '.$reports['affiliate_name'].'</a></li>';
          }
          elseif($reports_counter == 3)
          {
          $shown_reports .= '<li><a href="index.php?module=reporting/preview&referrer=list&rid='.$reports['rid'].'" target="_blank">Q'.$reports['quarter'].'/'.$reports['year'].' - '.$reports['affiliate_name'].'</a>';
          }
          elseif($reports_counter > 3)
          {
          break;
          }
          }

          if($reports_counter > 3) {
          $reports_list = "<ul style='list-style:none; padding:2px;margin-top:0px;'>".$shown_reports.", <a href='index.php?module=reporting/list&filterby=spid&filtervalue={$eid}' class='smalltext'>read more</a></li></ul>";
          }
          else
          {
          $reports_list = '<ul style="list-style:none; padding:2px;margin-top:0px;">'.$shown_reports.'</ul>';
          }

          if(!empty($reports_list)) {
          $reports_section = '<tr><td  valign="top" style="padding:10px;"><span class="subtitle">'.$report_lang.'</span><br />'.$reports_list.'</td><td valign="top" style="padding:10px;">&nbsp;</td></tr>';
          }
         */
        /* Load supplier's quarterly reports - End */

        /* Load the locations  - Start */
        $locations = $entity->get_locations();
        if(is_array($locations)) {
            foreach($locations as $location) {
                $location->city = $location->get_city();
                $locationslist .= '<tr><td>'.$lang->{$location->locationType}.'</td><td>'.$location->addressLine1.'<br />'.$location->addressLine2.'</td><td>'.$location->city->name.'</td><td>'.$location->city->get_country()->get_displayname().'</td></tr>';
            }
            eval("\$products_section = \"".$template->get('profiles_entityprofile_locations')."\";");
            unset($location);
        }
        /* Load the locations  - END */
        /* Load supplier's products list - Start */
        $products = $entity->get_products();
        if(is_array($products)) {
            foreach($products as $pid => $product) {
                $defaultchemfunc = $product->get_defaultchemfunction();
                $defautcfpid = $defaultchemfunc->get_id();
                if(!empty($defautcfpid)) {
                    $defaultchemfunc_output = $defaultchemfunc->get_chemicalfunction()->title.' - '.$defaultchemfunc->get_segmentapplication()->title.' - '.$defaultchemfunc->get_segment()->title;
                }
                else {
                    $defaultchemfunc_output = $product->get_genericproduct_legacy()['title'].' - '.$product->get_productsegment();
                }
                $productslist .= '<tr><td style="width:50%;">'.$product->parse_link().'</td><td>'.$defaultchemfunc_output.'</td></tr>';
                unset($defaultchemfunc_output);
            }
            eval("\$products_section .= \"".$template->get('profiles_entityprofile_products')."\";");
        }
        /* Load supplier's products list - End */

        /* Prepare the private part of the profile - Start */
        if($core->usergroup['profiles_canViewEntityPrivateProfile'] == '1') {
            /* Get related files - End */
            $files_query = $db->query("SELECT *, f.title AS file_title FROM ".Tprefix."files f LEFT JOIN ".Tprefix."fileversions fv ON (f.fid=fv.fid)
									  WHERE f.referenceId={$eid} AND f.reference='eid' ORDER BY fv.timeLine DESC");
            $files_counter = 0;
            while($files = $db->fetch_array($files_query)) {
                $time = date($core->settings['dateformat'], $files['timeLine']);
                if(++$files_counter > 3) {
                    $hidden_files .= "<li><a href='index.php?module=profiles/entityprofile&action=download&fvid={$files[fvid]}' target='_blank'>{$files['file_title']}({$time})</a></li>";
                }
                elseif($files_counter == 3) {
                    $shown_files .= "<li><a href='index.php?module=profiles/entityprofile&action=download&fvid={$files[fvid]}' target='_blank'>{$files['file_title']}({$time})</a>";
                }
                else {
                    $shown_files .= "<li><a href='index.php?module=profiles/entityprofile&action=download&fvid={$files[fvid]}' target='_blank'>{$files['file_title']}({$time})</a></li>";
                }
            }

            if($files_counter > 3) {
                $files_list = "<ul style='list-style:none; padding:2px;margin-top:0px;'>".$shown_files.", <a href='#files' id='showmore_files_{$files[fvid]}' class='smalltext'>read more</a></li> <span style='display:none;' id='files_{$files[fvid]}'>{$hidden_files}</span></ul>";
            }
            else {
                $files_list = '<ul style="list-style:none; padding:2px;margin-top:0px;">'.$shown_files.'</ul>';
            }

            if(!empty($files_list)) {
                $entityprofile_private = '<tr><td  valign="top" style="padding:10px;"><span class="subtitle">'.$lang->lastfiles.'</span><br />'.$files_list.'</td><td valign="top" style="padding:10px;">&nbsp;</td></tr>';
            }
            /* Get related files - End */

            /* Get sent reports - Start */
            $sentreports_query = $db->query("SELECT * FROM ".Tprefix."reports WHERE isSent=1 AND spid='{$eid}' ORDER BY year DESC");
            while($ready_report = $db->fetch_assoc($sentreports_query)) {
                $ready_affids[] = $ready_report['affid'];
                $ready_reports_link[$ready_report['affid']] = $core->settings['rootdir'].'/index.php?module=reporting/preview&referrer=direct&identifier='.base64_encode(serialize(array('year' => $current_report_details['year'], 'quarter' => $current_report_details['quarter'], 'spid' => $current_report_details['eid'], 'affid' => $ready_affids)));
                $sentreportslist .= '<tr><td style="width:50%;"><a href="'.$ready_reports_link[$ready_report['affid']].'">sent reports Q'.$ready_report['quarter'].' </a></td><td>'.$ready_report['year'].'</td></tr>';
            }
            eval("\$sent_reports .= \"".$template->get('profiles_entityprofile_sentreports')."\";");

            if(!empty($sent_reports)) {
                $entityprofile_private .= '<tr><td  valign="top" style="padding:10px;"><span class="subtitle">'.$lang->sentqreports.'</span><br />'.$sent_reports.'</td><td valign="top" style="padding:10px;">&nbsp;</td></tr>';
            }
            /* Get sent reports - End */
        }
        /* Prepare the private part of the profile - End */

        /* Parse Maturity Section - START */
        $maturity_section = '<div id="rml_maindiv"><span class="subtitle">'.$lang->rmlevel.'</span><br />'.get_rml_bar($eid).'</div>';
        if($core->usergroup['profiles_canUpdateRML'] == 1) {
            $header_rmljs = '$(document).on("click",".rmlselectable", function() {
					if(sharedFunctions.checkSession() == false) {
						return;
					}
                        sharedFunctions.requestAjax("post", "index.php?module=profiles/entityprofile&action=do_updaterml", "target="+$(this).attr("id")+"&eid="+$("#eid").val(), "rml_bars", "rml_bars", "html");
                    });

                    $(document).on("hover",".rmlselectable", function() {
                        $(this).prevAll("div").toggleClass("rmlhighlight");
						$(this).toggleClass("rmlhighlight");
                    });';
        }
        /* Parse Maturity Section - END */

        /* Parse Rating Section - START */
        $maxstars = 5;
        $rating_section = '';
        $inpagescriptforrating;
        $readonlyratings = true;
        if(count($core->user['suppliers']) > 0) {
            if(isset($core->user['suppliers']['eid'][$eid])) { /* if the supplier is for the core user */
                $readonlyratings = false;
            }

            $ratingcriteria_query = $db->query('SELECT * FROM '.Tprefix.'entities_ratingcriteria');
            $criteriaandstars = '';

            if($db->num_rows($ratingcriteria_query) > 0) {
                while($criterion = $db->fetch_assoc($ratingcriteria_query)) {
                    $criterion['currentrating'] = get_current_rating($eid, $criterion['ercid']);

                    if(isset($lang->{$criterion['name']})) {
                        $criterion['title'] = $lang->{$criterion['name']};
                    }

                    $criteriaandstars .= '<div class="evaluation_criterium" name="'.$criterion['ercid'].'"><div class="criterium_name" style="display:inline-block; width:30%; padding: 2px;">'.$criterion['title'].'</div>';
                    $criteriaandstars .= '<div class="ratebar" style="width:40%; display:inline-block;">';

                    if($readonlyratings) {
                        $criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="'.$criterion['currentrating'].'"></div>';
                    }
                    else {
                        $criteriaandstars .= '<input type="range" min="0" max="'.$maxstars.'" value="'.$criterion['currentrating'].'" step="1" id="rating_'.$criterion['ercid'].'" class="ratingscale">';
                        $criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-resetable="false" data-rateit-backingfld="#rating_'.$criterion['ercid'].'" data-rateit-value="'.$criterion['currentrating'].'"></div>';
                    }
                    $criteriaandstars .= '</div></div>';
                }
            }

            if(!$readonlyratings) {
                $header_ratingjs = '$(".rateit").click(function() {
					if(sharedFunctions.checkSession() == false) {
						return;
					}
					var targetid = $(this).parent().parent().attr("name");
					var returndiv = "";

					sharedFunctions.requestAjax("post", "index.php?module=profiles/entityprofile&action=do_rateentity", "target="+targetid+"&value="+$("#rating_"+targetid).val()+"&eid="+$("#eid").val(), returndiv, returndiv, "html");
				});';
            }
            else {
                $header_ratingjs = '';
            }
            $rating_section = '<div>'.$criteriaandstars.'</div><hr>';
        }
        /* Parse Rating Section - END */

        if($core->usergroup['profiles_canViewContractInfo'] == 1) {
            $entity_isCentralPurchase_output = '<img src="images/invalid.gif" border="0">';
            if($entity->isCentralPurchase == 1) {
                $entity_isCentralPurchase_output = '<img src="images/valid.gif" border="0">';
            }
            $contracted_objs = $entity->get_contractedcountires();
            $check_fields = array('isExclusive', 'selectiveProducts', 'isAgent', 'isDistributor');
            if(is_array($contracted_objs)) {
                foreach($contracted_objs as $eccid => $contractedcountry) {
                    $contractedcountry->displayName = $contractedcountry->get_country()->get_displayname();
                    foreach($check_fields as $check_field) {
                        $check_field_output = $check_field.'_output';
                        $contractedcountry->{$check_field_output} = '<img src="images/invalid.gif" border="0">';
                        if($contractedcountry->{$check_field} == 1) {
                            $contractedcountry->{$check_field_output} = '<img src="images/valid.gif" border="0">';
                        }
                    }

                    eval("\$profilepage_contractual_rows .= \"".$template->get('profiles_entityprofile_contractinfo_row')."\";");
                }

                eval("\$contractinfo_section = \"".$template->get('profiles_entityprofile_contractinfo')."\";");
            }
        }
    }
    elseif($profile['type'] == 'c') {
        $report_lang = $lang->lastvisited;
        $visitreport_query = $db->query("SELECT *, CONCAT(firstName, ' ', lastName) AS employeename
                                        FROM ".Tprefix." visitreports r JOIN ".Tprefix."users u ON (u.uid=r.uid)
                                        WHERE r.cid={$eid}
                                        ORDER BY r.date DESC
                                        LIMIT 0, 4");

        $reports_counter = 0;
        while($report = $db->fetch_array($visitreport_query)) {
            ++$reports_counter;
            if($reports_counter < 3) {
                $shown_reports .= "<li><a href='index.php?module=crm/previewvisitreport&amp;referrer=list&amp;vrid={$report[vrid]}' target='_blank'>".parse_calltype($report['type']).' / '.date($core->settings['dateformat'], $report['date'])." - {$report[employeename]}</a></li>";
            }
            elseif($reports_counter == 3) {
                $shown_reports .= "<li><a href='index.php?module=crm/previewvisitreport&amp;referrer=list&amp;vrid={$report[vrid]}' target='_blank'>".parse_calltype($report['type']).' / '.date($core->settings['dateformat'], $report['date'])." - {$report[employeename]}</a>";
            }
            else {
                break;
            }
        }

        if($reports_counter > 3) {
            $reports_list = "<ul style='list-style:none; padding:2px;margin-top:0px;'>".$shown_reports."<br /> <a href='index.php?module=crm/listvisitreports&filterby=cid&filtervalue={$eid}' class='smalltext'>Read more</a></li></ul>";
        }
        else {
            $reports_list = '<ul style="list-style:none; padding:2px;margin-top:0px;">'.$shown_reports.'</ul>';
        }
    }
    /* parse Minites Of Meetings --START */
    if($core->usergroup['canUseMeetings'] == 1) {
        $lang->load('meetings_meta');
        $meetings = $entity->get_meetings();
        if(is_array($meetings)) {
            foreach($meetings as $mtid => $meeting_obj) {
                if(!$meeting_obj->can_viewmeeting()) {
                    continue;
                }
                $meeting = $meeting_obj->get();
                $meeting['businessMgr'] = $meeting_obj->get_createdby()->get()['displayName'];
                $meeting['title'] = ucwords($meeting['title']);
                if(($core->usergroup['canViewAllSupp'] == 0 && $meeting['isPublic'] == 0) && $meeting['createdBy'] != $core->user['uid']) {
                    continue;
                }

                if(!empty($meeting['fromDate'])) {
                    $meeting['fromDate_output'] = date($core->settings['dateformat'], $meeting['fromDate']);
                }
                eval("\$meetingslist .= \"".$template->get('profiles_entityprofile_meetings_row')."\";");
            }
            eval("\$meetings_section  = \"".$template->get('profiles_entityprofile_meetings')."\";");
        }
    }
    /* parse Minites Of Meetings --END */

    if($core->usergroup['profiles_canAddMkIntlData'] == 1) {
        $brandsproducts = $entity->get_brandsproducts();
        $output = '';
        if(is_array($brandsproducts)) {
            foreach($brandsproducts as $brandproduct_obj) {
                $brandproduct = $brandproduct_obj->get();
                $brandproduct_brand = $brandproduct_obj->get_entitybrand();
                $brandproduct_productype = $brandproduct_obj->get_endproduct();
                $options[$brandproduct_obj->ebpid] = $brandproduct_brand->parse_link();
                if(!is_object($brandproduct_productype)) {
                    $brandproduct_productype = new EntBrandsProducts();
                    $brandproduct_productype->title = $lang->unspecified;
                }
                else {
                    $characteristic_output = '';
                    $characteristic = $brandproduct_obj->get_charactersticvalue();
                    if(is_object($characteristic)) {
                        $characteristic_output = ' <small>('.$characteristic->get_displayname().')</small>';
                    }
                    $options[$brandproduct_obj->ebpid] .= ' - '.$brandproduct_productype->parse_link();
                }

                eval("\$brandsendproducts .= \"".$template->get('profiles_entityprofile_brandsproducts')."\";");
            }

            $entitiesbrandsproducts_list = parse_selectlist('marketdata[ebpid]', 7, $options, '');
        }

        $endproducttypes = EndProducTypes::get_endproductypes();
        if(is_array($endproducttypes)) {
            foreach($endproducttypes as $productype) {
                $value = $productype->title;
                $pplication = $productype->get_application()->parse_link();
                if($pplication !== null) {
                    $value .=' - '.$pplication;
                }
                $parent = $productype->get_endproducttype_chain();
                if(!empty($parent)) {
                    $values[$productype->eptid] = $parent.' > '.$value;
                }
                else {
                    $values[$productype->eptid] = $value;
                }
            }
            asort($values);
            foreach($values as $key => $value) {
                $checked = $rowclass = '';
                $endproducttypes_list .= ' <tr class="'.$rowclass.'">';
                $endproducttypes_list .= '<td><input id="producttypefilter_check_'.$key.'" type="checkbox"'.$checked.' value="'.$key.'" name="entitybrand[endproducttypes]['.$key.']">'.$value.'</td><tr>';
            }
        }


        /* parse visit report --START */
        $visitreport_objs = CrmVisitReports::get_visitreports(array('uid' => $core->user['uid'], 'cid' => $eid, 'isDraft' => 1), array('order' => array('by' => 'date', 'sort' => 'DESC'), 'returnarray' => 1));
        if(is_array($visitreport_objs)) {
            $profiles_mincustomervisit_title = $lang->visitreport;
            $profiles_mincustomervisit = parse_selectlist('marketdata[vrid]', 7, $visitreport_objs, '', '', '', array('blankstart' => 1));
        }
        /* parse visit report --END */

        unset($endproducttypes);
        $packaging_list = parse_selectlist('marketdata[competitor]['.$rowid.'][packaging]', 7, Packaging::get_data('name IS NOT NULL'), '', '', '', array('blankstart' => 1));
        $incoterms_list = parse_selectlist('marketdata[competitor]['.$rowid.'][incoterms]', 8, Incoterms::get_data('titleAbbr IS NOT NULL'), '', '', '', array('blankstart' => 1));
        $saletype_list = parse_selectlist('marketdata[competitor]['.$rowid.'][saletype]', 8, SaleTypes::get_data('stid IN (1,4)'), '', '', '', array('blankstart' => 1));
        $samplacquire = parse_radiobutton('marketdata[competitor]['.$rowid.'][isSampleacquire]', array(1 => 'yes', 0 => 'no'), '', true);
        $css['display']['chemsubfield'] = 'none';
        $css['display']['basicingsubfield'] = 'none';
        $css['display']['product'] = 'none';
//        eval("\$profiles_michemfuncproductentry = \"".$template->get('profiles_michemfuncsubstancentry')."\";");
//        eval("\$profiles_minproductentry = \"".$template->get('profiles_michemfuncproductentry')."\";");
//
        $mkdchem_rowid = 0;
        eval("\$profiles_michemfuncproductentry_row = \"".$template->get('profiles_michemfuncsubstancentry')."\";");
        eval("\$profiles_michemfuncproductentry = \"".$template->get('profiles_michemfuncsubstancentry_rows')."\";");

        $mkdprod_rowid = 0;
        eval("\$profiles_minproductentry_row = \"".$template->get('profiles_michemfuncproductentry')."\";");
        eval("\$profiles_minproductentry = \"".$template->get('profiles_michemfuncproductentry_rows')."\";");

        $mkdbing_rowid = 0;
        eval("\$profiles_mibasicingredientsentry_row = \"".$template->get('profiles_mibasicingredientsentry')."\";");
        eval("\$profiles_mibasicingredientsentry = \"".$template->get('profiles_mibasicingredientsentry_rows')."\";");
        eval("\$popup_marketdata = \"".$template->get('popup_profiles_marketdata')."\";");
        $characteristics = ProductCharacteristicValues::get_data(null, array('returnarray' => true));
        $characteristics_list = parse_selectlist('entitybrand[pcvid]', 4, $characteristics, null, 0, null, array('blankstart' => true));
        eval("\$popup_createbrand = \"".$template->get('popup_createbrand')."\";");
    }

    eval("\$profilepage = \"".$template->get('profiles_entityprofile')."\";");
    output_page($profilepage);
}
else {
    if($core->input['action'] == 'download') {
        $path = $core->settings['rootdir'].'/uploads/entitiesfiles/';
        $download = new Download('fileversions', 'name', array('fvid' => $core->input['fvid']), $path);
        $download->download_file();
    }
    elseif($core->input['action'] == 'getentityusers' || $core->input['action'] == 'getallusers') {
        $eid = $db->escape_string($core->input['eid']);
        $query_string = '';
        if($core->input['action'] == 'getentityusers') {
            $query_string = " AND ae.affid = '".$db->escape_string($core->input['affid'])."'";
        }

        $entityusers_query = $db->query("SELECT *, CONCAT(firstName, ' ', lastName) AS fullname
							FROM ".Tprefix."assignedemployees e JOIN ".Tprefix."users u ON (e.uid=u.uid) JOIN ".Tprefix."affiliatedentities ae ON (ae.affid=e.affid)
							WHERE e.eid={$eid}{$query_string} AND u.gid!=7
							GROUP BY u.username
							ORDER BY u.firstName ASC");
        if($db->num_rows($entityusers_query) > 0) {
            while($entityusers = $db->fetch_array($entityusers_query)) {
                $entityusers_list .= "<li><a href='./users.php?action=profile&uid={$entityusers[uid]}' target='_blank'>{$entityusers['fullname']}</a></li>";
            }
        }
        else {
            $entityusers_list = $lang->na;
        }

        $entityusers_list_output = '<ul style="list-style:none; padding:2px; margin-top:0px;">'.$entityusers_list.'</ul>';
        output($entityusers_list_output);
    }
    elseif($core->input['action'] == 'get_contactpersoninformation') {
        $rpid = $db->escape_string(base64_decode($core->input['id']));

        $information = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."representatives WHERE rpid={$rpid}"));
        $segments = get_specificdata("representativessegments s JOIN ".Tprefix."productsegments seg ON(seg.psid=s.psid)", array('title', 's.psid as id'), 'id', 'title', '', 0, "rpid='{$rpid}'");
        if(is_array($segments)) {
            $information['segments'] = implode(',', $segments);
        }

        $positions = get_specificdata("representativespositions p JOIN ".Tprefix."positions pos ON(pos.posid=p.posid)", array('title', 'p.posid as id'), 'id', 'title', '', 0, "rpid='{$rpid}'");
        if(is_array($positions)) {
            $information['positions'] = implode(',', $positions);
        }
        $information['issuportive_icon'] = '<img src="'.DOMAIN.'/images/icons/question.gif"/>';
        if(!is_null($information['isSupportive'])) {
            switch($information['isSupportive']) {
                case 0:
                    $information['issuportive_icon'] = '<img src="'.DOMAIN.'/images/invalid.gif"/>';
                    break;
                case 1:
                    $information['issuportive_icon'] = '<img src="'.DOMAIN.'/images/icons/valid.png"/>';
                    break;
            }
        }
        eval("\$contactinformation = \"".$template->get('popup_profiles_contactpersoninformation')."\";");
        echo $contactinformation;
    }
    elseif($core->input['action'] == 'do_rateentity') {
        log_rating($core->input['target'], $core->input['eid'], $core->input['value']);
    }
    elseif($core->input['action'] == 'do_updaterml') {
        savematuritylevel($core->input['target'], $core->input['eid']);
        echo get_rml_bar($core->input['eid']);
    }
    elseif($core->input['action'] == 'do_addmartkerdata') {
        if(strpos(strtolower($_SERVER['HTTP_REFERER']), 'crm/fillvisitreport') !== false) {
            parse_str(parse_url($_SERVER['HTTP_REFERER'])[query], $query_string);
            $identifier = $query_string['identifier'];
        }
        $session->start_phpsession();
        $marketin_obj = new MarketIntelligence();
        $marketin_obj->create($core->input['marketdata']);
        if(strpos(strtolower($_SERVER['HTTP_REFERER']), 'crm/fillvisitreport') !== false) {
            if($session->isset_phpsession(('visitreportmidata_'.$identifier))) {
                $mibdids = unserialize($session->get_phpsession('visitreportmidata_'.$identifier));
                $mibdids[] = $marketin_obj->mibdid;
            }
            $session->set_phpsession(array('visitreportmidata_'.$identifier => serialize($mibdids)));
        }
        switch($marketin_obj->get_errorcode()) {
            case 0:
                header('Content-type: text/xml+javascript');
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'<![CDATA[<script>$("#perform_parsetimeline").trigger("click");</script>]]></message>');
                break;
            case 1:
                output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
        }
    }
    elseif($core->input['action'] == 'do_addbrand') {
        $entitybrand_obj = new EntitiesBrands();
        $entitybrand_obj->create($core->input['entitybrand']);
        switch($entitybrand_obj->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 1:
                output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>eeee'.$lang->itemalreadyexist.'</message>');
                break;
        }
    }
    elseif($core->input['action'] == 'get_updatemktintldtls') {
        $css[display]['radiobuttons'] = 'none';
        $mkdchem_rowid = 0;
        $mkdbing_rowid = 0;
        $mkdprod_rowid = 0;
        if(strpos(strtolower($_SERVER['HTTP_REFERER']), 'crm/fillvisitreport') !== false) {
            $exclude_visitreports = true;
        }
        if($core->usergroup['profiles_canAddMkIntlData'] == 0) {
            exit;
        }
        $midata = new MarketIntelligence($core->input['id']);
        $mimorerowsid = $midata->mibdid;
        $customer = $midata->get_customer();
        $brandsproducts = $customer->get_brandsproducts();
        $output = '';
        $main_attr = '';
        $basic_attrs = array('cfcid', 'cfpid', 'biid');
        foreach($basic_attrs as $attr) {
            $at = "'".$attr."'";
            if($midata->$attr > 0) {
                $main_attr = $attr;
            }
        }
        if(!isset($main_attr)) {
            exit;
        }
        $mainattr = $midata->$main_attr;
        $twelvemonths = 31536000;
        $notcurrent = 'mibdid != '.$midata->mibdid;
        $mi_pastobjs = MarketIntelligence::get_marketdata_dal(array('cid' => $midata->cid, 'CUSTOMSQL' => $notcurrent, 'ebpid' => $midata->ebpid, 'createdBy' => $core->user['uid'], $main_attr => $mainattr), array('simple' => false, 'order' => array('by' => 'createdOn', 'sort' => 'DESC'), 'operators' => array('CUSTOMSQL' => 'CUSTOMSQL')));
        if(is_array($mi_pastobjs)) {
            foreach($mi_pastobjs as $mi_pastobj) {
//                if($mi_pastobj->mibdid == $midata->mibdid) {
//                    continue;
//                }
                if(strlen($mi_pastobj->comments) == 0) {
                    continue;
                }
                if(TIME_NOW - $mi_pastobj->createdOn > $twelvemonths) {
                    continue;
                }
                $createdby = new Users($mi_pastobj->createdBy);
                $date = date($core->settings['datetime'], $mi_pastobj->createdOn);
                $comments.="<br>".$createdby->get_displayname()."   ".$date." :<br>".$mi_pastobj->comments;
            }
        }
        if(!empty($comments)) {
            $comments = 'Past Comments: <div style="display:block">'.$comments.'</div>';
        }
        if(is_array($brandsproducts)) {
            foreach($brandsproducts as $brandproduct) {
                $brandproduct_obj = $brandproduct;
                $brandproduct_brand = $brandproduct->get_entitybrand();
                $brandproduct_productype = $brandproduct->get_endproduct();
                $options[$brandproduct->ebpid] = $brandproduct_brand->name;
                if(!is_object($brandproduct_productype)) {
                    $brandproduct_productype = new EntBrandsProducts();
                    $brandproduct_productype->title = $lang->unspecified;
                }
                else {
                    $options[$brandproduct->ebpid] .= ' - '.$brandproduct_productype->title;
                }
                eval("\$brandsendproducts .= \"".$template->get('profiles_entityprofile_brandsproducts')."\";");
            }

            $entitiesbrandsproducts_list = parse_selectlist('marketdata[ebpid]', 7, $options, $midata->ebpid);
        }
        $endproducttypes = EndProducTypes:: get_endproductypes();
        if(is_array($endproducttypes)) {
            foreach($endproducttypes as $endproducttype) {
                $endproducttypes_list .= '<option value="'.$endproducttype->eptid.'">'.$endproducttype->title.' - '.$endproducttype->get_application()->get_displayname().'</option>';
            }
        }
        unset($endproducttypes);

        $basicingredients_obj = $midata->get_basicingredients();
        if(is_object($basicingredients_obj)) {
            $basicingredient = $basicingredients_obj->get_displayname();
            $css['display']['basicingsubfield'] = 'block';
            eval("\$profiles_michemfuncproductentry = \"".$template->get('profiles_mibasicingredientsentry')."\";");
            unset($basicingredients_obj, $basicingredient);
        }

        $chemfuncchemical = $midata->get_chemfunctionschemcials();
        if(is_object($chemfuncchemical)) {
            $chemsubstance = $chemfuncchemical->get_chemicalsubstance();
            $css['display']['chemsubfield'] = 'block';
            eval("\$profiles_michemfuncproductentry = \"".$template->get('profiles_michemfuncsubstancentry')."\";");
        }

        $brandedendprod_obj = new EntBrandsProducts($midata->ebpid);
        if(is_object($brandedendprod_obj)) {
            $brandname = $brandedendprod_obj->get_entitybrand()->get_displayname();
        }
        /* parse visit report --START */
        if(!$exclude_visitreports) {
            $visitreport_objs = CrmVisitReports::get_visitreports(array('uid' => $core->user['uid'], 'cid' => $midata->cid, 'isDraft' => 1), array('order' => array('by' => 'date', 'sort' => 'DESC'), 'returnarray' => 1));
            if(is_array($visitreport_objs)) {
                $profiles_mincustomervisit_title = $lang->visitreport;
                $profiles_mincustomervisit = parse_selectlist('marketdata[vrid]', 7, $visitreport_objs, $midata->vrid, '', '', array('blankstart' => 1));
            }
        }
        else {
            if(strpos(strtolower($_SERVER['HTTP_REFERER']), 'crm/fillvisitreport') !== false) {
                parse_str(parse_url($_SERVER['HTTP_REFERER'])[query], $query_string);
                $identifier = $query_string['identifier'];
                $visitreport = VisitReports::get_data(array('identifier' => $identifier));
                $profiles_mincustomervisit = '<input type="hidden" value="'.$visitreport->vrid.'" name="marketdata[vrid]"/>';
            }
        }
        /* parse visit report --END */
        $chemfuncproduct = $midata->get_chemfunctionproducts();
        if(is_object($chemfuncproduct)) {
            $product = $chemfuncproduct->get_produt();
            $css[display]['product'] = 'block';
            eval("\$profiles_minproductentry= \"".$template->get('profiles_michemfuncproductentry')."\";");
        }

        list($module, $modulefile) = explode('/', $core->input['module']);
        $elementname = 'marketdata[cid]';
        $action = 'do_addmartkerdata';
        $elemtentid = $customer->get_eid();
        /* Parse competitors related market Data */
        $mrktcompetitor_objs = $midata->get_competitors();
        if(is_array($mrktcompetitor_objs)) {
            end($mrktcompetitor_objs);
            $lastkey = key($mrktcompetitor_objs);
            foreach($mrktcompetitor_objs as $mrktcompetitor_obj) {
                if($mrktcompetitor_obj->micid == $lastkey) {
                    continue;
                }
                $rowid = $i;
                $traders = $mrktcompetitor_obj->get_entitytrader();
                if(is_array($traders)) {
                    $competitor['trader'] = $traders[$mrktcompetitor_obj->trader]->get_displayname();
                }
                $producer = $mrktcompetitor_obj->get_entityproducer();
                if(is_array(producer)) {
                    $competitor['producer'] = $producer[$mrktcompetitor_obj->producer]->get_displayname();
                }
                $product = $mrktcompetitor_obj->get_products();
                if(is_array($product)) {
                    $competitor['product'] = $product[$mrktcompetitor_obj->pid]->get_displayname();
                    $competitor['pid'] = $product[$mrktcompetitor_obj->pid]->pid;
                }
                $competitor['uniprice'] = $mrktcompetitor_obj->unitPrice;
                $packaging_list = parse_selectlist('marketdata[competitor]['.$i.'][packaging]', 7, Packaging::get_data('name IS NOT NULL'), $mrktcompetitor_obj->packaging, '', '', array('blankstart' => 1));
                $incoterms_list = parse_selectlist('marketdata[competitor]['.$i.'][incoterms]', 8, Incoterms::get_data('titleAbbr IS NOT NULL'), $mrktcompetitor_obj->incoterms, '', '', array('blankstart' => 1));
                $saletype_list = parse_selectlist('marketdata[competitor]['.$i.'][saletype]', 8, SaleTypes::get_data('stid IN (1,4)'), $mrktcompetitor_obj->saletype, '', '', array('blankstart' => 1));
                $samplacquire = parse_radiobutton('marketdata[competitor]['.$i.'][isSampleacquire]', array(1 => 'yes', 0 => 'no'), $mrktcompetitor_obj->isSampleacquire, true);
                eval("\$competitors_rows .= \"".$template->get('crm_marketpotentialdata_comptetitors')."\";");
                unset($mrktcompetitor_obj, $competitor);
            }

            $rowid = 2;
            $mrktcompetitor_obj = $mrktcompetitor_objs[$lastkey];
            $traders = $mrktcompetitor_obj->get_entitytrader();
            if(is_array($traders)) {
                $competitor['trader'] = $traders[$mrktcompetitor_obj->trader]->get_displayname();
            }
            $producer = $mrktcompetitor_obj->get_entityproducer();
            if(is_array(producer)) {
                $competitor['producer'] = $producer[$mrktcompetitor_obj->producer]->get_displayname();
            }
            $product = $mrktcompetitor_obj->get_products();
            if(is_array($product)) {
                $competitor['product'] = $product[$mrktcompetitor_obj->pid]->get_displayname();
                $competitor['pid'] = $product[$mrktcompetitor_obj->pid]->pid;
            }
            $producer = $mrktcompetitor_obj->get_entityproducer();
            if(is_array($producer)) {
                $competitor['producer'] = $producer[$mrktcompetitor_obj->producer]->get_displayname();
            }
            $competitor['uniprice'] = $mrktcompetitor_obj->unitPrice;
            $packaging_list = parse_selectlist('marketdata[competitor][2][packaging]', 7, Packaging::get_data('name IS NOT NULL'), $mrktcompetitor_obj->packaging, '', '', array('blankstart' => 1));
            $incoterms_list = parse_selectlist('marketdata[competitor][2][incoterms]', 8, Incoterms::get_data('titleAbbr IS NOT NULL'), $mrktcompetitor_obj->incoterms, '', '', array('blankstart' => 1));
            $saletype_list = parse_selectlist('marketdata[competitor][2][saletype]', 8, SaleTypes::get_data('stid IN (1,4)'), $mrktcompetitor_obj->saletype, '', '', array('blankstart' => 1));
            $samplacquire = parse_radiobutton('marketdata[competitor][2][isSampleacquire]', array(1 => 'yes', 0 => 'no'), $mrktcompetitor_obj->isSampleacquire, true);
        }
        else {
            $rowid = 2;
            $packaging_list = parse_selectlist('marketdata[competitor][2][packaging]', 7, Packaging::get_data('name IS NOT NULL'), '', '', '', array('blankstart' => 1));
            $incoterms_list = parse_selectlist('marketdata[competitor][2][incoterms]', 8, Incoterms::get_data('titleAbbr IS NOT NULL'), '', '', '', array('blankstart' => 1));
            $saletype_list = parse_selectlist('marketdata[competitor][2][saletype]', 8, SaleTypes::get_data('stid IN (1,4)'), '', '', '', array('blankstart' => 1));
            $samplacquire = parse_radiobutton('marketdata[competitor][2][isSampleacquire]', array(1 => 'yes', 0 => 'no'), '', true);
        }

        eval("\$popup_marketdata = \"".$template->get('popup_profiles_marketdata')."\";");
        output($popup_marketdata);
    }
    elseif($core->input['action'] == 'get_mktintldetails') {
        if($core->usergroup['profiles_canAddMkIntlData'] == 0) {
            exit;
        }

        $mkintentry = new MarketIntelligence($core->input['id']);
        $round_fields = array('potential', 'mktSharePerc', 'mktShareQty', 'unitPrice');
        foreach($round_fields as $round_field) {
            $mkintentry->{$round_field} = round($mkintentry->{$round_field});
        }
        $basic_attrs = array('cfcid', 'cfpid', 'biid');

        foreach($basic_attrs as $attr) {
            $at = "'".$attr."'";
            if($mkintentry->$attr > 0) {
                $main_attr = $attr;
            }
        }
        if(!isset($main_attr)) {
            exit;
        }
        $mainattr = $mkintentry->$main_attr;
        $twelvemonths = 31536000;
        $notcurrent = 'mibdid != '.$mkintentry->mibdid;
        $mi_pastobjs = MarketIntelligence::get_marketdata_dal(array('cid' => $mkintentry->cid, 'CUSTOMSQL' => $notcurrent, 'ebpid' => $mkintentry->ebpid, 'createdBy' => $core->user['uid'], $main_attr => $mainattr), array('simple' => false, 'operators' => array('CUSTOMSQL' => 'CUSTOMSQL'), 'order' => array('by' => 'createdOn', 'sort' => 'DESC')));
        if(is_array($mi_pastobjs)) {
            foreach($mi_pastobjs as $mi_pastobj) {
                if(strlen($mi_pastobj->comments) == 0) {
                    continue;
                }
                if(TIME_NOW - $mi_pastobj->createdOn > $twelvemonths) {
                    continue;
                }
                $createdby = new Users($mi_pastobj->createdBy);
                $date = date($core->settings['datetime'], $mi_pastobj->createdOn);
                $comments.="<br>".$createdby->get_displayname()."   ".$date." :<br>".$mi_pastobj->comments;
            }
        }
        if(!empty($comments)) {
            $comments = '<td><strong>Past Comments</strong></td><td><div style="width:300px; overflow:auto; height:80px; line-height:20px;">'.$comments.'</div></td>';
        }
        $mkintentry_customer = $mkintentry->get_customer();
        $mkintentry_brand = $mkintentry->get_entitiesbrandsproducts()->get_entitybrand();
        $mkintentry_endproducttype = $mkintentry->get_entitiesbrandsproducts()->get_endproduct();
        if(!is_object($mkintentry_endproducttype)) {
            $mkintentry_endproducttype = new EntBrandsProducts();
            $mkintentry_endproducttype->title = $lang->unspecified;
        }
        /* Parse competitors related market Data */
        $mrktcompetitor_objs = $mkintentry->get_competitors();
        if(is_array($mrktcom_petitor_objs)) {
            foreach($mrktcompetitor_objs as $mrktcompetitor_obj) {
                $mrktintl_detials['competitors'] = $mrktcompetitor_obj->get();
                if(is_array($mrktintl_detials['competitors'])) {
                    $marketintelligencedetail_competitors = ' <div class="thead">'.$lang->competitor.'</div>';
                    $mrktintl_detials['competitors']['unitPrice'] = round($mrktintl_detials['competitors']['unitPrice']);

                    /* Get competitor suppliers objects */
                    $competitorsentities_objs = $mrktcompetitor_obj->get_entities();
                    if(is_array($competitorsentities_objs)) {
                        foreach($competitorsentities_objs as $competitorsentities_obj) {
                            $mrktintl_detials_competitorsuppliers .= '<li>'.$competitorsentities_obj->get()['companyName'].'</li>';
                        }
                    }
                    /* Get competitor suppliers prodcuts */
                    $competitorsproducts_objs = $mrktcompetitor_obj->get_products();
                    if(is_array($competitorsproducts_objs)) {
                        foreach($competitorsproducts_objs as $competitorsproducts_obj) {
                            $mrktintl_detials_competitorproducts.='<li>'.$competitorsproducts_obj->get()['name'].'</li>';
                        }
                    }
                }
            }
            eval("\$marketintelligencedetail_competitors .= \"".$template->get('profiles_entityprofile_marketintelligence_competitors')."\";");
        }

        eval("\$marketintelligencedetail = \"".$template->get('popup_marketintelligencedetails')."\";");
        output($marketintelligencedetail);
    }
    elseif($core->input['action'] == 'parse_previoustimeline') {
        if($core->usergroup['profiles_canAddMkIntlData'] == 0) {
            exit;
        }
        $next_profiles = array('latestaggregatecustomersumbyproduct' => 'allprevious', 'allprevious' => null);
        $filter = unserialize($core->input['tlrelation']);

        $mrktint_obj = new MarketIntelligence();

        $miprofile = $mrktint_obj->get_miprofconfig_byname($core->input['miprofile']);
        $miprofile['next_miprofile'] = $next_profiles[$core->input['miprofile']];
        $mrkt_objs = $mrktint_obj->get_marketintelligence_timeline($filter, $miprofile);
//$mrkt_objs = $mrktint_obj->get_marketintelligence_timeline('customer', 'cfpid', $cfpid, array('time' => 'allprevious', 'filterchemfunctprod' => 1));

        $is_last = false;
        if(empty($miprofile['next_miprofile'])) {
            $is_last = true;
        }
        $depth = count($filter) - 1;
        $previoustimelinerows = '';
        if(is_array($mrkt_objs)) {
            foreach($mrkt_objs as $mktintldata) {
                $mktintldata['tlidentifier']['id'] = 'tlrelation-'.implode('-', $filter);
                $mktintldata['tlidentifier']['value'] = $filter;
                $previoustimelinerows .= $mrktint_obj->parse_timeline_entry($mktintldata, $miprofile, $depth, $is_last);
            }
        }
        output($previoustimelinerows);
    }
    elseif($core->input['action'] == 'ajaxaddmore_profmkdchemical') {
        $mkdchem_rowid = $db->escape_string($core->input['value']) + 1;
        eval("\$profiles_michemfuncproductentry_rows = \"".$template->get('profiles_michemfuncsubstancentry')."\";");
        echo $profiles_michemfuncproductentry_rows;
    }
    elseif($core->input['action'] == 'ajaxaddmore_profmkdbasicing') {
        $mkdbing_rowid = $db->escape_string($core->input['value']) + 1;
        eval("\$profiles_mibasicingredientsentry_rows = \"".$template->get('profiles_mibasicingredientsentry')."\";");
        echo $profiles_mibasicingredientsentry_rows;
    }
    elseif($core->input['action'] == 'ajaxaddmore_profmkdproduct') {
        $mkdprod_rowid = $db->escape_string($core->input['value']) + 1;
        eval("\$profiles_minproductentry_rows = \"".$template->get('profiles_michemfuncproductentry')."\";");
        echo $profiles_minproductentry_rows;
    }
}
function parse_calltype(&$value) {
    global $lang;

    switch($value) {
        case '1':
            $value = $lang->facetoface;
            break;
        case '2':
            $value = $lang->telephonecall;
            break;
        default: break;
    }
    return $value;
}

function validate_rating($eid, $value, $criterion) {
    global $core, $maxstars;
    if(empty($maxstars)) {
        $maxstars = 5;
    }
    /* Check if user is assigned to supplier */
    if(!isset($core->user['suppliers']['eid'][$eid])) {
        return false;
    }
    /* Check if criterion exists */
    if(!value_exists('entities_ratingcriteria', 'ercid', $criterion)) {
        return false;
    }
    /* Check if entity exists */
    if(!value_exists('entities', 'eid', $eid)) {
        return false;
    }
    /* Check if rate is valid */
    if($value < 0 || $value > $maxstars) {
        return false;
    }

    return true;
}

function log_rating($criterion, $eid, $value, $uid = '') {
    global $core, $db, $log;

    $new_rating['ercid'] = intval($criterion);
    $new_rating['eid'] = intval($core->sanitize_inputs($eid));
    $new_rating['rating'] = $db->escape_string($core->sanitize_inputs($value, array('removetags' => true)));

    if(empty($uid)) {
        $new_rating['uid'] = $core->user['uid'];
    }
    else {
        $new_rating['uid'] = intval($uid);
    }

    if(!validate_rating($new_rating['eid'], $new_rating['rating'], $new_rating['ercid'])) {
        return false;
    }

    $active_rating = $db->fetch_field($db->query('SELECT erid
                FROM '.Tprefix.'entities_ratings
                WHERE ercid = "'.$new_rating['ercid'].'" AND uid = "'.$new_rating['uid'].'" AND eid = "'.$new_rating['eid'].'" AND dateTime>"'.strtotime('last week').'"
                ORDER BY dateTime DESC
                LIMIT 0, 1'), 'erid');
    if(isset($active_rating) && !empty($active_rating)) {
        $query = $db->update_query('entities_ratings', array('rating' => $new_rating['rating']), 'erid = "'.$active_rating.'"');
        $log->record($active_rating);
    }
    else {
        $new_rating['dateTime'] = TIME_NOW;
        $query = $db->insert_query('entities_ratings', $new_rating);
        $log->record($db->last_id());
    }

    if($query) {
        return true;
    }
    return false;
}

function get_current_rating($eid, $ercid) {
    global $db;

    $getratings_query = $db->query('SELECT DISTINCT(uid), rating
                FROM '.Tprefix.'entities_ratings
                WHERE eid = '.intval($eid).' AND ercid = '.intval($ercid).'
                ORDER BY dateTime DESC');
    $returnvalue = $counter = 0;

    if($db->num_rows($getratings_query) > 0) {
        while($rating = $db->fetch_assoc($getratings_query)) {
            $returnvalue += $rating['rating'];
            $counter++;
        }
    }
    if($counter == 0) {
        return 0;
    }
    else {
        return ($returnvalue / $counter);
    }
}

function savematuritylevel($level, $eid) {
    global $db, $core, $log;

    if($core->usergroup['profiles_canUpdateRML'] != 1) {
        return false;
    }

    if(!value_exists('entities_rmlevels', 'ermlid', $level)) {
        return false;
    }

    if(!value_exists('entities', 'eid', $eid)) {
        return false;
    }

    $query = $db->update_query('entities', array('relationMaturity' => $level), 'eid = '.intval($eid));
    if($query) {
        $log->record($eid, $level);
    }
}

function get_rml_bar($eid) {
    global $core, $db;

//$multicolored = true;
    $maturity_bars = '';
    $readonlymaturity = true;

    if($core->usergroup['profiles_canUpdateRML'] == 1) {
        $readonlymaturity = false;
    }

    $rmllist = array();
    $levels_counter = 0;

    $rmllevels_query = $db->query('SELECT ermlid, er.title, er.category, erc.title AS categoryTitle
                FROM '.Tprefix.'entities_rmlevels er JOIN '.Tprefix.'entities_rmlevels_categories erc ON (erc.ercid = er.category)
                ORDER BY sequence');
    if($db->num_rows($rmllevels_query) > 0) {
        while($maturitylevelrow = $db->fetch_assoc($rmllevels_query)) {
            $rmllist[$maturitylevelrow['category']][$maturitylevelrow['ermlid']] = $maturitylevelrow['title'].' ('.$maturitylevelrow['categoryTitle'].')';
            $levels_counter++;
        }
    }
    else {
        return false;
    }


    $rmlcurrentlevel['id'] = $db->fetch_field($db->query('SELECT relationMaturity FROM '.Tprefix.'entities WHERE eid = '.intval($eid)), 'relationMaturity');

    $maturity_bars .= '<div id = "rml_bars" style = "text-align:left;">';
    $divclassactive = (!$readonlymaturity) ? 'rmlselectable rmlactive' : 'rmlactive';
    $divclassinactive = (!$readonlymaturity) ? 'rmlselectable rmlinactive' : 'rmlinactive';
    $parse_counter = 1;
    $positionclass = ' first';

    $is_coloredlevel = true;
    if(!isset($rmlcurrentlevel['id'])) {
        $is_coloredlevel = false;
    }
    $is_lastactiveitem = false;

    foreach($rmllist as $cat => $levels) {
        foreach($levels as $ermlid => $name) {
            if($is_coloredlevel == true) {
                if($rmlcurrentlevel['id'] == $ermlid) {
                    $is_lastactiveitem = true;
                    $rmlcurrentlevel['title'] = $name;
                }
            }

            if($parse_counter++ == $levels_counter) {
                if($parse_counter == 2) {
                    $positionclass = ' first last';
                }
                else {
                    $positionclass = ' last';
                }
            }
            else {
                if($parse_counter != 2) {
                    $positionclass = '';
                }
            }

            if($is_coloredlevel) {
                $maturity_bars .= '<div id = "'.$ermlid.'" class = "'.$divclassactive.$positionclass.'" title = "'.($lang->{$name} ? $lang->{$name} : $name).'">&nbsp;</div>';
            }
            else {
                $maturity_bars .= '<div id = "'.$ermlid.'" class = "'.$divclassinactive.$positionclass.'" title = "'.($lang->{$name} ? $lang->{$name} : $name).'">&nbsp;</div>';
            }

            if($is_lastactiveitem) {
                $is_coloredlevel = false;
                $is_lastactiveitem = false;
            }
        }

        if(trim($positionclass) != 'last') {
            $maturity_bars .= '&nbsp;&nbsp;';
        }
    }
    $maturity_bars .= '<br /><span class = "smalltext" style = "color:#999; font-style:italic;">'.$rmlcurrentlevel['title'].'</span></div>';

    return $maturity_bars;
}

?>