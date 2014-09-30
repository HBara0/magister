<tr style="width:100%;">
    <td style="width:50%">{$item->title}</td>
    <td style="width:10%">
        <input type="hidden" name="budgetinvst[{$item->biiid}][biiid]" value="{$item->biiid}">
        <!--  <input name="budgetinvst[{$item->biiid}][actualPrevTwoYears]" type="text" accept="numeric" id="budgetinvst_{$item->beciid}_{$item->becid}_actualPrevTwoYears" required value="{$budgetinvst[actualPrevTwoYears]}">-->
    </td>
    {$column_output}



    <td style="width:10%">
        <span id="budgetinvst_{$item->biiid}_{$item->bicid}_percVariation" style="font-weight:bold;width:100%;">{$invest_expenses->percVariation}</span>
        <input type="hidden" name="budgetinvst[{$item->biiid}][percVariation]" id="budgetinvst_{$item->biiid}_{$item->bicid}_percVariation" value="{$invest_expenses->percVariation}"></input>
    </td>
</tr>