<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Entity profile
 * $module: profiles
 * $id: entityprofile.php
 * Created:		@zaher.reda		September 28, 2010 | 11:08 AM
 * Last Update: @zaher.reda 	April 21, 2011 | 03:14 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
	if(!isset($core->input['eid']) || empty($core->input['eid'])) {
		redirect($_SERVER['HTTP_REFERER']);
	}

	$eid = $db->escape_string($core->input['eid']);
	$entity_obj = new Entities($eid, '', false);
	$profile = $entity_obj->get();

	/* Get/parse brands - START */
	$entbrandsproducts_objs = $entity_obj->get_brands($filter_where);
	if(is_array($entbrandsproducts_objs)) {
		foreach($entbrandsproducts_objs as $entbrandsproducts_obj) {
			$entbrandsproducts = $entbrandsproducts_obj->get();
			$entitybrand = $entbrandsproducts['name']; /* Loop over the brandobjcts and get their current brands */
			$endproduct_types_objs = $entbrandsproducts_obj->get_producttypes();
			$entbrandproducts_objs = $entbrandsproducts_obj->get_entbrandproducts();
			if(is_array($entbrandproducts_objs)) {
				foreach($entbrandproducts_objs as $entbrandproducts_obj) {
					$entbrandsproductsids = $entbrandproducts_obj->get()['ebpid'];
				}
			}
			if(is_array($endproduct_types_objs)) {
				foreach($endproduct_types_objs as $endproduct_types_obj) { /* Loop over the products types of the current entitiy object */
					$endproduct_types = $endproduct_types_obj->get()['name'];
				}
			}
			$entitiesbrandsproducts_list .= '<option value="'.$entbrandsproductsids.'">'.$endproduct_types.'-'.$entitybrand.' </option>';
		}
	}

	/* Get/parse brands - END */

	/* Market Data --START */
	$filter_where = 'eid IN ('.$eid.')';
	if($core->usergroup['profiles_canAddMkIntlData'] == 1) {
		$addmarketdata_link = '<div style="float:right;margin:-10px;" title="'.$lang->addmarket.'" ><a href="#"id="showpopup_marketdata" class="showpopup"><img alt="'.$lang->addmarket.'" src="'.$core->settings['rootdir'].'/images/icons/marketintelligence.png" width="44px;" height="44px;"/></a></div>';
		$field = '<input type="text" required="required" size="25" name="marketdata[cfpid]" id="chemfunctionproducts_1_QSearch" size="100"  autocomplete="off"/>
                  <input type="hidden"  id="chemfunctionproducts_1_id" name="marketdata[cfpid]" />
				  <input type="hidden" value="1" id="userproducts" name="userproducts" />
				<div id="searchQuickResults_1" class="searchQuickResults" style="display:none;"></div>';
		$array_data = array('module' => 'profiles', 'elemtentid' => $eid, 'fieldlabel' => $lang->product, 'action' => 'do_addmartkerdata', 'modulefile' => 'entityprofile');
		/* to be replacing the below variables */
		$module = 'profiles';
		$elemtentid = $eid;
		$elementname = 'marketdata[cid]';
		$lang->fieldlabel = $lang->product;
		$action = 'do_addmartkerdata';
		$modulefile = 'entityprofile';

		/* View detailed market intelligence box --START */
		$maktintl_mainobj = new Marketintelligence();
		$maktintl_objs = $maktintl_mainobj->get_marketintelligence_timeline($eid, array('currentyear' => 1, 'customer' => 1));
		if(is_array($maktintl_objs)) {
			$timedepth = 25;
			$height = 25;
			$round_fields = array('potential', 'mktSharePerc', 'mktShareQty', 'unitPrice');
			foreach($maktintl_objs as $maktintl_obj) {
				$altrow_class = alt_row($altrow_class);
				$mktintldata = $maktintl_obj->get();
				foreach($round_fields as $round_field) {
					$mktintldata[$round_field] = round($mktintldata[$round_field]);
				}
				$entity_brdprd_objs = $maktintl_obj->get_entitiesbrandsproducts();
				$entity_brandproducts = $entity_brdprd_objs->get();

				$entity_mrktendproducts_objs = $maktintl_obj->get_marketendproducts($entity_brandproducts['eptid']);
				$entity_mrktendproducts = $entity_mrktendproducts_objs->get()['title'];

				$mktintldata['previoustimeline'] = date($core->settings['dateformat'], $maktintl_obj->get()['createdOn']);
				$mktintldata['chemfunction'] = $maktintl_obj->get_chemfunctionproducts()->get_segapplicationfunction()->get_function()->get()['title'];
				$mktintldata['application'] = $maktintl_obj->get_chemfunctionproducts()->get_segapplicationfunction()->get_application()->get()['title'];
				$mktintldata['segment'] = $maktintl_obj->get_chemfunctionproducts()->get_segapplicationfunction()->get_segment()->get()['title'];
				$mktintldata['entity'] = $maktintl_obj->get_chemfunctionproducts()->get_produt()->get()['name'];  //get product from cfpid
				if(empty($mktintldata['entity'])) {
					continue;
				}
				eval("\$detailmarketbox .= \"".$template->get('profiles_entityprofile_mientry')."\";");
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

	$profile['country'] = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."countries WHERE coid=' {
			$profile[country]
		}'"), 'name');

	$profile['fulladdress'] .= $profile['country'];

	foreach($profile as $key => $val) {
		if(empty($val)) {
			$profile[$key] = $lang->na;
		}
	}

	if(!empty($profile['website'])) {
		$profile['website'] = '<a href = "'.$profile['website'].'" target = "_blank">'.$profile['website'].'</a>';
	}
	else {
		$profile['website'] = '';
	}

	$representative_query = $db->query("SELECT *
										FROM ".Tprefix."representatives r JOIN ".Tprefix."entitiesrepresentatives er ON (er.rpid=r.rpid)
										WHERE er.eid={$eid}");
	while($representative = $db->fetch_assoc($representative_query)) {
		$representativelist .= '<a href = "#" id = "contactpersoninformation_'.base64_encode($representative['rpid']).'_profiles/entityprofile_loadpopupbyid">'.$representative['name'].'</a> - <a href = "mailto:'.$representative['email'].'">'.$representative['email'].'</a><br />';
	}

	$segment_query = $db->query("SELECT * FROM ".Tprefix."entitiessegments es JOIN ".Tprefix." productsegments ps ON (es.psid=ps.psid) WHERE es.eid={$eid}");
	while($segment = $db->fetch_assoc($segment_query)) {
		$segmentlist .= $segment['title'].'<br />';
	}

	$affiliate_query = $db->query("SELECT *
								   FROM ".Tprefix."affiliates a LEFT JOIN ".Tprefix."affiliatedentities ae ON (ae.affid=a.affid)
								   WHERE ae.eid={$eid}
								   ORDER BY a.name ASC");
	while($affiliate = $db->fetch_array($affiliate_query)) {
		$listitem['link'] = 'index.php?module = profiles/affiliateprofile&affid = '.$affiliate['affid'];
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
			$hidden_entityusers .= "<li><a href='./users.php?action = profile&uid = {
			$entityusers[uid]
		}' target='_blank'>{$entityusers['fullname']}</a></li>";
		}
		elseif($entityusers_counter == $core->settings['itemsperlist']) {
			$shown_entityallusers .= "<li><a href='./users.php?action = profile&uid = {
			$entityusers[uid]
		}' target='_blank'>{$entityusers['fullname']}</a>";
		}
		else {
			$shown_entityallusers .= "<li><a href='./users.php?action = profile&uid = {
			$entityusers[uid]
		}' target='_blank'>{$entityusers['fullname']}</a></li>";
		}
	}

	if($entityusers_counter > $core->settings['itemsperlist']) {
		$entityallusers = "<ul style='list-style:none;
		padding:2px;
		margin-top:0px;
		'>".$shown_entityallusers.", <a href='#entityusers' id='showmore_entityusers_{$entityusers[uid]}' class='smalltext'>read more</a></li> <span style='display:none;' id='entityusers_{$entityusers[uid]}'>{$hidden_entityusers}</span></ul>";
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
				$row_class = alt_row($row_class);
				if($report['status'] == 1) {
					$icon_locked = '';
					if($report['isLocked'] == 1) {
						$icon_locked = '_locked';
					}
					$report_icon = '<a href="index.php?module=reporting/preview&referrer=list&amp;affid='.$report['affid'].'&amp;spid='.$report['spid'].'&amp;quarter='.$report['quarter'].'&amp;year='.$report['year'].'"><img src="images/icons/report'.$icon_locked.'.gif" alt="'.$report['status'].'" border="0"/></a>';
				}

				$finalized_reports .= '<tr class="'.$row_class.'"><td>'.$report['affiliate_name'].'</td><td>Q'.$report['quarter'].'</td><td>'.$report['year'].'</td><td style="width:1%; text-align:right;">'.$report_icon.'</td></tr>';
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

		/* Load supplier's products list - Start */
		$products_query = $db->query("SELECT p.pid, p.name, gp.title AS genericname FROM ".Tprefix."products p JOIN ".Tprefix."genericproducts gp ON (gp.gpid=p.gpid) WHERE p.spid={$eid}");

		//$products_counter = 0;
		$productslist = '';
		if($db->num_rows($products_query) > 0) {
			while($product = $db->fetch_assoc($products_query)) {
				$row_class = alt_row($row_class);
				$productslist .= '<tr class="'.$row_class.'"><td style="width:50%;">'.$product['name'].'</td><td>'.$product['genericname'].'</td></tr>';
			}
			eval("\$products_section = \"".$template->get('profiles_entityprofile_products')."\";");
		}

		/* if(++$products_counter > $core->settings['itemsperlist'])  {
		  $hidden_products .= '<li>'.$products['name'].'</li>';
		  }
		  elseif($products_counter == $core->settings['itemsperlist'])
		  {
		  $shown_products .= '<li>'.$products['name'].'</a>';
		  }
		  else
		  {
		  $shown_products .= '<li>'.$products['name'].'</li>';
		  }

		  }

		  if($products_counter != 0) {
		  if($products_counter > $core->settings['itemsperlist']) {
		  $productslist = '<ul style="list-style:none; padding:2px;margin-top:0px;">'.$shown_products.', <a href="#products" id="showmore_products_'.$products['pid'].'" class="smalltext">read more</a></li> <span style="display:none;" id="products_'.$products['pid'].'">'.$hidden_products.'</span></ul>';
		  }
		  else
		  {
		  $productslist .= '<ul style="list-style:none; padding:2px;margin-top:0px;">'.$shown_products.'</ul>';
		  }
		  }
		  else
		  {
		  $productslist = ' - ';
		  } */
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

				$sent_reports .= '<a href="'.$ready_reports_link[$ready_report['affid']].'">sent reports Q'.$ready_report['quarter'].' </a><br />';
			}

			if(!empty($sent_reports)) {
				$entityprofile_private .= '<tr><td  valign="top" style="padding:10px;"><span class="subtitle">'.$lang->lastfiles.'</span><br />'.$sent_reports.'</td><td valign="top" style="padding:10px;">&nbsp;</td></tr>';
			}
			/* Get sent reports - End */
		}
		/* Prepare the private part of the profile - End */

		/* Parse Maturity Section - START */
		$maturity_section = '<div id="rml_maindiv"><span class="subtitle">'.$lang->rmlevel.'</span><br />'.get_rml_bar($eid).'</div>';
		if($core->usergroup['profiles_canUpdateRML'] == 1) {
			$header_rmljs = '$(".rmlselectable").live("click", function() {
					if(sharedFunctions.checkSession() == false) {
						return;
					}
                        sharedFunctions.requestAjax("post", "index.php?module=profiles/entityprofile&action=do_updaterml", "target="+$(this).attr("id")+"&eid="+$("#eid").val(), "rml_bars", "rml_bars", "html");
                    });

                    $(".rmlselectable").live("hover", function() {
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
		$meetings = $entity_obj->get_meetings();
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

	$entity_brand_objs = $entity_obj->get_brands();
	if(is_array($entity_brand_objs)) {
		foreach($entity_brand_objs as $entity_brand_obj) {
			$rowclass = alt_row($rowclass);
			$entity_brands = $entity_brand_obj->get();
			//getentitiesbrandsproducts
			$endproductstypes_objs = $entity_brand_obj->get_producttypes(); //Entbrandsproducts::get_endproducts($entity_brands['ebid']);
			if(is_array($endproductstypes_objs)) {
				foreach($endproductstypes_objs as $endproductstypes_obj) {
					$entity_endproducts = $endproductstypes_obj->get();
					eval("\$brandsendproducts .= \"".$template->get('profiles_entityprofile_brandsproducts')."\";");
				}
			}
		}
	}
	else {
		$brandsendproducts = '<tr><td colspan="2">'.$lang->na.'</td></tr>';
	}
	eval("\$popup_marketdata= \"".$template->get("popup_marketdata")."\";");
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
		echo $entityusers_list_output;
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
		$marketin_obj = new Marketintelligence();
		$marketin_obj->create($core->input[marketdata]);
		switch($marketin_obj->get_errorcode()) {
			case 0:
				output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
				break;
			case 1:
				output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
				break;
			case 2:
				output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
				break;
		}
	}
	elseif($core->input['action'] == 'get_mktintldetails') {
		$mibdid = $db->escape_string($core->input['id']);
		$mrktint_obj = new Marketintelligence($mibdid);
		$mrktintl_detials = $mrktint_obj->get();
		$round_fields = array('potential', 'mktSharePerc', 'mktShareQty', 'unitPrice');
		foreach($round_fields as $round_field) {
			$mrktintl_detials[$round_field] = round($mrktintl_detials[$round_field]);
		}
		$mrktintl_detials['brand'] = $mrktint_obj->get_entitiesbrandsproducts()->get_entitybrand()->get()['name'];
		$mrktintl_detials['endproduct'] = $mrktint_obj->get_entitiesbrandsproducts()->get_endproduct()->get()['name'];

		/* Parse competitors related market Data */
		$mrktcompetitor_objs = $mrktint_obj->get_competitors();
		if(is_array($mrktcompetitor_objs)) {
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
							$mrktintl_detials_competitorproducts.= '<li>'.$competitorsproducts_obj->get()['name'].'</li>';
						}
					}
				}
			}
		}

		eval("\$marketintelligencedetail_competitors .= \"".$template->get('profiles_entityprofile_marketintelligence_competitors')."\";");
		eval("\$marketintelligencedetail = \"".$template->get('popup_marketintelligencedetails')."\";");
		output($marketintelligencedetail);
	}
	elseif($core->input['action'] == 'parse_previoustimeline') {
		$cfpid = $db->escape_string($core->input['cfpid']);
		$mrktint_obj = new Marketintelligence();
		$mrkt_objs = $mrktint_obj->get_marketintelligence_timeline($cfpid, array('prevyear' => 1, 'filterchemfunctprod' => 1));
		if(is_array($mrkt_objs)) {
			foreach($mrkt_objs as $mrkt_obj) {
				$prevmktintldata = $mrkt_obj->get();
				$prevmktintldata['previoustimeline'] = date($core->settings['dateformat'], $mrkt_obj->get()['createdOn']);
				$entity_brdprd_objs = $mrkt_obj->get_entitiesbrandsproducts();
				$entity_brandproducts = $entity_brdprd_objs->get();

				$entity_mrktendproducts_objs = $mrkt_obj->get_marketendproducts($entity_brandproducts['eptid']);
				$entity_mrktendproducts = $entity_mrktendproducts_objs->get()['title'];
				$previoustimelinerows .= '
				<div class="timeline_entry timeline_entry_dependent">
					<div class="circle" style="top:50%; left:-9px; height:15px; width:15px;"></div>
					<div>
					<div class="timeline_column smalltext" style="width:20%;">'.$prevmktintldata['previoustimeline'].'</div>
					<div class="timeline_column">'.$entity_mrktendproducts_objs->get()['name'].'</div>
					<div class="timeline_column">'.$prevmktintldata['potential'].'</div>
					<div class="timeline_column">'.$prevmktintldata['unitPrice'].'</div>
					<div class="timeline_column">'.$prevmktintldata['mktSharePerc'].'</div>
					<div class="timeline_column">'.$prevmktintldata['mktShareQty'].'</div>
					<div class="timeline_column" style="width:1%;"><span style="margin-left:-45px;"><a style="cursor:pointer;" title="'.$lang->viewmrktbox.' '.$prevmktintldata['previoustimeline'].'" id="mktintldetails_'.$prevmktintldata['mibdid'].'_profiles/entityprofile_loadpopupbyid" rel="mktdetail_'.$prevmktintldata['mibdid'].'"><img src="'.$core->settings['rootdir'].'/images/icons/search.gif"/></a></span></div>
					</div>
		
        
			  </div>
				</div>';
			}
			echo $previoustimelinerows;
		}
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
                                    WHERE ercid="'.$new_rating['ercid'].'" AND uid="'.$new_rating['uid'].'" AND eid="'.$new_rating['eid'].'" AND dateTime>"'.strtotime('last week').'"
									ORDER BY dateTime DESC
									LIMIT 0, 1'), 'erid');
	if(isset($active_rating) && !empty($active_rating)) {
		$query = $db->update_query('entities_ratings', array('rating' => $new_rating['rating']), 'erid="'.$active_rating.'"');
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
                                    WHERE eid='.intval($eid).' AND ercid='.intval($ercid).'
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

	$query = $db->update_query('entities', array('relationMaturity' => $level), 'eid='.intval($eid));
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
								FROM '.Tprefix.'entities_rmlevels er JOIN '.Tprefix.'entities_rmlevels_categories erc ON (erc.ercid=er.category)
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


	$rmlcurrentlevel['id'] = $db->fetch_field($db->query('SELECT relationMaturity FROM '.Tprefix.'entities WHERE eid='.intval($eid)), 'relationMaturity');

	$maturity_bars .= '<div id="rml_bars" style="text-align:left;">';
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
				$maturity_bars .= '<div id="'.$ermlid.'" class="'.$divclassactive.$positionclass.'" title="'.($lang->{$name} ? $lang->{$name} : $name).'">&nbsp;</div>';
			}
			else {
				$maturity_bars .= '<div id="'.$ermlid.'" class="'.$divclassinactive.$positionclass.'" title="'.($lang->{$name} ? $lang->{$name} : $name).'">&nbsp;</div>';
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
	$maturity_bars .= '<br /><span class="smalltext" style="color:#999; font-style:italic;">'.$rmlcurrentlevel['title'].'</span></div>';

	return $maturity_bars;
}

?>