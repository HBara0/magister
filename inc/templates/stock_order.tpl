<h1>{$lang->stockorder}</h1>
<form id="do_stock/order_Form" name="do_stock/order_Form" action="index.php?module=stock/order&amp;action=do_stockorder" method="post">
    <input type="hidden" value="{$identifier}" name="identifier" />
    <input type='hidden' name='timeLine' id='altpickDate_timeLine' value="{$stockorder_data[timeLine]}"/>
    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="border-bottom: 1px solid #F2F2F2;width:50%;" width="50%" colspan="2">
                <table width="100%">
                    <tr>
                        <th>{$lang->affiliate}</th><td>{$affiliates_list}</td>
                        <th>{$lang->type}</th><td>{$type_order}</td>
                        <th>{$lang->nborder}</th>
                        <td><input type="text" name="orderNumber" id="orderNumber" tabindex="4" size="14" value="{$stockorder_data[orderNumber]}" autocomplete='off'/><div name="orderNumber_Loading" id="orderNumber_Loading"></div></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #F2F2F2;" colspan="2">
                <table width="100%">
                    <tr>
                        <td style="text-align:right;">{$lang->currency}</td>
                        <td style="text-align:right;">{$currency}</td>
                        <td style="text-align:right;">{$lang->fxto}</td><td><input type="text" name="fxUSD" id="fxUSD" tabindex="6" size="6" accept="numeric"  value="{$stockorder_data[fxUSD]}" autocomplete='off'/></td>
                        <td style="text-align:right;">{$lang->warehousing} {$selectlist[warehouseunit]}</td><td colspan="3"><input type="text" name="warehouseUnitSize" id="warehouseUnitSize" tabindex="7" size="5" accept="numeric" value="{$stockorder_data[warehouseUnitSize]}" autocomplete='off'/></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" class="sectionbox">
        <tr>
            <th style="text-align:center;" class="thead" colspan="4">{$lang->supplier}</th>
        </tr>
        <tr>
            <td style="text-align:left;">{$lang->supplier}</td><td colspan="4"><input type='text' id='supplier_1_autocomplete' autocomplete='off' value="{$spid}" name="supplier[companyName]"/><input type="text" size="3" id="supplier_1_id_output"  disabled="disabled" value="{$stockorder_data[spid]}" /><input type='hidden' id='supplier_1_id' name='supplier[spid]' value="{$stockorder_data[spid]}"/><a href="index.php?module=contents/addproducts" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a><div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div></td>
        </tr>
        <tr>
            <td>{$lang->incoterms}</td>
            <td>{$incoterms} {$incotermslocation}</td>
            <td>{$lang->ets}</td>
            <td>
                <input type='text' id='pickDate_etsdate' autocomplete='off' tabindex="12" size="12" value="{$stockorder_data[expectedShippingDate_output]}"/>
                <input type='hidden' name='supplier[expectedShippingDate]' id='altpickDate_etsdate' value="{$stockorder_data[expectedShippingDate]}"/>
            </td>
        </tr>
        <tr>
            <td class="tdleft">{$lang->paymentterms}</td>
            <td><input type="text" name="supplier[paymentTermsDays]" id="supplierPaymentTermsDays" tabindex="11" size="5" accept="numeric" value="{$stockorder_data[paymentTermsDays]}" autocomplete='off' /> {$lang->daysfrom} {$supplier_payment_terms_from} <div name="paymenttermss_Loading" id="paymenttermss_Loading"></div></td>
            <td>{$lang->approxtt}</td>
            <td>
                <input type="text" name="supplier[daysToDeliver]" id="daysToDeliver" tabindex="13" size="7" accept="numeric" value="{$stockorder_data[daysToDeliver]}" autocomplete='off' />
            </td>
        </tr>
    </table>
    <br />
    <table width="100%" class="sectionbox">
        <tr>
            <th colspan="4" style="text-align:center;" class="thead">{$lang->criteria}</th>
        </tr>
        <tr>
            <td>
                {$lang->interestrate}: {$affiliate_info[bankInterest]} <input type="hidden" value="{$affiliate_info[bankInterest]}" name="bankInterest" id="bankInterest" /><br />
                {$lang->risk}: {$affiliate_info[risk]} <input type="hidden" value="{$affiliate_info[risk]}" name="risk" id="risk" />
            </td>
            <td>
                {$lang->warehousecharges}: {$affiliate_info[warehouseCharges]}/CPM<input type="hidden" value="{$affiliate_info[warehouseCharges]}" name="warehouseCharges" id="warehouseCharges" />
            </td>
        </tr>
    </table>
    <br />
    <table width="100%" class="sectionbox">
        <tr>
            <th style="text-align:center;" class="thead" colspan="4">{$lang->potentialcustomers}</th>
        </tr>
        <tr>
            <td colspan="4">
                <table width="100%">
                    <thead>
                        <tr>
                            <td>
                                <div style="widows:40%; display:inline-block;">{$lang->customer}</div>
                                <div style="widows:20%; display:inline-block; text-align:center; float:right;">{$lang->paymentterms}</div>
                            </td>
                        </tr>
                    </thead>
                    <tbody id="customer_tbody">
                        {$customer_row}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"><img src="./images/add.gif" id="ajaxaddmore_stock/order_customer_1" alt="{$lang->add}"></td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="4"><hr /></td>
        </tr>
        <tr>
            <td colspan="4"><span class="subtitle">{$lang->unallocatedquantities}</span>
                <table width="100%">
                    <thead>
                        <tr class="altrow2"><th>{$lang->product}</th><th>{$lang->qty}</th><th>{$lang->daysinstock}</th></tr>
                    </thead>
                    <tbody id="customerproducts_0_tbody">
                        <tr id="{$customerproduct_rowid}">
                            <td>
                                <input type='text' id="product_sectionexception_parent0_id1_autocomplete" autocomplete='off' value="{$customer[0][products][1][productName]}" name="unallocatedquantity[1][productName]"/>
                                <input type='text' size='2' id='product_parent0_id1_id_output' value="{$customer[0][products][1][pid]}" disabled="disabled"/>
                                <input type='hidden' id='product_parent0_id1_id' name='unallocatedquantity[1][pid]' value="{$customer[0][products][1][pid]}"/><div id='searchQuickResults_product_sectionexception_parent0_id1' class='searchQuickResults' style='display:none;'></div>
                            </td>
                            <td><input type="text" size="5" name="unallocatedquantity[1][firstOrderQty]" id="product_parent0_id1_firstOrderQty" accept="numeric" value="{$stockorder_data[customers][$key][firstOrderQty]}"/></td>
                            <td><input type="text" size="5" name="unallocatedquantity[1][daysInStock]" id="product_parent0_id1_daysInStock" accept="numeric" value="{$stockorder_data[customers][$key][daysInStock]}"/></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"><img src="./images/add.gif" id="addmore_customerproducts_0" alt="{$lang->add}"></td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="4"><hr /></td>
        </tr>
        <tr>
            <td>{$lang->customerspayment}</td>
            <td><input type='text' id='pickDate_customerdate' autocomplete='off' tabindex='18' size='20' value="{$stockorder_data[customerPaymentDate_output]}"/><input type='hidden' name='customerPaymentDate' id='altpickDate_customerdate' value="{$stockorder_data[customerPaymentDate]}" /></td>
            <td>{$lang->supplierpayment}</td>
            <td><input type='text' id='pickDate_supplierdate' value="{$stockorder_data[supplierPaymentDate_output]}" autocomplete='off' tabindex='19' size='20'/><input type='hidden' name='supplierPaymentDate' id='altpickDate_supplierdate' value="{$stockorder_data[supplierPaymentDate]}"/></td>
        </tr>
    </table>
    <br />
    <table width="100%" class="sectionbox datatable">
        <tr>
            <th style="text-align:center;" class="thead">{$lang->product}</th>
        </tr>
        <tr>
            <td valign="top" colspan="2">
                <table width="100%">
                    <thead>
                        <tr>
                            <th>{$lang->product}</th><th>{$lang->packingtype}</th><th>{$lang->packingweight}</th><th>{$lang->qty}</th><th>{$lang->purchaseprice}</th><th>{$lang->sellingprice}</th>
                            <th>{$lang->clearingfees}</th><th>{$lang->lcfees}</th><th>{$lang->purchaseamount}</th><th>{$lang->sellingamount}</th>
                        </tr>
                    </thead>
                    <tbody id="product_tbody">
                        {$product_row}
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <br />
    <table width="100%" class="sectionbox">
        <tr>
            <th colspan="4" style="text-align:center;" class="thead">{$lang->approvals}</th>
        </tr>
        <tr>
            <th class="altrow2">{$lang->submittedby}</th><th colspan="3" class="altrow2">{$lang->purchasecommitteeapproval}</th>
        </tr>
        <tr>
            <th>{$lang->businessmanager}</th><th>{$lang->generalmanager}</th><th>{$lang->regionalmanager}</th><th>{$lang->financemanager}</th>
        </tr>
        <tr>
            <td>{$submittedby_list}</td><td>{$managers[$affiliate_info[generalManager]]}<input type="hidden" name="generalManager" id="generalManager" value="{$affiliate_info[generalManager]}"/></td><td>{$managers[$affiliate_info[regionalManager]]}<input type="hidden" name="regionalManager" id="regionalManager" value="{$affiliate_info[regionalManager]}"/></td><td class="tdright">{$managers[$affiliate_info[financialManager]]}<input type="hidden" name="financeManager" id="financeManager" value="{$affiliate_info[financialManager]}"/></td>
        </tr>
    </table>
    <input type="submit" id="do_stock/order_Button" value="{$lang->preview}" class="button"/><input type="reset" value="{$lang->reset}" />
    <div id="do_stock/order_Results"></div>
</form>