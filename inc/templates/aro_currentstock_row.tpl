<tr>
    <td>
        <input type='hidden' name='currentstock[$csrowid][inputChecksum]' id="currentstock_{$csrowid}_inputChecksum" value='{$currentstock->inputChecksum}'/>
        <input type='text' id="currentstock_{$csrowid}_productName" value="{$currentstock->productName}" style="width:200px;" readonly/>
        <input type='hidden' id="currentstock_{$csrowid}_arlsid" name="currentstock[$csrowid][arlsid]" value="{$currentstock->arlsid}"/>
        <input type='hidden' id="currentstock_{$csrowid}_pid" name="currentstock[$csrowid][pid]" value="{$currentstock->pid}"/>
    </td>
    <td>
        <input type="text" name="currentstock[$csrowid][packingTitle]" id="currentstock_{$csrowid}_packingTitle" value="{$currentstock->packingTitle}" style="width:100px;" readonly/>
        <input type="hidden" name="currentstock[$csrowid][packing]" id="currentstock_{$csrowid}_packing" value="{$currentstock->packing}" style="width:100px;"/>
    </td>
    <td>
        <input type="number"  step="1" name="currentstock[$csrowid][quantity]" id="currentstock_{$csrowid}_quantity" value="{$currentstock->quantity}" style="width:100px;" readonly/>
    </td>
    <td>
        <input type="number"  step="1" name="currentstock[$csrowid][stockValue]" id="currentstock_{$csrowid}_stockValue" value="{$currentstock->stockValue}" style="width:100px;" readonly/>
    </td>
    <td>
        <input type="text" id="pickDate_stock_{$csrowid}" autocomplete="off" tabindex="2" value="{$currentstock->dateOfStockEntry_output}"/>
        <input type="hidden" name="currentstock[$csrowid][dateOfStockEntry]" id="altpickDate_stock_{$csrowid}" value="{$currentstock->dateOfStockEntry_formatted}"/>

    </td>
    <td>
        <input type="number"  step="any" name="currentstock[$csrowid][expiryDate]" id="currentstock_{$csrowid}_expiryDate" value="{$currentstock->expiryDate}" style="width:100px;"/>
    </td>
    <td>
        <input type="text" id="pickDate_sale_{$csrowid}" autocomplete="off" tabindex="2" value="{$currentstock->estDateOfSale_output}"/>
        <input type="hidden" name="currentstock[$csrowid][estDateOfSale]" id="altpickDate_sale_{$csrowid}" value="{$currentstock->estDateOfSale_formatted}"/>
    </td>
</tr>