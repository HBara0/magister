
<tr id="{$rowid}">     
    <td  style=" vertical-align: top;padding:2px; border-bottom: dashed 1px #CCCCCC;" align="left"><input type="hidden" name="budgetline[$rowid][blid]" value="{$blid}"/><input type='text' id='customer_{$rowid}_QSearch' name="budgetline[$rowid][customerName]" value="{$budgetline[customerName]}" autocomplete='off' {$required}/>
        <input type='text' size='3' id='customer_{$rowid}_id_output' disabled='disabled' value="{$budgetline[cid]}" style="display:none;"/>
        <input type='hidden' value="{$budgetline[cid]}" id='customer_{$rowid}_id' name='budgetline[$rowid][cid]' />
        <a href="index.php?module=contents/addentities&type=customer" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a>
        <div id='searchQuickResults_{$rowid}' class='searchQuickResults' style='display:none;'></div>
    </td>

    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;"  align="left">
        <input type='text' name="budgetline[$rowid][pid]" id="product_1{$rowid}_QSearch" value="{$budgetline[productname]}" autocomplete='off' {$required}/>
        <input type='text' size='2' style="width:35px;display:none;" name='product_1{$rowid}_id_output' id='product_1{$rowid}_id_output' disabled='disabled' value="{$budgetline[pid]}"/>
        <input type='hidden' value='{$budgetline[pid]}' id='product_1{$rowid}_id' name='budgetline[$rowid][pid]' />
        <table width="1px;"><tr><td><a href="index.php?module=contents/addproducts" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a> </td></tr></table>
     
        <div id='searchQuickResults_1{$rowid}' class='searchQuickResults' style='display:none;'></div>
    </td>
    <td style="vertical-align:top; padding:2px;border-bottom: dashed 1px #CCCCCC;" align="center">{$saletype_selectlist}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_right">
        <input name="budgetline[$rowid][Quantity]" type="text" id="Qty_{$rowid}" size="10" accept="numeric" value="{$budgetline[Quantity]}"{$required}  /> 
        {$previous_yearsqty} </td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">    <select name="budgetline[$rowid][UoM]" disabled="disabled"> <option value="kg">KG</option></select> </td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><input name="budgetline[$rowid][ammount]" type="text" id="ammount_{$rowid}" size="10" accept="numeric" {$required} value="{$budgetline[ammount]}" autocomplete='off'/>{$previous_yearsamount}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><input name="budgetline[$rowid][ammountperc]" type="text" id="ammount_{$rowid}" size="10" accept="numeric" {$required} value="{$budgetline[ammountperc]}" autocomplete='off'/></td>
    <td style="vertical-align:top; padding:2px; border-bottom:  dashed 1px #CCCCCC;" align="center"><input name="budgetline[$rowid][income]"  value="{$budgetline[income]}" {$required}type="text" id="income_{$rowid}" size="10" accept="numeric" />{$previous_yearsincome}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"> <select name="budgetline[$rowid][originalCurrency]">{$budget_currencylist}</select></td>
    <td style="vertical-align:top; padding:2px;border-bottom: dashed 1px #CCCCCC;" align="center">{$saletype_selectlist}</td>
</tr>

