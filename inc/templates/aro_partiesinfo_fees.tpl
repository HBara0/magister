<tr>
    <td>{$lang->freight}</td>
    <td><input type="number" step="any" name="partiesinfo[freight]" id="partiesinfo_freight" value="{$aropartiesinfo_obj->freight}"></td>
    <td>{$lang->bankfees}</td>
    <td><input type="number" step="any" name="partiesinfo[bankFees]" id="partiesinfo_bankFees" value="{$aropartiesinfo_obj->bankFees}"></td>
    <td>{$lang->insurance}</td>
    <td><input type="number" step="any" name="partiesinfo[insurance]" id="partiesinfo_insurance" value="{$aropartiesinfo_obj->insurance}"></td>
</tr>
<tr>
    <td>{$lang->legalization}</td>
    <td><input type="number" step="any" name="partiesinfo[legalization]" id="partiesinfo_legalization" value="{$aropartiesinfo_obj->legalization}"></td>
    <td>{$lang->courier}</td>
    <td><input type="number" step="any" name="partiesinfo[courier]" id="partiesinfo_courier" value="{$aropartiesinfo_obj->courier}"></td>
    <td>{$lang->otherfees}</td>
    <td><input type="number" step="any" name="partiesinfo[otherFees]" id="partiesinfo_otherFees" value="{$aropartiesinfo_obj->otherFees}"></td>

</tr>
<tr class="altrow2"><td colspan="2">{$lang->totalfees}</td>
    <td>
        <input type="number" readonly name="partiesinfo[totalfees]" id="partiesinfo_totalintermedfees" value="{$partiesinfo[totalintermedfees]}" class="automaticallyfilled-noneditable">
        <input type="hidden" readonly name="partiesinfo[totalfees]" id="partiesinfo_totalfees" value="{$partiesinfo[totalfees]}">
    </td>
</tr>