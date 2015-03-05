<td>
    <input type="hidden" name="customeroder[$rowid][aocid]" value='{$customeroder[aocid]}'/>
    <input type="hidden" name="customeroder[$rowid][inputChecksum]" value="{$customeroder[inputChecksum]}"/>
    <input type="hidden" value="0" name="customeroder[$rowid][cid]"/>
    {$lang->unspecifiedcustomers}</td>
<td>{$altpayment_term}</td>
<td> <input type="text" name="customeroder[$rowid][paymentTermDesc]" value="{$customeroder[paymentTermDesc]}" style="width:210px;"/></td>
<td> <input type="text" id="pickDate_to_{$rowid}_altcid" autocomplete="off" tabindex="2" value="{$customeroder[paymenttermbasedate_output]}" style="width:200px;"/>
    <input type="hidden" name="customeroder[$rowid][paymentTermBaseDate]" id="altpickDate_to_{$rowid}_altcid" value="{$customeroder[paymenttermbasedate_formatted]}"/>
</td>
<td>
    <input type="checkbox" class="deletecheckbox" id="customeroder_{$rowid}_todelete" name="customeroder[$rowid][todelete]" value="1" label="Delete" oldtitle="If check-box is checked row is deleted">
    <label for="customeroder_{$rowid}_todelete"></label></input>
</td>