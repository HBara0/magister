<tr>
    <td>
        <input type='hidden' name='actualpurchase[$rowid][inputChecksum]' id="actualpurchase_{$rowid}_inputChecksum" value='{$actualpurchase->inputChecksum}'/>
        <input type='text' id="actualpurchase_{$rowid}_productName" value="{$actualpurchase->productName}" style="width:200px;" readonly/>
        <input type='hidden' id="actualpurchase_{$rowid}_arlsid" name="actualpurchase[$rowid][arlsid]" value="{$actualpurchase->arlsid}"/>
        <input type='hidden' id="actualpurchase_{$rowid}_pid" name="actualpurchase[$rowid][pid]" value="{$actualpurchase->pid}"/>
        <input type='hidden' id="actualpurchase_{$rowid}_daysInStock" name="actualpurchase[$rowid][daysInStock]" value="{$actualpurchase->daysInStock}"/>

    </td>
    <td>
        <input type="text" name="actualpurchase[$rowid][packing]" id="actualpurchase_{$rowid}_packing" value="{$actualpurchase->packing}" style="width:100px;" readonly/>

    </td>
    <td>
        <input type="number"  step="1" name="actualpurchase[$rowid][quantity]" id="actualpurchase_{$rowid}_quantity" value="{$actualpurchase->quantity}" style="width:100px;" readonly/>
    </td>
    <td>
        <input type="number"  step="1" name="actualpurchase[$rowid][totalValue]" id="actualpurchase_{$rowid}_totalValue" value="{$actualpurchase->totalValue}" style="width:100px;" readonly/>
    </td>
    <td>
        <input type="text" id="pickDate_from_stock_{$rowid}" autocomplete="off" tabindex="2" value="{$actualpurchase->estDateOfStockEntry_output}"/>
    </td>
    <td>
        <input type="number"  step="any" name="actualpurchase[$rowid][shelfLife]" id="actualpurchase_{$rowid}_shelfLife" value="{$actualpurchase->shelfLife}" style="width:100px;" readonly/>
    </td>
    <td>
        <input type="text" id="pickDate_from_sale_{$rowid}" autocomplete="off" tabindex="2" value="{$actualpurchase->estDateOfSale_output}"/>
    </td>
</tr>