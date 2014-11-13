<tr class="{$rowclass}" id="{$rowid}">
    <td>{$banks_list} <input type="hidden" name="bank[$rowid][bbfid]" value="$bankfacility->bbfid"/>
        <input type="hidden" name="bank[$rowid][inputChecksum]" value="{$inputChecksum}"/>
    </td>
    <td><input type="number" step="any" name="bank[$rowid][overDraft]" id="bank_{$rowid}_overDraft" value="{$bankfacility->overDraft}" style="width:100%;"></input></td>
    <td><input type="number" step="any" name="bank[$rowid][loan]" id="bank_{$rowid}_loan" value="{$bankfacility->loan}" style="width:100%;"></input></td>
    <td><input type="number" step="any" name="bank[$rowid][forexForward]" id="bank_{$rowid}_forexForward" value="{$bankfacility->forexForward}" style="width:100%;"></input></td>
    <td><input type="number" step="any" name="bank[$rowid][billsDiscount]" id="bank_{$rowid}_billsDiscount" value="{$bankfacility->billsDiscount}" style="width:100%;"></input></td>
    <td><input type="number" step="any" name="bank[$rowid][othersGuarantees]" id="bank_{$rowid}_othersGuarantees" value="{$bankfacility->othersGuarantees}" style="width:100%;"></input></td>
    <td>{$currencies_list}</td>
    <td><input type="number" step="any" name="bank[$rowid][interestRate]" id="bank_{$rowid}_interestRate" value="{$bankfacility->interestRate}" style="width:100%;"></input></td>
    <td><input type="text" name="bank[$rowid][premiumCommission]" id="" value="{$bankfacility->premiumCommission}"style="width:100%;"></input></td>
    <td><input type="number" step="any" name="bank[$rowid][totalAmount]" id="bank_{$rowid}_totalAmount" value="{$bankfacility->totalAmount}" style="width:100%;"></input></td>
    <td><input type="number" step="any" name="bank[$rowid][endquarterAmount]" id="bank_{$rowid}_endquarterAmount" value="{$bankfacility->endquarterAmount}" style="width:100%;"></input></td>
    <td><input type="text" name="bank[$rowid][comfortLetter]" id="" value="{$bankfacility->comfortLetter}" style="width:100%;"></input></td>
    <td><input type="text" id="pickDate_LastIssuanceDate[$rowid]" autocomplete="off" tabindex="1" style="width:100%;" name="bank[$rowid][LastIssuanceDate]" value="{$bankfacility->LastIssuanceDate}"/></td>
    <td> <input type="text" id="pickDate_LastRenewalDate[$rowid]" autocomplete="off" tabindex="1" style="width:100%;" name="bank[$rowid][LastRenewalDate]" value="{$bankfacility->LastRenewalDate}"/></td>
</tr>
