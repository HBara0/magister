<tr>
    <td>
        <input type='hidden' name='actualpurchase[$aprowid][inputChecksum]' id="actualpurchase_{$aprowid}_inputChecksum" value='{$actualpurchase->inputChecksum}'/>
        <input type='text' id="actualpurchase_{$aprowid}_productName" value="{$actualpurchase->productName}" style="width:200px;" readonly class="automaticallyfilled-noneditable"/>
        <input type='hidden' id="actualpurchase_{$aprowid}_arlsid" name="actualpurchase[$aprowid][arlsid]" value="{$actualpurchase->arlsid}"/>
        <input type='hidden' id="actualpurchase_{$aprowid}_pid" name="actualpurchase[$aprowid][pid]" value="{$actualpurchase->pid}"/>
        <input type='hidden' id="actualpurchase_{$aprowid}_daysInStock" name="actualpurchase[$aprowid][daysInStock]"/>

    </td>
    <td>
        <input type="text" name="actualpurchase[$aprowid][packingTitle]" id="actualpurchase_{$aprowid}_packingTitle" value="{$actualpurchase->packingTitle}" style="width:100px;" readonly class="automaticallyfilled-noneditable"/>
        <input type="hidden" name="actualpurchase[$aprowid][packing]" id="actualpurchase_{$aprowid}_packing" value="{$actualpurchase->packing}" style="width:100px;"/>
    </td>
    <td>
        <input type="number"  step="1" name="actualpurchase[$aprowid][quantity]" id="actualpurchase_{$aprowid}_quantity" value="{$actualpurchase->quantity}" style="width:100px;" readonly class="automaticallyfilled-noneditable"/>
    </td>
    <td>
        <input type="number"  step="1" name="actualpurchase[$aprowid][totalValue]" id="actualpurchase_{$aprowid}_totalValue" value="{$actualpurchase->totalValue}" style="width:100px;" readonly class="automaticallyfilled-noneditable"/>
    </td>
    <td>
        <input type="text" id="pickDate_stock_{$aprowid}" autocomplete="off" tabindex="2" value="{$actualpurchase->estDateOfStockEntry_output}" class="automaticallyfilled-editable"/>
        <input type="hidden" name="actualpurchase[$aprowid][estDateOfStockEntry]" id="altpickDate_stock_{$aprowid}" value="{$actualpurchase->estDateOfStockEntry_formatted}"/>

    </td>
    <td>
        <input type="number"  step="any" name="actualpurchase[$aprowid][shelfLife]" id="actualpurchase_{$aprowid}_shelfLife" value="{$actualpurchase->shelfLife}" style="width:100px;"/>
    </td>
    <td>
        <input type="text" id="pickDate_sale_{$aprowid}" autocomplete="off" tabindex="2" value="{$actualpurchase->estDateOfSale_output}" class="automaticallyfilled-editable"/>
        <input type="hidden" name="actualpurchase[$aprowid][estDateOfSale]" id="altpickDate_sale_{$aprowid}" value="{$actualpurchase->estDateOfSale_formatted}"/>
    </td>
    <td>
        <input type="text" id="diff_stockandsale_{$aprowid}" name="actualpurchase[$aprowid][diffStockSalesdates]" value="{$actualpurchase->diffStockSalesdates}" readonly class="automaticallyfilled-noneditable"/>
    </td>
</tr>