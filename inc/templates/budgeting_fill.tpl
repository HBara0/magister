<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillsurvey}</title>
        {$headerinc}
        <script type="text/javascript">
            $(function() {
                $('input[id^="amountper_"]').live('input', function() {
                    var id = $(this).attr("id").split("_");
                    $('input[id^=income_' + id[1] + ']').val((Number($(this).val()) / 100) * $('input[id^=amount_' + id[1] + ']').val());
                });

                $('input[id^="income_"]').live('input', function() {
                    var id = $(this).attr("id").split("_");
                    if ($('input[id^="amount_' + id[1] + '"]').val().length > 0) {
                        $('input[id^=amountper_' + id[1] + ']').val((Number($(this).val()) * 100) / $('input[id^=amount_' + id[1] + ']').val());
                    }
                });

                $('input[id^="amount_"]').live('input', function() {
                    var id = $(this).attr("id").split("_");
                    if ($('input[id^="amountper_' + id[1] + '"]').val().length > 0) {
                        $('input[id^="amountper_' + id[1] + '"]').trigger('input');
                    } else {

                        if ($('input[id^="income_' + id[1] + '"]').val().length > 0) {
                            $('input[id^="income_' + id[1] + '"]').trigger('input');
                        }
                    }
                });

                $('select[id^="salestype_"]').live('change', function() {
                    var id = $(this).attr("id").split("_");
                    var salestype = $(this).val();

                    var currencies = {$js_currencies};
                    var invoicetypes = {$js_saletypesinvoice};
                    if (typeof currencies[salestype] != 'undefined') {
                        $("#currency_" + id[1]).val(currencies[salestype]);
                    }

                    if (typeof invoicetypes[salestype] != 'undefined') {
                        $('#invoice_' + id[1]).val(invoicetypes[salestype]);
                    }

                });
            });</script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->fillbudget}
                <div style="font-style:italic; font-size:12px; color:#666;">{$budget_data[affiliateName]} | {$budget_data[supplierName]} | {$budget_data[year]}</div>
            </h3>
            <form id="perform_budgeting/fillbudget_Form" name="perform_budgeting/fillbudget_Form" action="index.php?module=budgeting/generatebudget&amp;identifier={$sessionidentifier}" method="post">
                <input type="hidden" id='spid' name="spid" value="{$core->input[budget][spid]}"/>
                <input type="hidden" id="identifier" name="identifier" value="{$sessionidentifier}">
                <input type="hidden" name="budget[bid]" value="{$budget_data[bid]}">
                <table width="100%" border="0" cellspacing="0" cellpadding="2">
                    <thead>
                        <tr style="vertical-align: top;">
                            <td  width="11.6%" class=" border_right" align="center" rowspan="2" valign="top" align="left">{$lang->customer}</strong</td>
                            <td width="11.6%" rowspan="2" valign="top" align="center" class=" border_right">{$lang->product}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->saleType}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->Quantity}<br /><span class="smalltext"><em>{$lang->mt}</em></span></td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->uom}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->amount}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->incomeperc}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->income}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->curr}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->invoice}</td>
                        </tr>
                    </thead>
                    <tbody id="budgetlines{$rowid}_tbody" style="width:100%;">
                        {$budgetlinesrows}
                    </tbody>
                    <tfoot>
                        <tr><td valign="top">  
                                <input name="numrows_budgetlines{$rowid}" type="hidden" id="numrows_budgetlines{$rowid}" value="{$rowid}">
                                <input type="hidden" name="ajaxaddmoredata[affid]" id="ajaxaddmoredata_affid" value="{$budget_data[affid]}"/> 
                                <img src="./images/add.gif" id="ajaxaddmore_budgeting/fillbudget_budgetlines_{$rowid}" alt="{$lang->add}">
                            </td></tr>
                        <tr>
                            <td>
                                <table width="100%">
                                    <tr> <td><input type="button" value="{$lang->prevcaps}" class="button" onClick="goToURL('index.php?module=budgeting/create&amp;identifier={$sessionidentifier}');"/></td>
                                        <td><input type="button" id="perform_budgeting/fillbudget_Button" value="{$lang->savecaps}" class="button"/></td>
                                        <td> <input type="submit" value="{$lang->nextcaps}" onClick='$("form:first").unbind("submit").trigger("submit");'class="button"/>     </td></tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td ><div id="perform_budgeting/fillbudget_Results"></div></td>
                        </tr>
                    </tfoot>
                </table>
            </form>
        </td>
    </tr>
</body>
</html>