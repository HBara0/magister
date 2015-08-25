$(function () {
    $(document).on("change", "#type,#pickDate_timeLine,#affid,#supplier_1_id_output,input[id^='customer_'][id$='_output'],select[id^='packingType_'][id$='_output']", getMoreData);
    function getMoreData() {
        if(sharedFunctions.checkSession() == false) {
            return;
        }

        var value = $(this).val();
        var id = $(this).attr('id');
        var vid = $(this).attr("id").split("_");
        var dataParam = '';
        var html = false;

        if(id == "type" || id == "affid" || id == "pickDate_timeLine") {

            dataParam += "&timeLine=" + $("#altpickDate_timeLine").val();
            dataParam += "&type=" + $("#type").val();
            dataParam += "&affid=" + $("#affid").val();
            get = "orderNumber";
            loadingIn = "orderNumber_Loading";
            contentIn = "orderNumber";
            var requires_ajax = true;
            var html = false;
        }
        else
        {
            if(id == "supplier_1_id_output") {
                dataParam += "&spid=" + $("#supplier_1_id_output").val();
                get = "supplierPaymentTermsDays";
                loadingIn = "paymenttermss_Loading";
                contentIn = "supplierPaymentTermsDays";
                var requires_ajax = true;
                $("input[id^='product_'][id$='_autocomplete'],input[id^='customerproduct_'][id$='_autocomplete']").removeAttr("disabled");
                var html = false;
            }

            if(vid[0] == 'customer')
            {
                dataParam += "&eid=" + $("#" + vid[0] + "_" + vid[1] + "_" + vid[2] + "_" + vid[3]).val();
                get = "customerpayments";
                loadingIn = "customerpayments_" + vid[1] + "_Loading";
                contentIn = "customerpayments_" + vid[1];
                var requires_ajax = true;
                var html = false;
            }

            if(vid[0] == 'packingType')
            {
                dataParam += "&pid=" + $("#pid_" + vid[1]).val();
                dataParam += "&packingType=" + $("#" + vid[0] + "_" + vid[1] + "_" + vid[2]).val();
                get = "packingWeight";
                loadingIn = "packingWeight_" + vid[1] + "_Loading";
                contentIn = "packingWeight_" + vid[1];
                var requires_ajax = true;
                var html = false;
            }

            if(vid[0] == 'product')
            {

                dataParam += "&pid=" + $("#" + vid[0] + "_" + vid[1] + "_id_output").val();
                get = "packingType";
                loadingIn = "packingType_" + vid[1] + "_Loading";
                contentIn = "packingType_" + vid[1] + "_output";
                var requires_ajax = true;
                var html = true;
            }
        }

        if(requires_ajax == true)
        { //alert("index.php?module=stock/order&action=get_" + get);
            var url = "index.php?module=stock/order&action=get_" + get;
            if(html == false)
            {
                $.ajax({method: "post",
                    url: url,
                    data: dataParam,
                    beforeSend: function () {
                        $("#" + loadingIn).html("<img src='" + imagespath + "/loading.gif' alt='" + loading_text + "'/>")
                    },
                    complete: function () {
                        $("#" + loadingIn).empty();
                    },
                    success: function (returnedData) {
                        $("#" + contentIn).val(returnedData);
                    }
                });
            }
            else
            {
                $.ajax({method: "post",
                    url: url,
                    data: dataParam,
                    beforeSend: function () {
                        $("#" + loadingIn).html("<img src='" + imagespath + "/loading.gif' alt='" + loading_text + "'/>")
                    },
                    complete: function () {
                        $("#" + loadingIn).empty();
                    },
                    success: function (returnedData) {
                        $("#" + contentIn).html(returnedData);
                    }
                });
            }
        }
    }

    $(document).on('change', "#currency", function () {
        $.ajax({method: "post",
            url: "index.php?module=stock/order&action=get_fxrate",
            data: "currency=" + $("#currency").val(),
            beforeSend: function () {
                $("#fxUSD").html("<img src='" + imagespath + "/loading.gif' alt='" + loading_text + "'/>")
            },
            complete: function () {
                $("#fxUSD").empty();
            },
            success: function (returnedData) {
                alert(returnedData);
                $("#fxUSD").val(returnedData);
            }
        });
    });

    $("input[id^='product_'][id$='_output']").change(function () {
        if(sharedFunctions.checkSession() == false) {
            return;
        }

        var value = $(this).val();
        var id = $(this).attr('id').split("_");

        var last_product_rowid = $("#product_tbody > tr:last").attr("id");
        var new_product_rowid = parseInt(last_product_rowid) + 1;
        var product_id = $("#product_" + id[1] + "_" + id[2] + "_id").val();

        if($("#product_name_" + last_product_rowid).html().length > 0) {
            var exit_process = false;
            $("input[id^='pid_']").each(function () {
                if($(this).val() == product_id) {
                    var pidrow_id = $(this).attr('id').split('_');
                    var pidrow_id_to_use = pidrow_id[1];
                    exit_process = true;
                }
            });

            if(exit_process == false) {
                sharedFunctions.addmoreRows("addmore_product_" + last_product_rowid);
                $("#product_name_" + new_product_rowid).html($("#" + id[0] + "_sectionexception_" + id[1] + "_" + id[2] + "_autocomplete").val());
                $("#pid_" + new_product_rowid).val(product_id);

                loadingIn = "packingType_" + new_product_rowid + "_Loading";
                contentIn = "packingType_" + new_product_rowid + "_output";
            }
        }
        else
        {
            $("#product_name_1").html($("#" + id[0] + "_sectionexception_" + id[1] + "_" + id[2] + "_autocomplete").val());
            $("#pid_1").val(product_id);
            loadingIn = "packingType_1_Loading";
            contentIn = "packingType_1_output";
            pidrow_id_to_use = 1;
        }

        sharedFunctions.requestAjax("post", "index.php?module=stock/order&action=get_packingType", "&pid=" + value, loadingIn, contentIn, 1);
    });

    $(document).on('change', "input[id^='product_'][id$='_firstOrderQty'],input[id^='product_'][id$='_numOrders'],input[id^='product_'][id$='_quantityPerNextOrder']", function () {
        var id = $(this).attr('id').split('_');
        var row_total = 0;
        if($("#product_" + id[1] + "_" + id[2] + "_expectedQuantity").length > 0) {
            var row_oldtotal = $("#product_" + id[1] + "_" + id[2] + "_expectedQuantity").val();
        }
        else
        {
            row_oldtotal = 0;
        }

        if($("#product_" + id[1] + "_" + id[2] + "_firstOrderQty").val().length > 0) {
            row_total = row_total + parseFloat($("#product_" + id[1] + "_" + id[2] + "_firstOrderQty").val());
        }

        if($("#product_" + id[1] + "_" + id[2] + "_numOrders").length > 0 && $("#product_" + id[1] + "_" + id[2] + "_quantityPerNextOrder").length > 0) {
            if($("#product_" + id[1] + "_" + id[2] + "_numOrders").val().length > 0 && $("#product_" + id[1] + "_" + id[2] + "_quantityPerNextOrder").val().length > 0) {
                row_total = row_total + ($("#product_" + id[1] + "_" + id[2] + "_numOrders").val() * $("#product_" + id[1] + "_" + id[2] + "_quantityPerNextOrder").val());
            }
        }
        if($("#product_" + id[1] + "_" + id[2] + "_firstOrderQty").length > 0) {
            $("#product_" + id[1] + "_" + id[2] + "_expectedQuantity").val(row_total);
        }

        $("input[id^='pid_']").each(function () {
            if($(this).val() == $("#product_" + id[1] + "_" + id[2] + "_id").val()) {
                var product_rowid = $(this).attr('id').split('_');

                $("#quantity_" + product_rowid[1]).val((row_total + parseFloat($("#quantity_" + product_rowid[1]).val())) - row_oldtotal);
            }
        });

    });


    $(document).on('change', "input[id^='purchasePrice_'],input[id^='quantity_'],input[id^='sellingPrice_']", function () {
        var id = $(this).attr('id').split('_');
        if(id[0] == 'purchasePrice') {
            $('#purchaseAmount_' + id[1]).val($('#quantity_' + id[1]).val() * $('#purchasePrice_' + id[1]).val());
            $('#purchaseAmount_' + id[1] + '_output').text($('#purchaseAmount_' + id[1]).val());
        }
        else if(id[0] == 'unitSellingPrice') {
            $('#sellingAmount_' + id[1]).val($('#quantity_' + id[1]).val() * $('#sellingPrice_' + id[1]).val());
            $('#sellingAmount_' + id[1] + '_output').text($('#sellingAmount_' + id[1]).val());
        }
        else
        {
            $('#purchaseAmount_' + id[1]).val($('#quantity_' + id[1]).val() * $('#purchasePrice_' + id[1]).val());
            $('#purchaseAmount_' + id[1] + '_output').text($('#purchaseAmount_' + id[1]).val());
            $('#sellingAmount_' + id[1]).val($('#quantity_' + id[1]).val() * $('#sellingPrice_' + id[1]).val());
            $('#sellingAmount_' + id[1] + '_output').text($('#sellingAmount_' + id[1]).val());
        }
    });
});