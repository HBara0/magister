<tr class="thead">
    <td style="width:25%">{$budgettitle}</td>
    {$header_actual}
    <td style="width:12.5%">{$lang->actual}</td>
    <td style="width:12.5%">{$lang->budget}</td>
    <td style="width:12.5%">{$lang->yef}</td>
    {$header_variation}
    {$header_yef}
    <td style="width:12.5%;">{$lang->budget}</td>
    {$header_budyef}
    {$header_percentage}
</tr>

<tr>
    <td style="width:25%"><input name="financialbudget[affid]" value="{$affid}" type="hidden"></td>
    <td style="width:12.5%"><span>{$financialbudget_prev2year}{$investprevyear}</span></td>
    <td style="width:12.5%"><span>{$financialbudget_prevyear}</span></td>
    <td style="width:12.5%"><span>{$financialbudget_prevyear}</span></td>
            {$actual}
    <td style="width:12.5%"><span>{$bud}{$financialbudget_prevyear}</span></td>
    <td style="width:12.5%"><span>{$financialbudget_year}</span><input name="financialbudget[year]" value="{$financialbudget_year}" type="hidden"></td>
    <td style="width:12.5%">{$pl_yefprevyear}</td>
</tr>