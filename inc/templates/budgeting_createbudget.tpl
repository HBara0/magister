<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillsurvey}</title>
        {$headerinc}
        <script src="{$core->settings[rootdir]}/js/fillreport.js" type="text/javascript"></script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->createbudget}</h1>
            <form name="perform_budgeting/create_Form" id="perform_budgeting/create_Form" action="index.php?module=budgeting/fillbudget&amp;stage=fillbudgetline" method="post">
                <input type="hidden" name="identifier" value="{$sessionidentifier}"/>
                <div style="display:block;">
                    <div style="display:inline-block;padding:8px;">{$lang->affiliate}</div>
                    <div style="display:inline-block;padding:8px;">{$affiliated_budget}</div>
                </div>

                <div id="budget_supplier" style="display:block;">
                    <div style="display:inline-block;padding:8px;">{$lang->supplier}</div>
                    <div style="display:inline-block;padding:8px;">{$budget_supplierslist}</div>  <div id="supplierslist_Loading" style="display:inline-block;padding:8px;"></div>
                </div>
                <!--  <div  id="budget_curr" style="display:block;">
                                    <div style="display:inline-block;padding:8px;">{$lang->curr}</div>
                                    <div style="display:inline-block;padding:8px;">{$budget_currencylist}</div> <div id="currlist_Loading" style="display:inline-block;padding:8px;"></div>
                                </div> -->
                <div  id="budget_year" style="display:block;">
                    <div style="display:inline-block;padding:8px;">{$lang->year}</div>
                    <div style="display:inline-block;padding:8px; margin-left:20px;"><select name="budget[year]" title="year" id="year">{$budget_year}</select></div>
                    <div id="years_Loading" style="display:inline-block;padding:8px;"></div>
                </div>
                <div id="budget_currautomatic" style="display:block;">
                    <div style="display:inline-block;padding:8px; margin-left:65px;"><input type="radio" value=1 checked name="budget[fxrate]"/></div>
                    <div style="display:inline-block;padding:8px;">{$lang->automaticusdrate}</div>
                </div>
                <div  id="budget_currspecify"style="display:block;">
                    <div style="display:inline-block;padding:8px; margin-left:65px;"><input type="radio" value=2 name="budget[fxrate]"/></div>
                    <div style="display:inline-block;padding:8px;">{$lang->specifyusdrate}</div>
                </div>


                <div  id="buttons_row" style=" display: none;clear:left;"><input type="submit" value="Proceed" class="button"  /></div>

            </div>
        </form>
        <div id="perform_budgeting/create_Results"></div>
    </td>
</tr>
{$footer}
</body>
</html>