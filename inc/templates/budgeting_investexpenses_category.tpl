<tr><td colspan="6" class="subtitle">{$category->title}</td></tr>
    {$budgeting_investexpenses_item}
<tr>
    <td>{$lang->subtotal} {$category->title}</td>
    <td></td>
    <td>
        <div id="subtotal_{$category->becid}_budgetPrevYear" style="font-weight: bold;">{$subtotal[budgetPrevYear]}</div>
        <input type="hidden" id="subtotal_{$category->becid}_budgetPrevYear" value="{$subtotal[budgetPrevYear]}"></input>
    </td>
    <td>
        <div id="subtotal_{$category->becid}_yefPrevYear" style="font-weight: bold;">{$subtotal[yefPrevYear]}</div>
        <input type="hidden" id="subtotal_{$category->becid}_yefPrevYear" value="{$subtotal[yefPrevYear]}"></input>
    </td>
    <td>
        <div id="subtotal_{$category->becid}_budgetCurrent" style="font-weight: bold;">{$subtotal[budgetCurrent]}</div>
        <input type="hidden" id="subtotal_{$category->becid}_budgetCurrent" value="{$subtotal[budgetCurrent]}"></input>
    </td>
    <td>
        <div id="subtotal_{$category->becid}_budYefPerc" style="font-weight: bold;"> {$subtotal[budYefPerc]}</div>
        <input type="hidden" id="subtotal_{$category->becid}_budYefPerc" value="{$subtotal[budYefPerc]}"></input>
    </td>
</tr>

