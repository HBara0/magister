<html>
    <head>
        <title>{$core->settings[systemtitle]} |{$lang->financialbudgetreport}</title>
        {$headerinc}

    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">  <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-top: 10px; margin-bottom: 10px; display: block;">{$output[currfxrates]}</div>
            <h1>{$lang->financialbudgetreport}<br /><small>{$affiliate->name} {$financialbudget_year}</small></h1>

            {$budgeting_headcount}
            {$budgeting_investmentfollowup}
            {$budgeting_financialadminexpenses}
            {$budgeting_forecastbalancesheet}
            {$budgeting_profitlossaccount}
        </td>
    </tr>
    {$footer}
</body>
</html>