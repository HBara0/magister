<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillsurvey}</title>
        {$headerinc}

    </head>
    <body>
        {$header}
    <tr>
        {$menu}

        <td class="contentContainer">
            <h3>{$lang->fillbudget}</h3>
            <div style="display:block;">
                <div style="display:inline-block;padding:0px;">{$affiliate_name}|</div>
                <div style="display:inline-block;padding:0px;">{$supplier_name}-{$budget_data[year]}|{$budget_data[currency]}</div>
            </div>

            <div style="display:block;padding:20px;">
                <form id="perform_budgeting/fillbudget_Form" name="perform_budgeting/fillbudget_Form" action="#" method="post">
                    <input type="hidden" id="rid" name="bid" value="{$core->input[bid]}">
                    <input type="hidden" id="identifier" name="identifier" value="{$core->input[identifier]}">
                    <input type="hidden" id="numrows" name="numrows" value="{$customerscount}">
                    <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-left: 8px;">
                        <thead>
                            <tr>
                                <td width="16.6%" class=" border_right" align="center" rowspan="2" valign="top" align="left">{$lang->customer}</strong</td>
                                <td width="16.6%" rowspan="2" valign="top" align="center" class=" border_right">{$lang->product}</td>
                                <td width="16.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->quantity}<br /><span class="smalltext"><em>{$lang->mt}</em></span></td>
                                <td width="16.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->saleamount}</td>
                                <td width="16.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->income}</td>
                                <td width="16.6%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->salestype}</td>
                            </tr>
                        </thead>
                        <tbody id="budgetlines_tbody">
                            {$budgetlinesrows}
                        </tbody>

                        <tr><td valign="top">{$addmore_customers}</td></tr>
                        <div style="float:right; position:fixed; top:470px;right:200px;"><input type="button" value="{$lang->prevcaps}" class="button" onClick="goToURL('index.php?module=fillbudget/create&amp;identifier={$core->input[identifier]}');"/> <input type="button" id="perform_budgeting/fillbudget_Button" value="{$lang->savecaps}" class="button"/>
                            <input type="submit" value="{$lang->nextcaps}" onClick='$("form:first").unbind("submit").trigger("submit");' class="button"/></div>        
                        <tr>
                            <td ><div id="perform_budgeting/fillbudget_Results"></div></td>
                        </tr>
                    </table>
                </form>
            </div>

        </td>

    </tr>
</body>
</html>