<tr>
    <td>
        <input type='hidden' name='currentstock[$csrowid][inputChecksum]' id="currentstock_{$csrowid}_inputChecksum" value='{$currentstock->inputChecksum}' required="required"/>
        <input type='text' id="currentstock_{$csrowid}_productName" value="{$currentstock->productName}" style="width:200px;" readonly class="automaticallyfilled-noneditable"/>
        <input type='hidden' id="currentstock_{$csrowid}_arlsid" name="currentstock[$csrowid][arcssid]" value="{$currentstock->arcssid}"/>
        <input type='hidden' id="currentstock_{$csrowid}_pid" name="currentstock[$csrowid][pid]" value="{$currentstock->pid}"/>
    </td>
    <td>
        <input type="text" name="currentstock[$csrowid][packingTitle]" id="currentstock_{$csrowid}_packingTitle" value="{$currentstock->packingTitle}" style="width:100px;" readonly required="required" class="automaticallyfilled-noneditable"/>
        <input type="hidden" name="currentstock[$csrowid][packing]" id="currentstock_{$csrowid}_packing" value="{$currentstock->packing}" style="width:100px;"/>
    </td>
    <td>
        <input type="number"  step="1" name="currentstock[$csrowid][quantity]" id="currentstock_{$csrowid}_quantity" value="{$currentstock->quantity}" style="width:100px;" required="required"/>
    </td>
    <td>
        <input type="number"  step="1" name="currentstock[$csrowid][stockValue]" id="currentstock_{$csrowid}_stockValue" value="{$currentstock->stockValue}" style="width:100px;"/>
    </td>
    <td>
        <input type="text" id="pickDate_currentstock_{$csrowid}" autocomplete="off" tabindex="2" value="{$currentstock->dateOfStockEntry_output}"/>
        <input type="hidden" name="currentstock[$csrowid][dateOfStockEntry]" id="altpickDate_currentstock_{$csrowid}" value="{$currentstock->dateOfStockEntry_formatted}"/>

    </td>
    <td>
        <input type="text" id="pickDate_currentstock_{$csrowid}_expiryDate" autocomplete="off" tabindex="2" value="{$currentstock->expiryDate_output}"/>
        <input type="hidden"  name="currentstock[$csrowid][expiryDate]" id="altpickDate_currentstock_{$csrowid}_expiryDate" value="{$currentstock->expiryDate_formatted}"/>
    </td>
    <td>
        <input type="text" id="pickDate_currentsale_{$csrowid}" autocomplete="off" tabindex="2" value="{$currentstock->estDateOfSale_output}"/>
        <input type="hidden" name="currentstock[$csrowid][estDateOfSale]" id="altpickDate_currentsale_{$csrowid}" value="{$currentstock->estDateOfSale_formatted}"/>
    </td>
</tr>