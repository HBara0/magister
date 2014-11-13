<tr class="thead">
    <td style="width:30%">{$budgettitle}</td>
    <td style="width:10%">{$lang->actual}</td>
    <td style="width:10%">{$lang->actual}</td>
    <td style="width:10%">{$lang->yef}</td>
    {$header_variations}
    <td style="width:10%">{$lang->budget}</td>
    {$header_budyef}
</tr>

<tr>
    <td style="width:30%"><input name="financialbudget[affid]" value="{$affid}" type="hidden">
        <input name="financialbudget[year]" value="{$financialbudget_year}" type="hidden"></td>
    <td style="width:10%"><span>{$financialbudget_prev3year}</span></td>
    <td style="width:10%"><span>{$financialbudget_prev2year}</span></td>
    <td style="width:10%"><span>{$financialbudget_prevyear}</span></td>
            {$header_variations_years}
    <td style="width:10%"><span>{$bud}{$financialbudget_year}</span></td>

<!-- <td style="width:12.5%"><span>{}</span><input name="financialbudget[year]" value="{$financialbudget_year}" type="hidden"></td> -->
</tr>