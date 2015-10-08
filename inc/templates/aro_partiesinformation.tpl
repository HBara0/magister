<a class="header" href="#" id="pldiv"><h2 id="aro_partiesinformation">{$lang->partiesinformation}</h2></a>
<div>
    <p>
        {$interm_vendor}
    <table>
        <tbody width="100%;">
            <tr><td class="subtitle" colspan="6">{$lang->shipmentparameters}</td></tr>
                {$partiesinfo_shipmentparameters}
            <tr><td class="subtitle" colspan="6">{$lang->fees}</td></tr>
                {$partiesinfo_fees}

            <tr><td colspan="2"></td>
                <td class="subtitle">{$lang->intermediary}</td>
                <td class="subtitle">{$lang->vendor}</td>
            </tr>
            <tr><td colspan="2">{$lang->estdateofpayment}</td>
                <td><input type="text" id="pickDate_intermed_estdateofpayment" autocomplete="off" tabindex="2" value="{$partiesinfo[intermedEstDateOfPayment_formatted]}" {$partiesinfo[required_intermedpolicy]} style="width:150px;" disabled="disabled" class="automaticallyfilled-noneditable"/>
                    <input type="hidden" name="partiesinfo[intermedEstDateOfPayment]" id="altpickDate_intermed_estdateofpayment" value="{$partiesinfo[intermedEstDateOfPayment_output]}"/>
                </td>
                <td><input type="text" id="pickDate_vendor_estdateofpayment" autocomplete="off" tabindex="2" value="{$partiesinfo['vendorEstDateOfPayment_formatted']}" required="required" style="width:150px;" disabled="disabled" class="automaticallyfilled-noneditable"/>
                    <input type="hidden" name="partiesinfo[vendorEstDateOfPayment]" id="altpickDate_vendor_estdateofpayment" value="{$partiesinfo['vendorEstDateOfPayment_output']}"/>
                </td>
                <td colspan="2"> {$lang->diffbetweenpaymentdays}
                    <input type="number"  id="partiesinfo_diffbtwpaymentdates" value="{$partiesinfo[diffbtwpaymentdates]}" style="width:100px" readonly class="automaticallyfilled-noneditable"/>

                </td>
            </tr>
            <tr><td colspan="2">{$lang->acceptablepaymentterm}</td>
                <td><input type="number" step="any" name="partiesinfo[ptAcceptableMargin]" id="partiesinfo_intermed_ptAcceptableMargin" value="{$aropartiesinfo_obj->ptAcceptableMargin}" {$is_disabled} class="automaticallyfilled-editable"/></td>
            </tr>
            <tr><td colspan="2">{$lang->promiseofpayment}  <a href="#" title="{$lang->promiseofpaymentdesc}"><img src="./images/icons/question.gif" ></a></td>
                <td><input type="text" id="pickDate_intermed_promiseofpayment" autocomplete="off" tabindex="2" value="{$partiesinfo[promiseOfPayment_formatted]}" {$partiesinfo[required_intermedpolicy]} style="width:150px;" class="automaticallyfilled-noneditable"/>
                    <input type="hidden" name="partiesinfo[promiseOfPayment]" id="altpickDate_intermed_promiseOfPayment" value="{$partiesinfo[promiseOfPayment_output]}"/>
                </td>
            </tr>

        </tbody>
    </table>
</p>
</div>


