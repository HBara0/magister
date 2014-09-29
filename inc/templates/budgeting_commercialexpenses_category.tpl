<tr><td colspan="6" class="subtitle">{$category->title}</td></tr>
    {$budgeting_commercialexpenses_item}
<tr>
    <td>{$lang->subtotal} {$category->title}</td>
    <td>
        <input type="hidden" id="subtotal_{$category->becid}_actualPrevTwoYears" value="{$subtotal[actualPrevTwoYears]}"></input>
        <div id="subtotal_{$category->becid}_actualPrevTwoYears">{$subtotal[actualPrevTwoYears]}</div>
    </td>
    <td>
        <div id="subtotal_{$category->becid}_budgetPrevYear">{$subtotal[budgetPrevYear]}</div>
        <input type="hidden" id="subtotal_{$category->becid}_budgetPrevYear" value="{$subtotal[budgetPrevYear]}"></input>
    </td>
    <td>
        <div id="subtotal_{$category->becid}_yefPrevYear">{$subtotal[yefPrevYear]}</div>
        <input type="hidden" id="subtotal_{$category->becid}_yefPrevYear" value="{$subtotal[yefPrevYear]}"></input>
    </td>
    <td>
        <div id="subtotal_{$category->becid}_budgetCurrent">{$subtotal[budgetCurrent]}</div>
        <input type="hidden" id="subtotal_{$category->becid}_budgetCurrent" value="{$subtotal[budgetCurrent]}"></input>
    </td>
    <td>
        <div id="subtotal_{$category->becid}_budYefPerc"> {$subtotal[budYefPerc]}</div>
        <input type="hidden" id="subtotal_{$category->becid}_budYefPerc" value="{$subtotal[budYefPerc]}"></input>
    </td>
</tr>

