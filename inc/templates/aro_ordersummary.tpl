<a class="header" href="#" id="ordersummary"><h2>{$lang->ordersummary}</h2></a>
<div>
    <p>
    <table>
        <thead>
            <tr><td></td>
                <td class="subtitle"><input type="text" id="ordersummary_intermedaff" style="width:150px;" readonly></span></td>
                <td class="subtitle"><input type="text" id="ordersummary_localaff" style="width:150px;" readonly></span></td>
            </tr>
        </thead>
        <tbody style="width:100%;">

            <tr>
                <td>{$lang->invoicevalue}</td>
                <td><input type="text" id="ordersummary_invoicevalue_intermed" value="" style="width:150px;" disabled="disabled"/> </td>
                <td><input type="text" id="ordersummary_invoicevalue_local" value="" style="width:150px;" disabled="disabled"/> </td>
            </tr>
            <tr>
                <td>{$lang->feespaidbyintermed}</td>
                <td colspan="2">
                    <textarea id="ordersummary_totalintermedfees_usd" value="" disabled="disabled" style="width:300px">
                    </textarea>
            </tr>
            <tr>
                <td>{$lang->invoicevalueusd}</td>
                <td><input type="text" id="ordersummary_invoicevalueusd_intermed" value="" style="width:150px;" disabled="disabled"/> </td>
                <td><input type="text" id="ordersummary_invoicevalueusd_local" value="" style="width:150px;" disabled="disabled"/> </td>
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
                <td>{$lang->total} {$lang->totalquantity}</td>
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
                <td>
                    <input type="button" id="ordersummary_btn" style="display:none"/>
                </td>
            </tr>

        </tbody>
    </table>
    <div id="arrData"></div>
</p>
</div>


