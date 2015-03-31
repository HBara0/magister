/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: aro_managedocuments.js
 * Created:        @tony.assaad    Feb 19, 2015 | 9:59:17 AM
 * Last Update:    @tony.assaad    Feb 19, 2015 | 9:59:17 AM
 */
$(function() {
    
    $('.accordion .header').accordion({collapsible: true});
    $('.accordion .header').click(function() {
        $(this).next().toggle();
        return false;
    }).next().hide();
    //  var json2 = "{'orderreference':'Dave Stewart'}";
    //  var json2 = eval("(" + json2 + ");"); /* convert the json to object */
    // var form = document.forms['perform_aro/managearodouments_Form'];
    // $(form).populate(json2, {debug: 0})
    //
    //--------------------------------------------------------------
    //
    var url= window.location.href;   
    var url = url.split('&');
    if(typeof url[2] !== 'undefined'){
    var referrer=url[2].split("=");
    if(referrer[1]=='toapprove'){
    $("form[id='perform_aro/managearodouments_Form'] :input:not([id^='approve_aro'])").attr("disabled", true);
    }}
    if(typeof url[1] !== 'undefined'){
    var id=url[1].split("=");
        $.ajax({type: 'post',
                url: rootdir + "index.php?module=aro/managearodouments&action=viewonly",
                data: "id=" + id[1],
                beforeSend: function() {
                },
                complete: function() {
                 //   $("#modal-loading").dialog("close").remove();
                },
                success: function(returnedData) {
                     var json = eval("(" + returnedData + ");");
                        if(json['disable'] ==1){
                            $("form[id='perform_aro/managearodouments_Form'] :input:not([id^='approve_aro'])").attr("disabled", true);
                    }
                }
            });
            }
    /////-----------------------------------------------------------
//
    $("select[id$='purchasetype'],select[id$='affid']").live('change', function() {
        if(sharedFunctions.checkSession() == false) {
            return;
        }
        $(this).data('affid', $('select[id=affid]').val());
        var affid = $(this).data('affid');
        $(this).data('purchasetype', $('select[id=purchasetype]').val());
        var ptid = $(this).data('purchasetype');
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populatedocnum&affid= ' + affid + '&ptid= ' + ptid);
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateaffpolicy&affid= ' + affid + '&ptid= ' + ptid);


    $.ajax({type: 'post',
                url: rootdir + "index.php?module=aro/managearodouments&action=generateapprovalchain",
                data: "affid=" + affid+ "&ptid=" + ptid,
                beforeSend: function() {
//                    $("body").append("<div id='modal-loading'></div>");
//                    $("#modal-loading").dialog({height: 150, modal: true, closeOnEscape: false, title: 'Loading...', resizable: false, minHeight: 0
//                    });
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
                jQuery.each(obj, function(i, val) {
                      var id = val.split(" ");
             $("select[id^='" + i + "'] option[value='"+id[0]+"']").attr("selected", "true");
           $("select[id^='" + i + "']").trigger("change");
                });
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
                }
            });
            $("#parmsfornetmargin_warehouse").trigger("change");
        }
        if($(this).attr('id') === 'purchasetype') {
            /*Disable days in Stock, QPS and warehousing section according to seleced purchasetype*/
            /*trigger productline fields*/
            var ptid = $(this).val();
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
                var warehousing_fields = ["warehouse", "warehousingRate", "warehousingPeriod", "warehousingTotalLoad", "uom"];
                for(var i = 0; i < warehousing_fields.length; i++) {
                    if($("input[id='parmsfornetmargin_warehousing_disabled']").val() == 0) {
                        $("input[id='parmsfornetmargin_" + warehousing_fields[i] + "']").attr('value', '0');
                        $("input[id='parmsfornetmargin_" + warehousing_fields[i] + "']").attr("readonly", "true");
                        $("select[id ='parmsfornetmargin_" + warehousing_fields[i] + "']").append('<option value="0" selected></option>');
                        $("select[id ='parmsfornetmargin_" + warehousing_fields[i] + "']").attr("disabled", "true");
                    }
                    else {
                        $("input[id='parmsfornetmargin_" + warehousing_fields[i] + "']").removeAttr("readonly");
                        $("input[id='parmsfornetmargin_" + warehousing_fields[i] + "'],select[id ='parmsfornetmargin_" + warehousing_fields[i] + "']").removeAttr("disabled");
                    }
                }
            });
            $("select[id='partiesinfo_intermed_aff']").trigger("change");
        }
        /* Loop over all product lines to update the numbers based on the new policy */
        $("tbody[id^='productline_']").find($("select[id$='_quatity']")).each(function() {
            var id = $(this).attr('id').split('_');
            if($("input[id='product_noexception_" + id[1] + "_id_output']").val().length > 0) {
                $(this).trigger("change");
            }
        });
        $("input[id='parmsfornetmargin_localBankInterestRate']").trigger("change");
    });
    //-----------------Get Exchang Rate  ------------------------//
    $("#currencies").live('change', function() {
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=getexchangerate&currency=' + $(this).val());
        $("input[id='exchangeRateToUSD']").attr("readonly", "true");
        var exchrate = setTimeout(function() {
            if($("input[id='exchangeRateToUSD_disabled']").val() == 0){
               $("input[id='exchangeRateToUSD']").removeAttr("readonly");
            }
        }, 2000);    
    });
    //----------------------------------------------------------//

    //-----------------Populate intermediary affiliate policy------------------------------//
    $("select[id='partiesinfo_intermed_aff']").live('change', function() {
        var ptid = $("select[id='purchasetype']").val();
        var intermedAff = $("select[id='partiesinfo_intermed_aff']").val();
        var estimatedImtermedPayment = $("input[id='pickDate_intermed_estdateofpayment']").val();
        var estimatedManufacturerPayment = $("input[id='pickDate_vendor_estdateofpayment']").val();
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateintermedaffpolicy&ptid= ' + ptid + '&intermedAff=' + intermedAff + '&estimatedImtermedPayment=' + estimatedImtermedPayment + '&estimatedManufacturerPayment=' + estimatedManufacturerPayment);
        var triggercomm = setTimeout(function() {
            $("input[id='partiesinfo_commission']").trigger("change");
        }, 2000);
    });
    //-------------------------------------------------------------------------------------//

    //-----------------------------------------------------------------------------------
    $("select[id^='paymentermdays_']").live('change', function() {
        var id = $(this).attr('id').split('_');
        var parentContainer = $(this).closest('div');
        var paymentdays = [];
        var salesdates=[];
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
                    if($(this).val() !== '') {
                        salesdates.push($(this).val());
                    }
                });
        var purchasetype = $("input[id^='cpurchasetype']").val();
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', 'http://127.0.0.1/ocos/index.php?module=aro/managearodouments&action=getestimatedate&paymentermdays[]= ' + paymentdays + '&ptid= ' + purchasetype +'&salesdates[]='+salesdates);
    });
    $(window).load(function() {
        $("select[id^='paymentermdays_']").trigger("change");
    }); //Trigger payment terms days on modify
    //-----------------------------------------------------------------------------------

    var fields_array = ["quantity", "qtyPotentiallySold", "intialPrice", "costPrice", "sellingPrice", "daysInStock"];
    $("input[id^='productline_'],select[id$='packing']").live('change', function() {
        var id = $(this).attr('id').split("_");
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
        parmsfornetmargin += '&warehousingRate=' + $("select[id='parmsfornetmargin_warehousingRate']").val();
        parmsfornetmargin += "&commission=" + $('input[id=partiesinfo_commission]').val();
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        var totalquantity = {};
        var totalqty = 0;
        var refernece = 0;//quantity*initialprice
        $("tbody[id^='productline_']").find($("select[id$='_uom']")).each(function() {
            var id = $(this).attr('id').split('_');
            totalqty = parseFloat($("input[id='productline_" + id[1] + "_quantity']").val());
            intialprice = parseFloat($("input[id='productline_" + id[1] + "_intialPrice']").val());
            refernece += (totalqty * intialprice);
            totalquantity[$(this).val()] = parseFloat(totalquantity[$(this).val()] || 0) + totalqty; //Fill array of qty per uom
        });
        //alert(totalquantity[4]);
        var i = 0;
        var qty =totalqtyperuom={};
        var qtyperunit = '';
        $.each(totalquantity, function(key, value) {
            if(i !== 0) {
                qtyperunit += "_";
            }
            qty[i] = value;
            totalqtyperuom[key]=value;
            qtyperunit += key + ":" + value;
            i++;
        });
        var totalfees = $('input[id=partiesinfo_totalfees]').val();
        var qtyperc = ((parseFloat($("input[id='productline_" + id[1] + "_quantity']").val()) * parseFloat($("input[id='productline_" + id[1] + "_intialPrice']").val())) / refernece) * 100;
        if(i === 1)// if only one product line
        {var qtyperc = (parseFloat($("input[id='productline_" + id[1] + "_quantity']").val()) / parseFloat(qty[0])) * 100;}
        fees = ((qtyperc / 100) * totalfees).toFixed(3);
        var unitfees=$("input[id='ordersummary_unitfee']").val();
        var totalQtyPerUom=totalqtyperuom[$("select[id$='"+id[1]+"_uom']").val()];
        parmsfornetmargin += "&fees=" + fees+'&unitfees='+unitfees+"&totalQty="+totalQtyPerUom+"&riskRatio=" +$("input[id='parmsfornetmargin_localRiskRatio']").val();
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateproductlinefields&rowid=' + id[1] + fields + '&parmsfornetmargin=' + parmsfornetmargin);
    });
    $("input[id^='productline_']").live('blur', function() {
        var id = $(this).attr('id').split("_");
        triggerproductlines(id);
        addactualpurchaselines(id[1]);
        $("input[id='ordersummary_btn']").trigger("click");
    });
    $("select[id^='productline_'][id$='_uom']").live('change', function() {
        var id = $(this).attr('id').split("_");
        triggerproductlines(id);
        $("input[id='ordersummary_btn']").trigger("click");

    });
//    /*-------------Disable qtyPotentiallySold if daysInStock=0 ------------------*/
    $("input[id$='_daysInStock']").live('change keyup', function() {
        var id = $(this).attr('id').split("_");
        $("input[id='productline_" + id[1] + "_qtyPotentiallySold']").removeAttr("readonly");
        if($(this).val() == 0) {
            $("input[id='productline_" + id[1] + "_qtyPotentiallySold']").attr('value', '0');
            $("input[id='productline_" + id[1] + "_qtyPotentiallySold']").attr("readonly", "true");
        }
    });
    //---------------------------------------------------------------------------//

    //-------------------Get Warehouse policy parms------------------------------//
    $("#parmsfornetmargin_warehouse").live('change', function() {
        var warehouse = $(this).val();
        var ptid = $("#purchasetype").val();
        if(warehouse !== '' && warehouse !== typeof undefined) {
            $.getJSON(rootdir + 'index.php?module=aro/managearodouments&action=populatewarehousepolicy&warehouse= ' + warehouse + '&ptid=' + ptid, function(data) {
                var jsonStr = JSON.stringify(data);
                obj = JSON.parse(jsonStr);
                jQuery.each(obj, function(i, val) {
                    if(i === 'parmsfornetmargin_warehousingRate') {
                        var id = val.split(" ");
                        $("select[id^='" + i + "']").empty().append("<option value='" + id[0] + "' selected>" + val + "</option>");
                    }
                    else {
                        $("input[id^='" + i + "']").val(val);
                    }
                });
            });
        }
    });
    //---------------------------------------------------------------------------//

    //------Form Submitting after 30 seconds--------------//
//    var auto_refresh = setInterval(function() {
//        submitform();
//    }, 30000);
//    function submitform() {     //Form submit function
//        $("input[id^='perform_'][id$='_Button']").trigger("click");
//    }
//---------------------------------------------------//

//-------------If Vendor is affiliate, such select affiliate not entity and Disable  intermediary section----------------------//
//Trigger Intermediary Aff Policy
    $("input[id='vendor_isaffiliate']").change(function() {
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

    // If Inco terms are different between intermediary and vendor, freight is mandatory
    $("select[id='partiesinfo_intermed_incoterms'],select[id='partiesinfo_vendor_incoterms']").live('change', function() {
        $("input[id='partiesinfo_freight']").removeAttr("required");
        if($("select[id='partiesinfo_intermed_incoterms']").val() !== '' || $("select[id='partiesinfo_vendor_incoterms']").val() !== '') {
            if($("select[id='partiesinfo_intermed_incoterms']").val() !== $("select[id='partiesinfo_vendor_incoterms']").val()) {
                $("input[id='partiesinfo_freight']").attr("required", "true");
            }
        }
    });
    //----------------------------------------------------------------------------------------------------------------------------//

    $("input[id='parmsfornetmargin_localBankInterestRate'],input[id='parmsfornetmargin_localPeriodOfInterest']").live('change', function() {
        var localBankInterestRate = $("input[id='parmsfornetmargin_localBankInterestRate']").val();
        var localPeriodOfInterest = $("input[id='parmsfornetmargin_localPeriodOfInterest']").val();
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=getinterestvalue&localBankInterestRate=' + localBankInterestRate + '&localPeriodOfInterest=' + localPeriodOfInterest);

        var updatetotalfees = setTimeout(function() {
            $("input[id$='freight']").trigger("change");
        }, 2000);
    });
    //------------------------------------------------

    //--------------Populate dates of PartiepickDate_estDateOfShipments Information----------------------------//
    $("input[id='pickDate_estDateOfShipment'],select[id='partiesinfo_intermed_paymentterm'],select[id='partiesinfo_vendor_paymentterm'],input[id='partiesinfo_intermed_ptAcceptableMargin']").live('change', function() {
        var estDateOfShipment = $("input[id='pickDate_estDateOfShipment']").val();
        var ptAcceptableMargin = $("input[id='partiesinfo_intermed_ptAcceptableMargin']").val();
        var intermedPaymentTerm = $("select[id = 'partiesinfo_intermed_paymentterm']").val();
        var vendorPaymentTerm = $("select[id ='partiesinfo_vendor_paymentterm']").val();
        var ptid = $('select[id=purchasetype]').val();
        var est_local_pay=$("input[id='avgeliduedate']").val();
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populatepartiesinfofields&intermedPaymentTerm=' + intermedPaymentTerm + '&vendorPaymentTerm=' + vendorPaymentTerm + '&estDateOfShipment=' + estDateOfShipment + '&ptAcceptableMargin=' + ptAcceptableMargin + '&ptid=' + ptid +'&est_local_pay=' +est_local_pay);
        $("select[id='partiesinfo_intermed_aff']").trigger("change");
    });
    //----------------------------------------------------------------------------------------------------------------------------//

    //--------------Calculate of Aff Buying Price (on change of parties Infrmation commission of Fees ----------------------------//
    $("input[id$='freight'],input[id$='bankFees'],input[id$='insurance'],input[id$='legalization'],input[id$='courier'],input[id$='otherFees']").bind('change', function() {
        var total = 0;
        $("input[id$='freight'],input[id$='bankFees'],input[id$='insurance'],input[id$='legalization'],input[id$='courier'],input[id$='otherFees']").each(function() {
            if(!jQuery.isEmptyObject(this.value)) {
                total += parseFloat(this.value);
            }
        });
        var interestvalue = $("input[id='parmsfornetmargin_interestvalue']").val();
        if(interestvalue.length > 0) {
            total += parseFloat(interestvalue);
        }
        $("input[id='partiesinfo_totalfees']").val(total);
        $("input[id$='_intialPrice']").trigger("change");
    });
    //-----------------------------------------------------------------------------------------------------------------------------///
    $("input[id='partiesinfo_commission']").live('change', function() {
        var totalQty = 5000;
        var commission = ($("input[id='partiesinfo_commission']").val() / 100) * totalQty;
        if((commission != 0 || commission != '') && commission < 250) {
            var commpercentage = (250 * 100) / totalQty;
            $("input[id='partiesinfo_commission']").val(commpercentage);
        }
        $("input[id$='_intialPrice']").trigger("change");
    });
    //----------------------------------------------------------------------------------------------------------------------------///
    //note need to check triggers
    //needs optimization (loop through array for fields
    $("input[id='ordersummary_btn']").click(function() {
        var totalfees = $('input[id=partiesinfo_totalfees]').val();
        var exchangeRateToUSD = $("#exchangeRateToUSD").val();
        var aff = $('select[id=affid]').val();
        var intermedAff = $("select[id='partiesinfo_intermed_aff']").val();
        attributes = '&exchangeRateToUSD=' + exchangeRateToUSD + '&intermedAff=' + intermedAff + '&aff=' + aff;
        var totalquantity = {};
        var totalfees = {};
        var totalqty = 0;
        var totalfee = 0;
        var invoicevalue_local = invoicevalue_local_RIC = invoicevalue_intermed = sellingpriceqty_product = local_netMargin = 0;
        $("tbody[id^='productline_']").find($("select[id$='_uom']")).each(function() {
            var id = $(this).attr('id').split('_');
            totalqty = parseFloat($("input[id='productline_" + id[1] + "_quantity']").val());
            if(!isNaN(totalqty)) {
                totalquantity[$(this).val()] = parseFloat(totalquantity[$(this).val()] || 0) + totalqty;
            }
            totalfee = parseFloat($("input[id='productline_" + id[1] + "_fees']").val());
            if(!isNaN(totalfee)) {
                totalfees[$(this).val()] = parseFloat(totalfees[$(this).val()] || 0) + totalfee;
            }
            var intialprice = parseFloat($("input[id='productline_" + id[1] + "_intialPrice']").val());
            if(!isNaN(totalfee) && !isNaN(intialprice)) {
                invoicevalue_intermed += (totalqty * intialprice);
            }
            invoicevalue_parameter = (parseFloat($("input[id='productline_" + id[1] + "_totalBuyingValue']").val()) * (parseFloat($("input[id='productline_" + id[1] + "_sellingPrice']").val())));
            if(!isNaN(invoicevalue_parameter)) {
                invoicevalue_local += invoicevalue_parameter;
                if(!isNaN(totalqty)) {
                    invoicevalue_local_RIC += (totalqty * invoicevalue_parameter);
                }
            }
            if(!isNaN(parseFloat($("input[id='productline_" + id[1] + "_netMargin']").val()))) {
                local_netMargin += parseFloat($("input[id='productline_" + id[1] + "_netMargin']").val());
            }
            if(!isNaN((parseFloat($("input[id='productline_" + id[1] + "_sellingPrice']").val()) * totalqty))) {
                sellingpriceqty_product += (parseFloat($("input[id='productline_" + id[1] + "_sellingPrice']").val()) * totalqty);
            }
        });
        var i = 0;
        var qtyperunit = '';
        $.each(totalquantity, function(key, value) {
            if(i !== 0) {
                qtyperunit += "_";
            }
            qtyperunit += key + ":" + value.toFixed(3);
            i++;
        });
        var j = 0;
        var feeperunit = '';
        $.each(totalfees, function(key, value) {
            if(j !== 0) {
                feeperunit += "_";
            }
            feeperunit += key + ":" + value.toFixed(3);
            j++;
        });


        attributes = attributes + '&qtyperunit=' + qtyperunit + '&feeperunit=' + feeperunit + '&invoicevalue_intermed=' + invoicevalue_intermed + '&invoicevalue_local=' + invoicevalue_local + '&invoicevalue_local_RIC=' + invoicevalue_local_RIC.toFixed(3) + '&local_netMargin=' + local_netMargin.toFixed(3);
        attributes = attributes + '&sellingpriceqty_product=' + sellingpriceqty_product.toFixed(3);
        // Note check if not NaN

        //['purchasetype','parmsfornetmargin_intermedBankInterestRate','parmsfornetmargin_intermedPeriodOfInterest'
        attributes = attributes + "&ptid=" + $('select[id=purchasetype]').val();
        attributes = attributes + "&InterBR=" + $('input[id=parmsfornetmargin_intermedBankInterestRate]').val();
        attributes = attributes + "&POIintermed=" + $('input[id=parmsfornetmargin_intermedPeriodOfInterest]').val();
        attributes = attributes + "&intermedAff=" + $("select[id='partiesinfo_intermed_aff']").val();
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateordersummary' + attributes);
    });
    //---------------------------------------
    
    
    //------------Total Funds Engaged-------
    $("input[id^='totalfunds_']").live('change', function() {
        var totalfunds=0;
       $("input[id$='_orderShpInvOverdue'],input[id$='_orderShpInvNotDue'],input[id$='_ordersAppAwaitingShp'],input[id$='_odersWaitingApproval']").each(function() {
            if(!jQuery.isEmptyObject(this.value)) {
                totalfunds += parseFloat(this.value);
            }
        });
         $("input[id='totalfunds_total']").val(totalfunds);
        });
    //-------------------------------
});
var rowid = '';
function addactualpurchaserow(id) {
    if(rowid == '') {
        rowid = $("input[id^='numrows_actualpurchaserow_']").val();
    }

    $("input[id^='numrows_actualpurchaserow']").attr("value", id);
    $("input[id^='numrows_currentstockrow']").attr("value", id);
    $("img[id='ajaxaddmore_aro/managearodouments_actualpurchaserow_" + rowid + "']").trigger("click");
    
             var a= setTimeout(function() {
                        $("img[id='ajaxaddmore_aro/managearodouments_currentstockrow_" + rowid + "']").trigger("click");
  }, 2000);
    return true;
}

function addactualpurchaselines(id) {
    var fields = '';
    var operation = 'create';
    $("tbody[id^='productline_']").find($("input[id^='productline_" + id + "'],select[id^='productline_" + id + "']")).each(function() {
        var field = $(this).attr('id').split('_');
        if(field[2] == 'netMargin' || field[2] == 'netMarginPerc' || field[2] == 'grossMarginAtRiskRatio') {
            return true;
        }
        if((($(this).val().length == 0) || ($(this).val() == null))) {
            fields = '';
            return false;
        }
        if(!(($(this).val().length == 0) || ($(this).val() == null))) {
            var value = $(this).val();
            if(field[2] === 'inputChecksum') {
                if($("input[id='actualpurchase_" + field[1] + "_inputChecksum']").val() == value) {
                    operation = 'update';
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
        if(operation == 'update') {
            sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateactualpurchaserow&rowid=' + id + '&fields=' + fields);
            sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populatecurrentstockrow&rowid=' + id + '&fields=' + fields);
        } else if(operation == 'create') {
            if(addactualpurchaserow(id) == true) {
                var x = setTimeout(function() {
                    sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateactualpurchaserow&rowid=' + id + '&fields=' + fields);
                    sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populatecurrentstockrow&rowid=' + id + '&fields=' + fields);
                }, 3000);
            }
        }
    }
}

function triggerproductlines(id) {
    var fields_array = ["quantity", "qtyPotentiallySold", "intialPrice", "costPrice", "sellingPrice", "daysInStock", "uom"];
    if($.inArray(id[id.length - 1 ], fields_array) != -1) {
        $("tbody[id^='productline_1']").find($("input[id$='_quantity']")).each(function() {
            if(id.join('_') !== $(this).attr('id')) {
                $(this).trigger("change");
            }
        });
    }
}