<td><input type="checkbox" value="unsepcifiedCustomer" name="customeroder[$rowid][cid]" {$checked[unsepcifiedCustomer]}/>
    <input type="hidden" name="customeroder[$rowid][aocid]" value='{$customeroder[aocid]}'/>
    <input type="hidden" name="customeroder[$rowid][inputChecksum]" value="{$customeroder[inputChecksum]}"/>
</td>
<td>Unspecified customers. </td>
<td>Payment Terms  </td>
<td>{$altpayment_term}</td>
<td>Payment Terms Description </td>
<td> <input type="text" name="customeroder[$rowid][paymentTermDesc]" value="{$customeroder[paymentTermDesc]}"/></td>

<td>Payment Term Base Date</td>
<td> <input type="text" id="pickDate_to_{$rowid}_altcid" autocomplete="off" tabindex="2" value="{$customeroder[paymenttermbasedate_output]}" />  </td>
<td> <input type="hidden" name="customeroder[$rowid][paymentTermBaseDate]" id="altpickDate_to_{$rowid}_altcid" value="{$customeroder[paymenttermbasedate_formatted]}"/></td>
