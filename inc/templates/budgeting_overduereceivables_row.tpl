<tr class="{$rowclass}" id="{$rowid}">
    <td style="width:20%"> <input type='text' id='customer_noexception_{$rowid}_autocomplete' name="clientoverdue[$rowid][customerName]" value="{$clientoverdue->customername}" autocomplete='off' {$required} style="width:100%;"/>
        <input type='hidden' value="{$clientoverdue->cid}" id='customer_noexception_{$rowid}_id' name='clientoverdue[$rowid][cid]' />
        <input type='hidden' value="{$clientoverdue->boid}"   name='clientoverdue[$rowid][boid]' />
        <input type="hidden" name="clientoverdue[$rowid][inputChecksum]" value="{$inputChecksum}"/>
    </td>
    <td style="width:10%"><input type="text" name="clientoverdue[$rowid][legalAction]" id="" value="{$clientoverdue->legalAction}" style="width:100%;"/></td>
    <td style="width:10%">
        <input type="text" id="pickDate_from[$rowid]" autocomplete="off" tabindex="1" style="width:100%;" name="clientoverdue[$rowid][oldestUnpaidInvoiceDate]" value="$clientoverdue->oldestUnpaidInvoiceDate"/>
    </td>
    <td style="width:10%"><input type="number" step="any" name="clientoverdue[$rowid][totalAmount]" id="clientoverdue_{$rowid}_totalAmount" value="{$clientoverdue->totalAmount}" style="width:100%;"/>
    </td>
    <td style="width:25%;">
        <textarea name="clientoverdue[$rowid][reason]" id="" cols="15" rows="2" style="width:100%;">$clientoverdue->reason</textarea>
    </td>
    <td style="width:15%;"><input type="text" name="clientoverdue[$rowid][action]" id="" value="{$clientoverdue->action}" style="width:100%;"/></td>
</tr>
