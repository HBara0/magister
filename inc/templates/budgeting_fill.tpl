<script type="text/javascript">
    $(function() {
        $(document).on('keyup', 'input[id^="amountper_"]', function() {
            var id = $(this).attr("id").split("_");
            if(!jQuery.isNumeric($('input[id=amountper_' + id[1] + ']').val())) {
                return;
            }
            $('input[id=income_' + id[1] + ']').val((Number($(this).val()) / 100) * $('input[id=amount_' + id[1] + ']').val());
            $('input[id="localincomeper_' + id[1] + '"]').trigger('keyup');
        });


        $(document).on('keyup', 'input[id^="localincomeper_"]', function() {
            var id = $(this).attr("id").split("_");
            if(!jQuery.isNumeric($('input[id=localincomeper_' + id[1] + ']').val())) {
                return;
            }
            $('input[id=localincome_' + id[1] + ']').val((Number($(this).val()) / 100) * $('input[id=income_' + id[1] + ']').val());

        });

        $(document).on('keyup change', 'input[id^="localincome_"]', function() {
            var id = $(this).attr("id").split("_");

            if(!jQuery.isNumeric($('input[id=localincome_' + id[1] + ']').val())) {
                return;
            }
            if($('input[id="localincome_' + id[1] + '"]').val().length > 0) {
                $('input[id=localincomeper_' + id[1] + ']').val((Number($(this).val()) * 100) / $('input[id=income_' + id[1] + ']').val());
            }
        });
        $(document).on('keyup', 'input[id^="income_"]', function() {
            var id = $(this).attr("id").split("_");
            if(!jQuery.isNumeric($('input[id=income_' + id[1] + ']').val())) {
                return;
            }
            if($('input[id="amount_' + id[1] + '"]').val().length > 0) {
                $('input[id=amountper_' + id[1] + ']').val((Number($(this).val()) * 100) / $('input[id=amount_' + id[1] + ']').val());
            }
            $('input[id="localincomeper_' + id[1] + '"]').trigger('keyup');
        });

        $(document).on('keyup', 'input[id^="unitprice_"]', function() {
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

        $(document).on('keyup', 'input[id^="Qty_"]', function() {
            var id = $(this).attr("id").split("_");
            $('input[id="unitprice_' + id[1] + '"]').trigger('keyup');
            $('input[id="amountper_' + id[1] + '"]').trigger('keyup');
            $('input[id="localincomeper_' + id[1] + '"]').trigger('keyup');
        });

        $(document).on('keyup', 'input[id^="amount_"]', function() {
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

        $(document).on('keyup', 'input[id^="s1perc_"]', function(e) {
            var id = $(this).attr("id").split("_");
            if($(this).val() > 100) {
                e.preventDefault();
            }
            else if($(this).val().length > 0 && $(this).val() <= 100) {
                $('input[id="s2perc_' + id[1] + '"]').val(Number(100 - $(this).val()));
            }
        });

        $(document).on('keyup', 'input[id^="s2perc_"]', function(e) {
            var id = $(this).attr("id").split("_");
            if($(this).val() > 100) {
                e.preventDefault();
            }
            else if($(this).val().length > 0 && $(this).val() <= 100) {
                $('input[id="s1perc_' + id[1] + '"]').val(Number(100 - $(this).val()));
            }
        });

        $(document).on('change', 'select[id^="salestype_"]', function() {
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

        $(document).on('change', "input[type='checkbox'][id$='_unspecifiedCustomer']", function() {
            var id = $(this).attr("id").split("_");
            $("div[id$='" + id[1] + "_unspecifiedCustomer_country']").slideToggle();
        });
    });</script>
<h1>{$lang->fillbudget}
    <div style="font-style:italic; font-size:12px; color:#666;">{$budget_data[affiliateName]} | {$budget_data[supplierName]} | {$budget_data[year]}</div>
</h1>
<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;">
    <p><h2><small>Please Read First</small></h2><strong>Important:</strong>Keeping the product field empty will result in deleting the row even if the product name hint is displayed below it. <u>You MUST pick the product from the results list, not just type it in the field.</u><br /><strong>Note:</strong> For better consistency we recommend picking up the customer if you can only see the customer name hint below the field. The hint comes from your previous budgets.<br />When importing these previous budgets, some customer names could not be matched to those on OCOS, so we simply used the customer name as is as an alternative way to identify the customer of the given budget line.<br /><strong>Do not pick a customer that is not in reality the same company as the one displayed below the field.</strong><hr /><em>"Unspecified Customer"</em> is exclsively used in the case when you don't already know the end customer of the budgeted items; if you tick it, you are not obliged to specify a customer.</p>
<p><h2><small>Sale Types Manual:</small></h2>{$tooltips[saletype]}</p>
</div>
<form id="perform_budgeting/fillbudget_Form" name="perform_budgeting/fillbudget_Form" action="index.php?module=budgeting/generatebudget&amp;identifier={$sessionidentifier}" method="post">
    <input type="hidden" id='spid' name="spid" value="{$core->input[budget][spid]}"/>
    <input type="hidden" id='affid' name="affid" value="{$core->input[budget][affid]}"/>
    <input type="hidden" id='year' name="year" value="{$core->input[budget][year]}"/>
    <input type="hidden" id="identifier" name="identifier" value="{$sessionidentifier}">
    <input type="hidden" name="budget[bid]" value="{$budget_data[bid]}">
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <thead>
            <tr style="vertical-align: top;">
                <td  width="1%" class="border_right"  rowspan="2" valign="top" align="left"></td>
                <td  width="11.6%" class=" border_right" align="center" rowspan="2" valign="top" align="left">{$lang->customer} <a href="index.php?module=contents/addentities&type=customer" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a></td>
                <td width="11.6%" rowspan="2" valign="top" align="center" class=" border_right">{$lang->product} <a href="index.php?module=contents/addproducts&amp;referrer=budgeting" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a></td>
                <td width="11.6%" class="border_right" rowspan="2" valign="top" align="center">{$lang->saletype} <a href="#" title="{$tooltips[saletype]}"><img src="./images/icons/question.gif" ></a></td>
                <td width="11.6%" class="border_right" rowspan="2" valign="top" align="center">{$lang->quantity}</td>
                <td width="11.6%" class="border_right" rowspan="2" valign="top" align="center">{$lang->uom}</td>
                <td width="11.6%" class="border_right" rowspan="2" valign="top" align="center">{$lang->unitprice}</td>
                <td width="11.6%" class="border_right" rowspan="2" valign="top" align="center">{$lang->amount}</td>
                <td width="11.6%" class="border_right" rowspan="2" valign="top" align="center">{$lang->incomeperc}</td>
                <td width="11.6%" class="border_right" rowspan="2" valign="top" align="center">{$lang->income}</td>
                {$hidden_colcells[localincome_head]}
                {$hidden_colcells[localincomeper_head]}
                {$hidden_colcells[remainingcommaff_head]}
                <td width="11.6%" class="border_right" rowspan="2" valign="top" align="center">{$lang->curr}</td>
                <td width="11.6%" class="border_right" rowspan="2" valign="top" align="center">{$lang->entitypurchasingfromsupplier}</td>
                <td width="11.6%" class="border_right" rowspan="2" valign="top" align="center">{$lang->s1perc}</td>
                <td width="11.6%" class="border_right" rowspan="2" valign="top" align="center">{$lang->s2perc}</td>
                <td width="11.6%" class="border_right" rowspan="2" valign="top" align="center">{$lang->purchasedfromaffiliate} <a href="#" title="The affiliate from which you are buying the items in exceptional cases. Only applicable for the case of intercompany transactions; one affiliate (ex. Orkila Free Zone - Alex) selling and invoicing the other affiliate (ex. Orkila Egypt). This will automatically create an intercompany sale in the budget of the select affiliate. This should not be filled unless in exceptional cases."><img src="./images/icons/question.gif" ></a></td>
            </tr>
        </thead>
        <tbody id="budgetlines_{$rowid}_tbody" style="width:100%;">
            {$budgetlinesrows}
        </tbody>
        <tfoot>
            <tr><td valign="top" colspan="2">
                    <input name="numrows_budgetlines{$rowid}" type="hidden" id="numrows_budgetlines_{$rowid}" value="{$rowid}">
                    <input type="hidden" name="ajaxaddmoredata[spid]" id="ajaxaddmoredata_spid" value="{$budget_data[spid]}"/>
                    <input type="hidden" name="ajaxaddmoredata[affid]" id="ajaxaddmoredata_affid" value="{$budget_data[affid]}"/>
                    <img src="./images/add.gif" id="ajaxaddmore_budgeting/fillbudget_budgetlines_{$rowid}" alt="{$lang->add}">
                </td></tr>
            <tr>
                <td colspan="2">
                    <table width="100%">
                        <tr> <td><input type="button" value="{$lang->prevcaps}" class="button" onClick="goToURL('index.php?module=budgeting/create&amp;identifier={$sessionidentifier}');"/></td>
                            <td><input type="button" id="perform_budgeting/fillbudget_Button" value="{$lang->savecaps}" class="button"/></td>
                            <!--<td> <input type="submit" value="{$lang->nextcaps}" onClick='$("form:first").unbind("submit").trigger("submit");'class="button"/>     </td>--> </tr>
                    </table>
                </td>
            </tr>

        </tfoot>
    </table>
    <div id="perform_budgeting/fillbudget_Results"></div>
</form>
