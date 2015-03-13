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
                    }
                    else {
                        $("input[id$='" + fields[i] + "']").removeAttr("readonly");
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
            var plfields = ["quantity"];
            //, "qtyPotentiallySold", "intialPrice", "costPrice", "sellingPrice", "daysInStock"];
            for(var i = 0; i < plfields.length; i++) {
                $("input[id^='productline_'][id$='_" + plfields[i] + "']").trigger('change');
            }
            $("select[id='partiesinfo_intermed_aff']").trigger("change");
        }

    });
    //-----------------Get Exchang Rate  ------------------------//
    $("#currencies").live('change', function() {
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=getexchangerate&currency=' + $(this).val());
    });
    //----------------------------------------------------------//

    //-----------------Populate intermediary affiliate policy------------------------------//
    $("select[id='partiesinfo_intermed_aff']").live('change', function() {
        var ptid = $("select[id='purchasetype']").val();
        var intermedAff = $("select[id='partiesinfo_intermed_aff']").val();
        var estimatedImtermedPayment = $("input[id='pickDate_intermed_estdateofpayment']").val();
        var estimatedManufacturerPayment = $("input[id='pickDate_vendor_estdateofpayment']").val();

        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateintermedaffpolicy&ptid= ' + ptid + '&intermedAff=' + intermedAff + '&estimatedImtermedPayment=' + estimatedImtermedPayment + '&estimatedManufacturerPayment=' + estimatedManufacturerPayment);
    });
    //-------------------------------------------------------------------------------------//

    //-----------------------------------------------------------------------------------
    $("select[id^='paymentermdays_']").live('change', function() {
        var id = $(this).attr('id').split('_');
        var avgesdateofsale = '11-02-2015';
        var parentContainer = $(this).closest('div');
        var paymentdays = [];
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
        var purchasetype = $("input[id^='cpurchasetype']").val();
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', 'http://127.0.0.1/ocos/index.php?module=aro/managearodouments&action=getestimatedate&avgesdateofsale= ' + avgesdateofsale + '&paymentermdays[]= ' + paymentdays + '&ptid= ' + purchasetype);
    });
    $(window).load(function() {
        $("select[id^='paymentermdays_']").trigger("change");
    }); //Trigger payment terms days on modify
    //-----------------------------------------------------------------------------------

    $("input[id^='productline_'],select[id$='packing']").live('change', function() {
        var id = $(this).attr('id').split("_");
        var fields_array = ["quantity", "qtyPotentiallySold", "intialPrice", "costPrice", "sellingPrice", "daysInStock"];
        var fields = '';
        $.each(fields_array, function(index, value) {
            fields += '&' + value + '=' + $("input[id='productline_" + id[1] + "_" + value + "']").val();
        });
        var ptid = $("#purchasetype").val();
        var exchangeRateToUSD = $("#exchangeRateToUSD").val();
        fields += '&ptid=' + ptid + '&exchangeRateToUSD=' + exchangeRateToUSD;
        var parmsfornetmargin_fields = new Array('localPeriodOfInterest', 'localBankInterestRate', 'warehousingPeriod', 'warehousingTotalLoad', 'intermedBankInterestRate', 'intermedPeriodOfInterest');
        var parmsfornetmargin = '';
        $.each(parmsfornetmargin_fields, function(index, value) {
            parmsfornetmargin += '&' + value + '=' + $("input[id='parmsfornetmargin_" + value + "']").val();
        });
        parmsfornetmargin += '&warehousingRate=' + $("select[id='parmsfornetmargin_warehousingRate']").val();
        parmsfornetmargin += "&commission=" + $('input[id=partiesinfo_commission]').val();
        parmsfornetmargin += "&fees=" + $('input[id=partiesinfo_totalfees]').val();
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateproductlinefields&rowid=' + id[1] + fields + '&parmsfornetmargin=' + parmsfornetmargin);

        addactualpurchaselines(id[1]);

    });

    /*-------------Disable qtyPotentiallySold if daysInStock=0 ------------------*/
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
    // var auto_refresh = setInterval(function() {
    //      submitform(); }, 30000);
    function submitform() {     //Form submit function
        $("input[id^='perform_'][id$='_Button']").trigger("click");
    }
    //---------------------------------------------------//

    //-------------If Vendor is affiliate, such select affiliate not entity and Disable  intermediary section----------------------//
    //Trigger Intermediary Aff Policy
    $("input[id='vendor_isaffiliate']").change(function() {
        $("td[id='vendor_affiliate']").css("display", "none");
        $("input[id='supplier_1_autocomplete']").attr('value', '');
        $("input[id='supplier_1_id']").attr('value', '');
        $("input[id='supplier_1_autocomplete']").removeAttr("disabled");
        var fields = ["aff", "paymentterm", "incoterms", "IncotermsDesc", "PaymentTermDesc", "ptAcceptableMargin"];
        for(var i = 0; i < fields.length; i++) {
            $("input[id='partiesinfo_intermed_" + fields[i] + "']").removeAttr("disabled");
            $("select[id='partiesinfo_intermed_" + fields[i] + "']").removeAttr("disabled");
            $("input[id='pickDate_intermed_" + fields[i] + "']").removeAttr("disabled");
            $("select[id='partiesinfo_intermed_" + fields[i] + "'] option[value='0']").remove();
        }

        if($(this).is(":checked")) {
            var fields = ["aff", "paymentterm", "incoterms", "IncotermsDesc", "PaymentTermDesc", "ptAcceptableMargin"];
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

    //--------------Populate dates of PartiepickDate_estDateOfShipments Information----------------------------//
    $("input[id='pickDate_estDateOfShipment'],select[id='partiesinfo_intermed_paymentterm'],select[id='partiesinfo_vendor_paymentterm'],input[id='partiesinfo_intermed_ptAcceptableMargin']").live('change', function() {
        var estDateOfShipment = $("input[id='pickDate_estDateOfShipment']").val();
        var ptAcceptableMargin = $("input[id='partiesinfo_intermed_ptAcceptableMargin']").val();
        var intermedPaymentTerm = $("select[id = 'partiesinfo_intermed_paymentterm']").val();
        var vendorPaymentTerm = $("select[id ='partiesinfo_vendor_paymentterm']").val();
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populatepartiesinfofields&intermedPaymentTerm=' + intermedPaymentTerm + '&vendorPaymentTerm=' + vendorPaymentTerm + '&estDateOfShipment=' + estDateOfShipment + '&ptAcceptableMargin=' + ptAcceptableMargin);
        $("select[id='partiesinfo_intermed_aff']").trigger("change");

    });
    //----------------------------------------------------------------------------------------------------------------------------//

    //--------------Calculate of Aff Buying Price (on change of parties Infrmation commission of Fees ----------------------------//
    $("input[id$='freight'],input[id$='bankFees'],input[id$='insurance'],input[id$='legalization'],input[id$='courier'],input[id$='otherFees']").bind('change keyup', function() {
        var total = 0;
        $("input[id$='freight'],input[id$='bankFees'],input[id$='insurance'],input[id$='legalization'],input[id$='courier'],input[id$='otherFees']").each(function() {
            if(!jQuery.isEmptyObject(this.value)) {
                total += parseFloat(this.value);
            }
        });
        $("input[id='partiesinfo_totalfees']").val(total);
        $("input[id$='_intialPrice']").trigger("change");
    });
    $("input[id='partiesinfo_commission']").live('change', function() {
        var totalQty = 5000;
        var commission = ($("input[id='partiesinfo_commission']").val() / 100) * totalQty;
        if(commission < 250) {
            var commpercentage = (250 * 100) / totalQty;
            $("input[id='partiesinfo_commission']").val(commpercentage);
        }
        $("input[id$='_intialPrice']").trigger("change");
    });
    //----------------------------------------------------------------------------------------------------------------------------//

});
var rowid = '';
function addactualpurchaserow() {
    if(rowid == '') {
        rowid = $("input[id^='numrows_actualpurchaserow_']").val();
    }
    $("img[id='ajaxaddmore_aro/managearodouments_actualpurchaserow_" + rowid + "']").trigger("click");
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
        if(operation == 'update') {
            sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateactualpurchaserow&rowid=' + id + '&fields=' + fields);
        } else if(operation == 'create') {
            if(addactualpurchaserow() == true) {
                setTimeout(function() {
                    sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateactualpurchaserow&rowid=' + id + '&fields=' + fields);
                }, 3000);
            }
        }
    }
}
