<tr id="{$plrowid}">
    <td>
       <!-- <input type='hidden' name='productline[$plrowid][isTriggered]' id='productline_{$plrowid}_isTriggered' value='0'/>-->
        <input type='hidden' name='productline[$plrowid][arlid]' value='{$productline[arlid]}'/>
        <input type='hidden' name='productline[$plrowid][inputChecksum]' value='{$productline[inputChecksum]}' id="productline_{$plrowid}_inputChecksum"/>
        <input type='text' id="product_noexception_{$plrowid}_autocomplete" value="{$productline[productName]}" autocomplete='off' {$required}/>
        <input type='hidden' name="productline[$plrowid][pid]" id='product_noexception_{$plrowid}_id_output' value="{$productline[pid]}"/>
        {$segments_selectlist}
    </td>
    <td>
        {$packaging_list}
    </td>
    <td>
        <input type="number"  step="1" name="productline[$plrowid][quantity]" id="productline_{$plrowid}_quantity" value="{$productline[quantity]}" style="width:100px;"/>
    </td>
    <td>
        {$uom_list}
    </td>
    <td>
        <input style="width:50px;" type="number"  step="1" name="productline[$plrowid][daysInStock]" id="productline_{$plrowid}_daysInStock" value="{$productline[daysInStock]}" {$disabled_fields[daysInStock]} style="width:100px;"/>
    </td>
    <td>
        <input type="number"  step="any" name="productline[$plrowid][qtyPotentiallySold]" id="productline_{$plrowid}_qtyPotentiallySold" value="{$productline[qtyPotentiallySold]}" {$disabled_fields[qtyPotentiallySold]} style="width:100px;"/>

    </td>
    <td>
        <input type="number"  step="any" name="productline[$plrowid][qtyPotentiallySoldPerc]" id="productline_{$plrowid}_qtyPotentiallySoldPerc" value="{$productline[qtyPotentiallySoldPerc]}" style="width:70px;" readonly class="automaticallyfilled-noneditable"/>
    </td>
    <td>
        <input type="number"  step="any" name="productline[$plrowid][intialPrice]" id="productline_{$plrowid}_intialPrice" value="{$productline[intialPrice]}" style="width:100px;"/>
    </td>
    <td>
        <input type="number" step="any" name="productline[$plrowid][fees]" id="productline_{$plrowid}_fees" value="{$productline[fees]}" readonly style="width:100px;" class="automaticallyfilled-noneditable"/>
    </td>
    <td>
        <input type="text" name="productline[$plrowid][affBuyingPrice]" id="productline_{$plrowid}_affBuyingPrice" value="{$productline[affBuyingPrice]}" readonly style="width:100px;" class="automaticallyfilled-noneditable"/>
    </td>
    <td>
        <input type="text" name="productline[$plrowid][totalBuyingValue]" id="productline_{$plrowid}_totalBuyingValue" value="{$productline[totalBuyingValue]}" readonly style="width:100px;" class="automaticallyfilled-noneditable"/>
    </td>
    <td>
        <input type="number"  step="any" name="productline[$plrowid][costPrice]" id="productline_{$plrowid}_costPrice" value="{$productline[costPrice]}" style="width:100px;"/>
    </td>
    <td>
        <input type="text" name="productline[$plrowid][costPriceAtRiskRatio]" id="productline_{$plrowid}_costPriceAtRiskRatio" value="{$productline[costPriceAtRiskRatio]}" readonly style="width:70px;" class="automaticallyfilled-noneditable"/>
    </td>
    <td>
        <input type="number"  step="any" name="productline[$plrowid][sellingPrice]" id="productline_{$plrowid}_sellingPrice" value="{$productline[sellingPrice]}" style="width:100px;"/>
    </td>
    <td>
        <input type="text" name="productline[$plrowid][grossMarginAtRiskRatio]" id="productline_{$plrowid}_grossMarginAtRiskRatio"  value="{$productline[grossMarginAtRiskRatio]}" readonly style="width:70px;" class="automaticallyfilled-noneditable"/>
    </td>
    <td>
        <input type="text" name="productline[$plrowid][netMargin]" id="productline_{$plrowid}_netMargin" value="{$productline[netMargin]}" readonly style="width:100px;" class="automaticallyfilled-noneditable"/>
    </td>
    <td>
        <input type="text" name="productline[$plrowid][netMarginPerc]" id="productline_{$plrowid}_netMarginPerc" value="{$productline[netMarginPerc]}" readonly style="width:70px;" class="automaticallyfilled-noneditable"/>
    </td>
    <td>
        <input type="checkbox" class="deletecheckbox" id="productline_{$plrowid}_todelete" name="productline[$plrowid][todelete]" value="1" label="Delete" oldtitle="If check-box is checked row is deleted">
        <label for="productline_{$plrowid}_todelete"></label></input>
    </td>
</tr>