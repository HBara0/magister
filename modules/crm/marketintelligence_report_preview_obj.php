<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: marketintelligence_report_preview.php
 * Created:        @tony.assaad    Mar 11, 2014 | 11:07:54 AM
 * Last Update:    @tony.assaad    Mar 11, 2014 | 11:07:54 AM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['crm_canGenerateMIRep'] == 0) {
    // error($lang->sectionpermision);
}
if(!($core->input['action'])) {
    if($core->input['referrer'] == 'generate') {

        $mireportdata = ($core->input['mireport']);

        /* split the dimension and explode them into chuck of array */
        $mireportdata['dimension'] = explode(',', $mireportdata['dimension'][0]);
        $mireportdata['dimension'] = array_filter($mireportdata['dimension']);

        /* to create array using existing values (using array_values()) and range() to create a new range from 1 to the size of the  dimension array */
        $mireportdata['dimension'] = array_combine(range(1, count($mireportdata['dimension'])), array_values($mireportdata['dimension']));

        $marketdata_indexes = array('potential', 'mktSharePerc', 'mktShareQty', 'unitPrice');
        /* get Market intellgence baisc Data  --START */

        /* Get cfpid of segment  ----END */
        $dimensionalize_ob = new DimentionalData();
        /* split the dimension and explode them into chuck of array */

        /* Get cfpid of segment ----START */
        if(isset($mireportdata['filter']['spid'])) {
            $mireportdata['filter']['cfpid'] = 'SELECT cfpid FROM '.Tprefix.'chemfunctionproducts WHERE pid IN (SELECT pid FROM '.Tprefix.'products WHERE spid IN ('.implode(',', $mireportdata['filter']['spid']).'))';
        }

        if(isset($mireportdata['filter']['psid'])) {
            $mireportdata['filter']['psid'] = array_map(intval, $mireportdata['filter']['psid']);
            $mireportdata['filter']['cfpid2'] = 'SELECT cfpid FROM '.Tprefix.'chemfunctionproducts WHERE safid IN (SELECT safid FROM '.Tprefix.'segapplicationfunctions WHERE psaid IN (SELECT psaid FROM '.Tprefix.'segmentapplications WHERE psid IN ('.implode(',', $mireportdata['filter']['psid']).')))';

            if(isset($mireportdata['filter']['cfpid'])) {
                $mireportdata['filter']['cfpid'] .= ' AND cfpid IN ('.$mireportdata['filter']['cfpid2'].')';
            }
            else {
                $mireportdata['filter']['cfpid'] = $mireportdata['filter']['cfpid2'];
            }
        }
        if(isset($mireportdata['filter']['psid']) && !empty($mireportdata['filter']['cfpid'])) {
            $mireportdata['filter']['cfcid'] = 'SELECT cfcid FROM '.Tprefix.'chemfunctionchemcials WHERE safid IN (SELECT safid FROM '.Tprefix.'segapplicationfunctions WHERE psaid IN (SELECT psaid FROM '.Tprefix.'segmentapplications WHERE psid IN ('.implode(',', $mireportdata['filter']['psid']).')))';
        }

        if(isset($mireportdata['filter']['ctype'])) {
            $mireportdata['filter']['ctype'] = array_map($db->escape_string, $mireportdata['filter']['ctype']);  /* apply the call bak function dbescapestring to the the value */
            $mireportdata['filter']['cid'] = 'SELECT eid FROM '.Tprefix.'entities WHERE type IN  (\''.implode('\',\'', $mireportdata['filter']['ctype']).'\')';
        }

        unset($mireportdata['filter']['coid'], $mireportdata['filter']['cfpid2'], $mireportdata['filter']['spid'], $mireportdata['filter']['psid'], $mireportdata['filter']['ctype']);
        /* Get cfpid of segment ----END */

        $marketin_objs = MarketIntelligence::get_marketdata_dal($mireportdata['filter'], array('simple' => false, 'operators' => array('coid' => 'IN', 'cid' => 'IN', 'cfpid' => 'IN', 'cfcid' => 'IN')));

        /* START presentiation layer */
        /* Get the id related to the chemfunctionproducts  from the object and send them to the dimensional data class   */
        if(is_array($marketin_objs)) {
            foreach($marketin_objs as $marketin_obj) {
                $market_data[$marketin_obj->get()['mibdid']] = $marketin_obj->get();
                $customer_obj = new Customers($market_data[$marketin_obj->get()['mibdid']]['cid'], '', false);
                $market_data[$marketin_obj->get()['mibdid']]['ctype'] = $customer_obj->get()['type'];
                $market_data[$marketin_obj->get()['mibdid']]['spid'] = $marketin_obj->get_chemfunctionproducts()->get_produt()->get_supplier()->get()['eid'];
                $market_data[$marketin_obj->get()['mibdid']]['pid'] = $marketin_obj->get_chemfunctionproducts()->get_produt()->pid;
                $market_data[$marketin_obj->get()['mibdid']]['psid'] = $marketin_obj->get_chemfunctionproducts()->get_segapplicationfunction()->get_segment()->psid;
                $market_data[$marketin_obj->get()['mibdid']]['psaid'] = $marketin_obj->get_chemfunctionproducts()->get_segapplicationfunction()->get_application()->psaid;
                if(is_object($marketin_obj->get_chemfunctionschemcials())) {
                    $market_data[$marketin_obj->get()['mibdid']]['csid'] = $marketin_obj->get_chemfunctionschemcials()->get_chemicalsubstance()->csid;
                }
            }
            $dimensionalize_ob = new DimentionalData();
            $mireportdata['dimension'] = $dimensionalize_ob->set_dimensions($mireportdata['dimension']);
            $dimensionalize_ob->set_requiredfields($marketdata_indexes);
            $dimensionalize_ob->set_data($market_data);

            $parsed_dimension = $dimensionalize_ob->get_output(array('outputtype' => 'table', 'noenclosingtags' => true, 'overwritecalculation' => array('mktSharePerc' => array('fields' => array('divider' => 'mktShareQty', 'dividedby' => 'potential'), 'operation' => '/'))));
            $headers_title = $dimensionalize_ob->get_requiredfields();
            foreach($headers_title as $report_header => $header_data) {
                $header_data = strtolower($header_data);
                $dimension_head .= '<th>'.$lang->{$header_data}.'</th>';
            }
        }
        else {
            redirect($_SERVER['HTTP_REFERER'], 2, $lang->nomatchfound);
        }
        /* get Market intellgence baisc Data  --END */
    }

    eval("\$mireport_output = \"".$template->get('crm_marketintelligence_report_output')."\";");
    output_page($mireport_output);
}
?>
