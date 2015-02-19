<tr id="{$rowid}">
    <td> customer
        <input type='hidden' name='customeroder[corder][$rowid][aocid]' value='{$customeroder[aocid]}'/>
        <input type='hidden' name='customeroder[corder][$rowid][inputChecksum]' value='{$customeroder[inputChecksum]}'/>
    </td>
    <td><input type='text' id='customer_{$rowid}_autocomplete' name="customeroder[corder][$rowid][customerName]"  value="{$customeroder[customerName]}" autocomplete='off' {$required}/>
        <input type='text' size='3' id='customer_{$rowid}_id_output' disabled='disabled' value="{$customeroder[cid]}" style="display:none;"/>
        <input type='hidden' value="{$customeroder[cid]}" id='customer_{$rowid}_id' name='customeroder[corder][$rowid][cid]' /> </td>
    <td>Payment Terms  </td>
    <td>{$payment_term}</td>
    <td>Payment Terms Description </td>
    <td> <input type="text" name="customeroder[corder][$rowid][paymentTermDesc]"/></td>

    <td>Payment Term Base Date</td>
    <td> <input type="text" id="pickDate_to_{$rowid}" autocomplete="off" tabindex="2" value="{$customeroder[paymenttermbasedate_output]}" required="required" />  </td>
    <td> <input type="hidden" name="customeroder[corder][$rowid][paymentTermBaseDate]" id="altpickDate_to_{$rowid}" value="{$customeroder[paymenttermbasedate_formatted]}"/></td>

</tr>