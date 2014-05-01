<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Generate a report for preview and export
 * $module: reporting
 * $id: preview.php	
 * Last Update: @zaher.reda 	June 6, 2013 | 11:33 AM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canGenerateReports'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('reporting_generatereport');
if(!$core->input['action']) {
    $quarter = currentquarter_info();

    $quarter_selected[$quarter['quarter']] = ' selected="selected"';
    $additional_where = getquery_entities_viewpermissions();

    /* Parse available years - Start */
    $years_list = "<option value='0'>&nbsp;</option>";
    $query = $db->query("SELECT DISTINCT(year) 
						FROM ".Tprefix."reports r
						WHERE quarter='{$quarter[quarter]}' AND status='1' AND type='q'{$additional_where[extra]}
						ORDER BY year ASC");
    while($year = $db->fetch_array($query)) {
        $year_selected = '';
        if($year['year'] == $quarter['year']) {
            $year_selected = ' selected="selected"';
        }
        $years_list .= "<option value='{$year[year]}'{$year_selected}>{$year[year]}</option>";
    }

    /* Parse available years - End */

    /* Parse available suppliers - Start */
    $available_suppliers = "<option value='0'>&nbsp;</option>";
    $query = $db->query("SELECT DISTINCT(s.companyName), r.spid 
				FROM ".Tprefix."entities s LEFT JOIN ".Tprefix."reports r ON (r.spid=s.eid) 
				WHERE r.quarter='{$quarter[quarter]}' AND r.year='{$quarter[year]}' AND r.status='1'{$additional_where[extra]}
				ORDER BY s.companyName ASC");

    while($supplier = $db->fetch_array($query)) {
        $available_suppliers .= "<option value='{$supplier[spid]}'>{$supplier[companyName]}</option>";
    }
    /* Parse available suppliers - End */

    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where = " AND affid IN ({$inaffiliates})";
    }
    $query = $db->query("SELECT DISTINCT(affid) FROM ".Tprefix."reports WHERE type='q'{$affiliate_where}");

    if($db->num_rows($query) == 0) {
        $message = $lang->noreportsavailable;
        eval("\$generatepage = \"".$template->get('reporting_generatereport')."\";");
        output_page($generatepage);
        exit;
    }
    while($affiliate = $db->fetch_array($query)) {
        $availableaffiliates .= $comma.$affiliate['affid'];
        $comma = ',';
    }

    $affiliates_attributes = array('affid', 'name');
    $affiliates_order = array('by' => 'name', 'sort' => 'ASC');
    $affiliates = get_specificdata('affiliates', $affiliates_attributes, 'affid', 'name', $affiliates_order, 1, "affid IN ({$availableaffiliates})");
    $affiliates_list = parse_selectlist('affid', 1, $affiliates, '');

    eval("\$generatepage = \"".$template->get('reporting_generatereport')."\";");
    output_page($generatepage);
}
else {
    if($core->input['action'] == "get_years") {
        $quarter = $db->escape_string($core->input['id']);

        $additional_where = getquery_entities_viewpermissions();

        $years_list = "<option value='0'>&nbsp;</option>";
        $query = $db->query("SELECT DISTINCT(year) 
							FROM ".Tprefix."reports r
							WHERE quarter='{$quarter}' AND status='1' AND type='q'{$additional_where[extra]}
							ORDER BY year ASC");
        while($year = $db->fetch_array($query)) {
            $years_list .= "<option value='{$year[year]}'>{$year[year]}</option>";
        }
        echo $years_list;
    }
    elseif($core->input['action'] == 'get_affiliateslist') {
        if(isset($core->input['year'])) {
            $year = $db->escape_string($core->input['year']);
            $spid = $db->escape_string($core->input['id']);
            $supplier_where = " AND r.spid='{$spid}' ";
            $additional_where = getquery_entities_viewpermissions('affiliatebyspid', $spid);
        }
        else {
            $year = $db->escape_string($core->input['id']);
            $additional_where = getquery_entities_viewpermissions();
        }
        $quarter = $db->escape_string($core->input['quarter']);

        /* if($core->usergroup['canViewAllAff'] == 0) {
          $inaffiliates = implode(',', $core->user['affiliates']);
          $extra_where = " AND r.affid IN ({$inaffiliates}) ";
          }
          if($core->usergroup['canViewAllSupp'] == 0) {
          $insuppliers = implode(',', $core->user['suppliers']);
          $extra_where .= " AND r.spid IN ({$insuppliers}) ";
          } */

        $affiliates_list = "<option value='0'>&nbsp;</option>";
        $query = $db->query("SELECT DISTINCT(a.name), r.affid 
							FROM ".Tprefix."affiliates a JOIN ".Tprefix."reports r ON (r.affid=a.affid) 
							WHERE r.quarter='{$quarter}' AND r.year='{$year}' AND r.status='1'{$additional_where[extra]}{$supplier_where}
							ORDER BY a.name ASC");
        while($affiliate = $db->fetch_array($query)) {
            $affiliates_list .= "<option value='{$affiliate[affid]}'>{$affiliate[name]}</option>";
        }
        echo $affiliates_list;
    }
    elseif($core->input['action'] == 'get_supplierslist') {
        if(isset($core->input['year'])) {
            $year = $db->escape_string($core->input['year']);
            $affid = $db->escape_string($core->input['id']);
            $affiliate_where = " AND r.affid='{$affid}' ";
            $additional_where = getquery_entities_viewpermissions('suppliersbyaffid', $affid);
        }
        else {
            $year = $db->escape_string($core->input['id']);
            $additional_where = getquery_entities_viewpermissions();
        }

        $quarter = $db->escape_string($core->input['quarter']);

        /* if($core->usergroup['canViewAllAff'] == 0) {
          $inaffiliates = implode(',', $core->user['affiliates']);
          $extra_where = " AND r.affid IN ({$inaffiliates}) ";
          }

          if($core->usergroup['canViewAllSupp'] == 0) {
          $insuppliers = implode(',', $core->user['suppliers']);
          $extra_where .= " AND r.spid IN ({$insuppliers}) ";
          } */

        $suppliers_list = "<option value='0'>&nbsp;</option>";
        $query = $db->query("SELECT DISTINCT(s.companyName), r.spid 
							FROM ".Tprefix."entities s LEFT JOIN ".Tprefix."reports r ON (r.spid=s.eid) 
							WHERE r.quarter='{$quarter}' AND r.year='{$year}' AND r.status='1'{$additional_where[extra]}{$affiliate_where}
							ORDER BY s.companyName ASC");

        while($supplier = $db->fetch_array($query)) {
            $suppliers_list .= "<option value='{$supplier[spid]}'>{$supplier[companyName]}</option>";
        }
        echo $suppliers_list;
    }
}
?>