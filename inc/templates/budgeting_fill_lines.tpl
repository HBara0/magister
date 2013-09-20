<tr id="{$rowid}">
    <td style="vertical-align: top; border-bottom: dashed 1px #CCCCCC; text-align: left;"> 
        <input type="hidden" name="budgetline[$rowid][blid]" value="{$blid}"/>
        <input type='text' id='customer_{$rowid}_QSearch' name="budgetline[$rowid][customerName]" value="{$prevbudgetline[customerName]}" autocomplete='off' {$required}/>
        <input type='text' size='3' id='customer_{$rowid}_id_output' disabled='disabled' value="{$budgetline[cid]}" style="display:none;"/>
        <input type='hidden' value="{$budgetline[cid]}" id='customer_{$rowid}_id' name='budgetline[$rowid][cid]' />
        <a href="index.php?module=contents/addentities&type=customer" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a>
        <div id='searchQuickResults_{$rowid}' class='searchQuickResults' style='display:none;'></div>
    </td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;"  align="left">
        <input type='text' name="budgetline[$rowid][pid]" id="product_1{$rowid}_QSearch" value="{$prevbudgetline[productname]}" autocomplete='off' {$required}/>
        <input type='text' size='2' style="width:35px;display:none;" name='product_1{$rowid}_id_output' id='product_1{$rowid}_id_output' disabled='disabled' value="{$budgetline[pid]}"/>
        <input type='hidden' value='{$budgetline[pid]}' id='product_1{$rowid}_id' name='budgetline[$rowid][pid]' />
        <table width="1px;"><tr><td><a href="index.php?module=contents/addproducts" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a> </td></tr></table>

        <div id='searchQuickResults_1{$rowid}' class='searchQuickResults' style='display:none;'></div>
    </td>
    <td style="vertical-align:top; padding:2px;border-bottom: dashed 1px #CCCCCC;" align="center">{$saletype_selectlist}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_right">
        <input name="budgetline[$rowid][Quantity]" type="text" id="Qty_{$rowid}" size="10" accept="numeric" value="{$budgetline[Quantity]}"{$required}  />
        {$previous_yearsqty} </td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><select name="budgetline[$rowid][UoM]" disabled="disabled"> <option value="kg">KG</option></select> </td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><input name="budgetline[$rowid][amount]" type="text" id="amount_{$rowid}" size="10" accept="numeric" {$required} value="{$budgetline[amount]}" autocomplete='off'/>{$previous_yearsamount}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><input name="budgetline[$rowid][incomeperc]"  placeholder="% of income" type="text" id="amountper_{$rowid}" size="10" accept="numeric" {$required} value="{$budgetline[incomeperc]}" autocomplete='off'/></td>
    <td style="vertical-align:top; padding:2px; border-bottom:  dashed 1px #CCCCCC;" align="center"><input name="budgetline[$rowid][income]"  value="{$budgetline[income]}" {$required}type="text" id="income_{$rowid}" size="10" accept="numeric" />{$previous_yearsincome}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"> <select id="currency_{$rowid}" name="budgetline[$rowid][originalCurrency]">{$budget_currencylist}</select><span id="currency_details_{$rowid}"></span></td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center">{$invoice_selectlist}</td>
</tr>