<tr id="{$rownums}">
    <td style="vertical-align: top; border-bottom: dashed 1px #CCCCCC; text-align: left;">
        <input type="hidden" name="budgetline[{$rowid}][inputCheckSum]" value="{$rowid}"/>
        <input type="hidden" name="budgetline[{$rowid}][yeflid]" value="{$budgetline['yeflid']}"/>
        <input type="hidden" name="budgetline[{$rowid}][blid]" value="{$budgetline['blid']}"/>
        <input type='text' id='customer_noexception_{$rowid}_autocomplete' name="budgetline[{$rowid}][customerName]" {$disabledattrs[cid]} value="{$budgetline[customerName]}" autocomplete='off' {$required}/>
        <input type='text' {$readonly} size='3' id='customer_noexception_{$rowid}_id_output' disabled='disabled' value="{$budgetline[cid]}" style="display:none;"/>
        <input type='hidden' value="{$budgetline[cid]}" id='customer_noexception_{$rowid}_id' name='budgetline[{$rowid}][cid]' />
        <input type="hidden" id="budgetline_{$rowid}_altCid" name="budgetline[{$rowid}][altCid]" value="{$budgetline[altCid]}"/>
        {$budgetline[alternativecustomer]} {$previous_yeflid}
        <span style="padding:8px;"><br /><input {$readonly} type="checkbox" name="budgetline[{$rowid}][unspecifiedCustomer]" title="{$lang->unspecifiedcust}" {$disabledattrs[unspecifiedCustomer]} value="1" {$checked_checkboxes[$rowid][unspecifiedCustomer]} id="budgetline_{$rowid}_unspecifiedCustomer"/>{$lang->unspecifiedcust}</span>
            {$previous_customercountry}
        <div id="budgetline_{$rowid}_unspecifiedCustomer_country" style="display:{$display};width:100%">
            <span style="display:inline-block;width:10%;"> in</span> <div style="display:inline-block;width:85%">{$countries_selectlist}</div>
        </div>
        <div id='searchQuickResults_customer_{$rowid}' class='searchQuickResults' style='display:none;'></div>
        {$alert_div}

    </td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;"  align="left">
        <input type='text' {$disabledattrs[pid]}  name="budgetline[{$rowid}][pid]" id="product_noexception_{$rowid}_autocomplete" value="{$budgetline[productName]}" autocomplete='off' />
        <input type='text' size='2' style="width:35px;display:none;" name='product_{$rowid}_id_output' id='product_noexception_{$rowid}_id_output' disabled='disabled' value="{$budgetline[pid]}"/>
        <input type='hidden' value='{$budgetline[pid]}' id='product_noexception_{$rowid}_id' name='budgetline[{$rowid}][pid]' />
        <div id='searchQuickResults_product_{$rowid}' class='searchQuickResults' style='display:none;'></div>
        {$budgetline[alternativeproduct]}
        {$segments_selectlist}
    </td>
    <td style="vertical-align:top; padding:2px;border-bottom: dashed 1px #CCCCCC;" align="center">{$saletype_selectlist}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_right"><input id="october_{$rowid}" type="hidden" name="budgetline[{$rowid}][october]" value="{$budgetline[october]}"><input name="budgetline[{$rowid}][octoberqty]" data-perc="october_{$rowid}" data-quantity="{$rowid}" type="text" id="octoberqty_{$rowid}" size="10" accept="numeric" required="required" value="{$budgetline[octoberqty]}" autocomplete='off'/><span style="color:red" id="alertpercentage_{$rowid}"></span></td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_right"><input id="november_{$rowid}" type="hidden" name="budgetline[{$rowid}][november]" value="{$budgetline[november]}"><input name="budgetline[{$rowid}][novemberqty]" data-perc="november_{$rowid}" data-quantity="{$rowid}"  type="text" id="novemberqty_{$rowid}" size="10" accept="numeric" required="required" value="{$budgetline[novemberqty]}" autocomplete='off'/></td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_right"><input id="december_{$rowid}" type="hidden" name="budgetline[{$rowid}][december]" value="{$budgetline[december]}"><input name="budgetline[{$rowid}][decemberqty]" data-perc="december_{$rowid}" data-quantity="{$rowid}"  type="text" id="decemberqty_{$rowid}" size="10" accept="numeric" required="required" value="{$budgetline[decemberqty]}" autocomplete='off'/></td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_right">
        <input name="budgetline[{$rowid}][quantity]" type="text" id="Qty_{$rowid}" size="10" accept="numeric" data-rowid="{$rowid}" data-totalquantity="{$rowid}" data-name="{$lang->quantity}" data-max="{$maxbudgetline[quantity]}" value="{$budgetline[quantity]}"{$required} readonly="readonly">
        {$previous_yearsqty}
    </td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><select name="budgetline[{$rowid}][UoM]" disabled="disabled"><option value="kg">KG</option></select></td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><input name="budgetline[{$rowid}][unitPrice]" type="text" id="unitprice_{$rowid}" size="10" accept="numeric" {$required} value="{$budgetline[unitPrice]}" data-rowid="{$rowid}"  data-max="{$maxbudgetline[unitPrice]}" data-name="{$lang->unitprice}" autocomplete='off' />{$prevyear_unitprice}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$budget_currencylist}<span id="currency_details_{$rowid}"></span></td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><input name="budgetline[{$rowid}][amount]" type="text" id="amount_{$rowid}" size="10" accept="numeric" {$required} value="{$budgetline[amount]}" data-rowid="{$rowid}" data-name="{$lang->amount}" data-max="{$maxbudgetline[amount]}" autocomplete='off' />{$previous_yearsamount}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left"><input name="budgetline[{$rowid}][incomePerc]"  type="text" id="amountper_{$rowid}" size="10" accept="numeric" {$required} value="{$budgetline[incomePerc]}"  autocomplete='off' />{$prevyear_incomeperc}</td>
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center"><input name="budgetline[{$rowid}][income]"  value="{$budgetline[income]}" data-rowid="{$rowid}" data-name="{$lang->income}" data-max="{$maxbudgetline[income]}" {$required}type="text" id="income_{$rowid}" size="10" accept="numeric" />{$previous_yearsincome}</td>
        {$hidden_colcells[localincomeper_row]}
        {$hidden_colcells[localincome_row]}
        {$hidden_colcells[remainingcommaff_header_row]}
    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center">{$purchasingentity_selectlist}</td>

    <td style="vertical-align:top; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$purchasefromaff}{$frombudgetline}
    </td>
</tr>