<td><input type="checkbox" value="0" name="customeroder[$rowid][cid]" {$checked[unsepcifiedCustomer]}/>
    <input type="hidden" name="customeroder[$rowid][aocid]" value='{$customeroder[aocid]}'/>
    <input type="hidden" name="customeroder[$rowid][inputChecksum]" value="{$customeroder[inputChecksum]}"/>
</td>
<td>{$lang->unspecifiedcustomers}</td>
<td>{$lang->paymentterms}</td>
<td>{$altpayment_term}</td>
<td>{$lang->paymenttermsdesc}</td>
<td> <input type="text" name="customeroder[$rowid][paymentTermDesc]" value="{$customeroder[paymentTermDesc]}"/></td>

<td>{$lang->paymenttermbasedate}</td>
<td> <input type="text" id="pickDate_to_{$rowid}_altcid" autocomplete="off" tabindex="2" value="{$customeroder[paymenttermbasedate_output]}" />  </td>
<td> <input type="hidden" name="customeroder[$rowid][paymentTermBaseDate]" id="altpickDate_to_{$rowid}_altcid" value="{$customeroder[paymenttermbasedate_formatted]}"/></td>