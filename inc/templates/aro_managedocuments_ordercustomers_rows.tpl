<tr id="{$rowid}">
    <td>
        <input type='hidden' name='customeroder[$rowid][aocid]' value='{$customeroder[aocid]}'/>
        <input type='hidden' name='customeroder[$rowid][inputChecksum]' value='{$customeroder[inputChecksum]}'/>
        <input type='text' id='customer_{$rowid}_autocomplete' name="customeroder[$rowid][customerName]"  value="{$customeroder[customerName]}" autocomplete='off'/>
        <input type='text' size='3' id='customer_{$rowid}_id_output' disabled='disabled' value="{$customeroder[cid]}" style="display:none;"/>
        <input type='hidden' value="{$customeroder[cid]}" id='customer_{$rowid}_id' name='customeroder[$rowid][cid]' /> </td>
    <td>{$payment_term}</td>
    <td> <input type="text" name="customeroder[$rowid][paymentTermDesc]" value="{$customeroder[paymentTermDesc]}" style="width:210px;"/></td>

    <td> <input type="text" id="pickDate_to_{$rowid}" autocomplete="off" tabindex="2" value="{$customeroder[paymenttermbasedate_output]}" style="width:200px;"/>
        <input type="hidden" name="customeroder[$rowid][paymentTermBaseDate]" id="altpickDate_to_{$rowid}" value="{$customeroder[paymenttermbasedate_formatted]}"/></td>
    <td>
        <input type="checkbox" class="deletecheckbox" id="customeroder_{$rowid}_todelete" name="customeroder[$rowid][todelete]" value="1" label="Delete" oldtitle="If check-box is checked row is deleted">
        <label for="customeroder_{$rowid}_todelete"></label></input>
    </td>
</tr>