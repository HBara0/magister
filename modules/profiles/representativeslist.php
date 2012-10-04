<?php
/*
* Orkila Central Online System (OCOS)
* Copyright © 2011 Orkila International Offshore, All Rights Reserved
* List representatives
* $module: profiles
* $id: representativeslist.php
*
* Created:	   @najwa.kassem	August 02, 2011 | 9:37 AM
* Last Update: @zaher.reda 	August 11, 2011 | 09:08 AM
*/

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) { 
	$sort_query = 'name ASC';
	
	if(isset($core->input['sortby'], $core->input['order'])) {
		$sort_query = $db->escape_string($core->input['sortby']).' '.$db->escape_string($core->input['order']);
	}
	
	$sort_url = sort_url();
	$limit_start = 0;
	/* if(!isset($core->input['id']) || empty($core->input['id'])) {
		redirect("index.php?module=profiles/supplierslist");
	} */
	
	if(isset($core->input['start'])) {
		$limit_start = $db->escape_string($core->input['start']);
	}
	
	if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
		$core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
	}
	
	if($core->usergroup['canViewAllCust'] == 0) {
		$multipage_where .= $permission_filter = ' AND e.eid IN (SELECT eid FROM '.Tprefix.'assignedemployees WHERE uid='.$core->user['uid'].')';
	}
	
	if(isset($core->input['filterby'], $core->input['filtervalue'])) {
		switch($core->input['filterby']) {
			case 'entityid': $filter_where = ' WHERE e.eid='.$db->escape_string($core->input['filtervalue']);
							 $multipage_where .= ' e.eid='.$db->escape_string($core->input['filtervalue']);
							 break;
			case 'type': $filter_where = ' WHERE e.type="'.$db->escape_string($core->input['filtervalue']).'"';
				 		 $multipage_where .= ' e.type="'.$db->escape_string($core->input['filtervalue']).'"';
				 		 break;
			default: break;
		}	
	}
	
	$query = $db->query("SELECT r.*, e.companyName, er.eid
						 FROM ".Tprefix."representatives r 
						 JOIN ".Tprefix."entitiesrepresentatives er ON (r.rpid=er.rpid) 
						 JOIN ".Tprefix."entities e ON (e.eid=er.eid)
						 {$filter_where}{$permission_filter}
						 ORDER BY {$sort_query}
						 LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

	if($db->num_rows($query) > 0) {
		while($representative = $db->fetch_assoc($query)) {
			$row_class = alt_row($row_class);
		
			$edit_link = '';
			$edit_link = '<a href="#'.$representative['rpid'].'" id="editrepresentative_'.$representative['rpid'].'_profiles/representativeslist_loadpopupbyid"><img src="./images/icons/edit.gif" border="0" alt="'.$lang->edit.'"/></a>';
			
			$position_query = $db->query("SELECT title FROM ".Tprefix."representativespositions rp JOIN ".Tprefix."positions p ON (rp.posid=p.posid) WHERE rp.rpid={$representative[rpid]} ORDER BY title ASC");
			$positionslist = array();
			if($db->num_rows($position_query) > 0) {
				 while($position = $db->fetch_assoc($position_query)) {
					$positionslist[] = $position['title'];
				}
				$representative['positions'] = implode(',', $positionslist);
			}
			else
			{
				$representative['positions'] = '-';
			}
					
			eval("\$representatives_list .= \"".$template->get('profiles_representativeslist_representativerow')."\";");
		}	
							
		$multipages = new Multipages('representatives r 
									  JOIN '.Tprefix.'entitiesrepresentatives er ON (r.rpid=er.rpid) 
						 		     JOIN '.Tprefix.'entities e ON (e.eid=er.eid)', $core->settings['itemsperlist'], $multipage_where);
		$representatives_list .= '<tr><td colspan="6">'.$multipages->parse_multipages().'</td></tr>'; 
	}
	else
	{
		$representatives_list = '<tr><td colspan="6">'.$lang->nomatchfound.'</td></tr>';
	}
	
	eval("\$listpage = \"".$template->get('profiles_representativeslist')."\";");
	output_page($listpage);
}
else
{	 if($core->input['action'] == 'get_editrepresentative') {
		$rpid = $db->escape_string($core->input['id']);
		
		$representative = $db->fetch_assoc($query = $db->query("SELECT * FROM representatives WHERE rpid={$rpid}"));
		
		$entity = $db->fetch_assoc($db->query("SELECT e.companyName, er.eid, e.type FROM ".Tprefix."entities e JOIN ".Tprefix."entitiesrepresentatives er ON (e.eid=er.eid) WHERE er.rpid={$rpid}"));
		if($entity['type']=='c'){
			$type = 'customer';
		}
		else{
			$type = 'supplier';
		}
		
		$representative['entity'] = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities e JOIN ".Tprefix."entitiesrepresentatives er ON (e.eid=er.eid) WHERE er.rpid={$rpid}"), 'companyName');
		$representative['eid'] = $db->fetch_field($db->query("SELECT eid FROM ".Tprefix."entitiesrepresentatives WHERE rpid={$rpid}"), 'eid');
		
		$representative_positions = get_specificdata('representativespositions rp JOIN positions p ON (rp.posid=p.posid)', 'p.posid', 'posid', 'posid', '', 0, "rp.rpid={$rpid}");
		$representative_segments = get_specificdata('representativessegments rs JOIN productsegments ps ON (rs.psid=ps.psid)', 'ps.psid', 'psid', 'psid', '', 0, "rs.rpid={$rpid}");

		$phones_index = array('phone');
		foreach($phones_index as $val) {

			$phone[$val] = explode('-', $representative[$val]);
			$representative[$val] = array();
			
			$representative[$val]['intcode'] = $phone[$val][0];
			$representative[$val]['areacode'] = $phone[$val][1];
			$representative[$val]['number'] = $phone[$val][2];
		}

		$positions =  get_specificdata('positions', array('posid', 'title'), 'posid', 'title', array('by' => 'title', 'sort' => 'ASC'), 0, '');
		$representative['positions'] = parse_selectlist('positions[]', 1, $positions, $representative_positions, 1);
		
		$segments =  get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', array('by' => 'title', 'sort' => 'ASC'), 0, '');
		$representative['segments'] = parse_selectlist('segments[]', 1, $segments, $representative_segments, 1);
		
		eval("\$editbox = \"".$template->get("popup_profiles_representativeslist_edit")."\";");
		echo $editbox;
	} 
	elseif($core->input['action'] == 'do_edit'){
		if(is_empty($core->input['name'], $core->input['email'], $core->input['eid'])) {
			output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>"); 
			exit;
		} 
		
		if(!isvalid_email($core->input['email'])) {
			output_xml("<status>false</status><message>{$lang->invalidemailaddress}</message>"); 
			exit;
		}
		
		$phones_index = array('phone');
		foreach($phones_index as $val) {
			if(isset($core->input[$val.'_intcode'], $core->input[$val.'_areacode'], $core->input[$val.'_number'])) {
				if(!empty($core->input[$val.'_intcode']) || !empty($core->input[$val.'_areacode']) || !empty($core->input[$val.'_number'])) {
					$core->input[$val] = $core->input[$val.'_intcode'].'-'.$core->input[$val.'_areacode'].'-'.$core->input[$val.'_number'];
				}
				else
				{
					$core->input[$val] = '';
				}
				unset($core->input[$val.'_intcode'], $core->input[$val.'_areacode'], $core->input[$val.'_number']);
			}
		}
	
		$rpid = $db->escape_string($core->input['rpid']);
		$representative = array(
			'name'	 => $core->input['name'],
			'email'	=> $core->input['email'],
			'phone'  	=> $core->input['phone']
		);
				
		$representative_query = $db->update_query('representatives', $representative, "rpid={$rpid}" );
		
		if($representative_query) {
			/* Clean up - Start */
			$db->delete_query('entitiesrepresentatives', "rpid={$rpid}");
			/* Clean up - End */
			$query = $db->insert_query('entitiesrepresentatives', array('eid' => $core->input['eid'], 'rpid' => $rpid), "rpid={$rpid}");
			//$query = $db->update_query('entitiesrepresentatives', array('eid' => $core->input['eid']), "rpid={$rpid}");
			
			if($query) {
				if(is_array($core->input['positions'])) {
					$db->delete_query('representativespositions', "rpid='{$rpid}'");
					foreach($core->input['positions'] as $key => $val) { 
						$position = $db->insert_query('representativespositions', array('posid' => $val, 'rpid' => $rpid));
					}
				}

				if(is_array($core->input['segments'])) {
					$db->delete_query('representativessegments', "rpid='{$rpid}'");
					foreach($core->input['segments'] as $key => $val) { 
						$db->insert_query('representativessegments', array('psid' => $val, 'rpid' => $rpid));
					}
				}
			}
			output_xml("<status>true</status><message>{$lang->updatedsuccessfully}</message>");
		}
		else
		{
			output_xml("<status>false</status><message>{$lang->updateerror}</message>");
		}
	} 
}
?>