<tr><td colspan="7" class="subtitle">{$category->title}</td></tr>
    {$budgeting_investexpenses_item}
<tr>
    <td style="width:25%;">{$lang->subtotal} {$category->title}</td>
    <td style="width:12.5%;">
        <div id="subtotal_{$category->bicid}_actualPrevThreeYears" style="font-weight: bold;">{$subtotal[actualPrevThreeYears]}</div>
        <input type="hidden" id="subtotal_{$category->bicid}_actualPrevThreeYears" value="{$subtotal[actualPrevThreeYears]}"></input>
    </td>
    <td style="width:12.5%;">
        <div id="subtotal_{$category->bicid}_actualPrevTwoYears" style="font-weight: bold;">{$subtotal[actualPrevTwoYears]}</div>
        <input type="hidden" id="subtotal_{$category->bicid}_actualPrevTwoYears" value="{$subtotal[actualPrevTwoYears]}"></input>
    </td>
    <!-- <td style="width:12.5%;">
        <div id="subtotal_{$category->bicid}_actualPrevYear" style="font-weight: bold;">{$subtotal[actualPrevYear]}</div>
        <input type="hidden" id="subtotal_{$category->bicid}_actualPrevYear" value="{$subtotal[actualPrevYear]}"></input>
    </td>
    <td style="width:12.5%;">
        <div id="subtotal_{$category->bicid}_budgetPrevYear" style="font-weight: bold;">{$subtotal[budgetPrevYear]}</div>
        <input type="hidden" id="subtotal_{$category->bicid}_budgetPrevYear" value="{$subtotal[budgetPrevYear]}"></input>
    </td>-->
    <td style="width:12.5%;">
        <div id="subtotal_{$category->bicid}_yefPrevYear" style="font-weight: bold;">{$subtotal[yefPrevYear]}</div>
        <input type="hidden" id="subtotal_{$category->bicid}_yefPrevYear" value="{$subtotal[yefPrevYear]}"></input>
    </td>
    <!-- <td style="width:12.5%;">
        <div id="subtotal_{$category->bicid}_percVariation" style="font-weight: bold;"> {$subtotal[percVariation]}</div>
        <input type="hidden" id="subtotal_{$category->bicid}_percVariation" value="{$subtotal[percVariation]}"></input>
    </td>-->
    <td style="width:12.5%;">
        <div id="subtotal_{$category->bicid}_budgetCurrent" style="font-weight: bold;">{$subtotal[budgetCurrent]}</div>
        <input type="hidden" id="subtotal_{$category->bicid}_budgetCurrent" value="{$subtotal[budgetCurrent]}"></input>
    </td>
</tr>

