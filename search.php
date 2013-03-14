<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Search tools
 * $id: search.php
 * Created: 		@zaher.reda			Mar 21, 2009 | 11:40 AM
 * Last Update: 	@zaher.reda			February 28, 2013 | 08:56 AM
 */
require_once 'global.php';

if($core->input['type'] == 'quick') {
	$dofilter = false;
	if(isset($core->input['filter'])) {
		$dofilter = true;
		if(isset($core->input['rid']) && !empty($core->input['rid'])) {
			$report_data = $db->fetch_array($db->query("SELECT affid, spid FROM ".Tprefix."reports WHERE rid='".$db->escape_string($core->input['rid'])."'"));
		}
		else {
			if(isset($core->input['cid']) && !empty($core->input['cid'])) {
				$customer_filter = "er.eid='".$db->escape_string($core->input['cid'])."'";
			}

			if(isset($core->input['spid']) && !empty($core->input['spid'])) {
				if(is_array($core->input['spid'])) {
					if($core->input['for'] == 'product') {
						$supplier_filter = "spid IN ('".$db->escape_string(implode(',', $core->input['spid']))."')";
					}
					else {
						$supplier_filter = "er.eid IN ('".$db->escape_string(implode(',', $core->input['spid']))."')";
					}
				}
				else {
					if($core->input['for'] == 'product') {
						$supplier_filter = "spid='".$db->escape_string($core->input['spid'])."'";
					}
					else {
						$supplier_filter = "er.eid='".$db->escape_string($core->input['spid'])."'";
					}
				}
			}
		}
	}

	if(isset($core->input['for'])) {
		if($core->input['for'] == 'supplier' || $core->input['for'] == 'customer') {
			if($core->input['for'] == 'supplier') {
				$type = 's';
				if($core->usergroup['canViewAllSupp'] == 0) {
					$inentities = implode(',', $core->user['suppliers']['eid']);
					$extra_where = 'eid IN ('.$inentities.')';
				}
			}
			else {
				$type = 'c';
				if($core->usergroup['canViewAllCust'] == 0) {
					$inentities = implode(',', $core->user['customers']);
					$extra_where = 'eid IN ('.$inentities.')';
				}
			}

			$table = 'entities';
			$attributes = array('companyName', 'companyNameAbbr');
			$key_attribute = 'eid';
			$select_attributes = array('companyName');
			$order = array('by' => 'companyName', 'sort' => 'ASC');
			if(!empty($extra_where)) {
				$extra_where .= ' AND type="'.$type.'"';
			}
			else {
				$extra_where = 'type="'.$type.'"';
			}
		}
		elseif($core->input['for'] == 'chemicalproducts') {

			$table = 'chemicalsubstances';
			$attributes = array('name', 'casNum', 'synonyms');
			$key_attribute = 'csid';

			$select_attributes = array('name', 'casNum');
			$order = array('by' => 'name', 'sort' => 'ASC');
		}
		elseif($core->input['for'] == 'product') {
			if(isset($core->input['rid']) && !empty($core->input['rid'])) {
				$extra_where .= 'spid = "'.$report_data['spid'].'"';
			}

			if(!empty($supplier_filter)) {
				$extra_where .= $supplier_filter;
			}

			$table = 'products';
			$attributes = array('name');
			$key_attribute = 'pid';
			$select_attributes = array('name');
			$order = array('by' => 'name', 'sort' => 'ASC');
		}
		elseif($core->input['for'] == 'representative' || $core->input['for'] == 'supprepresentative') {
			if(IN_AREA == 'user') {
				if($core->input['for'] == 'supprepresentative') {
					if(!empty($supplier_filter)) {
						$extra_where = $supplier_filter;
					}
					else {
						if($core->usergroup['canViewAllSupp'] == 0) {
							$inentities = implode(',', $core->user['suppliers']['eid']);
							$extra_where = 'er.eid IN ('.$inentities.')';
						}
					}
					if(!empty($extra_where)) {
						$extra_where_and = ' AND ';
					}
					$extra_where .= $extra_where_and.'e.type="s"';
				}
				else {
					if(!empty($customer_filter)) {
						$extra_where = $customer_filter;
					}
					else {
						if($core->usergroup['canViewAllCust'] == 0) {
							$inentities = implode(',', $core->user['customers']);
							$extra_where = 'er.eid IN ('.$inentities.')';
						}
					}
					if(!empty($extra_where)) {
						$extra_where_and = ' AND ';
					}
					$extra_where .= $extra_where_and.'e.type="c"';
				}
			}

			if(!empty($supplier_filter) || !empty($customer_filter)) {
				$table = Tprefix.'representatives r  JOIN '.Tprefix.'entitiesrepresentatives er ON (r.rpid=er.rpid)  JOIN '.Tprefix.'entities e ON (e.eid=er.eid)';
			}
			else {
				$extra_where = '';
				$table = Tprefix.'representatives r LEFT JOIN '.Tprefix.'entitiesrepresentatives er ON (r.rpid=er.rpid)';
			}
			$attributes = array('r.name', 'r.email');
			$key_attribute = 'r.rpid';
			$select_attributes = array('name', 'email');
			$order = array('by' => 'name', 'sort' => 'ASC');
		}
		elseif($core->input['for'] == 'user') {
			$table = 'users';
			$attributes = array('firstName', 'lastName', 'displayName');
			$key_attribute = 'uid';
			$select_attributes = array('displayName'); //array('Concat(firstName, \' \', lastName) AS employeename'); 
			$order = array('by' => 'firstName', 'sort' => 'ASC');
		}

		if(isset($core->input['exclude']) && !empty($core->input['exclude'])) {
			if(empty($extra_where)) {
				$extra_where = "{$key_attribute} NOT IN ({$core->input[exclude]})";
			}
			else {
				$extra_where .= " AND {$key_attribute} NOT IN ({$core->input[exclude]})";
			}
		}

		$results_list = quick_search($table, $attributes, $core->input['value'], $select_attributes, $key_attribute, $order, $extra_where);
		$referrer = explode('&', $_SERVER['HTTP_REFERER']);
		$module = substr($referrer[0], strpos(strtolower($referrer[0]), 'module=') + 7);
		if($core->input['for'] == 'supplier') {
			if(strpos(strtolower($_SERVER['HTTP_REFERER']), ADMIN_DIR) !== false) {
				$results_list .= "<p><hr />&rsaquo;&rsaquo; <a href='index.php?module=entities/add&amp;type=supplier' target='_blank'>{$lang->add}</a></p>";
			}
			else {
				$results_list .= "<p><hr />&rsaquo;&rsaquo; <a href='index.php?module=contents/addentities&amp;type=supplier' target='_blank'>{$lang->add}</a></p>";
			}
		}
		/* else
		  {
		  $results_list .= "<p><hr />&rsaquo;&rsaquo; <a href='#' id='addnew_{$module}_".$core->input['for']."'>{$lang->add}</a></p>";
		  } */
		output_page($results_list);
	}
}
?>