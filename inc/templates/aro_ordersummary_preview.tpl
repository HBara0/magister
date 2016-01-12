<a class="header" href="#" id="ordersummary"><h2 id="aro_ordersummary">{$lang->ordersummary}</h2></a>
<div>
    <table>
        <thead>
            <tr>
                <td></td>
                <td class="subtitle" style="text-align: center;width:15%;"> {$aroordersummary->firstpartytitle}</td>
                <td class="subtitle" style="text-align: center;width:15%;"> {$aroordersummary->secondpartytitle}</td>
                <td class="subtitle" id="ordersummary_thirdparty_1" {$ordersummarydisplay[thirdcolumn_display]}>
                    <div style="text-align: center;">{$aroordersummary->thirdpartytitle}</div></td>
            </tr>
            <tr><td></td>
                <td class="subtitle" style="text-align: center;width:25%;">{$firstparty}</td>
                <td class="subtitle" style="text-align: center;width:25%;">{$secondparty}</td>
                <td class="subtitle" id="ordersummary_thirdparty_2" {$ordersummarydisplay[thirdcolumn_display]}>
                    <div style="text-align: center;">{$thirdparty}</div>
                    <input type="hidden" value="" id="haveThirdParty"/></td>
            </tr>
        </thead>
        <tbody style="width:100%;"  class="datatable">

            <tr style="font-weight: bold;background-color: #D0F6AA">
                <td class="altrow">{$lang->invoicevalue} (<span id="ordersummary_curr_0"></span>{$arorequest[currency]}) <a href="#" title="{$lang->invoicevaluedef}"><img src="./images/icons/question.gif"/></a></td>
                <td style="text-align: right;">{$aroordersummary->invoiceValueIntermed}</td>
                <td style="text-align: right;">{$aroordersummary->invoiceValueLocal}</td>
                <td {$ordersummarydisplay[thirdcolumn_display]}></td>
            </tr>
            <tr style="font-weight: bold;background-color: #D0F6AA">
                <td class="altrow">{$lang->invoicevalueusd}</td>
                <td style="text-align: right;">{$aroordersummary->invoiceValueUsdIntermed}</td>
                <td style="text-align: right;">{$aroordersummary->invoiceValueUsdLocal}</td>
                <td {$ordersummarydisplay[thirdcolumn_display]}></td>
            </tr>
            <tr style="background-color: #D0F6AA">
                <td class="altrow">{$lang->totalquantity}</td>
                <td style="text-align: left;" colspan="3">{$aroordersummary->totalQuantityUom}</td>
            </tr>
            <tr style="background-color: #D0F6AA">
                <td class="altrow">{$lang->interestvalue} (<span id="ordersummary_curr_1"></span>{$arorequest[currency]}) <a href="#" title="{$lang->interestvaluedef}"><img src="./images/icons/question.gif"/></a></td>
                <td style="text-align: right;">{$aroordersummary->interestValue}</td>
                <td></td> <td {$ordersummarydisplay[thirdcolumn_display]}></td>

            </tr>
            <tr style="background-color: #D0F6AA">
                <td class="altrow">{$lang->interestvalue} (USD)</td>
                <td style="text-align: right;">{$aroordersummary->interestValueUsd}</td>
                <td></td> <td {$ordersummarydisplay[thirdcolumn_display]}></td>
            </tr>
            <tr style="background-color: #D0F6AA">
                <td class="altrow">{$lang->totalintermedfees} (<span id="ordersummary_curr_2"></span>{$arorequest[currency]})</td>
                <td style="text-align: right;">{$aroordersummary->totalIntermedFees}</td>
                <td></td> <td {$ordersummarydisplay[thirdcolumn_display]}></td>
            </tr>
            <tr style="background-color: #D0F6AA">
                <td class="altrow">{$lang->feespaidbyintermed}</td>
                <td style="text-align: right;">{$aroordersummary->totalIntermedFeesUsd}</td>
                <td></td> <td {$ordersummarydisplay[thirdcolumn_display]}></td>
            </tr>
            <tr style="background-color: #D0F6AA">
                <td class="altrow">{$lang->unitfee}</td>
                <td style="text-align: right;">{$aroordersummary->unitFee}</td>
                <td></td> <td {$ordersummarydisplay[thirdcolumn_display]}></td>
            </tr>

            <tr style="font-weight: bold;background-color: #D0F6AA">
                <td class="altrow">{$lang->total} {$lang->netmargin}<a href="#" title="{$lang->totalnetmargindef}"><img src="./images/icons/question.gif"/></a></td>
                <td style="text-align: right;">{$aroordersummary->netmarginIntermed}</td>
                <td style="text-align: right;">{$aroordersummary->netmarginLocal}</td>
                <td {$ordersummarydisplay[thirdcolumn_display]}></td>
            </tr>

            <tr {$ordersummarydisplay[thirdcolumn_display]}>
                <td class="altrow">{$lang->total} {$lang->netmargin} <br/>{$lang->afterdeduction}<a href="#" title="{$lang->totalnetmarginafterdeduction}"><img src="./images/icons/question.gif"/></a></td>
                <td style="text-align: right;background-color: #D0F6AA">{$aroordersummary->netmarginIntermed_afterdeduction}</td>
                <td style="text-align: right;background-color: #D0F6AA">{$aroordersummary->netmarginLocal}</td>
                <td id="ordersummary_thirdparty_4" style="text-align: right;background-color: #D0F6AA"><div style="text-align: right;">{$aroordersummary->invoiceValueThirdParty}</div></td>
            </tr>

            <tr style="font-weight: bold;background-color: #D0F6AA">
                 <td class="altrow">{$lang->total} {$lang->netmarginperc} <!--<a href="#" title="{$lang->totalnetmarginpercdef}"><img src="./images/icons/question.gif"/></a>--></td>
                <td style="text-align: right;">{$aroordersummary->netmarginIntermedPerc}%</td>
                <td style="text-align: right;">{$aroordersummary->netmarginLocalPerc}%</td>
                <td {$ordersummarydisplay[thirdcolumn_display]}> <div style="text-align: right;">{$aroordersummary->marginPercThirdParty}%</div></td>
            </tr>
            <tr style="font-weight: bold;background-color: #D0F6AA">
                <td class="altrow">{$lang->total} {$lang->globalnetmargin}
                    <a href="#" title="{$lang->globalnetmargindef}"><img src="./images/icons/question.gif"/></a></td>
                <td style="text-align:center" colspan="2">{$aroordersummary->globalNetmargin}</td>
                <td {$ordersummarydisplay[thirdcolumn_display]}</td>
            </tr>
            <tr style="font-weight: bold;background-color: #D0F6AA">
                <td class="altrow">{$lang->total} {$lang->globalnetmarginperc} %</td>
                <td style="text-align:center;" colspan="2">{$aroordersummary->globalNetmarginPerc}%</td>
                <td {$ordersummarydisplay[thirdcolumn_display]}</td>
            </tr>
        </tbody>
        <tfoot style="display:none;" id="ordersummary_tfoot" class="altrow2"  name="seemoredetails" >
            <tr>
                <td>{$lang->feespaidbyintermeduom}</td>
                <td colspan="2">{$aroordersummary->totalIntermedFeesUsdUom}</td>
            </tr>
            <tr>
                <td>{$lang->totalintermedfeesuom} (<span id="ordersummary_curr_3"></span>{$arorequest[currency]})</span></td>
                <td colspan="2">{$aroordersummary->totalIntermedFeesUom}
                </td>
            </tr>
            <tr>
                <td>{$lang->initialtotalcomm}</td>
                <td colspan="2">{$aroordersummary->initialCommission}</td>
            </tr>
            <tr>
                <td>{$lang->totalcomm}</td>
                <td colspan="2">{$aroordersummary->totalCommission}</td>
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


