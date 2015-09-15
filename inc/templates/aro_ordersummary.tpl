<a class="header" href="#" id="ordersummary"><h2>{$lang->ordersummary}</h2></a>
<div>
    <p>
    <table>
        <thead>
            <tr>
                <td></td>
                <td class="subtitle"> <input type="text" class="subtitle" id="ordersummary_col1_title"/>{$firstparty_title}</td>
                <td class="subtitle"> <input type="text" class="subtitle" id="ordersummary_col2_title"/>{$secondparty_title}</td>
                <td class="subtitle" id="ordersummary_thirdparty_1"> <input type="text" class="subtitle" id="ordersummary_col3_title"/>{$secondparty_title}</td>
            </tr>
            <tr><td></td>
                <td class="subtitle"><input type="text" id="ordersummary_intermedaff" style="width:150px;" readonly></span></td>
                <td class="subtitle"><input type="text" id="ordersummary_2ndparty" style="width:150px;" readonly></span></td>
                <td class="subtitle" id="ordersummary_thirdparty_2"><input type="text" id="ordersummary_3rdparty" style="width:150px;" readonly></span>
                    <input type="hidden" value="" id="haveThirdParty"/></td>

            </tr>
        </thead>
        <tbody style="width:100%;">

            <tr>
                <td>{$lang->invoicevalue}</td>
                <td><input type="text" id="ordersummary_invoicevalue_intermed" value="" style="width:150px;" disabled="disabled"/> </td>
                <td><input type="text" id="ordersummary_invoicevalue_local" value="" style="width:150px;" disabled="disabled"/> </td>
                <td id="ordersummary_thirdparty_3"><input type="text" id="ordersummary_invoicevalue_thirdparty" value="" style="width:150px;" disabled="disabled"/> </td>

            </tr>
            <tr>
                <td>{$lang->feespaidbyintermed}</td>
                <td colspan="2">
                    <textarea id="ordersummary_totalintermedfees_usd" value="" disabled="disabled" style="width:300px">
                    </textarea>
            </tr>
            <tr>
                <td>{$lang->invoicevalueusd}</td>
                <td><input type="text" id="ordersummary_invoicevalueusd_intermed" style="width:150px;" /> </td>
                <td><input type="text" id="ordersummary_invoicevalueusd_local" value="" style="width:150px;" disabled="disabled"/> </td>
                <td id="ordersummary_thirdparty_4"><input type="text" id="ordersummary_invoicevalueusd_thirdparty" value="" style="width:150px;" disabled="disabled"/> </td>

            </tr>
            <tr>
                <td>{$lang->total} {$lang->netmargin}</td>
                <td><input type="text" id="ordersummary_netmargin_intermed" value="" style="width:150px;" disabled="disabled"/> </td>
                <td><input type="text" id="ordersummary_netmargin_local" value="" style="width:150px;" disabled="disabled"/> </td>
            </tr>
            <tr>
                <td>{$lang->total} {$lang->netmarginperc}</td>
                <td><input type="text" id="ordersummary_netmargin_intermedperc" value="" style="width:150px;" disabled="disabled"/> </td>
                <td><input type="text" id="ordersummary_netmargin_localperc" value="" style="width:150px;" disabled="disabled"/> </td>
            </tr>

            <tr>
                <td>{$lang->total} {$lang->globalnetmargin}</td>
                <td colspan="2"><input type="text" id="ordersummary_globalnetmargin" value="" style="width:150px;" disabled="disabled"/> </td>
            </tr>
            <tr>
                <td>{$lang->totalquantity}</td>
                <td colspan="2">
                    <textarea id="ordersummary_totalquantity" value="" disabled="disabled" style="width:300px">
                    </textarea>
                </td>
            </tr>
            <tr>
                <td>{$lang->totalintermedfees}</td>
                <td colspan="2">
                    <textarea id="ordersummary_totalfees" value="" disabled="disabled" style="width:300px">
                    </textarea>
                </td>
            </tr>
            <tr>
                <td>{$lang->unitfee}</td>
                <td><input type="text" id="ordersummary_unitfee"/></td>
            </tr>
            <tr>
                <td>{$lang->initialtotalcomm}</td>
                <td><input type="text" id="ordersummary_initialtotalcomm"/></td>
            </tr>
            <tr>
                <td>{$lang->totalcomm}</td>
                <td><input type="text" id="ordersummary_totalcomm"/><input type="hidden" id="ordersummary_totalamount"/></td>
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


