<tr style="width:100%;">
    <td style="width:50%">{$item->title}</td>
    <td style="width:10%">
        <input type="hidden" name="budgetexps[{$item->beciid}][beciid]" value="{$item->beciid}">
        <input name="budgetexps[{$item->beciid}][actualPrevTwoYears]" type="number" accept="numeric" step="any" id="budgetexps_{$item->beciid}_{$item->becid}_actualPrevTwoYears" required value="{$budgetexps[actualPrevTwoYears]}">
    </td>
    <td style="width:10%"> <input name="budgetexps[{$item->beciid}][budgetPrevYear]" type="number" step="any" accept="numeric" id="budgetexps_{$item->beciid}_{$item->becid}_budgetPrevYear" value="{$budgetexps[budgetPrevYear]}" required {$readonly}></td>
    <td style="width:10%"> <input name="budgetexps[{$item->beciid}][yefPrevYear]" type="number" step="any" accept="numeric" id="budgetexps_{$item->beciid}_{$item->becid}_yefPrevYear" value="{$budgetexps[yefPrevYear]}" required></td>
    <td style="width:10%"> <input name="budgetexps[{$item->beciid}][budgetCurrent]" type="number" step="any" accept="numeric" id="budgetexps_{$item->beciid}_{$item->becid}_budgetCurrent" value="{$budgetexps[budgetCurrent]}" required></td>
    <td style="width:10%">
        <span id="budgetexps_{$item->beciid}_{$item->becid}_budYefPerc" style="font-weight:bold;width:100%;">{$budgetexps[budYefPerc]}</span>
        <input type="hidden" name="budgetexps[{$item->beciid}][budYefPerc]" id="budgetexps_{$item->beciid}_{$item->becid}_budYefPerc" value="{$budgetexps[budYefPerc]}">
    </td>
</tr>