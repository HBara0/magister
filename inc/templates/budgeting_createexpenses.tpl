<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->createcomadminexpenses}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->createcomadminexpenses}</h1>
            <form name="perform_budgeting/createexpenses_Form" id="perform_budgeting/createexpenses_Form" action="index.php?module=budgeting/commercialexpenses" method="post">
                <input type="hidden" name="identifier" value="{$sessionidentifier}"/>
                <div style="display:block;">
                    <div style="display:inline-block;padding:8px;">{$lang->affiliate}</div>
                    <div style="display:inline-block;padding:8px;">{$affiliated_budget}</div>
                </div>

                <div  id="budget_year" style="display:block;">
                    <div style="display:inline-block;padding:8px;">{$lang->year}</div>
                    <div style="display:inline-block;padding:8px; margin-left:20px;"><select name="financialbudget[year]" title="year" id="year">{$budget_year}</select></div>
                    <div id="years_Loading" style="display:inline-block;padding:8px;"></div>
                </div>
                <div>
                    <input type="submit" value="Proceed" class="button" />
                </div>
            </form>
            <div id="perform_budgeting/createexpenses_Results"></div>
        </td>
    </tr>
    {$footer}
</body>
</html>