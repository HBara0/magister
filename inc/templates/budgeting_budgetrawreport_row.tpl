<tr id="{$budgetline[blid]}" class="{$rowclass}">
    <td class="smalltext" style="opacity:0">{$budgetline[blid]}</td>

    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"> <a href="./users.php?action=profile&amp;uid={$budget[managerid]}" target="_blank">{$budget[manager]}</a></td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$customername}</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$budget[affiliate]}</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$budgetline[customerCountry]}</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$budget[supplier]}</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$budgetline[saleType]}</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$budgetline[segment]}</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$budgetline[product]}</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$budgetline[quantity]}</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$budgetline[uom]}</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="right" class="border_left">{$budgetline[unitPrice]}</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="right" class="border_left">{$budgetline[amount]}</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="right" class="border_left">{$budgetline[income]}</td>
    {$localincome_cell}
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$budgetline[s1Perc]}%</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$budgetline[s2Perc]}%</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$budgetline[interCompanyPurchase_output]}</td>
    <td class="smalltext" style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$budgetline[purchasingEntity]}</td>
</tr>