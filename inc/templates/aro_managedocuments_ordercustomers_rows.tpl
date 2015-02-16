<tr id="{$rowid}">
    <td> customer</td>
    <td><input type='text' id='customer_noexception_{$rowid}_autocomplete' name="customeroder[$rowid][$checksum][customerName]"  value="{$customeroder[customerName]}" autocomplete='off' {$required}/>
        <input type='text' size='3' id='customer_noexception_{$rowid}_id_output' disabled='disabled' value="{$customeroder[cid]}" style="display:none;"/>
        <input type='hidden' value="{$customeroder[cid]}" id='customer_noexception_{$rowid}_id' name='customeroder[$rowid][$checksum][cid]' /> </td>
    <td>Payment Terms  </td>
    <td>{$payment_term}</td>
    <td> <input type="text" name="customeroder[$rowid][$checksum][paymentTermDesc]"/></td>
    <td> </td>
    <td>Payment Term Base Date</td>
    <td> <input type="text" id="pickDate_to_{$rowid}" autocomplete="off" tabindex="2" value="{$customeroder[paymenttermbasedate_output]}" required="required" />  </td>
    <td> <input type="hidden" name="customeroder[$rowid][$checksum][paymentTermBaseDate]" id="altpickDate_to_{$rowid}" value="{$customeroder[paymenttermbasedate_formatted]}"/></td>

</tr>