<tr id="{$plrowid}">
    <td>
        <input type='hidden' name='productline[$plrowid][arlid]' value='{$productline[arlid]}'/>
        <input type='hidden' name='productline[$plrowid][inputChecksum]' value='{$productline[inputChecksum]}'/>
        <input type='text' id="product_noexception_{$plrowid}_autocomplete" value="{$productline[productName]}" autocomplete='off' {$required}/>
        <input type='hidden' name="productline[$plrowid][pid]" id='product_noexception_{$plrowid}_id_output' disabled='disabled' value="{$productline[pid]}"/>
        {$segments_selectlist}
    </td>
    <td>
        {$packaging_list}
    </td>
    <td>
        <input type="number"  step="1" name="productline[$plrowid][quantity]" id="productline_{$plrowid}_quantity" value="{$productline[quantity]}"/>
    </td>
    <td>
        {$uom_list}
    </td>
    <td>
        <input type="number"  step="1" name="productline[$plrowid][daysInStock]" id="productline_{$plrowid}_daysInStock" value="{$productline[daysInStock]} {$disabled_fields[daysInStock]}"/>
    </td>
    <td>
        <input type="number"  step="any" name="productline[$plrowid][qtyPotentiallySold]" id="productline_{$plrowid}_qtyPotentiallySold" value="{$productline[qtyPotentiallySold]}" {$disabled_fields[qtyPotentiallySold]}/>
    </td>
    <td>
        <input type="number"  step="any" name="productline[$plrowid][qtyPotentiallySoldPerc]" id="productline_{$plrowid}_qtyPotentiallySoldPerc" value="{$productline[qtyPotentiallySoldPerc]}"/>
    </td>
    <td>
        <input type="number"  step="any" name="productline[$plrowid][intialPrice]" id="productline_{$plrowid}_intialPrice" value="{$productline[intialPrice]}"/>
    </td>
    <td>
        <input type="text" name="productline[$plrowid][affBuyingPrice]" id="productline_{$plrowid}_affBuyingPrice" value="{$productline[affBuyingPrice]}" readonly/>
    </td>
    <td>
        <input type="text" name="productline[$plrowid][totalBuyingValue]" id="productline_{$plrowid}_totalBuyingValue" value="{$productline[totalBuyingValue]}" readonly/>
    </td>
    <td>
        <input type="number"  step="any" name="productline[$plrowid][costPrice]" id="productline_{$plrowid}_costPrice" value="{$productline[costPrice]}"/>
    </td>
    <td>
        <input type="text" name="productline[$plrowid][costPriceAtRiskRatio]" id="productline_{$plrowid}_costPriceAtRiskRatio" value="{$productline[costPriceAtRiskRatio]}" readonly/>
    </td>
    <td>
        <input type="number"  step="any" name="productline[$plrowid][sellingPrice]" id="productline_{$plrowid}_sellingPrice" value="{$productline[sellingPrice]}"/>
    </td>
    <td>
        <input type="text" name="productline[$plrowid][grossMarginAtRiskRatio]" id="productline_{$plrowid}_grossMarginAtRiskRatio"  value="{$productline[grossMarginAtRiskRatio]}" readonly/>
    </td>
    <td>
        <input type="text" name="productline[$plrowid][netMarginAff]" id="productline_{$plrowid}_netMarginAff" value="{$productline[netMarginAff]}" readonly/>
    </td>
    <td>
        <input type="text" name="productline[$plrowid][netMarginIntermed]" id="productline_{$plrowid}_netMarginIntermed" value="{$productline[netMarginIntermed]}" readonly/>
    </td>
    <td>
        <input type="text" name="productline[$plrowid][netMarginPerc]" id="productline_{$plrowid}_netMarginPerc" value="{$productline[netMarginPerc]}" readonly/>
    </td>
</tr>