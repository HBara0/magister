<tr><td colspan="7" class="subtitle">{$category->title}</td></tr>
    {$budgeting_commercialexpenses_item}
<tr>
    <td>{$lang->subtotal} {$category->title}</td>
    <td>
        <input type="hidden" id="subtotal_{$category->becid}_actualPrevTwoYears" value="{$subtotal[actualPrevTwoYears]}">
        <div id="subtotal_{$category->becid}_actualPrevTwoYears">{$subtotal[actualPrevTwoYears]}</div>
    </td>
    <td>
        <input type="hidden" id="subtotal_{$category->becid}_actualPrevYear" value="{$subtotal[actualPrevYear]}">
        <div id="subtotal_{$category->becid}_actualPrevYear">{$subtotal[actualPrevYear]}</div>
    </td>
    <td>
        <div id="subtotal_{$category->becid}_budgetPrevYear">{$subtotal[budgetPrevYear]}</div>
        <input type="hidden" id="subtotal_{$category->becid}_budgetPrevYear" value="{$subtotal[budgetPrevYear]}">
    </td>
    <td>
        <div id="subtotal_{$category->becid}_yefPrevYear">{$subtotal[yefPrevYear]}</div>
        <input type="hidden" id="subtotal_{$category->becid}_yefPrevYear" value="{$subtotal[yefPrevYear]}">
    </td>
    <td>
        <div id="subtotal_{$category->becid}_budgetCurrent">{$subtotal[budgetCurrent]}</div>
        <input type="hidden" id="subtotal_{$category->becid}_budgetCurrent" value="{$subtotal[budgetCurrent]}">
    </td>
    <td>
        <div id="subtotal_{$category->becid}_budYefPerc" style="margin-left:10px;">{$subtotal[budYefPerc]}</div>
        <input type="hidden" id="subtotal_{$category->becid}_budYefPerc" value="{$subtotal[budYefPerc]}">
    </td>
</tr>