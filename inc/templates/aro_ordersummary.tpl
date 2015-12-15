<a class="header" href="#" id="ordersummary"><h2 id="aro_ordersummary">{$lang->ordersummary}</h2></a>
<div>
    <table>
        <thead>
            <tr>
                <td></td>
                <td class="subtitle"> <input type="text" class="subtitle" id="ordersummary_col1_title" value="{$aroordersummary->firstpartytitle}" readonly/>{$firstparty_title}</td>
                <td class="subtitle"> <input type="text" class="subtitle" id="ordersummary_col2_title" value="{$aroordersummary->secondpartytitle}" readonly/>{$secondparty_title}</td>
                <td class="subtitle" id="ordersummary_thirdparty_1" {$ordersummarydisplay[thirdcolumn_display]}> <input type="text" class="subtitle" id="ordersummary_col3_title" value="{$aroordersummary->thirdpartytitle}"/>{$thirdparty_title}</td>
            </tr>
            <tr><td></td>
                <td class="subtitle"><input type="text" id="ordersummary_intermedaff" style="width:150px;" readonly value="{$firstparty}"></span></td>
                <td class="subtitle"><input type="text" id="ordersummary_2ndparty" style="width:150px;" readonly value="{$secondparty}"></span></td>
                <td class="subtitle" id="ordersummary_thirdparty_2" {$ordersummarydisplay[thirdcolumn_display]}><input type="text" id="ordersummary_3rdparty" style="width:150px;" readonly value="{$thirdparty}"></span>
                    <input type="hidden" value="" id="haveThirdParty"/></td>
            </tr>
        </thead>
        <tbody style="width:100%;">

            <tr>
                <td>{$lang->invoicevalue} (<span id="ordersummary_curr_0"></span>{$arorequest[currency]})</td>
                <td><input type="text" id="ordersummary_invoicevalue_intermed" name="ordersummary[invoiceValueIntermed]" value="{$aroordersummary->invoiceValueIntermed}" style="width:150px;" readonly="readonly"/> <a href="#" title="{$lang->invoicevalueintermeddef}"><img src="./images/icons/question.gif"/></a></td>
                <td><input type="text" id="ordersummary_invoicevalue_local" name="ordersummary[invoiceValueLocal]" value="{$aroordersummary->invoiceValueLocal}" style="width:150px;" readonly="readonly"/> <a href="#" title="{$lang->invoicevaluedef}"><img src="./images/icons/question.gif"/></a></td>
            </tr>
            <tr>
                <td>{$lang->invoicevalueusd}</td>
                <td><input type="text" id="ordersummary_invoicevalueusd_intermed" name="ordersummary[invoiceValueUsdIntermed]" value="{$aroordersummary->invoiceValueUsdIntermed}" style="width:150px;" readonly="readonly" /> </td>
                <td><input type="text" id="ordersummary_invoicevalueusd_local" name="ordersummary[invoiceValueUsdLocal]" value="{$aroordersummary->invoiceValueUsdLocal}" style="width:150px;" readonly="readonly"/> </td>

            </tr>
            <tr>
                <td>{$lang->totalquantity}</td>
                <td>
                    <textarea id="ordersummary_totalquantityperuom" name="ordersummary[totalQuantityUom]" readonly="readonly">
                        {$aroordersummary->totalQuantityUom} </textarea>
                </td>
            </tr>
            <!--<tr>
                <td>{$lang->totalquantity}</td>
                <td colspan="2">
                    <input type="text" id="ordersummary_totalquantity" name="ordersummary[totalQuantity]" readonly="readonly" style="width:300px" value="{$aroordersummary->totalQuantity}"/>
                </td>
            </tr>-->
            <tr>
                <td>{$lang->interestvalue} (<span id="ordersummary_curr_1"></span>{$arorequest[currency]}) <a href="#" title="{$lang->interestvaluedef}"><img src="./images/icons/question.gif"/></a></td>
                <td>
                    <input type="text" id="ordersummary_interestvalue" name="ordersummary[interestValue]" readonly="readonly" value="{$aroordersummary->interestValue}"/>
            </tr>
            <tr>
                <td>{$lang->interestvalue} (USD)</td>
                <td>
                    <input type="text" id="ordersummary_interestvalueUsd" name="ordersummary[interestValueUsd]" readonly="readonly" value="{$aroordersummary->interestValueUsd}"/>
            </tr>
            <tr>
                <td>{$lang->totalintermedfees} (<span id="ordersummary_curr_2"></span>{$arorequest[currency]})</td>
                <td>
                    <input type="text" id="ordersummary_totalfees" name="ordersummary[totalIntermedFees]" readonly="readonly" value="{$aroordersummary->totalIntermedFees}"/>
            </tr>
            <tr>
                <td>{$lang->feespaidbyintermed}</td>
                <td>
                    <input id="ordersummary_totalintermedfees_usd" name="ordersummary[totalIntermedFeesUsd]" readonly="readonly" value="{$aroordersummary->totalIntermedFeesUsd}"/>
            </tr>

            <tr>
                <td>{$lang->unitfee}</td>
                <td><input type="text" id="ordersummary_unitfee" name="ordersummary[unitFee]" value="{$aroordersummary->unitFee}" readonly="readonly"/></td>
            </tr>

            <tr style="font-weight:bold">
                <td>{$lang->total} {$lang->netmargin}<a href="#" title="{$lang->totalnetmargindef}"><img src="./images/icons/question.gif"/></a></td>
                <td><input type="text" id="ordersummary_netmargin_intermed" name="ordersummary[netmarginIntermed]" value="{$aroordersummary->netmarginIntermed}" style="width:150px;" readonly="readonly"/> </td>
                <td><input type="text" id="ordersummary_netmargin_local" name="ordersummary[netmarginLocal]" value="{$aroordersummary->netmarginLocal}" style="width:150px;" readonly="readonly"/> </td>
               <!-- <td id="ordersummary_thirdparty_4" {$ordersummarydisplay[thirdcolumn_display]}><input type="text" id="ordersummary_invoicevalueusd_thirdparty" name="ordersummary[invoiceValueThirdParty]" value="{$aroordersummary->invoiceValueThirdParty}" style="width:150px;" readonly="readonly"/> </td>-->
            </tr>

            <tr {$ordersummarydisplay[thirdcolumn_display]}>
                <td id="ordersummary_thirdparty_label">{$lang->total} {$lang->netmargin} <br/>{$lang->afterdeduction}</td>
                <td id="ordersummary_thirdparty_intermedafterdeduction"><input type="text" id="ordersummary_netmargin_intermedafterdeduction" name="" value="{$aroordersummary->netmarginIntermed_afterdeduction}" style="width:150px;" readonly="readonly"/> </td>
                <td id="ordersummary_thirdparty_afterdeduction"><input type="text" /></td>
                <td id="ordersummary_thirdparty_4" {$ordersummarydisplay[thirdcolumn_display]}><input type="text" id="ordersummary_invoicevalueusd_thirdparty" name="ordersummary[invoiceValueThirdParty]" value="{$aroordersummary->invoiceValueThirdParty}" style="width:150px;" readonly="readonly"/> </td>
            </tr>

            <tr style="font-weight:bold">
                <td>{$lang->total} {$lang->netmarginperc} <a href="#" title="{$lang->totalnetmarginpercdef}"><img src="./images/icons/question.gif"/></a></td>
                <td><input type="text" id="ordersummary_netmargin_intermedperc" name="ordersummary[netmarginIntermedPerc]" value="{$aroordersummary->netmarginIntermedPerc}" style="width:150px;" readonly="readonly"/>% </td>
                <td><input type="text" id="ordersummary_netmargin_localperc" name="ordersummary[netmarginLocalPerc]" value="{$aroordersummary->netmarginLocalPerc}" style="width:150px;" readonly="readonly"/>% </td>
            </tr>
            <tr style="font-weight:bold">
                <td>{$lang->total} {$lang->globalnetmargin}<a href="#" title="{$lang->globalnetmargindef}"><img src="./images/icons/question.gif"/></a></td>
                <td colspan="2"><input type="text" id="ordersummary_globalnetmargin" name="ordersummary[globalNetmargin]" value="{$aroordersummary->globalNetmargin}" style="width:150px;" readonly="readonly"/> </td>
            </tr>
        </tbody>
        <tfoot style="display:none;" id="ordersummary_tfoot" class="altrow2"  name="seemoredetails" >
            <tr>
                <td>{$lang->feespaidbyintermeduom}</td>
                <td colspan="2">
                    <textarea id="ordersummary_totalintermedfeesperunit_usd" name="ordersummary[totalIntermedFeesUsdUom]" readonly="readonly" style="width:300px">{$aroordersummary->totalIntermedFeesUsdUom}
                    </textarea>
            </tr>
            <tr>
                <td>{$lang->totalintermedfeesuom} (<span id="ordersummary_curr_3"></span>{$arorequest[currency]})</span></td>
                <td colspan="2">
                    <textarea id="ordersummary_totalfeesperunit" name="ordersummary[totalIntermedFeesUom]" readonly="readonly" style="width:300px">{$aroordersummary->totalIntermedFeesUom}
                    </textarea>
                </td>
            </tr>
            <tr>
                <td>{$lang->initialtotalcomm}</td>
                <td colspan="2">  <input type="text" id="ordersummary_initialtotalcomm" name="ordersummary[initialCommission]" value="{$aroordersummary->initialCommission}" readonly="readonly"/></td>
            </tr>
            <tr>
                <td>{$lang->totalcomm}</td>
                <td colspan="2"><input type="text" id="ordersummary_totalcomm" name="ordersummary[totalCommission]" readonly="readonly" value="{$aroordersummary->totalCommission}"/><input type="hidden" id="ordersummary_totalamount"/></td>
            </tr>
            <tr>
                <td>
                    <input type="button" id="ordersummary_btn" style="display:none"/>
                    <input type="button" id="unitfee_btn" style="display:none"/>
                </td>
            </tr>

        </tfoot>
    </table>
    <br/>
    <a href="#seemoredetails" id="ordersummary_seemore" class="altrow2" style="font-weight: bold;border:solid thin;padding:5px;">See More</a>

    <div id="arrData"></div>
</div>


