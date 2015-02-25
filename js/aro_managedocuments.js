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

    $("#affid,#purchasetype").live('change', function() {
        if(sharedFunctions.checkSession() == false) {
            return;
        }
        $(this).data('affid', $('select[id=affid]').val());
        var affid = $(this).data('affid')
        $(this).data('purchasetype', $('select[id=purchasetype]').val());
        var ptid = $(this).data('purchasetype')
        if(ptid !== '' && ptid != typeof undefined) {
            sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populatedocnum&affid= ' + affid + '&ptid= ' + ptid);
        }

    });
    $('select[id=affid]').live('change', function() {
        $('select[id=purchasetype]').trigger('change');
        var affid = $(this).val();
        $.ajax({type: 'post',
            url: rootdir + "index.php?module=aro/managearodouments&action=getwarehouses",
            data: "affid=" + affid,
            beforeSend: function() {
                $("body").append("<div id='modal-loading'></div>");
            },
            complete: function() {
                $("#modal-loading").dialog("close").remove();
            },
            success: function(returnedData) {
                $('#warehouse_list_td').html(returnedData);
            }
        });
    });


    $("#currencies").live('change', function() {
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=getexchangerate&currency=' + $(this).val());
    });
    $("select[id^='paymentermdays_']").live('change', function() {
        var id = $(this).attr('id').split('_');
        var avgesdateofsale = '11-02-2015';
        var parentContainer = $(this).closest('div');
        var paymentdays = [];
        parentContainer.children('table').find('tr').each(function() {
            /*check if the customer is selected */
            if($(this).find("input[id^='customer_']").val() != '') {
                $(this).find('select').each(function() {
                    if($(this).val() != '') {
                        paymentdays.push($(this).val());
                    }
                });
            }
        });

        alert(paymentdays);
        var purchasetype = $("input[id^='cpurchasetype']").val();

        sharedFunctions.populateForm('perform_aro/managearodouments_Form', 'http://127.0.0.1/ocos/index.php?module=aro/managearodouments&action=getestimatedate&avgesdateofsale= ' + avgesdateofsale + '&paymentermdays[]= ' + paymentdays + '&ptid= ' + purchasetype);
    });

    $("input[id$='_quantity'],input[id$='_daysInStock']").live('change keyup', function() {
        var id = $(this).attr('id').split("_");
        $("input[id='productline_" + id[1] + "_qtyPotentiallySold']").removeAttr("disabled");
        $("input[id='productline_" + id[1] + "_daysInStock']").removeAttr("disabled");
        var ptid = $("#purchasetype").val();
        var daysInStock = $("input[id='productline_" + id[1] + "_daysInStock']").val();
        var fields = 'rowid=' + id[1] + '&ptid=' + ptid;
        if(daysInStock !== '' && daysInStock != typeof undefined) {
            fields = fields + '&daysInStock=' + daysInStock;
        }
        if(sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=disablefields&' + fields)) {
            var fields = ["daysInStock", "qtyPotentiallySold"];
            alert($("input[id='productline_" + id[1] + "_" + fields[i] + "_disabled']").val());
            for(var i = 0; i < fields.length; i++) {
                if($("input[id='productline_" + id[1] + "_" + fields[i] + "_disabled']").val() == 0) {
                    $("input[id='productline_" + id[1] + fields[i] + "']").val('0');
                    $("input[id='productline_" + id[1] + "_" + fields[i] + "']").attr("disabled", "true");
                }
                else {
                    $("input[id='productline_" + id[1] + "_" + fields[i] + "']").removeAttr("disabled");
                }
            }
        }
    });

    $("input[id^='productline_']").live('change', function() {
        var id = $(this).attr('id').split("_");
        var fields_array = ["quantity", "qtyPotentiallySold", "intialPrice", "costPrice", "sellingPrice", "daysInStock"];
        var fields = '';
        $.each(fields_array, function(index, value) {
            fields += '&' + value + '=' + $("input[id='productline_" + id[1] + "_" + value + "']").val();
        });
        var ptid = $("#purchasetype").val();
        var exchangeRateToUSD = $("#exchangeRateToUSD").val();
        fields += '&ptid=' + ptid + '&exchangeRateToUSD=' + exchangeRateToUSD;
        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateproductlinefields&rowid=' + id[1] + fields);
    });


    //  $("input[id$='_daysInStock']").live('change keyup', function() {
    //     var id = $(this).attr('id').split("_");
    //     $("input[id='productline_" + id[1] + "_qtyPotentiallySold']").removeAttr("disabled");
    //     if($(this).val() == 0) {
    //         $("input[id='productline_" + id[1] + "_qtyPotentiallySold']").attr("disabled", "true");
    //      }
    // });

    $("#parmsfornetmargin_warehouse").live('change', function() {
        var warehouse = $(this).val();
        var ptid = $("#purchasetype").val();
        if(warehouse !== '' && warehouse != typeof undefined) {
            sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populatewarehousepolicy&warehouse= ' + warehouse + '&ptid=' + ptid);
        }
    });


    $("#orderreference").live('change', function() {
        $('select[id=affid]').trigger('change');
        $('select[id=parmsfornetmargin_warehouse]').trigger('change');


    });
});
