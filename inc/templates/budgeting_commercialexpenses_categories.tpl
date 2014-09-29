<table class="datatable" style="width:100%">
    <tr class="thead">
        <td style="width:50%">Company name:</td>
        <td style="width:10%">{$lang->actual}</td>
        <td style="width:10%">{$lang->budget}</td>
        <td style="width:10%">{$lang->yef}</td>
        <td style="width:10%">{$lang->budget}</td>
        <td style="width:10%">% {$lang->budyef}</td>
    </tr>
    <tr style="width:100%">
        <td style="width:50%"><span>{$affiliate->name}</span><input name="financialbudget[affid]" value ="{$affid}" type="hidden"></td>
        <td style="width:10%"><span>{$financialbudget_prev2year}</span></td>
        <td style="width:10%"><span>{$financialbudget_prevyear}</span></td>
        <td style="width:10%"><span>{$financialbudget_prevyear}</span></td>
        <td style="width:10%"><span>{$financialbudget_year}</span><input name="financialbudget[year]" value ="{$financialbudget_year}" type="hidden"></td>
        <td style="width:10%"></td>
    </tr>
    {$budgeting_commercialexpenses_category}

    <tr>
        <td style="width:50%;font-weight:bold;">{$lang->totalexpenses}</td>
        <td>
            <div style="font-weight:bold;" id="total_actualPrevTwoYears">{$total[actualPrevTwoYears]}</div>
            <input type="hidden" id="total_actualPrevTwoYears" value="{$total[actualPrevTwoYears]}"></input>
        </td>
        <td>
            <div style="font-weight:bold;" id="total_budgetPrevYear">{$total[budgetPrevYear]}</div>
            <input type="hidden" id="total_budgetPrevYear" value="{$total[budgetPrevYear]}"></input>
        </td>
        <td>
            <div style="font-weight:bold;" id="total_yefPrevYear">{$total[yefPrevYear]}</div>
            <input type="hidden" id="total_yefPrevYear" value="{$total[yefPrevYear]}"></input>
        </td>
        <td>
            <div style="font-weight:bold;" id="total_budgetCurrent">{$total[budgetCurrent]}</div>
            <input type="hidden" id="total_budgetCurrent" value="{$total[budgetCurrent]}"></input>
        </td>
        <td>
            <div style="font-weight:bold;" id="total_budYefPerc"></div>
            <input type="hidden" id="total_budYefPerc"></input>
        </td>
    </tr>
</table>
