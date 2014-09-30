<tr class="thead">
    <td style="width:50%">Company name:</td>
    {$header_actual}
    <td style="width:10%">{$lang->budget}</td>
    <td style="width:10%">{$lang->yef}</td>
    <td style="width:10%">{$lang->budget}</td>
    {$header_percentage}
</tr>

<tr style="width:100%">
    <td style="width:50%"><input name="financialbudget[affid]" value="{$affid}" type="hidden"></td>
    <td style="width:10%"><span>{$financialbudget_prev2year}</span></td>
    <td style="width:10%"><span>{$financialbudget_prevyear}</span></td>
    <td style="width:10%"><span>{$financialbudget_prevyear}</span></td>
    <td style="width:10%"><span>{$financialbudget_year}</span><input name="financialbudget[year]" value="{$financialbudget_year}" type="hidden"></td>
    <td style="width:10%"></td>
</tr>