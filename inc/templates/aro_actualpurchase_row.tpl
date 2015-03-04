<tr>
    <td>
        <input type='hidden' name='actualpurchase[$rowid][arlid]' value='{$actualpurchase[arlid]}'/>
        <input type='hidden' name='actualpurchase[$rowid][inputChecksum]' id="actualpurchase_{$rowid}_inputChecksum" value='{$actualpurchase[inputChecksum]}'/>
        <input type='text' id="product_noexception_{$rowid}_autocomplete" value="{$actualpurchase[productName]}" autocomplete='off' style="width:200px;"/>
        <input type='hidden' name="actualpurchase[$rowid][pid]" id='product_noexception_{$rowid}_id_output' value="{$actualpurchase[pid]}"/>
    </td>
    <td>
        {$packaging_list}
    </td>
    <td>
        <input type="number"  step="1" name="actualpurchase[$rowid][quantity]" id="actualpurchase_{$rowid}_quantity" value="{$actualpurchase[quantity]}" style="width:100px;" readonly/>
    </td>
    <td>
        <input type="number"  step="1" name="actualpurchase[$rowid][totalValue]" id="actualpurchase_{$rowid}_totalValue" value="{$actualpurchase[totalValue]}" style="width:100px;" readonly/>
    </td>
    <td>
        <input type="text" id="pickDate_from" autocomplete="off" tabindex="2" value="{$actualpurchase[estDateOfStockEntry_output]}"/>
        <input type="hidden" name="actualpurchase[estDateOfStockEntry]" id="altpickDate_from" value="{$actualpurchase[estDateOfStockEntry_formatted]}"/>
    </td>
    <td>
        <input type="number"  step="any" name="actualpurchase[$rowid][shelfLife]" id="actualpurchase_{$rowid}_shelfLife" value="{$actualpurchase[shelfLife]}" style="width:100px;" readonly/>
    </td>
    <td>
        <input type="text" id="pickDate_from" autocomplete="off" tabindex="2" value="{$actualpurchase[estDateOfSale_output]}"/>
        <input type="hidden" name="actualpurchase[estDateOfSale]" id="altpickDate_from" value="{$actualpurchase[estDateOfSale_formatted]}"/>
    </td>
    <td>
        <input type="checkbox" class="deletecheckbox" id="actualpurchase_{$rowid}_todelete" name="actualpurchase[$rowid][todelete]" value="1" label="Delete" oldtitle="If check-box is checked row is deleted">
        <label for="actualpurchase_{$rowid}_todelete"></label></input>
    </td>
</tr>