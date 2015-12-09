<a class="header" href="#" id="ordersummary"><h2 id="aro_ordersummary">{$lang->ordersummary}</h2></a>
<div>
    <table>
        <thead>
            <tr>
                <td></td>
                <td class="subtitle" style="text-align: center;width:25%;"> {$aroordersummary->firstpartytitle}</td>
                <td class="subtitle" style="text-align: center;width:25%;"> {$aroordersummary->secondpartytitle}</td>
                <td class="subtitle" id="ordersummary_thirdparty_1" {$ordersummarydisplay[thirdcolumn_display]}>{$aroordersummary->thirdpartytitle}</td>
            </tr>
            <tr><td></td>
                <td class="subtitle" style="text-align: center;width:25%;">{$firstparty}</span></td>
                <td class="subtitle" style="text-align: center;width:25%;">{$secondparty}</span></td>
                <td class="subtitle" id="ordersummary_thirdparty_2" {$ordersummarydisplay[thirdcolumn_display]}>{$thirdparty}</span>
                    <input type="hidden" value="" id="haveThirdParty"/></td>
            </tr>
        </thead>
        <tbody style="width:100%;"  class="datatable">

            <tr style="font-weight: bold;">
                <td>{$lang->invoicevalue} (<span id="ordersummary_curr_0"></span>{$arorequest[currency]}) <a href="#" title="{$lang->invoicevaluedef}"><img src="./images/icons/question.gif"/></a></td>
                <td style="text-align: right;">{$aroordersummary->invoiceValueIntermed}</td>
                <td style="text-align: right;">{$aroordersummary->invoiceValueLocal}</td>
            </tr>
            <tr style="font-weight: bold;">
                <td>{$lang->invoicevalueusd}</td>
                <td style="text-align: right;">{$aroordersummary->invoiceValueUsdIntermed}</td>
                <td style="text-align: right;">{$aroordersummary->invoiceValueUsdLocal}</td>

            </tr>
            <tr>
                <td>{$lang->totalquantity}</td>
                <td style="text-align: right;">{$aroordersummary->totalQuantityUom}</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td>{$lang->interestvalue} (<span id="ordersummary_curr_1"></span>{$arorequest[currency]}) <a href="#" title="{$lang->interestvaluedef}"><img src="./images/icons/question.gif"/></a></td>
                <td style="text-align: right;">{$aroordersummary->interestValue}</td>
                <td colspan="2"></td>

            </tr>
            <tr>
                <td>{$lang->interestvalue} (USD)</td>
                <td style="text-align: right;">{$aroordersummary->interestValueUsd}</td>
                <td colspan="2"></td>

            </tr>
            <tr>
                <td>{$lang->totalintermedfees} (<span id="ordersummary_curr_2"></span>{$arorequest[currency]})</td>
                <td style="text-align: right;">{$aroordersummary->totalIntermedFees}</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td>{$lang->feespaidbyintermed}</td>
                <td style="text-align: right;">{$aroordersummary->totalIntermedFeesUsd}</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td>{$lang->unitfee}</td>
                <td style="text-align: right;">{$aroordersummary->unitFee}</td>
                <td colspan="2"></td>
            </tr>

            <tr style="font-weight: bold;">
                <td>{$lang->total} {$lang->netmargin}<a href="#" title="{$lang->totalnetmargindef}"><img src="./images/icons/question.gif"/></a></td>
                <td style="text-align: right;">{$aroordersummary->netmarginIntermed}</td>
                <td style="text-align: right;">{$aroordersummary->netmarginLocal}</td>
                <td id="ordersummary_thirdparty_4" {$ordersummarydisplay[thirdcolumn_display]}>{$aroordersummary->invoiceValueThirdParty}</td>
            </tr>
            <tr style="font-weight: bold;">
                <td>{$lang->total} {$lang->netmarginperc} <a href="#" title="{$lang->totalnetmarginpercdef}"><img src="./images/icons/question.gif"/></a></td>
                <td style="text-align: right;">{$aroordersummary->netmarginIntermedPerc}%</td>
                <td style="text-align: right;">{$aroordersummary->netmarginLocalPerc}%</td>
            </tr>
            <tr style="font-weight: bold;">
                <td>{$lang->total} {$lang->globalnetmargin}<a href="#" title="{$lang->globalnetmargindef}"><img src="./images/icons/question.gif"/></a></td>
                <td style="text-align:center;"colspan="2">{$aroordersummary->globalNetmargin}</td>
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


