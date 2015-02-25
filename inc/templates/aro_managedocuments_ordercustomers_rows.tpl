<tr id="{$rowid}">
    <td> customer
        <input type='hidden' name='customeroder[$rowid][aocid]' value='{$customeroder[aocid]}'/>
        <input type='hidden' name='customeroder[$rowid][inputChecksum]' value='{$customeroder[inputChecksum]}'/>
    </td>
    <td><input type='text' id='customer_{$rowid}_autocomplete' name="customeroder[$rowid][customerName]"  value="{$customeroder[customerName]}" autocomplete='off' {$required}/>
        <input type='text' size='3' id='customer_{$rowid}_id_output' disabled='disabled' value="{$customeroder[cid]}" style="display:none;"/>
        <input type='hidden' value="{$customeroder[cid]}" id='customer_{$rowid}_id' name='customeroder[$rowid][cid]' /> </td>
    <td>Payment Terms  </td>
    <td>{$payment_term}</td>
    <td>Payment Terms Description </td>
    <td> <input type="text" name="customeroder[$rowid][paymentTermDesc]" value="{$customeroder[paymentTermDesc]}"/></td>

    <td>Payment Term Base Date</td>
    <td> <input type="text" id="pickDate_to_{$rowid}" autocomplete="off" tabindex="2" value="{$customeroder[paymenttermbasedate_output]}"/>  </td>
    <td> <input type="hidden" name="customeroder[$rowid][paymentTermBaseDate]" id="altpickDate_to_{$rowid}" value="{$customeroder[paymenttermbasedate_formatted]}"/></td>

</tr>