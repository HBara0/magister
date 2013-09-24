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
                     if ($('input[id^="amountper_' + id[1] + '"]').val().length > 0) {
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
                    var myArray = {0:"LBP", 1:"LBP",2:"USD",4:"EUR"};
                    if (typeof myArray[salestype] != 'undefined') {
                        $("#currency_" + id[1]).val(myArray[salestype]);
                    }
                    if ($(this).val() == 4) {
                        $('#invoice_' + id[1]).val('supplier');
                    }
                });
            });</script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->fillbudget}</h3>
            <div style="display:block;">
                <div style="display:inline-block; padding:0px;">{$affiliate_name}|</div>
                <div style="display:inline-block; padding:0px;">{$supplier_name}-{$budget_data[year]}|{$budget_data[currency]}</div>
            </div>

            <form id="perform_budgeting/fillbudget_Form" name="perform_budgeting/fillbudget_Form" action="index.php?module=budgeting/generatebudget&amp;identifier={$core->input[identifier]}" method="post">
        <!-- <input type="hidden" name="budgetline[$rowid][affid]" value="{$budgetline[affid]}"/> -->
                <input type="hidden" id='spid' name="spid" value="{$core->input[budget][spid]}"/>
                <input type="hidden" id="identifier" name="identifier" value="{$sessionidentifier}">
                <input type="hidden" name="budget[bid]" value="{$currentbudget[bid]}">
                <table width="100%" border="0" cellspacing="0" cellpadding="2">
                    <thead>
                        <tr style="vertical-align: top;">
                            <td  width="11.6%" class=" border_right" align="center" rowspan="2" valign="top" align="left">{$lang->customer}</strong</td>
                            <td width="11.6%" rowspan="2" valign="top" align="center" class=" border_right">{$lang->product}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->salestype}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->quantity}<br /><span class="smalltext"><em>{$lang->mt}</em></span></td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->uom}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->saleamount}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->incomeperc}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->income}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->curr}</td>
                            <td width="11.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->invoice}</td>
                        </tr>
                    </thead>

                    <tbody id="budgetlines{$rowid}_tbody" style="width:100%;">
                        {$budgetlinesrows}
                    </tbody> 

                    <tr><td valign="top">  
                            <input name="numrows_budgetlines{$rowid}" type="hidden" id="numrows_budgetlines{$rowid}" value="{$rowid}">
                            <img src="./images/add.gif" id="ajaxaddmore_budgeting/fillbudget_budgetlines_{$rowid}" alt="{$lang->add}">
                        </td></tr>

                    <tr>
                        <td>
                            <table width="100%">
                                <tr> <td><input type="button" value="{$lang->prevcaps}" class="button" onClick="goToURL('index.php?module=budgeting/create&amp;identifier={$core->input[identifier]}');"/></td>
                                    <td><input type="button" id="perform_budgeting/fillbudget_Button" value="{$lang->savecaps}" class="button"/></td>
                                    <td> <input type="submit" value="{$lang->nextcaps}" onClick='$("form:first").unbind("submit").trigger("submit");'class="button"/>     </td></tr>
                            </table>
                        </td>

                    </tr>
                    <tr>
                        <td ><div id="perform_budgeting/fillbudget_Results"></div></td>
                    </tr>

                </table>
            </form>
        </td>
    </tr>
</body>
</html>
