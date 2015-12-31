<table>
    <tbody  width="100%" >
        <tr>
            <td style="vertical-align:top;">
                <table class="datatable">
                    <tr><td></td>
                        <td class="subtitle" style="width:25%">{$lang->vendor}</td>
                        <td class="subtitle" style="width:25%">{$lang->intermediary}</td>
                    </tr>
                    <tr><td style="width:35%">{$lang->partiesinvolved}</td>
                        <td>{$vendor_displayname}{$aff[vendor_output]}</td>
                        <td>{$aff[intermed_output]}</td>
                    </tr>
                    <tr class="altrow"><td style="width:35%">{$lang->purchaser}</td>
                        <td>{$purchaser[fromvendor]}</td>
                        <td>{$purchaser[fromaff]}</td>
                    </tr>
                    <tr><td>{$lang->incoterms}</td>
                        <td>{$incoterms[vendor_output]}</td>
                        <td>{$incoterms[intermed_output]}</td>
                    </tr>
                    <tr class="altrow"><td>{$lang->incotermsdesc}</td>
                        <td>{$aropartiesinfo_obj->vendorIncotermsDesc}</td>
                        <td>{$aropartiesinfo_obj->intermedIncotermsDesc}</td>
                    </tr>
                    <tr>
                        <td>{$lang->paymentterms}</td>
                        <td>{$paymentterms[vendor_output]}</td>
                        <td>{$paymentterms[intermed_output]}</td>
                    </tr>
                    <tr class="altrow"><td>{$lang->paymenttermsdesc}</td>
                        <td>{$aropartiesinfo_obj->vendorPaymentTermDesc}</td>
                        <td>{$aropartiesinfo_obj->intermedPaymentTermDesc}</td>
                    </tr>
                    <tr>
                        <td>{$lang->paymentterms} {$lang->throughbank}</td>
                        <td><input type="checkbox" name="partiesinfo[vendorPTIsThroughBank]" value="1" {$checked[vendorPTIsThroughBank]} /></td>
                        <td><input type="checkbox" name="partiesinfo[intermedPTIsThroughBank]" value="1" {$checked[intermedPTIsThroughBank]}/></td>
                    </tr>
                    <tr class="altrow">
                        <td>{$lang->commission} <small>{$lang->commisionlimit}</small></td>
                        <td></td>
                        <td>{$aropartiesinfo_obj->commission}% </td>
                    </tr>
                    <tr class="altrow">
                        <td> <span style="{$aro_display[prtiesinfo][discount]}">{$lang->totaldiscount} <a href="#" title="{$lang->totaldiscountdesc}"><img src="./images/icons/question.gif" ></a></span></td>
                        <td></td>
                        <td style="{$aro_display[prtiesinfo][discount]}">{$aropartiesinfo_obj->totalDiscount}</td>
                    </tr>
                    <tr><td>{$lang->commfromintermed} % <a href="#" title="{$lang->commfromintermeddesc}"><img src="./images/icons/question.gif" ></a></td>
                        <td></td>
                        <td>{$aropartiesinfo_obj->commFromIntermed}%</td>
                    </tr>
                    <tr class="altrow">
                        <td>{$lang->isconsolidation}</td>
                        <td><input type="checkbox" name="partiesinfo[isConsolidation]" id="vendor_isConsolidationPlatform" value="1" {$checked[isConsolidation]}/></td>
                        <td></td>
                    </tr>
                    <tr {$consolidation_warehouses_display}>
                        <td>{$lang->consolidationwarehouse}</td>
                        <td id="consolidation_warehouse" {$consolidation_warehouses_display}>{$consolidation_warehouses_output}</td>
                        <td></td>
                    </tr>
                    <tr id="partiesinfo_forwarder" {$aro_display[prtiesinfo][forwarder]}>
                        <td colspan="3">
                            <span class="subtitle"><br/>{$lang->vendor} {$lang->incoterms} {$lang->exttrafields}</span><br/>
                            <div>
                                <div style="display:inline-block; width:30%;">  {$lang->forwarder}</div>{$aropartiesinfo_obj->forwarder}<br/>
                                <div style="display:inline-block; width:30%;"> {$lang->paymentterms} ({$lang->forwarder})</div> {$aropartiesinfo_obj->forwarderPT}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>
