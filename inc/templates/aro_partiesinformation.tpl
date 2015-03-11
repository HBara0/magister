<a class="header" href="#" id="pldiv"><h2>{$lang->partiesinformation}</h2></a>
<div>
    <p>
        {$interm_vendor}
    <table>
        <tbody width="100%;">
            <tr><td class="subtitle" colspan="6">{$lang->shipmentparameters}</td></tr>
                {$partiesinfo_shipmentparameters}
            <tr><td class="subtitle" colspan="6">{$lang->fees}</td></tr>
                {$partiesinfo_fees}

            <tr><td></td>
                <td class="subtitle">{$lang->intermediary}</td>
                <td class="subtitle">{$lang->vendor}</td>
            </tr>
            <tr><td>{$lang->estdateofpayment}</td>
                <td><input type="text" id="pickDate_intermed_estdateofpayment" autocomplete="off" tabindex="2" value="{$partiesinfo[intermedEstDateOfPayment_output]}" required="required" style="width:150px;" disabled="disabled"/>
                    <input type="hidden" name="partiesinfo[intermedEstDateOfPayment]" id="altpickDate_intermed_estdateofpayment" value="{$partiesinfo[intermedEstDateOfPayment_formatted]}"/>
                </td>
                <td><input type="text" id="pickDate_vendor_estdateofpayment" autocomplete="off" tabindex="2" value="{$partiesinfo['vendorEstDateOfPayment_formatted']}" required="required" style="width:150px;" disabled="disabled"/>
                    <input type="hidden" name="partiesinfo[vendorEstDateOfPayment]" id="altpickDate_vendor_estdateofpayment" value="{$partiesinfo['vendorEstDateOfPayment_formatted']}"/>
                </td>
            </tr>

        </tbody>
    </table>
</p>
</div>


