<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managepaymentterms.php
 * Created:        @rasha.aboushakra    Feb 11, 2015 | 10:41:36 AM
 * Last Update:    @rasha.aboushakra    Feb 11, 2015 | 10:41:36 AM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}
if(!$core->input['action']) {
    $sort_url = sort_url();
    if(isset($core->input['sortby']) && !empty($core->input['sortby'])) {
        $dal_config = array(
                'order' => array('by' => $core->input['sortby'], 'sort' => $core->input['order']),
                'returnarray' => true
        );
    }
    else {
        $dal_config = array(
                'returnarray' => true
        );
    }
    $paymentterms = PaymentTerms::get_data('', $dal_config);
    if(is_array($paymentterms)) {
        foreach($paymentterms as $paymentterm) {
            $edit_link = "<a href ='#{$paymentterm->ptid}' id ='addpaymentterms_{$paymentterm->ptid}_entities/paymenttermslist_loadpopupbyid'><img src ='../images/icons/edit.gif' border ='0' alt = '".$lang->editpaymentterm."'/></a>";
            $paymentterm->nextBusinessDayicon = '<img src="../images/false.gif" />';
            if($paymentterm->nextBusinessDay == 1) {
                $paymentterm->nextBusinessDayicon = '<img src="../images/true.gif" />';
            }
            $rowclass = alt_row($rowclass);
            eval("\$paymentterms_rows .= \"".$template->get('admin_entities_paymentterms_rows')."\";");
            $edit_link = '';
        }
    }
    else {
        $paymentterms_rows = '<tr><td colspan="4">'.$lang->nomatchfound.'</td></tr>';
    }

    eval("\$popup_addpaymentterms = \"".$template->get('popup_addpaymentterms')."\";");

    eval("\$paymenttermslist = \"".$template->get('admin_entities_paymentterms_list')."\";");
    output_page($paymenttermslist);
}
else {
    if($core->input['action'] == 'do_perform_addpaymentterms') {
        unset($core->input['identifier'], $core->input['module'], $core->input['action']);
        $exisitingpaymentterm = PaymentTerms::get_data(array('title' => $core->input['paymentterms']['title']));
        if(is_object($exisitingpaymentterm)) {
            if($exisitingpaymentterm->ptid != $core->input['paymentterms']['ptid']) {
                output_xml('<status>false</status><message>'.$lang->titleerror.'</message>');
                exit;
            }
        }
        $paymentterm = new PaymentTerms();
        $paymentterm->set($core->input['paymentterms']);
        $paymentterm->save();
        switch($paymentterm->get_errorcode()) {
            case 0:
            case 1:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                break;
        }
    }
    elseif($core->input['action'] == 'get_addpaymentterms') {
        if(isset($core->input['id']) && !empty($core->input['id'])) {
            $paymentterms = PaymentTerms::get_data(array('ptid' => $core->input['id']));
            if(is_object($paymentterms)) {
                if($paymentterms->nextBusinessDay == 1) {
                    $checked['nextBusinessDay'] = 'checked="checked"';
                }
            }
            else {
                redirect($_SERVER['HTTP_REFERER'], 2, $lang->nomatchfound);
            }
        }
        eval("\$popup_addpaymentterms = \"".$template->get('popup_addpaymentterms')."\";");
        output($popup_addpaymentterms);
    }
}