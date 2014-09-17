<tr id="{$customerproduct_rowid}">
    <td>
        <input type='text' id="product_sectionexception_parent{$customer_rowid}_id{$customerproduct_rowid}_autocomplete" autocomplete='off' value="{$customer[$customer_rowid][products][$customerproduct_rowid][productName]}" name="customers[$customer_rowid][products][$customerproduct_rowid][productName]"/>
        <input type='text' size='2' id='product_parent{$customer_rowid}_id{$customerproduct_rowid}_id_output' value="{$customer[$customer_rowid][products][$customerproduct_rowid][pid]}" disabled="disabled"/>
        <input type='hidden' id='product_parent{$customer_rowid}_id{$customerproduct_rowid}_id' name='customers[$customer_rowid][products][$customerproduct_rowid][pid]' value="{$customer[$customer_rowid][products][$customerproduct_rowid][pid]}"/><div id='searchQuickResults_product_sectionexception_parent{$customer_rowid}_id{$customerproduct_rowid}' class='searchQuickResults' style='display:none;'></div>
    </td>
    <td><input type="text" size="5" name="customers[$customer_rowid][products][$customerproduct_rowid][firstOrderQty]" id="product_parent{$customer_rowid}_id{$customerproduct_rowid}_firstOrderQty" accept="numeric" value="{$stockorder_data[customers][$key][firstOrderQty]}"/></td>
    <td><input type="text" id="pickDate_parent{$customer_rowid}_id{$customerproduct}_firstOrderDate" autocomplete="off" value="{$stockorder_data[customers][$key][firstOrderDate_output]}" /><input type="hidden" name="customers[$customer_rowid][products][$customerproduct_rowid][firstOrderDate]" id="altpickDate_parent{$customer_rowid}_id{$customerproduct}_firstOrderDate" value="{$stockorder_data[customers][$key][firstOrderDate]}"/></td>
    <td><input type="text" size="5" name="customers[$customer_rowid][products][$customerproduct_rowid][numOrders]" id="product_parent{$customer_rowid}_id{$customerproduct_rowid}_numOrders" accept="numeric" value="{$stockorder_data[customers][$key][numOrders]}"/></td>
    <td><input type="text" size="5" name="customers[$customer_rowid][products][$customerproduct_rowid][quantityPerNextOrder]" id="product_parent{$customer_rowid}_id{$customerproduct_rowid}_quantityPerNextOrder" accept="numeric" value="{$stockorder_data[customers][$key][quantityPerNextOrder]}"/></td>
    <td>
        <select name="customers[$customer_rowid][products][$customerproduct_rowid][nextOrdersInterval]" id="product_parent{$customer_rowid}_id{$customerproduct_rowid}_nextOrdersInterval">
            <option value="1">{$lang->dailyorder}</option>
            <option value="7">{$lang->weeklyorder}</option>
            <option value="30">{$lang->monthlyorder}</option>
            <option value="91">{$lang->quarterlyorder}</option>
            <option value="182">{$lang->semiannualorder}</option>
            <option value="365">{$lang->yearlyorder}</option>
        </select>
    </td>
    <td><input type="text" size="5" id="product_parent{$customer_rowid}_id{$customerproduct_rowid}_expectedQuantity" name="customers[$customer_rowid][products][$customerproduct_rowid][expectedQuantity]" accept="numeric" value="{$stockorder_data[customers][$key][expectedQuantity]}" disabled="disabled"/></td>
</tr>
