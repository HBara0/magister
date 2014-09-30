<tr style="width:100%;">
    <td style="width:50%">{$item->title}</td>
    <td style="width:10%">
        <input type="hidden" name="budgetinvst[{$item->biiid}][biiid]" value="{$item->biiid}">
        <!--  <input name="budgetinvst[{$item->biiid}][actualPrevTwoYears]" type="text" accept="numeric" id="budgetinvst_{$item->beciid}_{$item->becid}_actualPrevTwoYears" required value="{$budgetinvst[actualPrevTwoYears]}">-->
    </td>
    <td style="width:10%"> <input name="budgetinvst[{$item->biiid}][budgetPrevYear]" type="text" accept="numeric" id="budgetinvst_{$item->biiid}_{$item->bicid}_budgetPrevYear" value="{$budgetinvst[budgetPrevYear]}" required {$readonly}></td>
    <td style="width:10%"> <input name="budgetinvst[{$item->biiid}][yefPrevYear]" type="text" accept="numeric" id="budgetinvst_{$item->biiid}_{$item->bicid}_yefPrevYear" value="{$budgetinvst[yefPrevYear]}" required></td>
    <td style="width:10%"> <input name="budgetinvst[{$item->biiid}][budgetCurrent]" type="text" accept="numeric" id="budgetinvst_{$item->biiid}_{$item->bicid}_budgetCurrent" value="{$budgetinvst[budgetCurrent]}" required></td>
    <td style="width:10%">
        <span id="budgetinvst_{$item->biiid}_{$item->bicid}_percVariation" style="font-weight:bold;width:100%;">{$invest_expenses->budYefPerc}</span>
        <input type="hidden" name="budgetinvst[{$item->biiid}][percVariation]" id="budgetinvst_{$item->biiid}_{$item->bicid}_percVariation" value="{$comadmin_expenses->budYefPerc}"></input>
    </td>
</tr>