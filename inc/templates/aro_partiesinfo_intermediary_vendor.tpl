<table>
    <tbody  width="100%" >
        <tr>
            <td style="vertical-align:top;">
                <table>
                    <tr><td></td>
                        <td class="subtitle">{$lang->intermediary}</td>
                        <td class="subtitle">{$lang->vendor}</td>
                    </tr>
                    <tr class="altrow2"><td>{$lang->partiesinvolved}</td>
                        <td>{$affiliates_list['intermed']}</td>
                        <td><input id="supplier_1_autocomplete" autocomplete="off" type="text" value="{$vendor_displayname}" style="width:150px;" {$is_disabled}>
                            <input id="supplier_1_id" name="partiesinfo[vendorEid]"  value="{$aropartiesinfo_obj->vendorEid}" type="hidden">
                            <div id="searchQuickResults_1" class="searchQuickResults" style="display: none;"></div>
                        </td>
                        <td style="padding-left:10px">{$lang->isaff}<input type="checkbox" name="partiesinfo[vendorIsAff]" id="vendor_isaffiliate" value="1" {$checked[vendorisaff]}>
                            <a href="#" title="{$lang->vendorisaffdesc}"><img src="./images/icons/question.gif" ></a>
                        </td>
                        <td id="vendor_affiliate" {$display}>{$affiliates_list['vendor']}</td>
                    </tr>
                    <tr><td>{$lang->incoterms}</td>
                        <td>{$incoterms_list['intermed']}</td>
                        <td>{$incoterms_list['vendor']}</td>
                        <td style="padding-left:10px">{$lang->isconsolidation}<input type="checkbox" name="partiesinfo[isConsolidation]" id="vendor_isConsolidationPlatform" value="1" {$checked[isConsolidation]}/></td>
                        <td id="consolidation_warehouse" {$consolidation_warehouses_display}>{$consolidation_warehouses_list}</td>
                    </tr>
                    <tr class="altrow"><td>{$lang->incotermsdesc}</td>
                        <td><input type="text"  name="partiesinfo[intermedIncotermsDesc]" id="partiesinfo_intermed_IncotermsDesc" value="{$aropartiesinfo_obj->intermedIncotermsDesc}" placeholder="" {$partiesinfo[required_intermedpolicy]} style="width:150px;" {$is_disabled}/></td>
                        <td><input type="text"  name="partiesinfo[vendorIncotermsDesc]" id="partiesinfo_vendor_IncotermsDesc" value="{$aropartiesinfo_obj->vendorIncotermsDesc}" placeholder="" required='required' style="width:150px;"/></td>
                    </tr>
                    <tr><td>{$lang->paymentterms}</td>
                        <td>{$paymentterms_list['intermed']}</td>
                        <td>{$paymentterms_list['vendor']}</td>
                    </tr>
                    <tr class="altrow2"><td>{$lang->paymenttermsdesc}</td>
                        <td><input type="text"  name="partiesinfo[intermedPaymentTermDesc]" id="partiesinfo_intermed_PaymentTermDesc" value="{$aropartiesinfo_obj->intermedPaymentTermDesc}" placeholder="Ex: days from B/L" {$partiesinfo[required_intermedpolicy]} style="width:150px;" {$is_disabled}/></td>
                        <td><input type="text"  name="partiesinfo[vendorPaymentTermDesc]" id="partiesinfo_vendor_PaymentTermDesc" value="{$aropartiesinfo_obj->vendorPaymentTermDesc}" required='required' style="width:150px;" placeholder="Ex: days from B/L"/></td>
                    </tr>
                    <tr><td>{$lang->paymentterms} {$lang->throughbank}</td>
                        <td><input type="checkbox" name="partiesinfo[intermedPTIsThroughBank]" value="1" {$checked[intermedPTIsThroughBank]}/></td>
                        <td><input type="checkbox" name="partiesinfo[vendorPTIsThroughBank]" value="1" {$checked[vendorPTIsThroughBank]} /></td>
                    </tr>
                    <tr><td>{$lang->commission} <small>{$lang->commisionlimit}</small></td>
                        <td><input type="number" step="any" name="partiesinfo[commission]" id="partiesinfo_commission" value="{$aropartiesinfo_obj->commission}" class="automaticallyfilled-editable"/>%
                            <input type="hidden"  id="partiesinfo_defaultcommission"/></td>
                    </tr>
                    <tr class="altrow2">
                        <td> <span style="{$aro_display[prtiesinfo][discount]}">{$lang->totaldiscount} <a href="#" title="{$lang->totaldiscountdesc}"><img src="./images/icons/question.gif" ></a></span></td>
                        <td><input style="{$aro_display[prtiesinfo][discount]}" type="number" step="any" name="partiesinfo[totalDiscount]" id="partiesinfo_totaldiscount" value="{$aropartiesinfo_obj->totalDiscount}"/>
                        </td>
                    </tr>

                    <tr class="altrow2"><td>{$lang->commfromintermed} % <a href="#" title="{$lang->commfromintermeddesc}"><img src="./images/icons/question.gif" ></a></td>
                        <td><input type="number" step="any" name="partiesinfo[commFromIntermed]" id="partiesinfo_commFromIntermed" value="{$aropartiesinfo_obj->commFromIntermed}"/>%</td>
                    </tr>
                    <tr id="partiesinfo_forwarder" {$aro_display[prtiesinfo][forwarder]}>
                        <td colspan="3">
                            <span class="subtitle"><br/>{$lang->vendor} {$lang->incoterms} {$lang->exttrafields}</span><br/>
                            <div>
                                <div style="display:inline-block; width:30%;">  {$lang->forwarder}</div> <input type="text" id="partiesinfo_forwardername" name="partiesinfo[forwarder]" value="{$aropartiesinfo_obj->forwarder}"/><br/>
                                <div style="display:inline-block; width:30%;"> {$lang->paymentterms} ({$lang->forwarder})</div> <input type="text" id="partiesinfo_forwarderPT" name="partiesinfo[forwarderPT]" value="{$aropartiesinfo_obj->forwarderPT}"/>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>
