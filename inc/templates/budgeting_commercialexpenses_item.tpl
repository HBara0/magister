<tr>
    <td style="width:25%"><input type="hidden" name="budgetexps[{$item->beciid}][beciid]" value="{$item->beciid}">
        {$item->title}</td>
        {$column_output}
    <td style="width:12.5%">
        <span id="budgetexps_{$item->beciid}_{$item->becid}_budYefPerc" style="font-weight:bold;width:100%;margin-left:10px;">{$budgetexps[budYefPerc]}</span>
        <input type="hidden" name="budgetexps[{$item->beciid}][budYefPerc]" id="budgetexps_{$item->beciid}_{$item->becid}_budYefPerc" value="{$budgetexps[budYefPerc]}">
    </td>
</tr>