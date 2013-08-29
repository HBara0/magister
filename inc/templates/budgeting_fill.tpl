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

       
                <form id="perform_budgeting/fillbudget_Form" name="perform_budgeting/fillbudget_Form" action="index.php?module=budgeting/generatebudget&amp;identifier={$core->input[identifier]}" method="post">
                    <input type="hidden"  name="budgetline[bid]" value="{$core->input[bid]}">
                    <input type="hidden" id="identifier" name="identifier" value="{$core->input[identifier]}">
                    <table width="100%" border="0" cellspacing="0" cellpadding="2">
                        <thead>
                            <tr>
                                <td width="20%" class=" border_right" align="center" rowspan="2" valign="top" align="left">{$lang->customer}</strong</td>
                                <td width="20%" rowspan="2" valign="top" align="center" class=" border_right">{$lang->product}</td>
                                <td width="15%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->quantity}<br /><span class="smalltext"><em>{$lang->mt}</em></span></td>
                                <td width="15%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->saleamount}</td>
                                <td width="15%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->income}</td>
                                <td width="15%" class=" border_right" rowspan="2" valign="top" align="center">{$lang->salestype}</td>
                            </tr>
                        </thead>
                        <tbody id="budgetlines_tbody">
                            {$budgetlinesrows}
                        </tbody>

                        <tr><td valign="top">{$addmore_customers}</td></tr>

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