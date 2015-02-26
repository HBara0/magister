<a class="header " href="#"><h2>{$lang->netmarginparameters}</h2></a>
<div>
    <table>
        <thead>
            <tr style="font-weight:bold;">
                <td class="border_right" rowspan="2" colspan="3" valign="top" align="center">{$lang->riskandbankratios}</td>
                <td class="border_right" rowspan="2" colspan=3" valign="top" align="center">{$lang->warehousing}</td>
            </tr>
        </thead>
        <tbody id="parmsfornetmargin_{$plrowid}_tbody" style="width:100%;">
            <tr>
                <td></td>
                <td class="border_right" valign="top">{$lang->local} </td>
                <td class="border_right" valign="top">{$lang->intermed}</td>
            </tr>
            <tr>
                <td valign="top">{$lang->yearlybankinterestrate}</td>
                <td class="border_right" ><input type="number" min="0" name="parmsfornetmargin[localBankInterestRate]" id="parmsfornetmargin_localBankInterestRate" value="" style="width:150px;" readonly/></td>
                <td class="border_right" ><input type="number" min="0" name="parmsfornetmargin[intermedBankInterestRate]" id="parmsfornetmargin_intermedBankInterestRate" value="" style="width:150px;" readonly/></td>
                <td class="border_right" valign="top">{$lang->warehouse} </td>
                <td id="warehouse_list_td"></td>
            </tr>
            <tr>
                <td valign="top">{$lang->periodofinterest}</td>
                <td class="border_right" ><input type="number" min="0" name="parmsfornetmargin[localPeriodOfInterest]" id="parmsfornetmargin_localPeriodOfInterest" value="" style="width:150px;" readonly/></td>
                <td class="border_right" ><input type="number" min="0" name="parmsfornetmargin[intermedPeriodOfInterest]" id="parmsfornetmargin_intermedPeriodOfInterest" value="" style="width:150px;" readonly/></td>
                <td class="border_right" valign="top">{$lang->rate}</td>
                <td>  <select name="parmsfornetmargin[warehousingRate]" id="parmsfornetmargin_warehousingRate" style="width:150px;"></select></td>
                <td valign="top">{$lang->period}</td>
                <td> <input type="text" name="parmsfornetmargin[warehousingPeriod]" id="parmsfornetmargin_warehousingPeriod" value="" style="width:150px;"/>
                </td>
            </tr>
            <tr>
                <td valign="top">{$lang->riskratio}</td>
                <td class="border_right" ><input type="text" name="parmsfornetmargin[localRiskRatio]" id="parmsfornetmargin_localRiskRatio" value="" style="width:150px;" readonly/></td>
                <td class="border_right" ><input type="text" name="parmsfornetmargin[intermedRiskRatio]" id="parmsfornetmargin_intermedRiskRatio" value="" style="width:150px;" readonly/></td>
                <td valign="top">{$lang->totalload}</td>
                <td><input type="text" name="parmsfornetmargin[warehousingTotalLoad]" id="parmsfornetmargin_warehousingTotalLoad" value="" style="width:150px;"/></td>
                <td valign="top">{$lang->uom}</td>
                <td> {$uom_list}</td>
            </tr>

        </tbody>
        <tfoot>

        </tfoot>
    </table>
</div>
</a>