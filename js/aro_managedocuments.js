/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: aro_managedocuments.js
 * Created:        @tony.assaad    Feb 19, 2015 | 9:59:17 AM
 * Last Update:    @tony.assaad    Feb 19, 2015 | 9:59:17 AM
 */
$(function() {
    $('td').each(function() {
        var pattern = /^[ 0-9-,.%]*$/;
        if($(this).html().match(pattern)) {
            if($(this).html().indexOf('-') === 0) {
                $(this).css("color", "red");
            }

        }
    });

    $("body").append("<div id='modal-loading2'>Please wait untill the calculation is done. <span  style='display:block; width:100px; height:75%; margin:15px auto 0 auto;'><img  src='./images/loader.gif'/></span></div>");
    $("#modal-loading2").dialog({height: 150, modal: true, closeOnEscape: false, title: 'Loading...', resizable: false, minHeight: 0, autoOpen: false, position: 'center',
        open: function(event, ui) {
            $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
            $('.ui-dialog').css('z-index', 103);
        }
    });


    $('.accordion .header').accordion({collapsible: false});
    $('.accordion .header').click(function() {
        $(this).next().toggle();
        return false;
    }).next().hide();
    $('.accordion .header').trigger('click');
    //--------------------------------------------------------------


    // setTimeout(function() {
    //       $("input[id='ordersummary_btn']").trigger("click");
    //       $("input[id$='_netMargin']").trigger("change");
    //   }, 10);

    if(typeof getUrlParameter('referrer') !== 'undefined') {
        if(getUrlParameter('referrer') == 'toapprove' || getUrlParameter('referrer') == 'toapprove#' || (myUrl.substring(myUrl.length - 1) == '#' && getUrlParameter('referrer') == 'toapprove')) {
            $("form[id='perform_aro/managearodouments_Form'] :input:not([id^='approvearo'])").attr("disabled", true);
        }
    }

    else {
        if(typeof getUrlParameter('id') !== 'undefined') {
            $.ajax({type: 'post',
                url: rootdir + "index.php?module=aro/managearodouments&action=viewonly",
                data: "id=" + getUrlParameter('id'),
                beforeSend: function() {
                },
                complete: function() {
                    //   $("#modal-loading").dialog("close").remove();
                },
                success: function(returnedData) {
                    if(typeof returnedData != 'undefined' && returnedData.length > 0) {
                        var json = eval("(" + returnedData + ");");
                        if(json['disable'] === 1) {
                            $("form[id='perform_aro/managearodouments_Form'] :input:not([id^='approve_aro'])").attr("disabled", true);
                            $("button").attr("disabled", true);
                        }
                    }
                }
            });
        }
    }
///------------------------
    $(document).on("click", "input[id='approvearo']", function() {
        var url = window.location.href;
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=approvearo&id=' + $("input[id='approvearo_id']").val(), function() {
            window.location.href = url;
        });
    });
    /*-----------------------------------------------------------*/
    $(document).on("click", "input[id='po_sent']", function() {
        var url = window.location.href;
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=markpoassent&id=' + $("input[name='aorid']").val(), function() {
            window.location.href = url;
        });
    });
    /*-----------------------------------------------------------
     On change of affid Or purchase type document number, Affiliate policy , approval chain, default aff policy
     On change of affid only Get warehouses
     On Change of purchase type only (check which fields to disable))*/
    $(document).on("change", "select[id$='purchasetype'],select[id$='affid']", function() {
        if(sharedFunctions.checkSession() == false) {
            return;
        }
        $(this).data('affid', $('select[id=affid]').val());
        var affid = $(this).data('affid');
        $(this).data('purchasetype', $('select[id=purchasetype]').val());
        var ptid = $(this).data('purchasetype');
        var inputChecksum = $("input[id='inputChecksum']").val();
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populatedocnum&affid= ' + affid + '&ptid= ' + ptid + '&inputChecksum=' + inputChecksum);
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateaffpolicy&affid= ' + affid + '&ptid= ' + ptid);
        var aroBusinessManager = '';
        if(typeof $("input[id='user_0_id']").val() != "undefined") {
            aroBusinessManager = $("input[id='user_0_id']").val();
        }
        var intermedAff = $("select[id='partiesinfo_intermed_aff']").val();
        $.ajax({type: 'post',
            url: rootdir + "index.php?module=aro/managearodouments&action=generateapprovalchain",
            data: "affid=" + affid + "&ptid=" + ptid + "&aroBusinessManager=" + aroBusinessManager + "&intermedAff=" + intermedAff,
            beforeSend: function() {
            },
            complete: function() {
                //   $("#modal-loading").dialog("close").remove();
            },
            success: function(returnedData) {
                $('#aro_approvalcain').html(returnedData);
            }
        });
        $.getJSON(rootdir + 'index.php?module=aro/managearodouments&action=popultedefaultaffpolicy&affid= ' + affid + '&ptid= ' + ptid, function(data) {
            var jsonStr = JSON.stringify(data);
            obj = JSON.parse(jsonStr);
            if((typeof obj === "object") && (obj !== null)) {
                jQuery.each(obj, function(i, val) {
                    if(val == 0) { //If no default intermed policy (Ex: case of LSP)
                        $('select[id^="' + i + '"] option:selected').removeAttr('selected');
                    } else {
                        // var id = val.split(" ");
                        $("select[id^='" + i + "'] option[value='" + val + "']").attr("selected", "true").trigger("change");
                    }
                });
            }
        });
        if($(this).attr('id') === 'affid') {
            /*Get Affiliate Warehouses*/
            $.ajax({type: 'post',
                url: rootdir + "index.php?module=aro/managearodouments&action=getwarehouses",
                data: "affid=" + affid,
                beforeSend: function() {
                    $("body").append("<div id='modal-loading'></div>");
                    $("#modal-loading").dialog({height: 150, modal: true, closeOnEscape: false, title: 'Loading...', resizable: false, minHeight: 0
                    });
                },
                complete: function() {
                    $("#modal-loading").dialog("close").remove();
                },
                success: function(returnedData) {

                    $('#warehouse_list_td').html(returnedData);
                    $('#parmsfornetmargin_warehousingRate').find('option').remove();
                    $('#parmsfornetmargin_warehousingPeriod').val(0);
                    $('#parmsfornetmargin_warehousingRateUsd').find('option').remove();
                    $('#parmsfornetmargin_warehouseUsdExchangeRate').val('');

                }
            });
        }
        if($(this).attr('id') === 'purchasetype') {
            /*Disable days in Stock, QPS and warehousing section according to seleced purchasetype*/
            var ptid = $(this).val();
            $.getJSON(rootdir + 'index.php?module=aro/managearodouments&action=InolveIntermediary&ptid=' + ptid, function(data) {
                //var jsonStr = JSON.stringify(data);
                //obj = JSON.parse(jsonStr);
                // jQuery.each(obj, function(i, val) {
                var fields = ["aff", "paymentterm", "incoterms", "IncotermsDesc", "PaymentTermDesc", "ptAcceptableMargin", "promiseofpayment", "estdateofpayment"];
                if(data == 0) {
                    for(var i = 0; i < fields.length; i++) {
                        $("input[id='partiesinfo_intermed_" + fields[i] + "']").removeAttr("required");
                        $("select[id='partiesinfo_intermed_" + fields[i] + "']").removeAttr("required");
                        $("input[id='pickDate_intermed_" + fields[i] + "']").removeAttr("required");
                        $("select[id='partiesinfo_intermed_" + fields[i] + "'] option[value='0']").remove();
                    }
                    $("input[id='partiesinfo_commission']").attr('value', '0');
                } else {
                    for(var i = 0; i < fields.length; i++) {
                        $("input[id='partiesinfo_intermed_" + fields[i] + "']").attr("required", "true");
                        $("select[id='partiesinfo_intermed_" + fields[i] + "']").attr("required", "true");
                        $("input[id='pickDate_intermed_" + fields[i] + "']").attr("required", "true");
                    }
                }
            });
            $.getJSON(rootdir + 'index.php?module=aro/managearodouments&action=disablefields&ptid=' + ptid, function(data) {
                var jsonStr = JSON.stringify(data);
                obj = JSON.parse(jsonStr);
                jQuery.each(obj, function(i, val) {
                    $("input[id^='" + i + "']").val(val);
                });
                var fields = ["daysInStock", "qtyPotentiallySold"];
                for(var i = 0; i < fields.length; i++) {
                    if($("input[id='productline_" + fields[i] + "_disabled']").val() == 0) {
                        $("input[id$='" + fields[i] + "']").attr('value', '0')
                        $("input[id$='" + fields[i] + "']").attr("readonly", "true");
                        $("input[id$='shelfLife']").attr("readonly", "true");
                    }
                    else {
                        $("input[id$='" + fields[i] + "']").removeAttr("readonly");
                        $("input[id$='shelfLife']").removeAttr("readonly");
                    }
                }
                var warehousing_fields = ["warehouse", "warehousingRate", "warehousingRateUsd", "warehouseRateExchangeRate", "warehousingPeriod", "warehousingTotalLoad", "uom"];
                /*
                 * Consider refactoring to avoid loop
                 */
                for(var i = 0; i < warehousing_fields.length; i++) {
                    if($("input[id='parmsfornetmargin_warehousing_disabled']").val() == 0) {
                        $("input[id='parmsfornetmargin_" + warehousing_fields[i] + "']").attr('value', '0');
                        $("input[id='parmsfornetmargin_" + warehousing_fields[i] + "']").attr("readonly", "true");
                        $("select[id='parmsfornetmargin_" + warehousing_fields[i] + "']").append('<option value="0" selected></option>');
                        $("select[id='parmsfornetmargin_" + warehousing_fields[i] + "']").attr("disabled", "true");
                    }
                    else {
                        $("input[id='parmsfornetmargin_" + warehousing_fields[i] + "']").removeAttr("readonly");
                        $("input[id='parmsfornetmargin_" + warehousing_fields[i] + "'],select[id ='parmsfornetmargin_" + warehousing_fields[i] + "']").removeAttr("disabled");

                    }
                }
                if($("input[id='parmsfornetmargin_warehousing_disabled']").val() == 0) {
                    $("input[id='partiesinfo_intermed_ptAcceptableMargin']").val('');
                    $("input[id='partiesinfo_intermed_ptAcceptableMargin']").attr("readonly", "true");
                    $("input[id='pickDate_intermed_promiseofpayment']").val('');
                    $("input[id='pickDate_intermed_promiseofpayment']").attr("readonly", "true");
                }
                else {
                    $("input[id='partiesinfo_intermed_ptAcceptableMargin']").removeAttr("readonly");
                    $("input[id='pickDate_intermed_promiseofpayment']").removeAttr("readonly");
                }
            });
        }

        /* Loop over all product lines to update the numbers based on the new policy */
//        $("tbody[id^='productline_']").find($("select[id$='_quatity']")).each(function() {
//            var id = $(this).attr('id').split('_');
//            if($("input[id='product_noexception_" + id[1] + "_id_output']").val().length > 0) {
//                $(this).trigger("change");
//            }
//        });
    });
    //-----------------Populate intermediary affiliate policy-----------------------------//
    $(document).on("change", "select[id='partiesinfo_intermed_aff']", function() {
        var ptid = $("select[id='purchasetype']").val();
        var intermedAff = $("select[id='partiesinfo_intermed_aff']").val();
        var estimatedImtermedPayment = $("input[id='pickDate_intermed_estdateofpayment']").val();
        var estimatedManufacturerPayment = $("input[id='pickDate_vendor_estdateofpayment']").val();
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateintermedaffpolicy&ptid= ' + ptid + '&intermedAff=' + intermedAff + '&estimatedImtermedPayment=' + estimatedImtermedPayment + '&estimatedManufacturerPayment=' + estimatedManufacturerPayment);
//        var triggercomm = setTimeout(function() {
//            $("input[id$='_intialPrice']").trigger("change");
//        }, 2000);

        $("input[id='user_0_id_output']").trigger("change");
    });
    //------------------------------------------------------------------------------------//
    //-----------------Get Exchang Rate  -------------------------------------------------//
    $(document).on("change", "#currencies", function() {
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=getexchangerate&currency=' + $(this).val(), function(json) {
            if(json == null) {
                $("input[id='exchangeRateToUSD']").removeAttr("readonly").val('');
            }
            else {
                if(json.exchangeRateToUSD == '') {
                    $("input[id='exchangeRateToUSD']").removeAttr("readonly");
                }
                else {
                    $("input[id='exchangeRateToUSD']").attr("readonly", "true");
                }
            }
        });
        $("span[id^='ordersummary_curr']").text($(this).find(":selected").text());
    });
    //------------------------------------------------------------------------------------//
    //-------------------Get Warehouse policy parms---------------------------------------//
    $(document).on("change", "#parmsfornetmargin_warehouse", function() {
        var warehouse = $(this).val();
        var ptid = $("#purchasetype").val();
        if(warehouse !== '' && warehouse !== typeof undefined) {
            $.getJSON(rootdir + 'index.php?module=aro/managearodouments&action=populatewarehousepolicy&warehouse= ' + warehouse + '&ptid=' + ptid, function(data) {
                var jsonStr = JSON.stringify(data);
                obj = JSON.parse(jsonStr);
                jQuery.each(obj, function(i, val) {
                    if(i === 'parmsfornetmargin_warehousingRate' || i === 'parmsfornetmargin_warehousingRateUsd') {
                        var id = val.split(" ");
                        $("select[id='" + i + "']").empty().append("<option value='" + id[0] + "' selected>" + val + "</option>");
                    }
                    else if(i === 'parmsfornetmargin_uom') {
                        var id = val.split(" ");
                        $("select[id='" + i + "'] option[value='" + id[0] + "']").attr("selected", "selected");
                    }
                    else {
                        $("input[id='" + i + "']").val(val);
                    }
                });
            });
        }
    });
    $(document).on("change", "#parmsfornetmargin_warehouseUsdExchangeRate", function() {
        if(typeof $(this).val() !== 'undefined' && typeof $("select[id='parmsfornetmargin_warehousingRate']").val() !== 'undefined') {
            var value = $(this).val() * $("select[id='parmsfornetmargin_warehousingRate']").val();
            $("select[id='parmsfornetmargin_warehousingRateUsd']").empty().append("<option value='" + value + "' selected>" + value + "</option>");
        }
    });


    //------------------------------------------------------------------------------------//
    // If Inco terms are different between intermediary and vendor, freight is mandatory
    $(document).on("change", "select[id='partiesinfo_intermed_incoterms'],select[id='partiesinfo_vendor_incoterms']", function() {
        $("input[id='partiesinfo_freight']").removeAttr("required");
        if($("select[id='partiesinfo_intermed_incoterms']").val() !== '' && $("select[id='partiesinfo_vendor_incoterms']").val() !== '') {
            if($("select[id='partiesinfo_intermed_incoterms']").val() !== $("select[id='partiesinfo_vendor_incoterms']").val()) {
                $("input[id='partiesinfo_freight']").attr("required", "true");
                $.ajax({type: 'post',
                    url: rootdir + "index.php?module=aro/managearodouments&action=managevendorincoterms",
                    data: "incoterm=" + $("select[id='partiesinfo_vendor_incoterms']").val(),
                    beforeSend: function() {
                    },
                    complete: function() {
                    },
                    success: function(returnedData) {
                        if(typeof returnedData != 'undefined' && returnedData.length > 0) {
                            var json = eval("(" + returnedData + ");");
                            if(json['carriageOnBuyer'] === 1) {
                                $("tr[id='partiesinfo_forwarder']").show();
                            } else {
                                $("input[id='partiesinfo_forwardername']").val("");
                                $("input[id='partiesinfo_forwarderPT']").val("");
                                $("tr[id='partiesinfo_forwarder']").hide();
                            }
                        }
                    }
                });
            } else {
                $("input[id='partiesinfo_forwardername']").val("");
                $("input[id='partiesinfo_forwarderPT']").val("");
                $("tr[id='partiesinfo_forwarder']").hide();
            }
        }
    });
    //----------------------------------------------------------------------------------------------------------------------------//
    //--------------Populate dates of PartiepickDate_estDateOfShipments Information----------------------------//
    //Trigger(s): 10A, 7, 6, 11

    $(document).on("change", "input[id='pickDate_estDateOfShipment'],select[id='partiesinfo_intermed_paymentterm'],select[id='partiesinfo_vendor_paymentterm'],input[id='partiesinfo_intermed_ptAcceptableMargin'],#ordersummary_invoicevalue_local", function() {
        var estDateOfShipment = $("input[id='pickDate_estDateOfShipment']").val();
        var ptAcceptableMargin = $("input[id='partiesinfo_intermed_ptAcceptableMargin']").val();
        var intermedPaymentTerm = $("select[id = 'partiesinfo_intermed_paymentterm']").val();
        $("input[id='pickDate_intermed_estdateofpayment']").attr("disabled", "true");
        if(!(intermedPaymentTerm.length > 0)) {
            $("input[id='pickDate_intermed_estdateofpayment']").removeAttr("disabled");
        }
        var vendorPaymentTerm = $("select[id ='partiesinfo_vendor_paymentterm']").val();
        var ptid = $('select[id=purchasetype]').val();
        var est_local_pay = $("input[id='avgeliduedate']").val();
        var attributes = '&intermedPaymentTerm=' + intermedPaymentTerm + '&vendorPaymentTerm=' + vendorPaymentTerm + '&estDateOfShipment=' + estDateOfShipment + '&ptAcceptableMargin=' + ptAcceptableMargin + '&ptid=' + ptid + '&est_local_pay=' + est_local_pay;
        //Needed for local interest value calculation
        var localBankInterestRate = $("input[id='parmsfornetmargin_localBankInterestRate']").val();
        var totalbuyingvalue_total = 0;
        var totalbuyingvalue_total = $("input[id='ordersummary_invoicevalueusd_intermed']").val();
        if(typeof totalbuyingvalue_total != 'undefined') {
            attributes = attributes + '&totalbuyingvalue_total=' + totalbuyingvalue_total;
        }
        if(typeof localBankInterestRate != undefined) {
            attributes = attributes + '&localBankInterestRate=' + localBankInterestRate;
        }
        var intermedBankInterestRate = $("input[id='parmsfornetmargin_intermedBankInterestRate']").val();
        if(typeof intermedBankInterestRate != 'undefined') {
            attributes = attributes + '&intermedBankInterestRate=' + intermedBankInterestRate;
        }
//Update Total fees : Summation of total fees to be added to the interest value
        var totalintermedfees = 0;
        $("input[id$='freight'],input[id$='bankFees'],input[id$='insurance'],input[id$='legalization'],input[id$='courier'],input[id$='otherFees']").each(function() {
            if(!jQuery.isEmptyObject(this.value)) {
                totalintermedfees += parseFloat(this.value);
            }
        });
        attributes = attributes + '&totalintermedfees=' + totalintermedfees;
        if($(this).attr('id') == 'ordersummary_invoicevalue_local') {
            sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populate_localintvalue' + attributes);
        }
        else {
            sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populatepartiesinfofields' + attributes, function() {
                $("input[id='unitfee_btn']").trigger("click");
            });
        }
    });
    //----------------------------------------------------------------------------------------------------------------------------//
    //-------------on change of est date of sales (actual purchase) Trigger est. local invoice date------------------
    $(document).on("change", "input[id^='pickDate_sale_']", function() {
        $("select[id^='paymentermdays_']").trigger('change');
    });
    //---------------------------------------------------------------------------------------------------------
    //------------- on change of PT base date Trigger est. local invoice date
    $("tbody[id^='newcustomer_']").on('change', "input[id^='pickDate_to_']", function() {
        var id = $(this).attr('id').split('_');
        if($(this).val() == '') {
            $("input[id^='altpickDate_to_" + id[2] + "']").val("");
        }
        $("select[id^='paymentermdays_" + id[2] + "']").trigger("change");
    });
//-------------------------------------------------------------------------------------//
    $(document).on("change", "select[id^='paymentermdays_']", function() {
        var parentContainer = $(this).closest('div');
        var paymentdays = [];
        var salesdates = [];
        var ptbasedates = [];
        parentContainer.children('table').find('tr').each(function() {
            /*check if the customer is selected */
            if($(this).find("input[id^='customer_']").val() !== '') {
                $(this).find('select').each(function() {
                    if($(this).val() !== '') {
                        paymentdays.push($(this).val());
                    }
                });
            }
        });
        $("tbody[id^='actualpurchaserow_']").find("input[id^='altpickDate_sale_']").each(function() {
            var id = $(this).attr('id').split('_');
            if($("input[id='pickDate_to_" + id[2] + "']").val() == '') {
                if($(this).val() !== '') {
                    salesdates.push($(this).val());
                }
            }
        });
        $("tbody[id^='newcustomer_']").find("input[id^='altpickDate_to_']").each(function() {
            if($(this).val() !== '') {
                ptbasedates.push($(this).val());
            }
        });
        if(($("input[id='altpickDate_to_0_altcid']").val() !== '') && (typeof $("input[id='altpickDate_to_0_altcid']").val() != 'undefined')) {
            ptbasedates.push($("input[id='altpickDate_to_0_altcid']").val());
        }
        var purchasetype = $("input[id^='purchasetype']").val();
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=getestimatedate&paymentermdays[]= ' + paymentdays + '&ptid= ' + purchasetype + '&salesdates[]=' + salesdates + '&ptbasedates[]=' + ptbasedates, function() {
            // $("select[id='partiesinfo_intermed_paymentterm']").trigger('change');
        });
    });
//-------------------------------------------------------------------------------------//
    $(document).on("change", "input[id='pickDate_estDateOfShipment'],input[id='partiesinfo_transitTime'],input[id='partiesinfo_clearanceTime']", function() {

        var transitTime = $("input[id='partiesinfo_transitTime']").val();
        var clearanceTime = $("input[id='partiesinfo_clearanceTime']").val();
        var dateOfStockEntry = $("input[id='pickDate_estDateOfShipment']").val();
        var attr = '&';
        if(typeof transitTime != undefined) {
            attr = attr + 'transitTime=' + transitTime;
        }
        if(typeof clearanceTime != undefined) {
            attr = attr + '&clearanceTime=' + clearanceTime;
        }
        if(typeof dateOfStockEntry != undefined) {
            attr = attr + '&dateOfStockEntry=' + dateOfStockEntry;
        }
        $("tbody[id^='actualpurchaserow']").find($("input[id^='pickDate_stock_']")).each(function() {
            var id = $(this).attr('id').split("_");
            sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateactualpurchase_stockentrydate' + attr + '&rowid=' + id[2]);
        });
    });
//-------------------------------------------------------------------------------------//
//Calculate order summary  local interest value
//    $("input[id$='_affBuyingPrice']").live('change', function () {
//        var invoicevalue_local = invoicevalue_local_RIC = 0;
//        $("tbody[id^='productline_']").find($("input[id$='_affBuyingPrice']")).each(function () {
//            var id = $(this).attr('id').split('_');
//            invoicevalue_parameter = parseFloat($("input[id='productline_" + id[1] + "_totalBuyingValue']").val());
//            totalqty = parseFloat($("input[id='productline_" + id[1] + "_quantity']").val());
//
//            if(!isNaN(invoicevalue_parameter)) {
//                invoicevalue_local += invoicevalue_parameter;
//                if(!isNaN(totalqty)) {
//                    invoicevalue_local_RIC += (totalqty * (parseFloat($("input[id='productline_" + id[1] + "_sellingPrice']").val())));
//                }
//            }
//        });
//        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populate_localintersetvalues&invoicevalue_local_RIC' + invoicevalue_local_RIC + '&ptid=' + $("select[id=purchasetype]").val() + '&invoicevalue_local=' + invoicevalue_local + '&exchangeRateToUSD=' + $("#exchangeRateToUSD").val());
//        $("select[id='partiesinfo_intermed_paymentterm']").trigger("change");
//    });

//Trigger(s): 21
    var actualpurchaselines_tr;
    $(document).on("change", "input[id$='_netMargin'],input[id$='_grossMarginAtRiskRatio']", function() {
        var invoicevalue_local = invoicevalue_local_RIC = grossMarginAtRiskRatio = 0;
        $("tbody[id^='productline_']").find($("input[id$='_affBuyingPrice']")).each(function() {
            var id = $(this).attr('id').split('_');
            invoicevalue_parameter = parseFloat($("input[id='productline_" + id[1] + "_totalBuyingValue']").val());
            totalqty = parseFloat($("input[id='productline_" + id[1] + "_quantity']").val());
            if(!isNaN(invoicevalue_parameter)) {
                invoicevalue_local += invoicevalue_parameter;
                if(!isNaN(totalqty)) {
                    invoicevalue_local_RIC += (totalqty * (parseFloat($("input[id='productline_" + id[1] + "_sellingPrice']").val())));
                }
            }
        });
        var id = $(this).attr('id').split("_");
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populate_localintersetvalues&invoicevalue_local_RIC=' + invoicevalue_local_RIC + '&ptid=' + $("select[id=purchasetype]").val() + '&invoicevalue_local=' + invoicevalue_local + '&exchangeRateToUSD=' + $("#exchangeRateToUSD").val(), function() {
            $("select[id='partiesinfo_intermed_paymentterm']").trigger("change");
            clearTimeout(actualpurchaselines_tr);
            actualpurchaselines_tr = setTimeout(function() {
                addactualpurchaselines(id[1], function() {
                    if($("#modal-loading2").dialog("isOpen")) {
                        $("#modal-loading2").dialog("close");
                    }
                });
            }, 2000);
        });
    });
//------------------------------------------//
    $("input[id$='freight'],input[id$='bankFees'],input[id$='insurance'],input[id$='legalization'],input[id$='courier'],input[id$='otherFees']").bind('change', function() {
        var total = 0;
        $("input[id$='freight'],input[id$='bankFees'],input[id$='insurance'],input[id$='legalization'],input[id$='courier'],input[id$='otherFees']").each(function() {
            if(!jQuery.isEmptyObject(this.value)) {
                total += parseFloat(this.value);
            }
        });
        $("input[id='partiesinfo_totalintermedfees']").val(total);
        var interestvalue = $("input[id='parmsfornetmargin_interestvalue']").val();
        if(interestvalue.length > 0) {
            total += parseFloat(interestvalue);
        }
        $("input[id='partiesinfo_totalfees']").val(total);
        $("input[id='partiesinfo_totalfees']").trigger("change");
        $("input[id='partiesinfo_totalfees']").trigger("click", $("input[id='unitfee_btn']"));
        // $("input[id='partiesinfo_totalfees']").val(total).trigger("click", $("input[id='unitfee_btn']"));
//        $("input[id$='_intialPrice']").trigger("change");
//        var updateinterestvalue = setTimeout(function() {
//            $("input[id='ordersummary_btn']").trigger("click");
//        }, 2000);
    });
//------------------------------------------//
//-------------Disable qtyPotentiallySold if daysInStock=0 ------------------*/
// Trigger(s): 14
//    $("input[id$='_daysInStock']").live('change keyup', function() {
//        var id = $(this).attr('id').split("_");
//        $("input[id='productline_" + id[1] + "_qtyPotentiallySold']").removeAttr("readonly");
//        if($(this).val() == 0) {
//            $("input[id='productline_" + id[1] + "_qtyPotentiallySold']").attr('value', '0');
//            $("input[id='productline_" + id[1] + "_qtyPotentiallySold']").attr("readonly", "true");
//        }
//    });
//---------------------------------------------------------------------------//
//------Form Submitting after 30 seconds--------------//
//    var auto_refresh = setInterval(function() {
//        submitform();
//    }, 300000);
//    function submitform() {     //Form submit function
//        $("input[id^='perform_'][id$='_Button']").trigger("click");
//    }
//---------------------------------------------------//
//-------------If Vendor is affiliate, such select affiliate not entity and Disable  intermediary section----------------------//
//Trigger Intermediary Aff Policy
    $(document).on("change", "input[id='vendor_isaffiliate']", function() {
        $("td[id='vendor_affiliate']").css("display", "none");
        $("input[id='supplier_1_autocomplete']").attr('value', '');
        $("input[id='supplier_1_id']").attr('value', '');
        $("input[id='supplier_1_autocomplete']").removeAttr("disabled");
        var fields = ["aff", "paymentterm", "incoterms", "IncotermsDesc", "PaymentTermDesc", "ptAcceptableMargin", "promiseofpayment"];
        for(var i = 0; i < fields.length; i++) {
            $("input[id='partiesinfo_intermed_" + fields[i] + "']").removeAttr("disabled");
            $("select[id='partiesinfo_intermed_" + fields[i] + "']").removeAttr("disabled");
            $("input[id='pickDate_intermed_" + fields[i] + "']").removeAttr("disabled");
            $("select[id='partiesinfo_intermed_" + fields[i] + "'] option[value='0']").remove();
        }

        if($(this).is(":checked")) {
            var fields = ["aff", "paymentterm", "incoterms", "IncotermsDesc", "PaymentTermDesc", "ptAcceptableMargin", "promiseofpayment"];
            for(var i = 0; i < fields.length; i++) {
                $("input[id='partiesinfo_intermed_" + fields[i] + "']").attr("value", "");
                $("input[id='partiesinfo_intermed_" + fields[i] + "']").attr("disabled", "true");
                $("select[id='partiesinfo_intermed_" + fields[i] + "']").removeAttr("selected");
                $("select[id='partiesinfo_intermed_" + fields[i] + "']").append('<option value="0" selected="selected"></option>');
                $("select[id='partiesinfo_intermed_" + fields[i] + "']").attr("disabled", "true");
                $("input[id='pickDate_intermed_" + fields[i] + "']").attr("value", "");
                $("input[id='pickDate_intermed_" + fields[i] + "']").attr("disabled", "true");
                $("input[id='altpickDate_intermed_" + fields[i] + "']").attr("value", "");
            }
            $("input[id='partiesinfo_commission']").attr("value", "");
            $("input[id='supplier_1_autocomplete']").attr("disabled", "true");
            $("td[id='vendor_affiliate']").css("display", "block");
        }
        $("select[id='partiesinfo_intermed_aff']").trigger("change");
    });
//----------------------------------------------------------------------------------------------------------------------------//
    $(document).on("change", "input[id='vendor_isConsolidationPlatform']", function() {
        if($(this).is(":checked")) {
            $("td[id='consolidation_warehouse']").css("display", "block");
        } else {
            $("select[id='partiesinfo_consolidationWarehouse']").find('option[value="0"]').prop('selected', true);
            //  $("select[id='partiesinfo_consolidationWarehouse']").append('<option value="0" selected="selected"></option>');
            $("td[id='consolidation_warehouse']").css("display", "none");
        }
    });
//Trigger(s): 20A - 20B
    $(document).on("change", "#partiesinfo_totalfees,input[id='partiesinfo_totaldiscount'],select[id^='productline_'][id$='_uom'],input[id^='productline_'][id$='_quantity'],input[id^='productline_'][id$='_intialPrice']", function() {
        var field_id = $(this).attr('id').split('_');
//        if(field_id[2] == 'intialPrice') {
//            $("#modal-loading2").dialog("open");
//        }
        if($(this).attr('id') != 'partiesinfo_totalfees') {
            var totalamount = totalcommision = intialprice = totalqty = 0;
            $("tbody[id^='productline_']").find($("select[id$='_uom']")).each(function() {
                var id = $(this).attr('id').split('_');
                totalqty = parseFloat($("input[id='productline_" + id[1] + "_quantity']").val());
                var intialprice = parseFloat($("input[id='productline_" + id[1] + "_intialPrice']").val());
                if(!isNaN(totalqty) && !isNaN(intialprice)) {
                    totalamount += totalqty * intialprice; //reference amount
                }
            });
            comm = parseFloat($('input[id=partiesinfo_defaultcommission]').val());
            totalcommision = totalamount * (comm / 100)
            var ptid = $("select[id='purchasetype']").val();
            var totaldiscount = parseFloat($('input[id=partiesinfo_totaldiscount]').val());
            var attributes = '&totalamount=' + totalamount + '&totalcommision=' + totalcommision + '&defaultcomm=' + comm + '&ptid=' + ptid + '&totalDiscount=' + totaldiscount;
            sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=updatecommission' + attributes, function() {
                if(field_id[2] == 'intialPrice') {
                    var trigger = setTimeout(function() {
                        $('#productline_' + field_id[1] + '_grossMarginAtRiskRatio').trigger('change');
//                        $("#modal-loading2").dialog("close");
                    }, 500);
                }
            });
        }

        $('#unitfee_btn').trigger('click');
//        if(field_id[2] == 'intialPrice') {
//            var trigger2 = setTimeout(function() {
//                triggerproductlines(field_id.join('_'));
//            }, 1000);
//        }


    });

    $(document).on("change", "select[id^='productline_'][id$='_uom']", function() {
        var id = $(this).attr('id').split("_");
        /* Calculate Unit Fees*/

        //$("input[id='ordersummary_btn']").trigger("click");
    });
//    $('input[id$="_quantity"],input[id$="_intialPrice"]').live('change', function() {
//        var totalamount = totalcommision = intialprice = totalqty = 0;
//        $("tbody[id^='productline_']").find($("select[id$='_uom']")).each(function() {
//            var id = $(this).attr('id').split('_');
//            totalqty = parseFloat($("input[id='productline_" + id[1] + "_quantity']").val());
//            var intialprice = parseFloat($("input[id='productline_" + id[1] + "_intialPrice']").val());
//            if(!isNaN(totalqty) && !isNaN(intialprice)) {
//                totalamount += totalqty * intialprice; //reference amount
//            }
//        });
//        comm = parseFloat($('input[id=partiesinfo_defaultcommission]').val());
//        totalcommision = totalamount * (comm / 100)
//        var attributes = '&totalamount=' + totalamount + '&totalcommision=' + totalcommision + '&defaultcomm=' + comm;
//        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=updatecommission' + attributes, function() {
//            $("input[id='partiesinfo_commission']").trigger("change");
//        });
//    });

// Trigger(s): 16A - 16B - 16C ///input[id$="_intialPrice"] //#partiesinfo_commission
    $(document).on("change", '#ordersummary_unitfee', function() {
        var attrs = '';
        $("tbody[id^='productline_']").find($("select[id$='_uom']")).each(function() {
            var id = $(this).attr('id').split('_');
            attrs = '&intialPrice=' + $("input[id='productline_" + id[1] + "_intialPrice']").val() + '&quantity=' + $("input[id='productline_" + id[1] + "_quantity']").val();
            attrs += "&commission=" + $('input[id=partiesinfo_commission]').val();
            attrs += '&unitfees=' + $("input[id='ordersummary_unitfee']").val();
            attrs += '&rowid=' + id[1] + '&ptid=' + $('select[id=purchasetype]').val();
            sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateaffbuyingprice' + attrs, function() {
                //// !!!!!!!! NEED TO Modify this into a funtion and apply wherever it is used//
                var invoicevalue_local = invoicevalue_local_RIC = 0;
                $("tbody[id^='productline_']").find($("input[id$='_affBuyingPrice']")).each(function() {
                    var id = $(this).attr('id').split('_');
                    invoicevalue_parameter = parseFloat($("input[id='productline_" + id[1] + "_totalBuyingValue']").val());
                    totalqty = parseFloat($("input[id='productline_" + id[1] + "_quantity']").val());
                    if(!isNaN(invoicevalue_parameter)) {
                        invoicevalue_local += invoicevalue_parameter;
                        if(!isNaN(totalqty)) {
                            invoicevalue_local_RIC += (totalqty * (parseFloat($("input[id='productline_" + id[1] + "_sellingPrice']").val())));
                        }
                    }
                });

                /////////////////////////////////////////////////////////////////////////
                sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populate_localintersetvalues&invoicevalue_local_RIC=' + invoicevalue_local_RIC + '&ptid=' + $("select[id=purchasetype]").val() + '&invoicevalue_local=' + invoicevalue_local + '&exchangeRateToUSD=' + $("#exchangeRateToUSD").val(), function() {
                    $("input[id='ordersummary_btn']").trigger("click");
                });
            });
        });
    });
    var fields_array = ["quantity", "qtyPotentiallySold", "intialPrice", "costPrice", "sellingPrice", "daysInStock"];
// Trigger(s): 15A, 15B, 19, 18A

    $(document).on("change", "input[id^='productline_'][id$='_quantity'],input[id^='productline_'][id$='_qtyPotentiallySold'],input[id^='productline_'][id$='_costPrice'],input[id$='_sellingPrice']", function() {
        var id = $(this).attr('id').split("_");
        if(id[2] == 'costPrice') {
            $("input[id='productline_" + id[1] + "_sellingPrice']").attr("disabled", "true");
            var tr = setTimeout(function() {
                $("input[id='productline_" + id[1] + "_sellingPrice']").removeAttr("disabled");
            }, 1000);
        }


        var fields = '';
        $.each(fields_array, function(index, value) {
            fields += '&' + value + '=' + $("input[id='productline_" + id[1] + "_" + value + "']").val();
        });
        fields += '&ptid=' + $("#purchasetype").val() + '&exchangeRateToUSD=' + $("#exchangeRateToUSD").val();
        var parmsfornetmargin_fields = new Array('localPeriodOfInterest', 'localBankInterestRate', 'warehousingPeriod', 'warehousingTotalLoad', 'intermedBankInterestRate', 'intermedPeriodOfInterest');
        var parmsfornetmargin = '';
        $.each(parmsfornetmargin_fields, function(index, value) {
            parmsfornetmargin += '&' + value + '=' + $("input[id='parmsfornetmargin_" + value + "']").val();
        });
        parmsfornetmargin += '&warehousingRate=' + $("select[id='parmsfornetmargin_warehousingRateUsd']").val();
        parmsfornetmargin += "&commission=" + $('input[id=partiesinfo_commission]').val();
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        var totalquantity = {};
        var totalqty = 0;
        var refernece = 0; //quantity*initialprice;
        $("tbody[id^='productline_']").find($("select[id$='_uom']")).each(function() {
            var id = $(this).attr('id').split('_');
            totalqty = parseFloat($("input[id='productline_" + id[1] + "_quantity']").val());
            intialprice = parseFloat($("input[id='productline_" + id[1] + "_intialPrice']").val());
            refernece += (totalqty * intialprice);
            if(!isNaN(totalqty)) {
                totalquantity[$(this).val()] = parseFloat(totalquantity[$(this).val()] || 0) + totalqty; //Fill array of qty per uom
            }
        });
        var i = 0;
        var qty = totalqtyperuom = {};
        var qtyperunit = '';
        $.each(totalquantity, function(key, value) {
            if(i !== 0) {
                qtyperunit += "_";
            }
            qty[i] = value;
            totalqtyperuom[key] = value;
            qtyperunit += key + ":" + value;
            i++;
        });
        var totalfees = $('input[id=partiesinfo_totalfees]').val();
        /******Cecking Fees**********/
//        var qtyperc = ((parseFloat($("input[id='productline_" + id[1] + "_quantity']").val()) * parseFloat($("input[id='productline_" + id[1] + "_intialPrice']").val())) / refernece) * 100;
//        if(i === 1)// if only one product line
//        {
//            var qtyperc = (parseFloat($("input[id='productline_" + id[1] + "_quantity']").val()) / parseFloat(qty[0])) * 100;
//        }
//        fees = ((qtyperc / 100) * totalfees).toFixed(3);

        var unitfees = $("input[id='ordersummary_unitfee']").val();
        var totalQtyPerUom = totalqtyperuom[$("select[id$='" + id[1] + "_uom']").val()];
        parmsfornetmargin += "&totalQty=" + totalQtyPerUom + "&localRiskRatio=" + $("input[id='parmsfornetmargin_localRiskRatio']").val() + '&unitfees=' + unitfees;
        parmsfornetmargin += "&totalDiscount=" + $("input[id='partiesinfo_totaldiscount']").val();
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateproductlinefields&rowid=' + id[1] + fields + '&parmsfornetmargin=' + parmsfornetmargin, function(json) {
            //   $("input[id='unitfee_btn']").trigger("change");

            //   opendialog();

            if(json["productline_" + id[1] + '_grossMarginAtRiskRatio']) {
                if(id[2] == 'sellingPrice') {
                    $("#modal-loading2").dialog("open");
                }
                $('#productline_' + id[1] + '_grossMarginAtRiskRatio').trigger('change');
            }
            //$("input[id$='_netMargin']").trigger("change");
        });
    });
//    $("input[id^='productline_']").live('blur', function () {
//        var id = $(this).attr('id').split("_");
//        triggerproductlines(id);
//        addactualpurchaselines(id[1]);
//
//
////        var xxxxx = setTimeout(function() {
////            $("input[id='ordersummary_btn']").trigger("click");
////        }, 3000);
//
//    });

//needs optimization (loop through array for fields
    $(document).on("click", "input[id='ordersummary_btn']", function() {
        var totalfees = $('input[id=partiesinfo_totalfees]').val();
        var exchangeRateToUSD = $("#exchangeRateToUSD").val();
        var aff = $('select[id=affid]').val();
        var intermedAff = $("select[id='partiesinfo_intermed_aff']").val();
        attributes = '&exchangeRateToUSD=' + exchangeRateToUSD + '&intermedAff=' + intermedAff + '&aff=' + aff;
        var totalquantity = {};
        var totalfees = {};
        var totalqty = 0;
        var totalfee = 0;
        var totalamount = totalcommision = comm = invoicevalue_local = invoicevalue_local_RIC = 0;
        var invoicevalue_intermed = sellingpriceqty_product = local_netMargin = 0;
        var sum_totalqty = 0;
        var sum_totalfees = $("input[id='partiesinfo_totalintermedfees']").val();
        $("tbody[id^='productline_']").find($("select[id$='_uom']")).each(function() {
            var id = $(this).attr('id').split('_');
            totalqty = parseFloat($("input[id='productline_" + id[1] + "_quantity']").val());
            if(!isNaN(totalqty)) {
                totalquantity[$(this).val()] = parseFloat(totalquantity[$(this).val()] || 0) + totalqty;
                sum_totalqty = totalqty + parseFloat(sum_totalqty || 0);
            }
            totalfee = parseFloat($("input[id='productline_" + id[1] + "_fees']").val());
            if(!isNaN(totalfee)) {
                totalfees[$(this).val()] = parseFloat(totalfees[$(this).val()] || 0) + totalfee;
            }
            var intialprice = parseFloat($("input[id='productline_" + id[1] + "_intialPrice']").val());
            if(!isNaN(totalfee) && !isNaN(intialprice)) {
                invoicevalue_intermed += (totalqty * intialprice);
            }
//s  var invoicevalue_local = invoicevalue_local_RIC = 0;
//   invoicevalue_parameter = parseFloat($("input[id='productline_" + id[1] + "_totalBuyingValue']").val());
//  if(!isNaN(invoicevalue_parameter)) {
//      invoicevalue_local += invoicevalue_parameter;
//        if(!isNaN(totalqty)) {
//           invoicevalue_local_RIC += (totalqty * (parseFloat($("input[id='productline_" + id[1] + "_sellingPrice']").val())));
//       }
//  }
            if(!isNaN(parseFloat($("input[id='productline_" + id[1] + "_netMargin']").val()))) {
                local_netMargin += parseFloat($("input[id='productline_" + id[1] + "_netMargin']").val());
            }
            if(!isNaN((parseFloat($("input[id='productline_" + id[1] + "_sellingPrice']").val()) * totalqty))) {
                sellingpriceqty_product += (parseFloat($("input[id='productline_" + id[1] + "_sellingPrice']").val()) * totalqty);
            }
            totalamount += totalqty * intialprice; //reference amount
            comm = parseFloat($('input[id=partiesinfo_defaultcommission]').val());
            totalcommision = totalamount * (comm / 100);
        });
        var i = 0;
        var qtyperunit = '';
        $.each(totalquantity, function(key, value) {
            qtyperunit += key + ":" + value.toFixed(3);
            qtyperunit += "_";
            i++;
        });
        var j = 0;
        var feeperunit = '';
        $.each(totalfees, function(key, value) {
            feeperunit += key + ":" + value.toFixed(3);
            feeperunit += "_";
            j++;
        });
        var localinvoicevalue_usd = 0;
        localinvoicevalue_usd = $("input[id='ordersummary_invoicevalueusd_local']").val();
        attributes = attributes + '&qtyperunit=' + qtyperunit + '&feeperunit=' + feeperunit + '&invoicevalue_intermed=' + invoicevalue_intermed + '&invoicevalue_local=' + invoicevalue_local + '&invoicevalue_local_RIC=' + invoicevalue_local_RIC + '&local_netMargin=' + local_netMargin;
        attributes = attributes + '&sellingpriceqty_product=' + sellingpriceqty_product + '&totalcommision=' + totalcommision + '&totalamount=' + totalamount + '&defaultcomm=' + comm;
        attributes = attributes + "&ptid=" + $('select[id=purchasetype]').val() + '&localinvoicevalue_usd=' + localinvoicevalue_usd;
        attributes = attributes + "&InterBR=" + $('input[id=parmsfornetmargin_intermedBankInterestRate]').val();
        attributes = attributes + "&POIintermed=" + $('input[id=parmsfornetmargin_intermedPeriodOfInterest]').val();
        attributes = attributes + "&intermedAff=" + $("select[id='partiesinfo_intermed_aff']").val();
        attributes = attributes + "&customer=" + $("input[id='allcustomertypes_1_id']").val() + "&commFromIntermed=" + $("input[id='partiesinfo_commFromIntermed']").val() + "&totalfeespaidbyintermed=" + $('input[id=partiesinfo_totalfees]').val();
        attributes = attributes + "&summedqty=" + sum_totalqty + "&summedfees=" + sum_totalfees + "&interestvalue=" + $("input[id='parmsfornetmargin_interestvalue']").val();
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateordersummary' + attributes, function(json) {
            if(json["haveThirdParty"] == 1) {
                $("td[id^='ordersummary_thirdparty']").show();
            } else {
                $("td[id^='ordersummary_thirdparty']").hide();
            }
        });
    });
//---------------------------------------
    $(document).on("click", "input[id='unitfee_btn']", function() {
        var totalfeespaidbyintermed = $('input[id=partiesinfo_totalfees]').val();
        var totalquantity = {};
        var totalqty = 0;
        var totalfee = 0;
        var refernece = 0; //quantity*initialprice
        $("tbody[id^='productline_']").find($("select[id$='_uom']")).each(function() {
            var id = $(this).attr('id').split('_');
            totalqty = parseFloat($("input[id='productline_" + id[1] + "_quantity']").val());
            intialprice = parseFloat($("input[id='productline_" + id[1] + "_intialPrice']").val());
            refernece += (totalqty * intialprice);
            if(!isNaN(totalqty)) {
                totalquantity[$(this).val()] = parseFloat(totalquantity[$(this).val()] || 0) + totalqty;
            }
        });
        var i = 0;
        var feeperunit = qtyperunit = '';
        var qty = {};
        var totalfees2 = {};
        $.each(totalquantity, function(key, value) {
            qtyperunit += key + ":" + value.toFixed(3);
            qtyperunit += "_";
            qty[i] = value;
            i++;
        });
        var qtyperc = 0;
        $("tbody[id^='productline_']").find($("select[id$='_uom']")).each(function() {
            var id = $(this).attr('id').split('_');
            qtyperc = ((parseFloat($("input[id='productline_" + id[1] + "_quantity']").val()) * parseFloat($("input[id='productline_" + id[1] + "_intialPrice']").val())) / refernece) * 100;
            if(i === 1)// if only one product line
            {
                qtyperc = (parseFloat($("input[id='productline_" + id[1] + "_quantity']").val()) / parseFloat(qty[0])) * 100;
            }
            totalfee = ((qtyperc / 100) * totalfeespaidbyintermed).toFixed(3);
            if(!isNaN(totalfee) && $(this).val() != 0) {
                totalfees2[$(this).val()] = parseFloat(totalfees2[$(this).val()] || 0) + parseFloat(totalfee);
            }
            $("input[id='productline_" + id[1] + "_fees']").val(totalfee);
        });
        $.each(totalfees2, function(key, value) {
            feeperunit += key + ":" + value;
            feeperunit += "_";
        });
        attributes = '&exchangeRateToUSD=' + $("#exchangeRateToUSD").val() + '&qtyperunit=' + qtyperunit + '&feeperunit=' + feeperunit;
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=updateunitfee' + attributes, function() {
            $("input[id='ordersummary_unitfee']").trigger("change");
        });
    });
//------------Total Funds Engaged-------
    $(document).on("change", "input[id^='totalfunds_']", function() {
        var totalfunds = 0;
        $("input[id$='_orderShpInvOverdue'],input[id$='_orderShpInvNotDue'],input[id$='_ordersAppAwaitingShp'],input[id$='_odersWaitingApproval']").each(function() {
            if(!jQuery.isEmptyObject(this.value)) {
                totalfunds += parseFloat(this.value);
            }
        });
        $("input[id='totalfunds_total']").val(totalfunds);
    });
//--------------------------------------------------
    $(document).on("change", "input[id='user_0_id_output']", function() {
        var bmid = $("input[id='user_0_id']").val();
        if(typeof bmid != 'undefined' && bmid.length > 0) {
            var aroBusinessManager = $("input[id='user_0_id']").val();
            if(aroBusinessManager.length > 0) {
                var ptid = $("select[id='purchasetype']").val();
                var affid = $("select[id='affid']").val();
                var intermedAff = $("select[id='partiesinfo_intermed_aff']").val();
                $.ajax({type: 'post',
                    url: rootdir + "index.php?module=aro/managearodouments&action=generateapprovalchain",
                    data: "affid=" + affid + "&ptid=" + ptid + "&aroBusinessManager=" + aroBusinessManager + '&intermedAff=' + intermedAff,
                    beforeSend: function() {
                    },
                    complete: function() {
                        //   $("#modal-loading").dialog("close").remove();
                    },
                    success: function(returnedData) {
                        $('#aro_approvalcain').html(returnedData);
                    }
                });
            }
        }
    });
//---------------------------------------------------------------
    $(document).on("keyup", "input[id='partiesinfo_commission']", function() {
        $("input[id='partiesinfo_defaultcommission']").val($("input[id='partiesinfo_commission']").val());
    });
//--------------------------------------------------------------
    $(document).on("click", "a[id='ordersummary_seemore']", function() {
        if($(this).text() == 'See More') {
            $(this).text('See Less');
            $("tfoot[id='ordersummary_tfoot']").show();
        } else {
            $(this).text('See More');
            $("tfoot[id='ordersummary_tfoot']").hide();
        }
    });
//--------------------------------------------------------------

    $(document).on("click", "a[id='deletaro']", function() {
    });
});
var rowid = '';
function addactualpurchaserow(id, callback) {
    if(rowid == '') {
        rowid = $("input[id^='numrows_actualpurchaserow_']").val();
    }
    $("input[id^='numrows_actualpurchaserow']").attr("value", id);
    $("input[id^='numrows_currentstockrow']").attr("value", id);
    sharedFunctions.ajaxAddMore($("img[id='ajaxaddmore_aro/managearodouments_actualpurchaserow_" + rowid + "']"), function() {
        sharedFunctions.ajaxAddMore($("img[id='ajaxaddmore_aro/managearodouments_currentstockrow_" + rowid + "']"), callback);
    });
    return true;
}

function addactualpurchaselines(id) {
    var fields = '';
    var operation = 'create';
    $("tbody[id^='productline_']").find($("input[id^='productline_" + id + "'],select[id^='productline_" + id + "']")).each(function() {
        var field = $(this).attr('id').split('_');
        if(field[2] == 'netMargin' || field[2] == 'netMarginPerc' || field[2] == 'grossMarginAtRiskRatio' || field[2] == 'totalBuyingValue' || field[2] == 'psid') {
            return true;
        }
        if($(this).val() == null) {
            fields = '';
            if($("#modal-loading2").dialog("isOpen")) {
                $("#modal-loading2").dialog("close");
            }
            return false;
        }
        if($(this).val().length == 0) {
            fields = '';
            if($("#modal-loading2").dialog("isOpen")) {
                $("#modal-loading2").dialog("close");
            }
            return false;
        }
        if(!(($(this).val().length == 0) || ($(this).val() == null))) {//&& field[2] != 'totalBuyingValue'
            var value = $(this).val();
            if(field[2] === 'inputChecksum') {
                if($("input[id='actualpurchase_" + field[1] + "_inputChecksum']").length) {
                    if($("input[id='actualpurchase_" + field[1] + "_inputChecksum']").val() == value) {
                        operation = 'update';
                    }
                }
            }
            fields = fields + "&" + field[2] + "=" + value;
        }
    });
    if(fields != '') {
        fields = fields + "&productName=" + $("input[id$='product_noexception_" + id + "_autocomplete']").val() + "&pid=" + $("input[id$='product_noexception_" + id + "_id_output']").val();
        fields = fields + "&ptid=" + $('select[id=purchasetype]').val();
        fields = fields + "&transitTime=" + $('input[id=partiesinfo_transitTime]').val();
        fields = fields + "&clearanceTime=" + $('input[id=partiesinfo_clearanceTime]').val();
        fields = fields + "&dateOfStockEntry=" + $('input[id=pickDate_estDateOfShipment]').val();
        //       var triggered = $("input[id^='productline_" + id + "_isTriggered']").val();
//        if(triggered == 1) {
//            operation == 'update';
//        }


        attrs = '&intialPrice=' + $("input[id='productline_" + id + "_intialPrice']").val() + '&quantity=' + $("input[id='productline_" + id + "_quantity']").val();
        attrs += "&commission=" + $('input[id=partiesinfo_commission]').val();
        attrs += '&unitfees=' + $("input[id='ordersummary_unitfee']").val();
        attrs += '&rowid=' + id + '&ptid=' + $('select[id=purchasetype]').val();

        if(operation == 'update') {
            //var bvalue = $("input[id^='productline_" + id + "_totalBuyingValue']").val();
            // if(bvalue.length > 0) {
            sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateaffbuyingprice' + attrs, function() {
                var bvalue = $("input[id^='productline_" + id + "_totalBuyingValue']").val();
                if(bvalue.length > 0) {
                    fields = fields + "&totalBuyingValue=" + bvalue;
                }
                // fields = fields + "&totalBuyingValue=" + bvalue;
                //  }
                if($("input[id^='actualpurchase_" + id + "_inputChecksum']").length) {
                    sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateactualpurchaserow&rowid=' + id + '&fields=' + fields);
                }
                if($("input[id^='currentstock_" + id + "_inputChecksum']").length) {
                    sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populatecurrentstockrow&rowid=' + id + '&fields=' + fields, function(json) {
                        $("input[id^='pickDate_sale_" + id + "']").trigger('change');
                        if($("#modal-loading2").dialog("isOpen")) {
                            $("#modal-loading2").dialog("close");
                        }
                    });
                }
            });
        }
        else if(operation == 'create') {
            addactualpurchaserow(id, function() {
                sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateaffbuyingprice' + attrs, function() {
                    var bvalue = $("input[id^='productline_" + id + "_totalBuyingValue']").val();
                    if(bvalue.length > 0) {
                        fields = fields + "&totalBuyingValue=" + bvalue;
                    }
                    sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateactualpurchaserow&rowid=' + id + '&fields=' + fields);
                    sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populatecurrentstockrow&rowid=' + id + '&fields=' + fields, function(json) {
                        $("input[id^='pickDate_sale_" + id + "']").trigger('change');
                        if($("#modal-loading2").dialog("isOpen")) {
                            $("#modal-loading2").dialog("close");
                        }
                    });
                    //$("input[id^='productline_" + id + "_isTriggered']").val(1);

                });
            });
        }
    }
}

//function triggerproductlines(id) {
//    $("tbody[id^='productline_0']").find($("input[id$='_intialPrice']")).each(function() {
//        if(id != $(this).attr('id')) {
//            var field = $(this).attr('id').split('_');
//            addactualpurchaselines(field[1]);
//        }
//    });
//}


function getUrlParameter(sParam)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for(var i = 0; i < sURLVariables.length; i++)
    {
        var sParameterName = sURLVariables[i].split('=');
        if(sParameterName[0] == sParam)
        {
            return sParameterName[1];
        }
    }
}