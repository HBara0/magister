<table>
    <tbody  width="100%" >
        <tr>
            <td style="vertical-align:top;">
                <table class="datatable">
                    <tr><td class="border_right"></td>
                        <td class="subtitle border_right" style="width:25%">{$lang->vendor}</td>
                        <td class="subtitle border_right" style="width:25%">{$lang->intermediary}</td>
                    </tr>
                    <tr><td style="width:35%" class="border_right">{$lang->partiesinvolved}</td>
                        <td class="border_right">{$vendor_displayname}{$aff[vendor_output]}</td>
                        <td class="border_right">{$aff[intermed_output]}</td>
                    </tr>
                    <tr class="altrow"><td style="width:35%" class="border_right">{$lang->purchaser}</td>
                        <td class="border_right">{$purchaser[fromvendor]}</td>
                        <td class="border_right">{$purchaser[fromaff]}</td>
                    </tr>
                    <tr>
                        <td class="border_right" >{$lang->incoterms}</td>
                        <td class="border_right">{$incoterms[vendor_output]}</td>
                        <td class="border_right">{$incoterms[intermed_output]}</td>
                    </tr>
                    <tr class="altrow">
                        <td class="border_right">{$lang->incotermsdesc}</td>
                        <td class="border_right">{$aropartiesinfo_obj->vendorIncotermsDesc}</td>
                        <td class="border_right">{$aropartiesinfo_obj->intermedIncotermsDesc}</td>
                    </tr>
                    <tr>
                        <td class="border_right">{$lang->paymentterms}</td>
                        <td class="border_right">{$paymentterms[vendor_output]}</td>
                        <td class="border_right">{$paymentterms[intermed_output]}</td>
                    </tr>
                    <tr class="altrow">
                        <td class="border_right">{$lang->paymenttermsdesc}</td>
                        <td class="border_right">{$aropartiesinfo_obj->vendorPaymentTermDesc}</td>
                        <td class="border_right">{$aropartiesinfo_obj->intermedPaymentTermDesc}</td>
                    </tr>
                    <tr>
                        <td class="border_right">{$lang->paymentterms} {$lang->throughbank}</td>
                        <td class="border_right"><input type="checkbox" name="partiesinfo[vendorPTIsThroughBank]" value="1" {$checked[vendorPTIsThroughBank]} /></td>
                        <td class="border_right"><input type="checkbox" name="partiesinfo[intermedPTIsThroughBank]" value="1" {$checked[intermedPTIsThroughBank]}/></td>
                    </tr>
                    <tr class="altrow">
                        <td class="border_right">{$lang->commission} <small>{$lang->commisionlimit}</small></td>
                        <td class="border_right"></td>
                        <td class="border_right">{$aropartiesinfo_obj->commission}% </td>
                    </tr>
                    <tr class="altrow">
                        <td class="border_right"> <span style="{$aro_display[prtiesinfo][discount]}">{$lang->totaldiscount} <a class="hidden-print"  href="#" title="{$lang->totaldiscountdesc}"><img src="./images/icons/question.gif" ></a></span></td>
                        <td class="border_right"></td>
                        <td style="{$aro_display[prtiesinfo][discount]};width:100%" class="border_right">{$aropartiesinfo_obj->totalDiscount}</td>
                    </tr>
                    <tr>
                        <td class="border_right">{$lang->commfromintermed} % <a class="hidden-print" href="#" title="{$lang->commfromintermeddesc}"><img src="./images/icons/question.gif" ></a></td>
                        <td class="border_right"></td>
                        <td class="border_right">{$aropartiesinfo_obj->commFromIntermed}%</td>
                    </tr>
                    <tr class="altrow">
                        <td class="border_right">{$lang->isconsolidation}</td>
                        <td class="border_right"><input type="checkbox" name="partiesinfo[isConsolidation]" id="vendor_isConsolidationPlatform" value="1" {$checked[isConsolidation]}/></td>
                        <td></td>
                    </tr>
                    <tr {$consolidation_warehouses_display}>
                        <td class="border_right">{$lang->consolidationwarehouse}</td>
                        <td class="border_right" id="consolidation_warehouse" {$consolidation_warehouses_display}>{$consolidation_warehouses_output}</td>
                        <td></td>
                    </tr>
                </table>
                <table class="datatable">
                    <tr id="partiesinfo_forwarder" {$aro_display[prtiesinfo][forwarder]}>
                        <td class="subtitle">{$lang->vendor} {$lang->incoterms} {$lang->exttrafields}</td>
                    </tr>
                    <tr><td class="border_right" style="width:50%"> {$lang->forwarder}</td><td class="border_right">{$aropartiesinfo_obj->forwarder}</td></tr>
                    <tr><td class="border_right" style="width:50%">{$lang->paymentterms} ({$lang->forwarder})</td><td class="border_right">{$aropartiesinfo_obj->forwarderPT}</td></tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>
