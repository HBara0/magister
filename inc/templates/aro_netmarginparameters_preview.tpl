<a class="header " href="#"><h2 id="aro_parmsfornetmargin">{$lang->netmarginparameters}</h2></a>
<div>
    <table>
        <thead>
            <tr style="font-weight:bold;">
                <td class="border_right" rowspan="2" colspan="3" valign="top" align="center">{$lang->riskandbankratios}</td>
                <td class="border_right" rowspan="2" colspan=3" valign="top" align="center">{$lang->warehousing}</td>
            </tr>
        </thead>
        <tbody id="parmsfornetmargin_{$plrowid}_tbody" class="datatable">
            <tr>
                <td><input type="hidden" name="parmsfornetmargin[anpid]" value="{$netmarginparms->anpid}"/></td>
                <td class="border_right" valign="top" style="font-weight:bold;width:15%;text-align: center">{$lang->local} </td>
                <td class="border_right" valign="top" style="font-weight:bold;width:15%;text-align: center">{$lang->intermed}</td>
            </tr>
            <tr class="altrow">
                <td valign="top">{$lang->yearlybankinterestrate}</td>
                <td class="border_right" style="padding-left:10px;">{$netmarginparms->localBankInterestRate}</td>
                <td class="border_right" style="padding-left:10px;">{$netmarginparms->intermedBankInterestRate}</td>
                <td class="border_right" valign="top">{$lang->warehouse}</td>
                <td id="warehouse_list_td" style="width:15%">{$warehouse_output}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td valign="top">{$lang->periodofinterest}</td>
                <td class="border_right" style="padding-left:10px;">{$netmarginparms->localPeriodOfInterest}</td>
                <td class="border_right" style="padding-left:10px;">{$netmarginparms->intermedPeriodOfInterest}</td>
                <td class="border_right" valign="top">{$lang->rate}</td>
                <td style="padding-left:10px;"> {$netmarginparms_warehousingRate_output}</td>
                <td valign="top">{$lang->period}</td>
                <td style="width:15%;padding-left:10px;">{$netmarginparms->warehousingPeriod} </td>
            </tr>
            <tr class="altrow">
                <td valign="top">{$lang->riskratio} <a class="hidden-print" href="#" title="{$lang->zeroriskratio}"><img src="./images/icons/question.gif"/></a></td>
                <td class="border_right" style="padding-left:10px;" >{$netmarginparms->localRiskRatio}</td>
                <td class="border_right" ></td>
                <td valign="top">{$lang->totalload}</td>
                <td style="padding-left:10px;">{$netmarginparms->warehousingTotalLoad}</td>
                <td valign="top">{$lang->uom}</td>
                <td style="padding-left:10px;">{$warehouse_uom_output}</td>
            </tr>
            <tr>
                <td valign="top">{$lang->interestvalue}</td>
                <td></td>
                <td style="padding-left:10px;">{$netmarginparms->interestValue}</td>
                <td>Exchange rate to USD</td>
                <td style="padding-left:10px;">{$netmarginparms->warehouseUsdExchangeRate}</td>
                <td>Rate in USD</td>
                <td style="padding-left:10px;">{$netmarginparms_warehousingRateUsd_output}</td>
            </tr>

        </tbody>
        <tfoot>

        </tfoot>
    </table>
</div>
</a>