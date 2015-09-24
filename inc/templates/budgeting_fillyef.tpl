<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillyef}</title>
        {$headerinc}
        <script type="text/javascript">
            $(function () {
                $('input[id^="amountper_"]').live('keyup', function () {
                    var id = $(this).attr("id").split("_");
                    if(!jQuery.isNumeric($('input[id=amountper_' + id[1] + ']').val())) {
                        return;
                    }
                    $('input[id=income_' + id[1] + ']').val((Number($(this).val()) / 100) * $('input[id=amount_' + id[1] + ']').val());
                });
                $('input[id^="localincomeper_"]').live('keyup', function () {
                    var id = $(this).attr("id").split("_");
                    if(!jQuery.isNumeric($('input[id=localincomeper_' + id[1] + ']').val())) {
                        return;
                    }
                    $('input[id=localincome_' + id[1] + ']').val((Number($(this).val()) / 100) * $('input[id=income_' + id[1] + ']').val());
                });
                $('input[id^="localincome_"]').live('keyup change', function () {
                    var id = $(this).attr("id").split("_");
                    if(!jQuery.isNumeric($('input[id=localincome_' + id[1] + ']').val())) {
                        return;
                    }
                    if($('input[id="localincome_' + id[1] + '"]').val().length > 0) {
                        $('input[id=localincomeper_' + id[1] + ']').val((Number($(this).val()) * 100) / $('input[id=income_' + id[1] + ']').val());
                    }
                });
                $('input[id^="income_"]').live('keyup', function () {
                    var id = $(this).attr("id").split("_");
                    if(!jQuery.isNumeric($('input[id=income_' + id[1] + ']').val())) {
                        return;
                    }
                    if($('input[id="amount_' + id[1] + '"]').val().length > 0) {
                        $('input[id=amountper_' + id[1] + ']').val((Number($(this).val()) * 100) / $('input[id=amount_' + id[1] + ']').val());
                    }
                });
                $('input[id^="unitprice_"]').live('keyup', function () {
                    var id = $(this).attr("id").split("_");
                    if(!jQuery.isNumeric($('input[id=unitprice_' + id[1] + ']').val())) {
                        return;
                    }

                    if($('input[id="Qty_' + id[1] + '"]').val().length > 0) {
                        $('input[id=amount_' + id[1] + ']').val((Number($('input[id=Qty_' + id[1] + ']').val() * $('input[id=unitprice_' + id[1] + ']').val()))).trigger("input");
                        $('input[id="amountper_' + id[1] + '"]').trigger('keyup');
                        $('input[id="localincomeper_' + id[1] + '"]').trigger('keyup');
                    }

                });
                $('input[id^="Qty_"]').live('keyup', function () {
                    var id = $(this).attr("id").split("_");
                    $('input[id="unitprice_' + id[1] + '"]').trigger('keyup');
                    $('input[id="amountper_' + id[1] + '"]').trigger('keyup');
                    $('input[id="localincomeper_' + id[1] + '"]').trigger('keyup');
                });
                $('input[id^="amount_"]').live('keyup', function () {
                    var id = $(this).attr("id").split("_");
                    if(!jQuery.isNumeric($('input[id=amount_' + id[1] + ']').val())) {
                        return;
                    }
                    if($('input[id="amountper_' + id[1] + '"]').val().length > 0) {
                        $('input[id="amountper_' + id[1] + '"]').trigger('keyup');
                        $('input[id="localincomeper_' + id[1] + '"]').trigger('keyup');
                    } else {
                        if($('input[id="income_' + id[1] + '"]').val().length > 0) {
                            $('input[id="income_' + id[1] + '"]').trigger('keyup');
                            $('input[id="localincome_' + id[1] + '"]').trigger('keyup');
                        }
                    }

                    if($('input[id="Qty_' + id[1] + '"]').val().length > 0) {
                        $('input[id=unitprice_' + id[1] + ']').val(($('input[id=amount_' + id[1] + ']').val() / $('input[id=Qty_' + id[1] + ']').val()));
                    }

                });
                $('input[id^="s1perc_"]').live('keyup', function (e) {
                    var id = $(this).attr("id").split("_");
                    if($(this).val() > 100) {
                        e.preventDefault();
                    }
                    else if($(this).val().length > 0 && $(this).val() <= 100) {
                        $('input[id="s2perc_' + id[1] + '"]').val(Number(100 - $(this).val()));
                    }
                });
                $('input[id^="s2perc_"]').live('keyup', function (e) {
                    var id = $(this).attr("id").split("_");
                    if($(this).val() > 100) {
                        e.preventDefault();
                    }
                    else if($(this).val().length > 0 && $(this).val() <= 100) {
                        $('input[id="s1perc_' + id[1] + '"]').val(Number(100 - $(this).val()));
                    }
                });
                $('select[id^="salestype_"]').live('change', function () {
                    var id = $(this).attr("id").split("_");
                    var salestype = $(this).val();
                    var currencies = {$js_currencies};
                    var invoicetypes = {$js_saletypesinvoice};
                    //var saletypespurchase ={$js_saletypespurchase};
                    if(typeof currencies[salestype] != 'undefined') {
                        $("#currency_" + id[1]).val(currencies[salestype]);
                    }

                    if(typeof invoicetypes[salestype] != 'undefined') {
                        $('#invoice_' + id[1]).val(invoicetypes[salestype]);
                    }

                    if(typeof saletypespurchase[salestype] != 'undefined') {
                        $('#purchasingEntity_' + id[1]).val(saletypespurchase[salestype]);
                    }
                });

                $("input[type='checkbox'][id$='_unspecifiedCustomer']").live('change', function () {
                    var id = $(this).attr("id").split("_");
                    $("div[id$='" + id[1] + "_unspecifiedCustomer_country']").slideToggle();
                });

                $(document).on('keyup', 'input[data-max]', function () {
                    if($(this).attr('data-name') == "{$lang->quantity}") {
                        var month_qty = $(this).val() / 3;
                        var month_perc = (month_qty / $(this).val()) * 100;
                        $('input[data-quantity="' + $(this).attr('data-totalquantity') + '"]').each(function (i, obj) {
                            $(obj).val(month_qty.toFixed(2));
                            $(obj).removeClass("warning");
                            $('input[id="' + $(obj).attr('data-perc') + '"]').val(month_perc.toFixed(2));
                        });
                    }
                    if($(this).attr('data-max') > 0 && $(this).val() > parseFloat($(this).attr('data-max'))) {
                        $("#alert_" + $(this).attr('data-rowid')).text($('input[id="errormessage"]').val() + $(this).attr('data-name'));
                        $(this).addClass("warning");
                    } else {
                        $("#alert_" + $(this).attr('data-rowid')).text("");
                        $(this).removeClass("warning");
                    }
                });

                $(document).on('keyup', 'input[data-quantity]', function () {
                    var total = 0;
                    $('input[data-quantity="' + $(this).attr('data-quantity') + '"]').each(function (i, obj) {
                        total += parseFloat($(obj).val());
                        $(obj).removeClass("warning");
                    });
                    $('input[data-totalquantity="' + $(this).attr('data-quantity') + '"]').val(total);
                    $('input[data-quantity="' + $(this).attr('data-quantity') + '"]').each(function (i, obj) {
                        var month_perc = $(obj).val() / total * 100;
                        $('input[id="' + $(obj).attr('data-perc') + '"]').val(month_perc.toFixed(2));
                    });
                    if($('input[data-totalquantity="' + $(this).attr('data-quantity') + '"]').attr('data-max') > 0 && $('input[data-totalquantity="' + $(this).attr('data-quantity') + '"]').val() > parseFloat($('input[data-totalquantity="' + $(this).attr('data-quantity') + '"]').attr('data-max'))) {
                        $("#alert_" + $('input[data-totalquantity="' + $(this).attr('data-quantity') + '"]').attr('data-rowid')).text($('input[id="errormessage"]').val() + $('input[data-totalquantity="' + $(this).attr('data-quantity') + '"]').attr('data-name'));
                        $('input[data-totalquantity="' + $(this).attr('data-quantity') + '"]').addClass("warning");
                    } else {
                        $("#alert_" + $('input[data-totalquantity="' + $(this).attr('data-quantity') + '"]').attr('data-rowid')).text("");
                        $('input[data-totalquantity="' + $(this).attr('data-quantity') + '"]').removeClass("warning");
                    }
                });
            });</script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1><div id="page_title">{$lang->fillyef}</div>
                <div style="font-style:italic; font-size:12px; color:#666;">{$yef_data[affiliateName]} | {$yef_data[supplierName]} | {$yef_data[year]}</div>
            </h1>
            <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;">
                <p><h2><small>Please Read First</small></h2><strong>Important:</strong>Keeping the product field empty will result in deleting the row even if the product name hint is displayed below it. <u>You MUST pick the product from the results list, not just type it in the field.</u><br /><strong>Note:</strong> For better consistency we recommend picking up the customer if you can only see the customer name hint below the field. The hint comes from your previous budgets.<br />When importing these previous budgets, some customer names could not be matched to those on OCOS, so we simply used the customer name as is as an alternative way to identify the customer of the given budget line.<br /><strong>Do not pick a customer that is not in reality the same company as the one displayed below the field.</strong><hr /><em>"Unspecified Customer"</em> is exclsively used in the case when you don't already know the end customer of the budgeted items; if you tick it, you are not obliged to specify a customer.</p>
            <p><h2><small>Sale Types Manual:</small></h2>{$tooltips[saletype]}</p>
    </div>
    <form id="perform_budgeting/fillyearendforecast_Form" name="perform_budgeting/fillyearendforecast_Form" action="index.php?module=budgeting/generatebudget&amp;identifier={$sessionidentifier}" method="post">
        <input type="hidden" id='spid' name="spid" value="{$core->input[yef][spid]}"/>
        <input type="hidden" id='affid' name="affid" value="{$core->input[yef][affid]}"/>
        <input type="hidden" id='year' name="year" value="{$core->input[yef][year]}"/>
        <input type="hidden" name="yef[yefid]" value="{$currentyef[yefid]}">
        <input type="hidden" name="errormessage" id="errormessage" disabled="disabled" value="{$lang->numberspassedbudget}">
        <table width="100%" border="0" cellspacing="0" cellpadding="2">
            <thead>
                <tr style="vertical-align: top;">
                    <td  width="150px" class=" border_right" align="center" rowspan="2" valign="top" align="left">{$lang->customer} <a href="index.php?module=contents/addentities&type=customer" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a></td>
                    <td width="150px" rowspan="2" valign="top" align="center" class=" border_right">{$lang->product} <a href="index.php?module=contents/addproducts&amp;referrer=budgeting" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a></td>
                    <td width="150px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->saletype} <a href="#" title="{$tooltips[saletype]}"><img src="./images/icons/question.gif" ></a></td>
                    <td width="150px" class=" border_right" rowspan="2" valign="top" align="center"><div id="month_tour">{$lang->october} {$lang->quantity}</div></td>
                    <td width="150px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->november} {$lang->quantity}</td>
                    <td width="150px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->december} {$lang->quantity}</td>
                    <td width="150px" class=" border_right" rowspan="2" valign="top" align="center"><div id="quantity_tour">{$lang->totalquantity}</div></td>
                    <td width="150px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->uom}</td>
                    <td width="150px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->unitprice}</td>
                    <td width="150px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->currency}</td>
                    <td width="150px" class=" border_right" rowspan="2" valign="top" align="center"><div id="amount_tour">{$lang->amount}</div></td>
                    <td width="150px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->incomeperc}</td>
                    <td width="150px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->income}</td>
                    {$hidden_colcells[localincomeper_head]}
                    {$hidden_colcells[localincome_head]}
                    {$hidden_colcells[remainingcommaff_head]}
                    <td width="150px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->entitypurchasingfromsupplier}</td>
                    <td width="150px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->purchasedfromaffiliate} <a href="#" title="The affiliate from which you are buying the items in exceptional cases. Only applicable for the case of intercompany transactions; one affiliate (ex. Orkila Free Zone - Alex) selling and invoicing the other affiliate (ex. Orkila Egypt). This will automatically create an intercompany sale in the budget of the select affiliate. This should not be filled unless in exceptional cases."><img src="./images/icons/question.gif" ></a></td>
                </tr>
            </thead>
            <tbody id="budgetlines_{$rownums}_tbody" style="width:100%;">
                {$budgetlinesrows}
            </tbody>
            <tfoot>
                <tr><td valign="top">
                        <input name="numrows_budgetlines{$rownums}" type="hidden" id="numrows_budgetlines_{$rownums}" value="{$rownums}">
                        <input type="hidden" name="ajaxaddmoredata[affid]" id="ajaxaddmoredata_affid" value="{$yef_data[affid]}"/>
                        <input type="hidden" name="ajaxaddmoredata[spid]" id="ajaxaddmoredata_spid" value="{$yef_data[spid]}"/>
                        <img src="./images/add.gif" id="ajaxaddmore_budgeting/fillyearendforecast_budgetlines_{$rownums}" alt="{$lang->add}">
                    </td></tr>
                <tr>
                    <td>
                        <table width="100%">
                            <tr> <td><input type="button" value="{$lang->prevcaps}" class="button" onClick="goToURL('index.php?module=budgeting/create&amp;identifier={$sessionidentifier}')
                                            ;"/></td>
                                <td><input type="button" id="perform_budgeting/fillyearendforecast_Button" value="{$lang->savecaps}" class="button"/></td>
                                <!--<td> <input type="submit" value="{$lang->nextcaps}" onClick='$("form:first").unbind("submit").trigger("submit");'class="button"/>     </td>--> </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td ><div id="perform_budgeting/fillyearendforecast_Results"></div></td>
                </tr>
            </tfoot>
        </table>
    </form>
</td>
</tr>
{$helptour}
</body>
</html>