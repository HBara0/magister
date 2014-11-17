<tr class="thead">
    <td style="width:20%">{$lang->clientoverdues}:
        <input type='hidden' value="{$budget_data['year']}"  name="financialbudget[year]" />
        <input type='hidden' value="{$budget_data['affid']}"  name="financialbudget[affid]" />
    </td>
    <td style="width:10%">{$lang->legalaction}</td>
    <td style="width:10%">{$lang->oldestunpaidinvoice}</td>
    <td style="width:10%">{$lang->totallocal}</td>
    {$total}
    <td style="width:25%;">{$lang->reason}</td>
    <td style="width:15%;">{$lang->action}</td>

</tr>

