<script type="text/javascript">
    $(function() {
    $("#quantity,#product").change(getMoreData);
            function getMoreData() {
            if(sharedFunctions.checkSession() == false) {
            return;
            }

            var value = $(this).val();
                    var id = $(this).attr('id');
                    var dataParam = '';
                    if(id == "product") {
            dataParam += "&pid=" + $("#product").val();
                    dataParam += "&affid=" + $("#affid").val();
                    get = "price";
                    loadingIn = "price_Loading";
                    contentIn = "price";
                    //$("#price").empty();
                    var requires_ajax = true;
            }
            else if(id == "quantity") {

            /*dataParam += "&price=" + $("#price").val();
             dataParam += "&quantity=" + $("#quantity").val();
             dataParam += "&main_price=" + $("#main_price").val();
             get = "total";*/
            var total = 0;
                    alert($("#price").val());
                    var x = parseFloat($("#quantity").val());
                    var y = parseFloat($("#price").val());
                    total = parseFloat($("#quantity").val()) * parseFloat($("#price").val());
                    //var price = 37.444;
                    //total = $("#price").val();

                    alert($("#price").val());
                    //total = Number($("#price").val());

                    //total = typeof($("#price").val());
                    /* loadingIn = "total_Loading";
                     contentIn = "total";   */

                    $('#total').val(total);
                    //document.getElementById("total").innerHTML = total;
                    //$("#total").empty();
                    var requires_ajax = false;
            }

            if(requires_ajax == true)
            {
            var url = "index.php?module=grouppurchase/affiliateorder&action=get_" + ge        t;
                    $.ajax({method: "post",
                                        url: url,
                                        data: dataParam,
                                        /* beforeSend: function() { $("#" + loadingIn).html("<img src='" + imagespath +"/loading.gif' alt='" + loading_text + "'/>")},
                            complete: function () { $("#" + loadingIn).empty();
                            }, * /

                            success: function(returnedData) {
                            //returnedData = '   2f   ';
                            //returnedData = jQuery.trim(returnedData);
                            //returnedData = returnedData.slice( 5 );
                            //alert(jQuery.trim(returnedData));
                            alert(returnedData);
                                    //alert(console.log(returnedData));
                                    //returnedData = '2';
                                    $("#" + contentIn).val(returnedData);
                            }
                    });
            }
            //	var id = $(this).attr("id");
            }

    $("input[id='change']").change(function() {
    if($(this).is(":checked")) {
    $("#price_output").removeAttr("disabled");
    }
    else
    {
    $("#price_output").attr("disabled", "true");
    }
    });
    });
</script>
<div class="container">
    <h1>{$lang->affiliateorder}</h1>
    <form id="add_grouppurchase/affiliateorder_Form" name="add_grouppurchase/affiliateorder_Form" action="#" method="post">
        <table cellpadding="0" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th width="30%">{$lang->affiliates}</th>
                    <th width="30%">{$lang->product}</th>
                    <th width="10%">{$lang->price}</th>
                    <th width="10%">{$lang->quantity}</th>
                    <th width="10%">{$lang->currentstock}</th>
                    <th width="10%">{$lang->total}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="30%">{$affiliates_list}</td>
                    <td>{$products_list}<span id="productslist_Loading"></span></td>
                    <td><input type="text" id="price_output" name="price_output"  value="{$price}" size="5"/><span id="price_Loading"></span>
                        <input type="text" id="price" name="price" size="5" value="{$price}"/>
                        <input type='checkbox' name='change' id='change' value="1"{$checkedboxes[change]}>{$lang->changeprice}<input type="text" id="main_price" name="main_price"  size="5" disabled= "disabled" value="{$price}" /></td>
                    <td><input type="text" id="quantity" name="quantity" size="5" accept="numeric"/></td>
                    <td><input type="text" id="currentStock" name="currentStock" size="5" accept="numeric"/></td>
                    <td><input type="text" id="total" name="total"  size="5" /><span id="total_Loading"></span></td>
                </tr>
                <tr>
                    <th width="30%">{$lang->remarks}</th>
                    <td colspan="5"><textarea cols="30" rows="5" id="remark" name="remark" ></textarea></td>
                </tr>
            </tbody>
            <tr>
                <td colspan="6"><input type="button" id="add_grouppurchase/affiliateorder_Button" value="{$lang->order}" /><input type="reset" value="{$lang->reset}" />
                    <div id="add_grouppurchase/affiliateorder_Results"></div>
                </td>
            </tr>
        </table>
    </form>
</div>