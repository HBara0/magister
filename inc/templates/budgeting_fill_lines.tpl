<tr id="{$rowid}">
    <td style="vertical-align: top; border-bottom: dashed 1px #CCCCCC; text-align: left;">
        <input type="hidden" name="budgetline[$rowid][blid]" value="{$prev_budgetline[blid]}"/>
        <input type='text' id='customer_noexception_{$rowid}_QSearch' name="budgetline[$rowid][customerName]" value="{$budgetline[customerName]}" autocomplete='off' {$required}/>
        <input type='text' size='3' id='customer_noexception_{$rowid}_id_output' disabled='disabled' value="{$budgetline[cid]}" style="display:none;"/>
        <input type='hidden' value="{$budgetline[cid]}" id='customer_noexception_{$rowid}_id' name='budgetline[$rowid][cid]' />
        <input type='hidden' value="{$budgetline[altCid]}" id='altCid' name='budgetline[$rowid][altCid]' />
        <input type="hidden"  id="budgetline[altCid]" name="budgetline[altCid]" value="{$prev_budgetline[altCid]}"/>
            {$budgetline[alternativecustomer]}
        <span style="padding:8px;"><br /><input type="checkbox" name="budgetline[$rowid][unspecifiedCustomer]" title="{$lang->unspecifiedcust}" value="1"/>{$lang->unspecifiedcust}</span>
        <div id='searchQuickResults_customer_{$rowid}' class='searchQuickResults' style='display:none;'></div>
    </td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;"  align="left">
        <input type='text' name="budgetline[$rowid][pid]" id="product_noexception_{$rowid}_QSearch" value="{$budgetline[productName]}" autocomplete='off' {$required}/>
        <input type='text' size='2' style="width:35px;display:none;" name='product_{$rowid}_id_output' id='product_noexception_{$rowid}_id_output' disabled='disabled' value="{$budgetline[pid]}"/>
        <input type='hidden' value='{$budgetline[pid]}' id='product_noexception_{$rowid}_id' name='budgetline[$rowid][pid]' />
        <div id='searchQuickResults_product_{$rowid}' class='searchQuickResults' style='display:none;'></div>
    </td>
    <td style="vertical-align:top; padding:2px;border-bottom: dashed 1px #CCCCCC;" align="center">{$saletype_selectlist}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_right">
        <input name="budgetline[$rowid][quantity]" type="text" id="Qty_{$rowid}" size="10" accept="numeric" value="{$budgetline[quantity]}"{$required}  />
        {$previous_yearsqty}
    </td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><select name="budgetline[$rowid][UoM]" disabled="disabled"><option value="kg">KG</option></select></td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><input name="budgetline[$rowid][unitPrice]" type="text" id="unitprice_{$rowid}" size="10" accept="numeric" {$required} value="{$budgetline[unitPrice]}" autocomplete='off'/>{$prevyear_unitprice}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><input name="budgetline[$rowid][amount]" type="text" id="amount_{$rowid}" size="10" accept="numeric" {$required} value="{$budgetline[amount]}" autocomplete='off'/>{$previous_yearsamount}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><input name="budgetline[$rowid][incomePerc]"  placeholder="% of income" type="text" id="amountper_{$rowid}" size="10" accept="numeric" {$required} value="{$budgetline[incomePerc]}" autocomplete='off'/>{$prevyear_incomeperc}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input name="budgetline[$rowid][income]"  value="{$budgetline[income]}" {$required}type="text" id="income_{$rowid}" size="10" accept="numeric" />{$previous_yearsincome}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"> <select id="currency_{$rowid}" name="budgetline[$rowid][originalCurrency]">{$budget_currencylist}</select><span id="currency_details_{$rowid}"></span></td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center">{$invoice_selectlist}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><input name="budgetline[$rowid][s1Perc]" type="text" id="s1perc_{$rowid}" size="10" accept="numeric" {$required} value="{$budgetline[s1Perc]}" autocomplete='off'/></td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><input name="budgetline[$rowid][s2Perc]" type="text" id="s2perc_{$rowid}" size="10" accept="numeric" {$required} value="{$budgetline[s2Perc]}" autocomplete='off'/></td>
</tr>