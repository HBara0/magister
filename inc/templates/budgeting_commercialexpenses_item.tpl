<tr>
    <td style="width:30%"><input type="hidden" name="budgetexps[{$item->beciid}][beciid]" value="{$item->beciid}">
        {$item->title}</td>
        {$column_output}
    <td style="width:10%">
        <span id="budgetexps_{$item->beciid}_{$item->becid}_budYefPerc" style="width:100%;margin-left:10px;">{$budgetexps[budYefPerc_output]}</span>
        <input type="hidden" name="budgetexps[{$item->beciid}][budYefPerc]" id="budgetexps_{$item->beciid}_{$item->becid}_budYefPerc" value="{$budgetexps[budYefPerc]}">
    </td>
</tr>