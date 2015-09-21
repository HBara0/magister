<a class="header" href="#" id="ordersummary"><h2>{$lang->ordersummary}</h2></a>
<div>
    <p>
    <table>
        <thead>
            <tr>
                <td></td>
                <td class="subtitle"> <input type="text" class="subtitle" id="ordersummary_col1_title" value="{$aroordersummary->firstpartytitle}"/>{$firstparty_title}</td>
                <td class="subtitle"> <input type="text" class="subtitle" id="ordersummary_col2_title" value="{$aroordersummary->secondpartytitle}"/>{$secondparty_title}</td>
                <td class="subtitle" id="ordersummary_thirdparty_1" {$ordersummary[thirdcolumn_display]}> <input type="text" class="subtitle" id="ordersummary_col3_title" value="{$aroordersummary->thirdpartytitle}"/>{$thirdparty_title}</td>
            </tr>
            <tr><td></td>
                <td class="subtitle"><input type="text" id="ordersummary_intermedaff" style="width:150px;" readonly value="{$firstparty}"></span></td>
                <td class="subtitle"><input type="text" id="ordersummary_2ndparty" style="width:150px;" readonly value="{$secondparty}"></span></td>
                <td class="subtitle" id="ordersummary_thirdparty_2" {$ordersummary[thirdcolumn_display]}><input type="text" id="ordersummary_3rdparty" style="width:150px;" readonly value="{$thirdparty}"></span>
                    <input type="hidden" value="" id="haveThirdParty"/></td>

            </tr>
        </thead>
        <tbody style="width:100%;">

            <tr>
                <td>{$lang->invoicevalue}</td>
                <td><input type="text" id="ordersummary_invoicevalue_intermed" name="ordersummary[invoiceValueIntermed]" value="{$aroordersummary->invoiceValueIntermed}" style="width:150px;" readonly="readonly"/> </td>
                <td><input type="text" id="ordersummary_invoicevalue_local" name="ordersummary[invoiceValueLocal]" value="{$aroordersummary->invoiceValueLocal}" style="width:150px;" readonly="readonly"/> </td>
            </tr>
            <tr>
                <td>{$lang->feespaidbyintermed}</td>
                <td colspan="2">
                    <textarea id="ordersummary_totalintermedfees_usd" name="ordersummary[totalIntermedFeesUsd]" readonly="readonly" style="width:300px">{$aroordersummary->totalIntermedFeesUsd}
                    </textarea>
            </tr>
            <tr>
                <td>{$lang->invoicevalueusd}</td>
                <td><input type="text" id="ordersummary_invoicevalueusd_intermed" name="ordersummary[invoiceValueUsdIntermed]" value="{$aroordersummary->invoiceValueUsdIntermed}" style="width:150px;" readonly="readonly" /> </td>
                <td><input type="text" id="ordersummary_invoicevalueusd_local" name="ordersummary[invoiceValueUsdLocal]" value="{$aroordersummary->invoiceValueUsdLocal}" style="width:150px;" readonly="readonly"/> </td>

            </tr>
            <tr>
                <td>{$lang->total} {$lang->netmargin}</td>
                <td><input type="text" id="ordersummary_netmargin_intermed" name="ordersummary[netmarginIntermed]" value="{$aroordersummary->netmarginIntermed}" style="width:150px;" readonly="readonly"/> </td>
                <td><input type="text" id="ordersummary_netmargin_local" name="ordersummary[netmarginLocal]" value="{$aroordersummary->netmarginLocal}" style="width:150px;" readonly="readonly"/> </td>
                <!-- <td id="ordersummary_thirdparty_3"><input type="text" id="ordersummary_invoicevalue_thirdparty" value="" style="width:150px;" disabled="disabled"/> </td>-->
                <td id="ordersummary_thirdparty_4" {$ordersummary[thirdcolumn_display]}><input type="text" id="ordersummary_invoicevalueusd_thirdparty" name="ordersummary[invoiceValueThirdParty]" value="{$aroordersummary->invoiceValueThirdParty}" style="width:150px;" readonly="readonly"/> </td>
            </tr>
            <tr>
                <td>{$lang->total} {$lang->netmarginperc}</td>
                <td><input type="text" id="ordersummary_netmargin_intermedperc" name="ordersummary[netmarginIntermedPerc]" value="{$aroordersummary->netmarginIntermedPerc}" style="width:150px;" readonly="readonly"/> </td>
                <td><input type="text" id="ordersummary_netmargin_localperc" name="ordersummary[netmarginLocalPerc]" value="{$aroordersummary->netmarginLocalPerc}" style="width:150px;" readonly="readonly"/> </td>
            </tr>

            <tr>
                <td>{$lang->total} {$lang->globalnetmargin}</td>
                <td colspan="2"><input type="text" id="ordersummary_globalnetmargin" name="ordersummary[globalNetmargin]" value="{$aroordersummary->globalNetmargin}" style="width:150px;" readonly="readonly"/> </td>
            </tr>
            <tr>
                <td>{$lang->totalquantity}</td>
                <td colspan="2">
                    <textarea id="ordersummary_totalquantity" name="ordersummary[totalQuantity]" readonly="readonly" style="width:300px">{$aroordersummary->totalQuantity}
                    </textarea>
                </td>
            </tr>
            <tr>
                <td>{$lang->totalintermedfees}</td>
                <td colspan="2">
                    <textarea id="ordersummary_totalfees" name="ordersummary[totalIntermedFees]" readonly="readonly" style="width:300px">{$aroordersummary->totalIntermedFees}
                    </textarea>
                </td>
            </tr>
            <tr>
                <td>{$lang->unitfee}</td>
                <td><input type="text" id="ordersummary_unitfee" name="ordersummary[unitFee]" value="{$aroordersummary->unitFee}" readonly="readonly"/></td>
            </tr>
            <tr>
                <td>{$lang->initialtotalcomm}</td>
                <td><input type="text" id="ordersummary_initialtotalcomm" name="ordersummary[initialCommission]" value="{$aroordersummary->initialCommission}" readonly="readonly"/></td>
            </tr>
            <tr>
                <td>{$lang->totalcomm}</td>
                <td><input type="text" id="ordersummary_totalcomm" name="ordersummary[totalCommission]" readonly="readonly" value="{$aroordersummary->totalCommission}"/><input type="hidden" id="ordersummary_totalamount"/></td>
            </tr>
            <tr>
                <td>
                    <input type="button" id="ordersummary_btn" style="display:none"/>
                    <input type="button" id="unitfee_btn" style="display:none"/>
                </td>
            </tr>

        </tbody>
    </table>
    <div id="arrData"></div>
</p>
</div>


