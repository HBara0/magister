<tr id="{$rowid}">
    <td> {$lang->customer}
        <input type='hidden' name='customeroder[$rowid][aocid]' value='{$customeroder[aocid]}'/>
        <input type='hidden' name='customeroder[$rowid][inputChecksum]' value='{$customeroder[inputChecksum]}'/>
    </td>
    <td><input type='text' id='customer_{$rowid}_autocomplete' name="customeroder[$rowid][customerName]"  value="{$customeroder[customerName]}" autocomplete='off' {$required}/>
        <input type='text' size='3' id='customer_{$rowid}_id_output' disabled='disabled' value="{$customeroder[cid]}" style="display:none;"/>
        <input type='hidden' value="{$customeroder[cid]}" id='customer_{$rowid}_id' name='customeroder[$rowid][cid]' /> </td>
    <td>{$lang->paymentterms}</td>
    <td>{$payment_term}</td>
    <td>{$lang->paymenttermsdesc}</td>
    <td> <input type="text" name="customeroder[$rowid][paymentTermDesc]" value="{$customeroder[paymentTermDesc]}"/></td>

    <td>{$lang->paymenttermbasedate}</td>
    <td> <input type="text" id="pickDate_to_{$rowid}" autocomplete="off" tabindex="2" value="{$customeroder[paymenttermbasedate_output]}"/>  </td>
    <td> <input type="hidden" name="customeroder[$rowid][paymentTermBaseDate]" id="altpickDate_to_{$rowid}" value="{$customeroder[paymenttermbasedate_formatted]}"/></td>

</tr>