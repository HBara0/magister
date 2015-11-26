<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managearodouments.php
 * Created:        @tony.assaad    Feb 11, 2015 | 11:53:19 AM
 * Last Update:    @tony.assaad    Feb 11, 2015 | 11:53:19 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['canUseAro'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!($core->input['action'])) {
    /* Order idendtifications */
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = $core->user['affiliates'];
    }
    if(is_array($inaffiliates)) {
        foreach($inaffiliates as $affid) {
            $affiliate[$affid] = new Affiliates($affid);
        }
    }
    else {
        $affiliate = Affiliates::get_affiliates(array('isActive' => 1), array('returnarray' => true));
    }
    $intermedaffiliates = Affiliates::get_affiliates(array('isActive' => 1), array('returnarray' => true));

    $purchasetypes = PurchaseTypes::get_data('name IS NOT NULL', array('returnarray' => true));
    if(!is_array($purchasetypes)) {
        error($lang->missingconfigurations.' (Purchase Types)');
    }
    $dal_config = array('returnarray' => true);
    $inspections = array('Pre-Shipment' => 'Pre-Shipment', ' Post-Shipment' => ' Post-Shipment', 'Pre and Post Shipment' => 'Pre and Post Shipment', ' To Be Advised Later' => ' To Be Advised Later', 'none' => 'none');
    $payment_terms = PaymentTerms::get_data('', array('returnarray' => true, 'order' => array('sort' => 'ASC', 'by' => 'overduePaymentDays')));
    $segments = ProductsSegments::get_segments('', array('order' => array('sort' => 'ASC', 'by' => 'title')));
    $packaging = Packaging::get_data('name IS NOT NULL', $dal_config);
    $uom = Uom::get_data(array('isWeight' => 1), $dal_config);
    $uom_where = ' isWeight=1 OR isArea=1 OR isVolume=1';
    $warehouseuoms = Uom::get_data($uom_where, $dal_config);
    $mainaffobj = new Affiliates($core->user['mainaffiliate']);
    $currencies = Currencies::get_data('', array('order' => array('sort' => 'ASC', 'by' => 'name')));
    $incoterms = Incoterms::get_data('name IS NOT NULL', $dal_config);
    $countries = Countries::get_data('', $dal_config);

    $aro_display['prtiesinfo']['discount'] = "display:inline-block;";
    if($core->usergroup['aro_canMakeDiscounts'] == 0) {
        $aro_display['prtiesinfo']['discount'] = "display:none;";    //change al other display variables to this array
    }
    $aroorderrequest = new AroRequests();
    $helptour = new HelpTour();
    $helptour->set_id('aro_helptour');
    $helptour->set_cookiename('aro_helptour');
    $helptouritems_obj = new HelpTourItems();
    $touritems = $helptouritems_obj->get_helptouritems('aro');
    if(is_array($touritems)) {
        $helptour->set_items($touritems);
        $helptour_output = $helptour->parse();
    }

    if(!isset($core->input['id'])) {
        //order identification
        $affiliate_list = parse_selectlist('affid', 1, $affiliate, $orderid[affid], '', '', array('blankstart' => true, 'id' => "affid", 'required' => 'required'));
        $purchasetypelist = parse_selectlist('orderType', 4, $purchasetypes, $orderid['ptid'], '', '', array('blankstart' => true, 'id' => "purchasetype", 'required' => 'required'));
        $currencies_list = parse_selectlist('currency', 4, $currencies, '', '', '', array('blankstart' => 1, 'id' => "currencies", 'required' => 'required'));
        $inspectionlist = parse_selectlist('inspectionType', 4, $inspections, 'none');

        //order Customers
        $customeroder['inputChecksum'] = generate_checksum('ucl');
        $rowid = 0;
        $altpayment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
        eval("\$unspecified_customer_row = \"".$template->get('aro_unspecifiedcustomer_row')."\";");
        $rowid++;
        $customeroder['inputChecksum'] = generate_checksum('cl');
        $payment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
        eval("\$aro_managedocuments_ordercustomers_rows = \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");

        //product Lines
        $plrowid = 1;
        $productline['inputChecksum'] = generate_checksum('pl');
        $segments_selectlist = parse_selectlist('productline['.$plrowid.'][psid]', '', $segments, '', null, null, array('id' => "productline_".$plrowid."_psid", 'placeholder' => 'Overwrite Segment', 'width' => '100%'));
        $packaging_list = parse_selectlist('productline['.$plrowid.'][packing]', '', $packaging, '', '', '', array('id' => "productline_".$plrowid."_packing", 'blankstart' => 1));
        $kg = Uom::get_data(array('name' => 'Kilogram'));
        $uom_list = parse_selectlist('productline['.$plrowid.'][uom]', '', $uom, $kg->uomid, '', '', array('id' => "productline_".$plrowid."_uom", 'blankstart' => 1, 'width' => '70px'));
        eval("\$aroproductlines_rows = \"".$template->get('aro_productlines_row')."\";");

        $aprowid = 0;
        $csrowid = 0;

        //Net Margin Parameters
        $partiesinfo['required_intermedpolicy'] = "required='required'";
        $netmarginparms_uomlist = parse_selectlist('parmsfornetmargin[uom]', '', $warehouseuoms, '', '', '', array('id' => "parmsfornetmargin_uom", 'blankstart' => 1, 'width' => '100px'));

        //Parties Information
        $parties = array('intermed', 'vendor');
        foreach($parties as $party) {
            $config_class = '';
            if($party == 'intermed') {
                $isdisabled = $disabled_list;
                $config_class = 'automaticallyfilled-editable';
            }
            $affiliates_list[$party] = parse_selectlist('partiesinfo['.$party.'Aff]', 1, $intermedaffiliates, '', '', '', array('blankstart' => 1, 'id' => 'partiesinfo_'.$party.'_aff', 'required' => 'required', 'width' => '100%', 'class' => $config_class));
            $paymentterms_list[$party] = parse_selectlist('partiesinfo['.$party.'PaymentTerm]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => 'partiesinfo_'.$party.'_paymentterm', 'required' => 'required', 'width' => '100%', 'class' => $config_class));
            $incoterms_list[$party] = parse_selectlist('partiesinfo['.$party.'Incoterms]', 4, $incoterms, '', '', '', array('blankstart' => 1, 'id' => 'partiesinfo_'.$party.'_incoterms', 'required' => 'required', 'width' => '100%', 'class' => $config_class));
        }


        eval("\$takeactionpage = \"".$template->get('aro_managearodocuments_takeaction')."\";");
        $takeactionpage = '<a class="header" href="#"><h2 id="aro_discussions">'.$lang->aromessages.'</h2></a><div style="margin-top:10px;">'.$takeactionpage.'</div>';


        $countryofshipment_list = parse_selectlist('partiesinfo[shipmentCountry]', '', $countries, '', '', '', array('blankstart' => 1, 'width' => '150px'));
        $countryoforigin_list = parse_selectlist('partiesinfo[originCountry]', '', $countries, '', '', '', array('blankstart' => 1, 'width' => '150px'));
        $display = 'style="display:none;"';
        $aropartiesinfo_obj = new AroRequestsPartiesInformation();
        $aropartiesinfo_obj->totalDiscount = $aropartiesinfo_obj->commFromIntermed = 0;
        $aro_display['prtiesinfo']['forwarder'] = 'style="display:none;"';
        eval("\$interm_vendor = \"".$template->get('aro_partiesinfo_intermediary_vendor')."\";");
        eval("\$partiesinfo_shipmentparameters = \"".$template->get('aro_partiesinfo_shipmentparameters')."\";");
        eval("\$partiesinfo_fees = \"".$template->get('aro_partiesinfo_fees')."\";");
        unset($aropartiesinfo_obj);
    }
    if(isset($core->input['id'])) {
        $aroorderrequest = AroRequests::get_data(array('aorid' => $core->input['id']), array('simple' => false));
        if(isset($aroorderrequest->aroBusinessManager) && !empty($aroorderrequest->aroBusinessManager)) {
            $aro_bm = Users::get_data(array('uid' => $aroorderrequest->aroBusinessManager));
            if(is_object($aro_bm)) {
                $aroorderrequest->aroBusinessManager_output = $aro_bm->get_displayname();
            }
        }
        if(isset($core->input['referrer']) && $core->input['referrer'] = 'toapprove') {
            $aroapproval = AroRequestsApprovals::get_data(array('aorid' => intval($core->input['id']), 'uid' => $core->user['uid']));
            $approve_btn[$core->user['uid']] = '<input type="button" class="button" id="approvearo" value="'.$lang->approve.'"/>'
                    .'<input type="hidden" id="approvearo_id" value="'.$aroorderrequest->aorid.'"/>';
        }
        if(is_object($aroorderrequest)) {
            if(!$aroorderrequest->getif_approvedonce($aroorderrequest->aorid) && $aroorderrequest->createdBy == $core->user['uid']) {
                $deletebutton = "<a class='button' href='#{$aroorderrequest->aorid}' id='deletearodocument_{$aroorderrequest->aorid}_aro/managearodouments_loadpopupbyid' >{$lang->delete}</a>";
            }
            if($aroorderrequest->isFinalized == 1) {
                $checked['aroisfinalized'] = 'checked="checked"';
            }
            $purchasetype = new PurchaseTypes($aroorderrequest->orderType);

            $affiliate_list = parse_selectlist('affid', 1, $affiliate, $aroorderrequest->affid, '', '', array('blankstart' => true, 'id' => 'affid', 'required' => 'required'));
            $purchasetypelist = parse_selectlist('orderType', 4, $purchasetypes, $aroorderrequest->orderType, '', '', array('blankstart' => true, 'id' => 'purchasetype', 'required' => 'required'));
            $currencies_list = parse_selectlist('currency', 4, $currencies, $aroorderrequest->currency, '', '', array('blankstart' => 1, 'id' => 'currencies', 'required' => 'required'));
            $inspectionlist = parse_selectlist('inspectionType', 4, $inspections, $aroorderrequest->inspectionType);
            //*********Aro Order Customers -Start *********//
            $requestcustomers = AroOrderCustomers::get_data(array('aorid' => $aroorderrequest->aorid), array('returnarray' => true));
            $rowid = 1;
            if($aroorderrequest->avgLocalInvoiceDueDate != 0) {
                $avgeliduedate = date($core->settings['dateformat'], $aroorderrequest->avgLocalInvoiceDueDate);
            }
            if(is_array($requestcustomers)) {
                foreach($requestcustomers as $customer) {
                    $customeroder = $customer->get();
                    if($customeroder['paymentTermBaseDate'] != 0) {
                        $customeroder['paymenttermbasedate_output'] = date($core->settings['dateformat'], $customeroder['paymentTermBaseDate']);
                    }
                    if($customeroder['paymentTermBaseDate'] != 0) {
                        $customeroder['paymenttermbasedate_formatted'] = date('d-m-Y', $customeroder['paymentTermBaseDate']);
                    }
                    if($customeroder['cid'] == 0) {
                        $unspecifiedcust = $customeroder;
                        continue;
                    }
                    $customer = new Customers($customeroder['cid']);
                    $payment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, $customeroder['ptid'], '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid, 'required' => 'required'));
                    $customeroder['customerName'] = $customer->get_displayname();
                    eval("\$aro_managedocuments_ordercustomers_rows .= \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");
                    $rowid++;
                }
                $clrowid = $rowid - 1;
                $rowid = 0;
                //Always parse the unspecified customer row
                unset($customeroder);
                if(isset($unspecifiedcust) && !empty($unspecifiedcust)) {
                    $customeroder = $unspecifiedcust;
                }
                else {
                    $customeroder['inputChecksum'] = generate_checksum('ucl');
                }
                $altpayment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, $unspecifiedcust['ptid'], '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
                eval("\$unspecified_customer_row = \"".$template->get('aro_unspecifiedcustomer_row')."\";");
                $rowid = $clrowid;

                // If only unspecified customer row exist, parse default customer order row
                if(empty($aro_managedocuments_ordercustomers_rows)) {
                    unset($customeroder);
                    $customeroder['inputChecksum'] = generate_checksum('cl');
                    $rowid = 1;
                    $payment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
                    eval("\$aro_managedocuments_ordercustomers_rows .= \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");
                }
            }
            else {
                $customeroder['inputChecksum'] = generate_checksum('ucl');
                $rowid = 0;
                $altpayment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
                eval("\$unspecified_customer_row = \"".$template->get('aro_unspecifiedcustomer_row')."\";");
                $rowid++;
                $customeroder['inputChecksum'] = generate_checksum('cl');
                $payment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
                eval("\$aro_managedocuments_ordercustomers_rows = \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");
            }
            //*********Aro Order Customers - End *********//
            //*********Parameters Influencing Net Margin Calculation -Start ********//
            if($purchasetype->qtyIsNotStored == 1) {
                $disabled['warehousing'] = 'disabled="disabled"';
                $readonly['warehousing'] = 'readonly="readonly"';
            }
            $netmarginparms = AroNetMarginParameters::get_data(array('aorid' => $core->input['id']));
            if(is_object($netmarginparms)) {
                $netmarginparms_uomlist = parse_selectlist('parmsfornetmargin[uom]', '', $warehouseuoms, $netmarginparms->uom, '', '', array('id' => "parmsfornetmargin_uom", 'blankstart' => 1, 'width' => '100px'));
                $warehouse = Warehouses::get_data(array('wid' => $netmarginparms->warehouse));
                $warehouse_list = '<select '.$disabled['warehousing'].'><option value='.$netmarginparms->warehouse.' selected>'.$warehouse->name.'</option>'
                        .'<option value="0"></option></select>';
                $netmarginparms_warehousingRate = '<option value = "'.$netmarginparms->warehousingRate.'">'.$netmarginparms->warehousingRate.'</option>';
                $netmarginparms_warehousingRateUsd = '<option value = "'.$netmarginparms->warehousingRateUsd.'">'.$netmarginparms->warehousingRateUsd.'</option>';
            }
            //*********Parameters Influencing Net Margin Calculation -End ********//
            //********** ARO Product Lines -Start **************//
            $plrowid = 0;
            $productlines = AroRequestLines::get_data(array('aorid' => $aroorderrequest->aorid), array('returnarray' => true, 'order' => array('by' => 'inputChecksum', 'sort' => 'ASC')));
            if(is_array($productlines)) {
                foreach($productlines as $line) {
                    $plrowid++;
                    $productline = $line->get();
                    $segments_selectlist = parse_selectlist('productline['.$plrowid.'][psid]', '', $segments, $productline['psid'], null, null, array('id' => "productline_".$plrowid."_psid", 'placeholder' => 'Overwrite Segment', 'width' => '100%'));
                    $packaging_list = parse_selectlist('productline['.$plrowid.'][packing]', '', $packaging, $productline['packing'], '', '', array('id' => "productline_".$plrowid."_packing", 'blankstart' => 1));
                    $uom_list = parse_selectlist('productline['.$plrowid.'][uom]', '', $uom, $productline['uom'], '', '', array('id' => "productline_".$plrowid."_uom", 'blankstart' => 1, 'width' => '70px'));
                    $product = new Products($productline['pid']);
                    $productline[productName] = $product->get_displayname();
                    if($purchasetype->qtyIsNotStored == 1) {
                        $disabled_fields['daysInStock'] = $disabled_fields['qtyPotentiallySold'] = 'readonly = "readonly"';
                    }
                    if($productline['daysInStock'] == 0) {
                        $disabled_fields['qtyPotentiallySold'] = 'readonly = "readonly"';
                    }
                    eval("\$aroproductlines_rows .= \"".$template->get('aro_productlines_row')."\";");
                    unset($disabled_fields);
                }
            }
            else {
                $productline['inputChecksum'] = generate_checksum('pl');
                $segments_selectlist = parse_selectlist('productline['.$plrowid.'][psid]', '', $segments, '', null, null, array('id' => "productline_".$plrowid."_psid", 'placeholder' => 'Overwrite Segment', 'width' => '100%'));
                $packaging_list = parse_selectlist('productline['.$plrowid.'][packing]', '', $packaging, '', '', '', array('id' => "productline_".$plrowid."_packing", 'blankstart' => 1));
                $kg = Uom::get_data(array('name' => 'Kilogram'));
                $uom_list = parse_selectlist('productline['.$plrowid.'][uom]', '', $uom, $kg->uomid, '', '', array('id' => "productline_".$plrowid."_uom", 'blankstart' => 1, 'width' => '70px'));
                eval("\$aroproductlines_rows .= \"".$template->get('aro_productlines_row')."\";");
            }
            //********** ARO Product Lines **************//
            //*********Aro Actual Purchase -Start *********//
            $aroreqlinesupervision = AroRequestLinesSupervision::get_data(array('aorid' => $aroorderrequest->aorid), array('returnarray' => true, 'order' => array('by' => 'inputChecksum', 'sort' => 'ASC')));

            $aprowid = 0;
            if(is_array($aroreqlinesupervision)) {
                foreach($aroreqlinesupervision as $actualpurchase) {
                    $aprowid++;
                    $products = new Products($actualpurchase->pid);
                    $actualpurchase->productName = $products->get_displayname();
                    if($actualpurchase->estDateOfStockEntry != 0) {
                        $actualpurchase->estDateOfStockEntry_output = date($core->settings['dateformat'], $actualpurchase->estDateOfStockEntry);
                        $actualpurchase->estDateOfStockEntry_formatted = date('d-m-Y', $actualpurchase->estDateOfStockEntry);
                    }
                    if($actualpurchase->estDateOfSale != 0) {
                        $actualpurchase->estDateOfSale_output = date($core->settings['dateformat'], $actualpurchase->estDateOfSale);
                        $actualpurchase->estDateOfSale_formatted = date('d-m-Y', $actualpurchase->estDateOfSale);
                    }
                    $packing = new Packaging($actualpurchase->packing);
                    $actualpurchase->packingTitle = $packing->get_displayname();
                    eval("\$actualpurchase_rows .= \"".$template->get('aro_actualpurchase_row')."\";");
                }
            }
            //*********Aro Actual Purchase-End *********//
            //********Current Stock -Start *********//
            $arocurrentstockrows = AroRequestsCurStkSupervision::get_data(array('aorid' => $aroorderrequest->aorid), array('returnarray' => true, 'order' => array('by' => 'inputChecksum', 'sort' => 'ASC')));
            $csrowid = 0;
            if(is_array($arocurrentstockrows)) {
                foreach($arocurrentstockrows as $currentstock) {
                    $csrowid++;
                    $products = new Products($currentstock->pid);
                    $currentstock->productName = $products->get_displayname();
                    if($currentstock->dateOfStockEntry != 0) {
                        $currentstock->dateOfStockEntry_output = date($core->settings['dateformat'], $currentstock->dateOfStockEntry);
                    }
                    if($currentstock->estDateOfSale != 0) {
                        $currentstock->estDateOfSale_output = date($core->settings['dateformat'], $currentstock->estDateOfSale);
                    }
                    if($currentstock->expiryDate != 0) {
                        $currentstock->expiryDate_output = date($core->settings['dateformat'], $currentstock->expiryDate);
                    }
                    $packing = new Packaging($currentstock->packing);
                    $currentstock->packingTitle = $packing->get_displayname();
                    eval("\$currentstock_rows .= \"".$template->get('aro_currentstock_row')."\";");
                }
            }
            //*********Current Stock -End *********//
            //*********Total Funds Engaged -Start *********//
            $totalfunds = AroRequestsFundsEngaged::get_data(array('aorid' => $aroorderrequest->aorid));

            //*********Total Funds Engaged -End   *********//
            //*********Aro Audit Trail -Start *********//
            if($aroorderrequest->createdOn != 0) {
                $aroorderrequest->createdOn_output = date($core->settings['dateformat'], $aroorderrequest->createdOn);
            }
            if($aroorderrequest->modifiedOn != 0) {
                $aroorderrequest->modifiedOn_output = date($core->settings['dateformat'], $aroorderrequest->modifiedOn);
            }
            $createdby_username = new Users($aroorderrequest->createdBy);
            if(!empty($aroorderrequest->modifiedBy)) {
                $modifiedby_username = new Users($aroorderrequest->modifiedBy);
                $aroorderrequest->modifiedBy_output = $modifiedby_username->parse_link($attributes_param = array('target' => '__blank'));
            }
            $aroorderrequest->createdBy_output = $createdby_username->parse_link($attributes_param = array('target' => "_blank"));
            $aroorderrequest->revision_output = $aroorderrequest->revision;
            eval("\$aro_managedocuments_audittrail_rows .= \"".$template->get('aro_managedocuments_audittrail_rows')."\";");
            eval("\$aro_audittrail= \"".$template->get('aro_managedocuments_audittrail')."\";");

            //*********Aro Audit Trail -End *********//
            //
            //*********Aro Parties Information -Start *********//
            if($purchasetype->needsIntermediary == 1) {
                $partiesinfo['required_intermedpolicy'] = "required='required'";
            }
            else {
                $partiesinfo['required_intermedpolicy'] = "";
            }
            $aropartiesinfo_obj = AroRequestsPartiesInformation::get_data(array('aorid' => $aroorderrequest->aorid));
            $parties = array('intermed', 'vendor');
            $disabled_list = '';
            $aff['intermed'] = $aff['vendor'] = 0;
            if(is_object($aropartiesinfo_obj)) {
                $aff['intermed'] = $aropartiesinfo_obj->intermedAff;
                $aff['vendor'] = $aropartiesinfo_obj->vendorAff;
                $paymentterm['intermed'] = $aropartiesinfo_obj->intermedPaymentTerm;
                $paymentterm['vendor'] = $aropartiesinfo_obj->vendorPaymentTerm;
                if($aropartiesinfo_obj->intermedPTIsThroughBank == 1) {
                    $checked['intermedPTIsThroughBank'] = 'checked="checked"';
                }
                if($aropartiesinfo_obj->vendorPTIsThroughBank == 1) {
                    $checked['vendorPTIsThroughBank'] = 'checked="checked"';
                }
                $selected_incoterms['intermed'] = $aropartiesinfo_obj->intermedIncoterms;
                $selected_incoterms['vendor'] = $aropartiesinfo_obj->vendorIncoterms;

                //show /hide forwarder fields based on vendor incoterms
                $aro_display['prtiesinfo']['forwarder'] = 'style="display:none;"';
                if($selected_incoterms['intermed'] != $selected_incoterms['vendor']) {
                    $vendorincoterm_obj = new Incoterms($selected_incoterms['vendor']);
                    if(is_object($vendorincoterm_obj) && $vendorincoterm_obj->carriageOnBuyer == 1) {
                        $aro_display['prtiesinfo']['forwarder'] = 'style="display:block;"';
                    }
                }
                ////////////////////////////////////
                $shipmentcountry = $aropartiesinfo_obj->shipmentCountry;
                $origincountry = $aropartiesinfo_obj->originCountry;
                if($aropartiesinfo_obj->vendorIsAff == 1) {
                    $checked['vendorisaff'] = 'checked="checked"';
                    $display = 'style="display:block;"';
                    $is_disabled = 'disabled="disabled"';
                    $disabled_list = 'disabled';
                }
                else {
                    $vendor = new Entities($aropartiesinfo_obj->vendorEid);
                    $vendor_displayname = $vendor->get_displayname();
                    $display = 'style="display:none;"';
                }
                $fields = array('vendorEstDateOfPayment', 'intermedEstDateOfPayment', 'promiseOfPayment', 'estDateOfShipment');
                foreach($fields as $field) {
                    if($aropartiesinfo_obj->$field != 0) {
                        $partiesinfo[$field.'_output'] = date('d-m-Y', $aropartiesinfo_obj->$field);
                        $partiesinfo[$field.'_formatted'] = date($core->settings['dateformat'], $aropartiesinfo_obj->$field);
                    }
                }
                $partiesinfo['diffbtwpaymentdates'] = date_diff(date_create($partiesinfo['vendorEstDateOfPayment_output']), date_create($partiesinfo['intermedEstDateOfPayment_output']));
                $partiesinfo['diffbtwpaymentdates'] = $partiesinfo['diffbtwpaymentdates']->format("%r%a");
                $fees = array('freight', 'bankFees', 'insurance', 'otherFees', 'legalization', 'courier');
                foreach($fees as $fee) {
                    $partiesinfo['totalintermedfees'] +=$aropartiesinfo_obj->$fee;
                }
                $partiesinfo['totalinterestvaluefees'] += $netmarginparms->interestValue;
                $partiesinfo['totalfees'] = $partiesinfo['totalintermedfees'] + $partiesinfo['totalinterestvaluefees'];
            }
            else {
                $display = 'style="display:none;"';
            }
            foreach($parties as $party) {
                $config_class = '';
                if($party == 'intermed') {
                    $isdisabled = $disabled_list;
                    $config_class = 'automaticallyfilled-editable';
                }
                $affiliates_list[$party] = parse_selectlist('partiesinfo['.$party.'Aff]', 1, $intermedaffiliates, $aff[$party], '', '', array('blankstart' => true, 'id' => 'partiesinfo_'.$party.'_aff', 'required' => $partiesinfo['required_intermedpolicy'], 'width' => '100%', 'class' => $config_class, $isdisabled => $isdisabled));
                $paymentterms_list[$party] = parse_selectlist('partiesinfo['.$party.'PaymentTerm]', 4, $payment_terms, $paymentterm[$party], '', '', array('blankstart' => 1, 'id' => 'partiesinfo_'.$party.'_paymentterm', 'required' => $partiesinfo['required_intermedpolicy'], 'width' => '100%', 'class' => $config_class, $isdisabled => $isdisabled));
                $incoterms_list[$party] = parse_selectlist('partiesinfo['.$party.'Incoterms]', 4, $incoterms, $selected_incoterms[$party], '', '', array('blankstart' => 1, 'id' => 'partiesinfo_'.$party.'_incoterms', 'required' => $partiesinfo['required_intermedpolicy'], 'width' => '100%', 'class' => $config_class, $isdisabled => $isdisabled));
                $isdisabled = '';
            }
            $countryofshipment_list = parse_selectlist('partiesinfo[shipmentCountry]', '', $countries, $shipmentcountry, '', '', array('blankstart' => 1, 'width' => '150px'));
            $countryoforigin_list = parse_selectlist('partiesinfo[originCountry]', '', $countries, $origincountry, '', '', array('blankstart' => 1, 'width' => '150px'));

            eval("\$interm_vendor = \"".$template->get('aro_partiesinfo_intermediary_vendor')."\";");
            eval("\$partiesinfo_shipmentparameters = \"".$template->get('aro_partiesinfo_shipmentparameters')."\";");
            eval("\$partiesinfo_fees = \"".$template->get('aro_partiesinfo_fees')."\";");
            //*********Aro Parties Information-End *********//
            $aroapprovalchain = AroRequestsApprovals::get_data(array('aorid' => $aroorderrequest->aorid), array('returnarray' => true, 'simple' => false, 'order' => array('by' => 'sequence', 'sort' => 'ASC')));
            if(is_array($aroapprovalchain)) {

                $apprs = '<td class="subtitle" style="border-right: 1px dashed ;margin-right:10px;"><span style="font-weight:bold;">'.$lang->position.'</span><br/>
                    <span style="width:100%;font-weight:bold;">'.$lang->approver.'</span><br/><br/>
                     <span style="width:100%;font-weight:bold;">'.$lang->dateofapprovalemail.'<small> (GMT)</small></span><br/><br/><br/><span style="width:100%;font-weight:bold;">'.$lang->dateofapproval.'<small> (GMT)</small></span></td>';

                foreach($aroapprovalchain as $approver) {
                    switch($approver->position) {// needs optimization
                        case 'businessManager':
                            $position = 'Local Business Manager';
                            break;
                        case 'lolm':
                            $position = 'Local Logistics Manager';
                            break;
                        case 'lfinancialManager':
                            $position = 'Local Finance Manager';
                            break;
                        case 'generalManager':
                            $position = 'General Manager';
                            break;
                        case 'gfinancialManager':
                            $position = 'Global Finance Manager';
                            break;
                        case 'cfo':
                            $position = $lang->globalcfo;
                            break;
                        case 'coo':
                            $position = 'Global COO';
                            break;
                        case 'regionalSupervisor':
                            $position = 'Regional Supervisor';
                            break;
                        case 'globalPurchaseManager':
                            $position = $lang->globalpurchasemgr;
                            break;
                        case 'user':
                            $position = 'User';
                            break;
                    }
                    $dateofapproval = '-';
                    $user = new Users($approver->uid);
                    if(is_object($user)) {
                        $username = $user->get_displayname();
                    }
                    if($approver->emailRecievedDate != 0) {
                        $dateofapprovalemail = gmdate("H:i:s", ($approver->emailRecievedDate)).'<br/>';
                        $dateofapprovalemail .=date($core->settings['dateformat'], $approver->emailRecievedDate);
                    }
                    if($approver->isApproved == 1) {
                        $class = 'greenbackground';
                        if($approver->timeApproved != 0) {
                            $dateofapproval = gmdate("H:i:s", ($approver->timeApproved)).'<br/>';
                            $dateofapproval .=date($core->settings['dateformat'], $approver->timeApproved);
                        }
                        $hourdiff_output = '-';
                        if($approver->emailRecievedDate != 0 && $approver->timeApproved != 0) {
                            $hourdiff = round(( $approver->timeApproved - $approver->emailRecievedDate) / 3600, 1);
                            if($hourdiff < 10) {
                                $hourdiff_output = '<span style="color:green;">'.$hourdiff.' '.$lang->hours.'</span>';
                            }
                            else {
                                $hourdiff_output = '<span style="color:red;">'.$hourdiff.' '.$lang->hours.'</span>';
                            }
                        }
                    }
                    else {
                        if($approver->uid == $core->user['uid']) {
                            $approve = '<input type="button" class="button" id="approvearo" value="'.$lang->approve.'"/>'
                                    .'<input type="hidden" id="approvearo_id" value="'.$aroorderrequest->aorid.'"/>';
                        }
                    }
                    eval("\$apprs .= \"".$template->get('aro_approvalchain_approver')."\";");
                    unset($class, $approve, $hourdiff_output, $hourdiff, $dateofapprovalemail, $dateofapproval);
                }
            }



            $aff_obj = new Affiliates($aroorderrequest->affid);
            $arorequest['affiliate'] = $aff_obj->get_displayname();
            $arorequest['purchasetype'] = $purchasetype->get_displayname();
            $currency = new Currencies($aroorderrequest->currency);
            $arorequest['currency'] = $currency->alphaCode;

            /* Conversation message --START */
            $takeactionpage_conversation = $aroorderrequest->parse_messages(array('uid' => $core->user['uid']));
            /* Conversation  message --END */
            eval("\$takeactionpage = \"".$template->get('aro_managearodocuments_takeaction')."\";");
            $takeactionpage = '<a class="header" href="#"><h2>'.$lang->aromessages.'</h2></a><div style="margin-top:10px;">'.$takeactionpage.'</div>';


            $aroordersummary = AroOrderSummary::get_data(array('aorid' => $aroorderrequest->aorid));
            $purchaseype = PurchaseTypes::get_data(array('ptid' => $aroorderrequest->orderType));
            $localaff = Affiliates::get_affiliates(array('affid' => $aroorderrequest->affid));
            if(is_object($aropartiesinfo_obj)) {
                $intermedaffiliate = Affiliates::get_affiliates(array('affid' => $aropartiesinfo_obj->intermedAff));
            }
            $ordersummarydisplay['thirdcolumn_display'] = "style='display:none;'";
            if(is_object($aroordersummary)) {
                $firstparty = '-';
                $aroordersummary->firstpartytitle = $lang->intermediary;

                if(is_object($intermedaffiliate)) {
                    $firstparty = $intermedaffiliate->get_displayname();
                }
                $secondparty = $localaff->get_displayname();
                $aroordersummary->secondpartytitle = $lang->local;
                $aroordersummary->thirdpartytitle = '';
                if(is_object($purchaseype) && $purchaseype->isPurchasedByEndUser == 1) {
                    $aroordersummary->secondpartytitle = $lang->customer;
                    $ordersummarycustomer = AroOrderCustomers::get_data(array('aorid' => $aroorderrequest->aorid));
                    if(is_object($ordersummarycustomer)) {
                        $customer_obj = new Customers($ordersummarycustomer->cid);
                        $secondparty = $customer_obj->get_displayname();
                    }
                    if(isset($aroordersummary->invoiceValueThirdParty) && !empty($aroordersummary->invoiceValueThirdParty)) {
                        $aroordersummary->thirdpartytitle = $lang->local;
                        $thirdparty = $localaff->get_displayname();
                        $ordersummarydisplay['thirdcolumn_display'] = "style='display:block;'";
                    }
                }
                if(empty($aroordersummary->interestValueUsd)) {
                    $aroordersummary->interestValueUsd = $aroordersummary->interestValue * $aroorderrequest->exchangeRateToUSD;
                }
            }
            $arodocument_title = $aroorderrequest->orderReference.' '.$localaff->get_displayname();
            $arodocument_header = '<h2>'.$aroorderrequest->orderReference.' / '.$localaff->get_displayname().' / '.$purchaseype->get_displayname().'</h2>';
        }
        else {
            redirect($_SERVER['HTTP_REFERER'], 2, $lang->nomatchfound);
        }
    }

    eval("\$aro_productlines = \"".$template->get('aro_fillproductlines')."\";");
    eval("\$aro_managedocuments_orderident= \"".$template->get('aro_managedocuments_orderidentification')."\";");
    eval("\$aro_ordercustomers= \"".$template->get('aro_managedocuments_ordercustomers')."\";");
    eval("\$partiesinformation = \"".$template->get('aro_partiesinformation')."\";");
    eval("\$aro_netmarginparms= \"".$template->get('aro_netmarginparameters')."\";");
    eval("\$actualpurchase = \"".$template->get('aro_actualpurchase')."\";");
    eval("\$currentstock = \"".$template->get('aro_currentstock')."\";");
    eval("\$orderummary = \"".$template->get('aro_ordersummary')."\";");
    unset($firstparty, $secondparty, $thirdparty);
    eval("\$totalfunds = \"".$template->get('aro_totalfunds')."\";");
    eval("\$approvalchain= \"".$template->get('aro_approvalchain')."\";");
    eval("\$aro_managedocuments= \"".$template->get('aro_managedocuments')."\";");
    output_page($aro_managedocuments);
}
else {
    if($core->input['action'] == 'getexchangerate') {
        $currencyobj = new Currencies('USD');
        if(isset($core->input['currency']) && !empty($core->input['currency'])) {
            $tocurrency = new Currencies($core->input['currency']);
        }
        $rateusd = $currencyobj->get_latest_fxrate($tocurrency->alphaCode, null);
        if(!empty($rateusd)) {
            $exchangerate = array('exchangeRateToUSD' => round(1 / $rateusd, 2), 'exchangeRateToUSD_disabled' => 1);
        }
        else {
            $exchangerate = array('exchangeRateToUSD' => '', 'exchangeRateToUSD_disabled' => 0);
        }
        echo json_encode($exchangerate);
    }
    if($core->input ['action'] == 'populatedocnum') {
        $orderreference = array('orderreference' => '');
        if(!empty($core->input['affid']) && !empty($core->input['ptid'])) {
            $filter['filter']['time'] = '('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
            $documentseq_obj = AroDocumentsSequenceConf::get_data(array('time' => $filter['filter']['time'], 'affid' => $core->input['affid'], 'ptid' => $core->input['ptid']), array('simple' => false, 'operators' => array('affid' => 'in', 'ptid' => 'in', 'time' => 'CUSTOMSQLSECURE')));
            if(is_object($documentseq_obj)) {
                /* create the array to be encoded each dimension of the array represent the html element in the form */
                $orderreference = array('cpurchasetype' => $core->input['ptid'], 'orderreference' => $documentseq_obj->prefix.'-'.$documentseq_obj->nextNumber.'-'.$documentseq_obj->suffix);
            }
        }
        echo json_encode($orderreference);
    }
    if($core->input['action'] == 'ajaxaddmore_newcustomer') {
        $rowid = intval($core->input['value']) + 1;
        $customeroder['inputChecksum'] = generate_checksum('cl');
        $payment_terms = PaymentTerms::get_data('', array('returnarray' => ture));
        $payment_term = parse_selectlist('customeroder['.$rowid.'][ptid]', 4, $payment_terms, '', '', '', array('blankstart' => 1, 'id' => "paymentermdays_".$rowid));
        eval("\$aro_managedocuments_ordercustomers_rows = \"".$template->get('aro_managedocuments_ordercustomers_rows')."\";");
        output($aro_managedocuments_ordercustomers_rows);
    }
    if($core->input['action'] == 'do_perform_managearodouments') {
        unset($core->input['module'], $core->input['action']);
        $orderident_obj = new AroRequests();
        /* get arodocument of the affid and pruchase type */
        $documentseq_obj = AroDocumentsSequenceConf::get_data(array('affid' => $core->input['affid'], 'ptid' => $core->input['orderType']), array('simple' => false, 'operators' => array('affid' => 'in', 'ptid' => 'in')));
        if(is_object($documentseq_obj)) {
            $nextsequence_number = $documentseq_obj->get_nextaro_identification();
            $core->input['nextnumid']['nextnum'] = $nextsequence_number;
        }
        $orderident_obj->set($core->input);
        $errorcode = $orderident_obj->save(); //  $x = $orderident_obj->get_errorcode();
        switch($errorcode) {
            case 0:
            case 1:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                break;
            case 3:
                output_xml('<status>false</status><message>'.$lang->productlineerror.$orderident_obj->get_errorid().'</message>');
                break;
        }
    }
    if($core->input['action'] == 'getestimatedate') {

        $purchasetype = new PurchaseTypes($core->input['ptid']);
        if($purchasetype->isPurchasedByEndUser != 0) {
            echo json_encode('error');
            exit;
        }

        if(is_array($core->input[paymentermdays])) {
            $paymentermdays = explode(',', $core->input[paymentermdays][0]);
        }
        if(is_array($core->input[salesdates])) {
            $salesdates = explode(',', $core->input[salesdates][0]);
        }
        if(is_array($core->input[ptbasedates])) {
            $ptbasedates = explode(',', $core->input[ptbasedates][0]);
        }

        //get average of payment terms
        if(is_array($paymentermdays)) {
            foreach($paymentermdays as $paymenterm) {
                $paymentermobjs = new PaymentTerms($paymenterm, false);
                $intervalspayment_terms[] = $paymentermobjs->overduePaymentDays; //get days
                $intervalspayment_terms = array_unique($intervalspayment_terms);
                if(!empty($intervalspayment_terms)) {
                    $countintervalspayment_terms = count($intervalspayment_terms);
                    $sumintervalspayment_terms = array_sum($intervalspayment_terms);
                    $avgpaymentterms = ($sumintervalspayment_terms / $countintervalspayment_terms);
                }
            }
        }
        // average of actual purchase rows est. date of sale
        if(is_array($salesdates)) {
            if(!is_empty(array_filter($salesdates))) {
                foreach($salesdates as $salesdate) {
                    if(!empty($salesdate) || $salesdate != '-') {
                        $salesdateobjs[] = strtotime($salesdate);
                        $intervalsales_dates = array_unique($salesdateobjs);
                        if(!empty($intervalsales_dates)) {
                            $countintervalsales_dates = count($intervalsales_dates);
                            $sumintervalsales_dates = array_sum($intervalsales_dates);
                            $avgsaledate = ($sumintervalsales_dates / $countintervalsales_dates);
                        }
                    }
                }
            }
        }
        $conv = '';
        $avgesdateofsale_output = date($core->settings['dateformat'], $avgsaledate);
        /* convert the average days of the paymentterms to days in order to sum them with the average date of sale */
        $est_averagedate = $avgpaymentterms * (86400) + $avgsaledate;
        if(!empty($avgpaymentterms) && !empty($avgsaledate)) {
            $conv = date($core->settings['dateformat'], ($est_averagedate));
        }
        if(is_array($ptbasedates)) {
            if(!is_empty(array_filter($ptbasedates))) {
                foreach($ptbasedates as $ptbasedate) {
                    $avgptdates[] = strtotime($ptbasedate) + $avgpaymentterms * (86400);
                }
                foreach($salesdates as $salesdate) {
                    if(!empty($salesdate)) {
                        $avgptdates[] = strtotime($salesdate) + $avgpaymentterms * (86400);
                    }
                }
                $est_averagedate = array_sum($avgptdates) / count($avgptdates);
                $conv = date($core->settings['dateformat'], ($est_averagedate));
            }
        }
        echo json_encode(array('avgeliduedate' => $conv)); //return json to the ajax request to populate in the form
    }
    if($core->input['action'] == 'ajaxaddmore_productline') {
        $plrowid = intval($core->input['value']) + 1;
        $display = 'none';
        $productlines_data = $core->input['ajaxaddmoredata'];
        $productline['inputChecksum'] = generate_checksum('pl');
        $packaging = Packaging::get_data('name IS NOT NULL');
        $segments = ProductsSegments::get_segments('');
        $segments_selectlist = parse_selectlist('productline['.$plrowid.'][psid]', '', $segments, '', null, null, array('id' => "productline_".$plrowid."_psid", 'placeholder' => 'Overwrite Segment', 'width' => '100%'));
        $packaging_list = parse_selectlist('productline['.$plrowid.'][packing]', '', $packaging, '', '', '', array('id' => "productline_".$plrowid."_packing", 'blankstart' => 1));
        $uom = Uom::get_data(array('isWeight' => 1));
        $kg = Uom::get_data(array('name' => 'Kilogram'));
        $uom_list = parse_selectlist('productline['.$plrowid.'][uom]', '', $uom, $kg->uomid, '', '', array('id' => "productline_".$plrowid."_uom", 'blankstart' => 1, 'width' => '70px'));
        eval("\$aroproductlines_rows = \"".$template->get('aro_productlines_row')."\";");
        output($aroproductlines_rows);
    }
    if($core->input['action'] == 'disablefields') {
        $purchasetype = new PurchaseTypes($core->input['ptid']);
        $data['daysInStock_disabled'] = $data['qtyPotentiallySold_disabled'] = $data['parmsfornetmargin_warehousing_disabled'] = 1;
        if(is_object($purchasetype)) {
            if($purchasetype->qtyIsNotStored == 1) {
                $data['daysInStock_disabled'] = 0;
                $data['qtyPotentiallySold_disabled'] = 0;
                $data['parmsfornetmargin_warehousing_disabled'] = 0;
            }
        }
        $productline = array(
                'productline_qtyPotentiallySold_disabled' => $data['qtyPotentiallySold_disabled'],
                'productline_daysInStock_disabled' => $data['daysInStock_disabled'],
                'parmsfornetmargin_warehousing_disabled' => $data['parmsfornetmargin_warehousing_disabled']
        );
        output(json_encode($productline));
    }
    if($core->input['action'] == 'populateproductlinefields') {
        $productline_obj = new AroRequestLines();
        $rowid = $core->input['rowid'];
        unset($core->input['action'], $core->input['module'], $core->input['rowid']);
        $parmsfornetmargin = array('localPeriodOfInterest', 'localBankInterestRate', 'warehousingPeriod', 'warehousingTotalLoad', 'warehousingRate', 'intermedBankInterestRate', 'intermedPeriodOfInterest', 'commission', 'totalDiscount', 'totalQty', 'localRiskRatio', 'unitfees');
        foreach($parmsfornetmargin as $parm) {
            $core->input['parmsfornetmargin'][$parm] = $core->input[$parm];
        }
        //$core->inut['parmsfornetmargin']['unitfees'] = $unitfee;
        $data = $core->input;
        $productline_data = $productline_obj->calculate_values($data);
        unset($productline_data['affBuyingPrice'], $productline_data['totalBuyingValue']);
        foreach($productline_data as $key => $value) {
            if($key == 'qtyPotentiallySoldPerc') {
                $productline['productline_'.$rowid.'_'.$key] = $value;
                continue;
            }
            if($value !== $data[$key]) {
                $productline['productline_'.$rowid.'_'.$key] = $value;
            }
        }
        //$productline['productline_'.$rowid.'_fees'] = $core->input['fees'];
        //   echo json_encode($productline);
        output(json_encode($productline));
    }
    if($core->input['action'] == 'populateaffbuyingprice') {
        $data = $core->input;
        $purchasetype = new PurchaseTypes($core->input['ptid']);
        $data['commission'] = $data['commission'] / 100;
        $data['isPurchasedByEndUser'] = $purchasetype->isPurchasedByEndUser;
        $productline_obj = new AroRequestLines();
        $affbuyingprice = $productline_obj->calculate_affbuyingprice($data);
        $data['affBuyingPrice'] = $affbuyingprice;
        $totalBuyingValue = $productline_obj->calculate_totalbuyingvalue($data);
        $productline['productline_'.$core->input['rowid'].'_affBuyingPrice'] = $affbuyingprice;
        $productline['productline_'.$core->input['rowid'].'_totalBuyingValue'] = $totalBuyingValue;

        echo json_encode($productline);
    }
    if($core->input['action'] == 'populatewarehousepolicy') {
        unset($core->input['action'], $core->input['module']);
        $aroorderrequest = new AroRequests();
        $netmarginparms_data = $aroorderrequest->calculate_netmaginparms($core->input);
        if($netmarginparms_data == false) {
            output($lang->nopolicy);
            exit;
        }
        foreach($netmarginparms_data as $key => $value) {
            if(!is_empty($value)) {
                $parmsfornetmargin['parmsfornetmargin_'.$key] = $value;
            }
        }
        echo json_encode($parmsfornetmargin);
    }
    if($core->input['action'] == 'getwarehouses') {
        $warehouse_objs = Warehouses::get_data(array('affid' => $core->input['affid'], 'isActive' => 1), array('returnarray' => true));
        $warehouse_list = parse_selectlist('parmsfornetmargin[warehouse]', 1, $warehouse_objs, '', '', '', array('id' => 'parmsfornetmargin_warehouse', 'blankstart' => 1, 'width' => '100%'));
        output(($warehouse_list));
    }
    if($core->input['action'] == 'populateaffpolicy') {
        $aropolicy_data = array('parmsfornetmargin_localBankInterestRate' => '',
                'parmsfornetmargin_localRiskRatio' => ''
        );
        unset($core->input['action'], $core->input['module']);
        if($core->input['affid'] != ' ' && !empty($core->input['affid']) && !empty($core->input['ptid']) && $core->input['ptid'] != ' ') {
            $filter = 'affid = '.$core->input['affid'].' AND purchaseType = '.$core->input['ptid'].' AND isActive = 1 AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
            $localaffpolicy = AroPolicies::get_data($filter);
            if(!is_object($localaffpolicy)) {
                output($lang->nopolicy);
                exit;
            }
            /* Complete to apply risk ratio for different currencies case */
//            $arorequest=  AroRequests::get_data(array('aorid'=>$core->input['aorid']));
//            if($arorequset->isSameCurr==1){
//                $increaseperday=$localaffpolicy->riskRatioIncreaseDiffCurrCN/$localaffpolicy->riskRatioDays;
//                               // riskRatioDiffCurrCP
//            }
            $aropolicy_data = array('parmsfornetmargin_localBankInterestRate' => $localaffpolicy->yearlyInterestRate,
                    'parmsfornetmargin_localRiskRatio' => $localaffpolicy->riskRatioSameCurrCN,
                    'partiesinfo_intermed_ptAcceptableMargin' => $localaffpolicy->defaultAcceptableMargin
            );
        }
        echo json_encode($aropolicy_data);
    }
    if($core->input['action'] == 'populateintermedaffpolicy') {
        $intermedpolicy_data = array('parmsfornetmargin_intermedBankInterestRate' => '');
        if($core->input ['intermedAff'] != ' ' && !empty($core->input['intermedAff']) && !empty($core->input['ptid']) && $core->input['ptid'] != ' ') {
            $intermedpolicy_filter = 'affid = '.$core->input['intermedAff'].' AND purchaseType = '.$core->input['ptid'].' AND isActive = 1 AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
            $intermedpolicy = AroPolicies::get_data($intermedpolicy_filter);
            if(!is_object($intermedpolicy)) {
                output($lang->nointermedpolicy);
                exit;
            }

            $intermedpolicy_data = array('parmsfornetmargin_intermedBankInterestRate' => $intermedpolicy->yearlyInterestRate,
                    'partiesinfo_commission' => $intermedpolicy->commissionCharged,
                    'partiesinfo_defaultcommission' => $intermedpolicy->commissionCharged);
        }
        echo json_encode($intermedpolicy_data);
    }
    if($core->input['action'] == 'ajaxaddmore_actualpurchaserow') {
        $aprowid = intval($core->input['value']);
        eval("\$actualpurchase_rows .= \"".$template->get('aro_actualpurchase_row')."\";");
        output($actualpurchase_rows);
    }
    if($core->input['action'] == 'populateactualpurchaserow') {
        $rowid = $core->input['rowid'];
        $core->input['totalValue'] = $core->input['totalBuyingValue'];
        unset($core->input['action'], $core->input['module'], $core->input['totalBuyingValue']);
        $actualpurchase_obj = new AroRequestLinesSupervision();
        $actualpurchase = $actualpurchase_obj->calculate_actualpurchasevalues($core->input);
        $packing = new Packaging($actualpurchase['packing']);
        $actualpurchase['packingTitle'] = $packing->get_displayname();
        $fields = array('productName', 'pid', 'quantity', 'packing', 'packingTitle', 'totalValue', 'shelfLife', 'inputChecksum', 'daysInStock');
        foreach($fields as $field) {
            $actualpurchase_data['actualpurchase_'.$rowid.'_'.$field] = $actualpurchase[$field];
        }

        if((isset($actualpurchase['estDateOfStockEntry_output']) && !empty($actualpurchase['estDateOfStockEntry_output'])) && (isset($actualpurchase['estDateOfSale_output']) && !empty($actualpurchase['estDateOfSale_output']))) {
            $data['stockentryandsalesdiff'] = date_diff(date_create($actualpurchase['estDateOfStockEntry_output']), date_create($actualpurchase['estDateOfSale_output']));
            $data['stockentryandsalesdiff'] = $data['stockentryandsalesdiff']->format("%r%a");
        }

        $actualpurchase_data['pickDate_stock_'.$rowid] = $actualpurchase['estDateOfStockEntry_output'];
        $actualpurchase_data['pickDate_sale_'.$rowid] = $actualpurchase['estDateOfSale_output'];
        $actualpurchase_data['altpickDate_stock_'.$rowid] = $actualpurchase['estDateOfStockEntry_formatted'];
        $actualpurchase_data['altpickDate_sale_'.$rowid] = $actualpurchase['estDateOfSale_formatted'];
        $actualpurchase_data['diff_stockandsale_'.$rowid] = $data['stockentryandsalesdiff'];
        echo json_encode($actualpurchase_data);
    }
    if($core->input['action'] == 'populateactualpurchase_stockentrydate') {
        $rowid = $core->input['rowid'];
        $fields = array('transitTime', 'clearanceTime');
        foreach($fields as $field) {
            if(isset($core->input[$field]) && !empty($core->input[$field])) {
                $data[$field] = $core->input[$field];
            }
        }
        $data['dateOfStockEntry'] = strtotime($core->input['dateOfStockEntry']);
        $actualpurchase_obj = new AroRequestLinesSupervision();
        $dates = $actualpurchase_obj->get_stockentryestdate($data);
        $actualpurchase_data['pickDate_stock_'.$rowid] = $dates['formatted'];
        $actualpurchase_data['altpickDate_stock_'.$rowid] = $dates['output'];
        echo json_encode($actualpurchase_data);
    }
    if($core->input['action'] == 'populatepartiesinfofields' || $core->input['action'] == 'populate_localintvalue') {
        $partiesinfo_obj = new AroRequestsPartiesInformation();
        if(isset($core->input ['estDateOfShipment']) && !empty($core->input['estDateOfShipment'])) {
            $intermediarydates = $partiesinfo_obj->get_intermediarydates($core->input);
            $partiesinfo['vendorEstDateOfPayment'] = $partiesinfo_obj->get_vendordates($core->input);
            $partiesinfo['intermedEstDateOfPayment'] = $intermediarydates['intermedEstDateOfPayment'];
            $partiesinfo['promiseOfPayment'] = $intermediarydates['promiseOfPayment'];

            $fields = array('vendorEstDateOfPayment', 'intermedEstDateOfPayment', 'promiseOfPayment');
            foreach($fields as $field) {
                $partiesinfo[$field.'_output'] = $partiesinfo[$field.'_formatted'] = '';
                if($partiesinfo[$field] != 0 && !empty($partiesinfo[$field])) {
                    $partiesinfo[$field.'_output'] = date('d-m-Y', $partiesinfo[$field]);
                    $partiesinfo[$field.'_formatted'] = date($core->settings['dateformat'], $partiesinfo[$field]);
                }
            }
        }
        if(isset($partiesinfo['intermedEstDateOfPayment_output']) && !empty($partiesinfo['intermedEstDateOfPayment_output']) && isset($partiesinfo['vendorEstDateOfPayment_output']) && !empty($partiesinfo['vendorEstDateOfPayment_output'])) {
            $data['intermedPeriodOfInterest'] = date_diff(date_create($partiesinfo['vendorEstDateOfPayment_output']), date_create($partiesinfo['intermedEstDateOfPayment_output']));
            $data['intermedPeriodOfInterest'] = $data['intermedPeriodOfInterest']->format("%r%a");
            $data['diffbetweendates'] = $data['intermedPeriodOfInterest']; // difference between payment days
            if($data['intermedPeriodOfInterest'] < 0) {
                $data['intermedPeriodOfInterest'] = 0;
            }
            $data['localPeriodOfInterest'] = 0;
            if(isset($core->input['est_local_pay']) && !empty($core->input['est_local_pay'])) { //est_local_pay= Estimated Local Invoice Due date (order customers section)
                $data['localPeriodOfInterest'] = date_diff(date_create($partiesinfo['intermedEstDateOfPayment_output']), date_create($core->input['est_local_pay']));
                $data['localPeriodOfInterest'] = $data['localPeriodOfInterest']->format("%r%a");
                if($data['localPeriodOfInterest'] < 0) {
                    $data['localPeriodOfInterest'] = 0;
                }
            }
        }
        $purchasetype = new PurchaseTypes($core->input['ptid']);
        if(is_object($purchasetype)) {
            if($purchasetype->isPurchasedByEndUser == 1) {
                $data['localPeriodOfInterest'] = 0;
            }
            if($purchasetype->needsIntermediary == 0) {
                if(isset($core->input['est_local_pay']) && !empty($core->input['est_local_pay'])) { // LSP purchase type (vendor-estlocal pay)
                    $data['localPeriodOfInterest'] = date_diff(date_create($partiesinfo['vendorEstDateOfPayment_output']), date_create($core->input['est_local_pay']));
                    $data['localPeriodOfInterest'] = $data['localPeriodOfInterest']->format("%r%a");
                    if($data['localPeriodOfInterest'] < 0) {
                        $data['localPeriodOfInterest'] = 0;
                    }
                }
            }
        }

        if(isset($core->input['intermedBankInterestRate']) && !empty($core->input['intermedBankInterestRate'])) {
            $interestvalue = (($core->input['intermedBankInterestRate'] / 365 ) / 100 ) * $data['intermedPeriodOfInterest'] * $core->input['totalbuyingvalue_total'];
        }
        $totalintermedfees = $core->input['totalintermedfees'];
        if(!empty($interestvalue)) {
            $totalintermedfees = $totalintermedfees + $interestvalue;
        }
        if($core->input['action'] == 'populate_localintvalue') {
            $partiesinfo_data = array(
                    'parmsfornetmargin_interestvalue' => round($interestvalue, 3),
                    'partiesinfo_totalfees' => round($totalintermedfees, 3)
            );
        }
        else {
            // $partiesinfo['intermedEstDateOfPayment_formatted'] = '';
            $partiesinfo_data = array('pickDate_vendor_estdateofpayment' => $partiesinfo['vendorEstDateOfPayment_formatted'],
                    'pickDate_intermed_estdateofpayment' => $partiesinfo['intermedEstDateOfPayment_formatted'],
                    'pickDate_intermed_promiseofpayment' => $partiesinfo['promiseOfPayment_formatted'],
                    'parmsfornetmargin_localPeriodOfInterest' => $data['localPeriodOfInterest'],
                    'partiesinfo_diffbtwpaymentdates' => $data['diffbetweendates'],
                    'parmsfornetmargin_intermedPeriodOfInterest' => $data['intermedPeriodOfInterest'],
                    'parmsfornetmargin_interestvalue' => round($interestvalue, 3),
                    'partiesinfo_totalfees' => round($totalintermedfees, 3)
            );
        }
        output(json_encode($partiesinfo_data));
    }

    if($core->input['action'] == 'updateunitfee') {
        $qtyperunit = $core->input['qtyperunit'];
        $feeperunit = $core->input['feeperunit'];
        $qtyperunit = split('_', $qtyperunit);
        $feeperunit = split('_', $feeperunit);

        $i = 0;
        foreach($qtyperunit as $qty) {
            if(empty($qty)) {
                continue;
            }
            $i++;
            $qty = split(':', $qty);
            $uom = new Uom($qty[0]);
            $qtyperunit_array[$i] = $qty[1]."/".$uom->get_displayname();
            $avgqty[$i] = $qty[1];
        }
        if(is_array($qtyperunit_array)) {
            $quantityperuom = implode("\n", $qtyperunit_array);
        }
        $i = 0;
        foreach($feeperunit as $fee) {
            if(empty($fee)) {
                continue;
            }
            $i++;
            $fee = split(':', $fee);
            $uom = new Uom($fee[0]);
            $feeperunit_array[$i] = $fee[1]."/".$uom->get_displayname();
            $feeperunit_usdarray[$i] = ($fee [1] * $core->input['exchangeRateToUSD'])."/".$uom->get_displayname();
            $total_intermedfees +=$fee[1];
            $avgfee[$i] = $fee[1];
        }

        for($j = 1; $j <= $i; $j++) { ///Calculate unit fee
            if($avgqty[$j] != 0) {
                $unitfee +=$avgfee[$j] / $avgqty[$j];  //(total Fee per unit /total qty per unit)
            }
        }
        if($i != 0) {
            $unitfee = $unitfee / $i; // unit fee=avg. of unit fees = $unitfee/(number of units)
        }
        $data = array('ordersummary_unitfee' => round($unitfee, 2));
        output(json_encode($data));
    }
    if($core->input['action'] == 'populateordersummary') {
        $intermedaffiliate = Affiliates::get_affiliates(array('affid' => $core->input['intermedAff']));
        $affiliate = new Affiliates($core->input['aff']);
        $qtyperunit = $core->input['qtyperunit'];
        $feeperunit = $core->input['feeperunit'];
        $qtyperunit = split('_', $qtyperunit);
        $feeperunit = split('_', $feeperunit);
        $summedqty = $core->input['summedqty'];
        $summedfees = $core->input['summedfees'];
        $summedfeesusd = $summedfees * $core->input['exchangeRateToUSD'];
        $interestvalue = $core->input['interestvalue'];
        $interestvalueusd = $core->input['interestvalue'] * $core->input['exchangeRateToUSD'];


        $i = 0;
        foreach($qtyperunit as $qty) {
            if(empty($qty)) {
                continue;
            }
            $i++;
            $qty = split(':', $qty);
            $uom = new Uom($qty[0]);
            $qtyperunit_array[$i] = $qty[1]."/".$uom->get_displayname();
            $avgqty[$i] = $qty[1];
        }
        if(is_array($qtyperunit_array)) {
            $quantityperuom = implode("\n", $qtyperunit_array);
        }
        $i = 0;
        foreach($feeperunit as $fee) {
            if(empty($fee)) {
                continue;
            }
            $i++;
            $fee = split(':', $fee);
            $uom = new Uom($fee[0]);
            $feeperunit_array[$i] = $fee[1]."/".$uom->get_displayname();
            $feeperunit_usdarray[$i] = ($fee [1] * $core->input['exchangeRateToUSD'])."/".$uom->get_displayname();
            $total_intermedfees +=$fee[1];
            $avgfee[$i] = $fee[1];
        }

        for($j = 1; $j <= $i; $j++) { ///Calculate unit fee
            if($avgqty[$j] != 0) {
                $unitfee +=$avgfee[$j] / $avgqty[$j];  //(total Fee per unit /total qty per unit)
            }
        }
        if($i != 0) {
            $unitfee = $unitfee / $i; // unit fee=avg. of unit fees = $unitfee/(number of units)
        }
        if(is_array($feeperunit_array)) {
            $feeperunit_array = implode("\n", $feeperunit_array);
        }
        if(is_array($feeperunit_usdarray)) {
            $feeperunit_usdarray = implode("\n", $feeperunit_usdarray);
        }
        $purchaseype = new PurchaseTypes($core->input['ptid']);
        $localnetmargin = $core->input['local_netMargin'];
        if($purchaseype->isPurchasedByEndUser == 1) {
            $localnetmargin = 0;
            $intermedmargin = $core->input['local_netMargin'];
        }
        if($purchaseype->needsIntermediary != 0) {
            $invoicevalueintermed = $core->input['invoicevalue_intermed'];
            $invoicevalueintermed_usd = $core->input['invoicevalue_intermed'] * $core->input['exchangeRateToUSD'];
        }
        if($purchaseype->isPurchasedByEndUser == 0) {
            $intermedmargin = '-';
            $intermedmargin_perc = '-';
            if(isset($core->input['intermedAff']) && !empty($core->input['intermedAff'])) {
                $YearDays = 365;
                $totalfees = $core->input['totalfeespaidbyintermed'];
                $total_intermedfees_usd = $totalfees * $core->input['exchangeRateToUSD'];
                $localinvoicevalue_usd = $core->input['localinvoicevalue_usd'];
                $intermedmargin = (($localinvoicevalue_usd - $invoicevalueintermed_usd - $total_intermedfees_usd )); //- ($invoicevalueintermed_usd + $total_intermedfees_usd ) * ($core->input['InterBR'] / $YearDays * $core->input['POIintermed']));
                if($invoicevalueintermed_usd != 0) {
                    $intermedmargin_perc = ($intermedmargin / ($invoicevalueintermed_usd + ($total_intermedfees * $core->input['exchangeRateToUSD']))) * 100;
                }
            }
        }
        else if($purchaseype->isPurchasedByEndUser == 1) {
            if($localinvoicevalue_usd != 0) {
                $intermedmargin_perc = $intermedmargin / $localinvoicevalue_usd;
            }
            else {
                $intermedmargin_perc = '-';
            }
        }

        $localnetmargin_perc = '';
        if(!empty($localnetmargin) && ($core->input['sellingpriceqty_product'] * $core->input['exchangeRateToUSD'] ) != 0) {
            $localnetmargin_perc = ($localnetmargin / ($core->input['sellingpriceqty_product'] * $core->input['exchangeRateToUSD'])) * 100;
        }

        $firstparty = '-';
        if(is_object($intermedaffiliate)) {
            $firstparty = $intermedaffiliate->get_displayname();
        }
        $secondparty = $affiliate->get_displayname();

        $firstpart_title = $lang->intermediary;
        $secondparty_title = $lang->local;
        $thirdparty_title = '';
        $haveThirdParty = 0;
        if($purchaseype->isPurchasedByEndUser == 1) {
            $secondparty_title = $lang->customer;
            $customer_obj = new Entities($core->input['customer']);
            $secondparty = $customer_obj->get_displayname();
            if(isset($core->input['commFromIntermed']) && !empty($core->input['commFromIntermed'])) {
                $thirdparty_title = $lang->local;
                $thirdparty = $affiliate->get_displayname();
                $invoicevalue_thirdparty = ($core->input['commFromIntermed'] / 100) * $invoicevalueintermed;
                $invoicevalue_thirdparty_usd = $invoicevalue_thirdparty * $core->input['exchangeRateToUSD'];
                $haveThirdParty = 1;
            }
        }
        $data = array(
                'ordersummary_col1_title' => $firstpart_title,
                'ordersummary_col2_title' => $secondparty_title,
                'ordersummary_col3_title' => $thirdparty_title,
                'ordersummary_intermedaff' => $firstparty,
                'ordersummary_2ndparty' => $secondparty,
                'ordersummary_3rdparty' => $thirdparty,
                'ordersummary_totalquantityperuom' => $quantityperuom,
                'ordersummary_totalquantity' => $summedqty,
                'ordersummary_totalfeesperunit' => $feeperunit_array,
                'ordersummary_totalfees' => $summedfees,
                'ordersummary_totalintermedfeesperunit_usd' => $feeperunit_usdarray,
                'ordersummary_totalintermedfees_usd' => $summedfeesusd,
                'ordersummary_interestvalue' => $interestvalue,
                'ordersummary_interestvalueUsd' => $interestvalueusd,
                'ordersummary_invoicevalue_intermed' => round($invoicevalueintermed, 2),
                'ordersummary_invoicevalueusd_intermed' => round($invoicevalueintermed_usd, 2),
                //'ordersummary_invoicevalue_thirdparty' => round($invoicevalue_thirdparty, 2),
                'ordersummary_invoicevalueusd_thirdparty' => round($invoicevalue_thirdparty_usd, 2),
                //'ordersummary_invoicevalue_local' => round($localinvoicevalue, 2),
                //'ordersummary_invoicevalueusd_local' => round($localinvoicevalue_usd, 2),
                'ordersummary_netmargin_local' => round($localnetmargin, 2),
                'ordersummary_netmargin_intermed' => round($intermedmargin, 2),
                'ordersummary_globalnetmargin' => round($localnetmargin + $intermedmargin, 2),
                'ordersummary_netmargin_localperc' => round($localnetmargin_perc, 2),
                'ordersummary_netmargin_intermedperc' => round($intermedmargin_perc, 2),
                'ordersummary_totalamount' => round($core->input['totalamount'], 2),
                'haveThirdParty' => $haveThirdParty,
        );
        echo json_encode($data);
    }
    if($core->input['action'] == 'popultedefaultaffpolicy') {
        if($core->input['affid'] != ' ' && !empty($core->input['affid']) && !empty($core->input['ptid']) && $core->input['ptid'] != ' ') {
            $filter = 'affid = '.$core->input['affid'].' AND purchaseType = '.$core->input['ptid'].' AND isActive = 1 AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
            $affpolicy = AroPolicies::get_data($filter);
            if(!is_object($affpolicy)) {
                output($lang->nopolicy);
                exit;
            }
            $defaultintermedfields = array('defaultCurrency', 'defaultIntermed', 'defaultIncoterms', 'defaultPaymentTerm');
            foreach($defaultintermedfields as $defaultintermedfields) {
                if(empty($affpolicy->$defaultintermedfields)) {
                    $affpolicy->$defaultintermedfields = 0;
                }
            }
            $defaultaffpolicy = array('currencies' => $affpolicy->defaultCurrency,
                    'partiesinfo_intermed_aff' => $affpolicy->defaultIntermed,
                    'partiesinfo_intermed_incoterms' => $affpolicy->defaultIncoterms,
                    'partiesinfo_intermed_paymentterm' => $affpolicy->defaultPaymentTerm
            );
        }
        echo json_encode($defaultaffpolicy);
    }
    if($core->input['action'] == 'generateapprovalchain') {
        if(isset($core->input['affid']) && !empty($core->input['affid']) && isset($core->input['ptid']) && !empty($core->input['ptid'])) {
            $data['affid'] = $core->input['affid'];
            $data['orderType'] = $core->input['ptid'];
            $data['orderreference'] = $core->input['orderreference'];
            if(isset($core->input['aroBusinessManager']) && !empty($core->input['aroBusinessManager'])) {
                $data['aroBusinessManager'] = $core->input['aroBusinessManager'];
            }
            $arorequest = new AroRequests();
            $arorequest->set($data);
            $aroapprovalchain = $arorequest->generate_approvalchain(null, array('aroBusinessManager' => $data['aroBusinessManager']));
            if(is_array($aroapprovalchain)) {
                foreach($aroapprovalchain as $key => $val) {
                    switch($key) {
                        case 'businessManager':
                            $position = 'Local Business Manager';
                            break;
                        case 'lolm':
                            $position = 'Local Logistics Manager';
                            break;
                        case 'lfinancialManager':
                            $position = 'Local Finance Manager';
                            break;
                        case 'generalManager':
                            $position = 'General Manager';
                            break;
                        case 'gfinancialManager':
                            $position = 'Global Finance Manager';
                            break;
                        case 'cfo':
                            $position = 'Global CFO';
                            break;
                        case 'coo':
                            $position = 'Global COO';
                            break;
                        case 'regionalSupervisor':
                            $position = 'Regional supervisor';
                            break;
                        case 'globalPurchaseManager':
                            $position = 'Global purchase manager';
                            break;
                        case 'user':
                            $position = 'User';
                            break;
                        case 'reportsTo':
                            $position = 'Reports To';
                            break;
                    }
                    //   if($key != 'businessManager') {
                    $user = new Users($val);
                    if(is_object($user)) {
                        $username = $user->get_displayname();
                    }
                    ///  }
//                    else {
//                        $username = $val;
//                    }
                    eval("\$apprs .= \"".$template->get('aro_approvalchain_approver')."\";");
                }
                output($apprs);
            }
        }
    }
    if($core->input['action'] == 'populate_localintersetvalues') {
        $localinvoicevalue = $core->input['invoicevalue_local'];
        $purchaseype = new PurchaseTypes($core->input['ptid']);
        if($purchaseype->isPurchasedByEndUser == 1) {
            $localinvoicevalue = $core->input['invoicevalue_local_RIC'];
        }
        $localinvoicevalue_usd = $localinvoicevalue * $core->input['exchangeRateToUSD'];
        $data = array(
                'ordersummary_invoicevalue_local' => round($localinvoicevalue, 2),
                'ordersummary_invoicevalueusd_local' => round($localinvoicevalue_usd, 2));
        echo json_encode($data);
    }
    if($core->input['action'] == 'populatecurrentstockrow') {
        $rowid = $core->input['rowid'];
        unset($core->input['action'], $core->input['module'], $core->input['totalBuyingValue']);
        $currentstock_obj = new AroRequestsCurStkSupervision();
        $currentstock = $core->input;
        $packing = new Packaging($currentstock['packing']);
        $currentstock['packingTitle'] = $packing->get_displayname();
        $fields = array('productName', 'pid', 'packing', 'packingTitle', 'inputChecksum'); // 'quantity', 'stockValue', 'expiryDate');
        foreach($fields as $field) {
            $currentstock_data['currentstock_'.$rowid.'_'.$field] = $currentstock[$field];
        }
        //   $currentstock_data['pickDate_currentstock_'.$rowid] = '';
        //  $currentstock_data['pickDate_currentsale_'.$rowid] = '';
        //  $currentstock_data['altpickDate_currentstock_'.$rowid] = '';
        //  $currentstock_data['altpickDate_currentsale_'.$rowid.''] = '';
        echo json_encode($currentstock_data);
    }
    if($core->input['action'] == 'ajaxaddmore_currentstockrow') {
        $csrowid = intval($core->input['value']);
        eval("\$curentstock_rows .= \"".$template->get('aro_currentstock_row')."\";");
        output($curentstock_rows);
    }
    if($core->input['action'] == 'viewonly') {
        $aroorderrequest = AroRequests::get_data(array('aorid' => $core->input['id']), array('simple' => false));
        if($aroorderrequest->isApproved == 1) {
            $viewonly = array('disable' => 1);
            output(json_encode($viewonly));
        }
    }
    if($core->input['action'] == 'approvearo') {
        $arorequest = AroRequests::get_data(array('aorid' => intval($core->input['id'])));
        if(is_object($arorequest)) {
            $aroapproval = AroRequestsApprovals::get_data(array('aorid' => $arorequest->aorid, 'uid' => $core->user['uid']));
            if($aroapproval->isApproved == 0) {
                $user = new Users($core->user['uid']);
                $approve = $arorequest->approve($user);
                if($approve) {
                    $arorequest->inform_nextapprover();

                    //Inform created By
                    $aroaffiliate_obj = new Affiliates($arorequest->affid);
                    $purchasteype_obj = PurchaseTypes::get_data(array('ptid' => $arorequest->orderType));
                    $createdby_obj = Users::get_data(array('uid' => $arorequest->createdBy));
                    if(is_object($createdby_obj) && !($arorequest->is_approved())) {
                        $email_data = array(
                                'from' => 'ocos@orkila.com',
                                'to' => $createdby_obj->email,
                                'subject' => "ARO [".$arorequest->orderReference."] Approval Status",
                                'message' => "Aro Request [".$arorequest->orderReference."] ".$aroaffiliate_obj->get_displayname()." ".$purchasteype_obj->get_displayname()." was approved by ".$user->get_displayname()
                        );
                        $viewarolink = '<a href="'.$core->settings['rootdir'].'/index.php?module=aro/managearodouments&id='.$arorequest->aorid.'">Click here to view the ARO</a>';
                        $mailer = new Mailer();
                        $mailer = $mailer->get_mailerobj();
                        $mailer->set_type();
                        $mailer->set_from($email_data['from']);
                        $mailer->set_subject($email_data['subject']);
                        $mailer->set_message($email_data['message'].'<br/>'.$viewarolink);
                        $mailer->set_to($email_data['to']);
                        $mailer->send();
                    }
                    if($arorequest->is_approved()) {
                        $arorequest = $arorequest->update_arorequeststatus();
                        $arorequest->notifyapprove();
                    }
                }
            }
        }
    }

    if($core->input['action'] == 'takeactionpage') {

        if(isset($core->input['id'], $core->input['requestKey'])) {
            $core->input['id'] = base64_decode($core->input['id']);
            $arorequest_obj = new AroRequests($core->input['id'], false);
            $aro_request = $arorequest_obj->get();
            $affiliate = new Affiliates($aro_request['affid']);
            $arorequest['affiliate'] = $affiliate->get_displayname();
            $purchasetype = new PurchaseTypes($aro_request['orderType']);
            $arorequest['purchasetype'] = $purchasetype->get_displayname();
            $currency = new Currencies($aro_request['currency']);
            $arorequest['currency'] = $currency->get_displayname();
            /* Conversation message --START */
            $takeactionpage_conversation = $arorequest_obj->parse_messages(array('uid' => $core->user['uid']));
            /* Conversation  message --END */
            $id = "errorbox";
            $align = 'align = "center"';
            eval("\$takeactionpage = \"".$template->get('aro_managearodocuments_takeaction')."\";");
            output_page($takeactionpage);
        }
    }
    elseif($core->input['action'] == 'perform_sendmessage') {
        $arorequestmessage_obj = new AroRequestsMessages();
        $arorequestmessage_obj = $arorequestmessage_obj->create_message($core->input['arorequestmessage'], $core->input['aorid'], array('source' => 'emaillink'));
        /* Errors Should be handled Here */
        switch($arorequestmessage_obj->get_errorcode()) {
            case 0:
                $arorequestmessage_obj = $arorequestmessage_obj->send_message();
                switch($arorequestmessage_obj->get_errorcode()) {
                    case 0:
                        output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                        break;
                    case 5:
                        output_xml("<status>false</status><message>{$lang->successfullysaved} - {$lang->errorsendingemail}".$arorequestmessage_obj->get_errorcode()."</message>");
                        break;
                }
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            case 2:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            case 3:
                output_xml("<status>false</status><message>{$lang->entryexists}</message>");
                break;
        }
        /* Need to have feedback message */
    }
    else if($core->input['action'] == 'InolveIntermediary') {
        $purchasetype = new PurchaseTypes($core->input['ptid']);

//$needsIntermed = array('needsIntermed' => $purchasetype->needsIntermediary);
//echo json_encode($needsIntermed);
        output($purchasetype->needsIntermediary);
    }
    else if($core->input['action'] == 'updatecommission') {
        $totalcomm = $comm = 0;
        $purcasetype = new PurchaseTypes($core->input['ptid']);
        if(is_object($purcasetype) && $purcasetype->needsIntermediary == 1) {
            $totalcomm = $core->input['totalcommision'];
            $comm = $core->input['defaultcomm'];
            if($core->input['totalcommision'] < 250) {
                if(!empty($core->input['totalamount']) && $core->input['totalamount'] != 0) {
                    $comm = (250 * 100 ) / $core->input['totalamount'];
                }
            }
        }
        if(isset($core->input['totalDiscount']) && !empty($core->input['totalDiscount'])) {
            $comm = $core->input['defaultcomm'] - $core->input['totalDiscount'];
        }
        $intialtotalcomm = $totalcomm;
        $totalcomm = $core->input['totalamount'] * ($comm / 100);
        $commission_data = array('partiesinfo_commission' => round($comm, 3),
                'ordersummary_initialtotalcomm' => round($intialtotalcomm, 2),
                'ordersummary_totalcomm' => round($totalcomm, 2)
        );
        echo json_encode($commission_data);
    }
    else if($core->input['action'] == 'managevendorincoterms') {
        $vendorincotermdetails = array('carriageOnBuyer' => 0);
        $incoterm = new Incoterms(intval($core->input['incoterm']));
        if(is_object($incoterm)) {
            if($incoterm->carriageOnBuyer == 1) {
                $vendorincotermdetails = array('carriageOnBuyer' => 1);
            }
        }
        echo json_encode($vendorincotermdetails);
    }
    elseif($core->input['action'] == 'perform_deletearodocument') {
        $aro = new AroRequests($db->escape_string($core->input['todelete']));
        $aro = $aro->delete_aro();
        switch($aro->get_errorcode()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successfullydeleted}</message>");
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->aroapprovedatleastonce}</message>");
                break;
            default:
                output_xml("<status>false</status><message>{$lang->errordeleting}</message>");
                break;
        }
    }
    elseif($core->input['action'] == 'get_deletearodocument') {
        eval("\$deletearocodbox = \"".$template->get('popup_deletearodocument')."\";");
        output($deletearocodbox);
    }
}