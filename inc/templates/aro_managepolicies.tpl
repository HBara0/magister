<script>
    $(function() {
        $(document).on("change", "input[id^='pickDate_from']", function() {
            var minDate = $("input[id^='altpickDate_from']").val();
            var date = minDate.split("-");
            $("input[id^='pickDate_to']").datepicker("option", "minDate", new Date(date[2], date[1] - 1, date[0]));
            //  $("input[id^='pickDate_to']").focus();
        })
    });
</script>
<h1>{$lang->managepolicies}</h1>
<form action="#" method="post" id="perform_aro/managepolicies_Form" name="perform_aro/managepolicies_Form">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="datatable" >
        <tr>
            <td>{$lang->affiliate}</td><td colspan="2">{$affiliates_list}
                <input type="hidden" value="{$aropolicy[apid]}" name="aropolicy[apid]" </td>
        </tr>
        <tr>
            <td>{$lang->country}</td>
            <td>
                <input id="countries_1_autocomplete" autocomplete="off" type="text" value="{$aropolicy[country]}" style="width:150px;">
                <input id="countries_1_id" name="aropolicy[coid]"  value="{$aropolicy[coid]}" type="hidden">
                <div id="searchQuickResults_1" class="searchQuickResults" style="display: none;"></div>

            </td>
        </tr>
        <tr>
            <td>{$lang->orderpurchasetype}</td><td colspan="2">{$purchasetypes_list}</td>
        </tr>
        <tr>
            <td>{$lang->effromdate}</td>
            <td>
                <input type="text" id="pickDate_from" autocomplete="off" tabindex="2" value="{$aropolicy[effectiveFrom_output]}" required="required" />
                <input type="hidden" name="aropolicy[effectiveFrom]" id="altpickDate_from" value="{$aropolicy[effectiveFrom_formatted]}"/>
            </td>
        </tr>
        <tr>
            <td>{$lang->eftodate}</td>
            <td>
                <input type="text" id="pickDate_to" autocomplete="off" tabindex="2" value="{$aropolicy[effectiveTo_output]}" required="required" />
                <input type="hidden" name="aropolicy[effectiveTo]" id="altpickDate_to" value="{$aropolicy[effectiveTo_formatted]}"/>
            </td>
        </tr>
        <tr>
            <td>{$lang->local} {$lang->yearlyintrestrate}</td>
            <td><input type="number" step="any" min="0" name="aropolicy[yearlyInterestRate]" id="aropolicy_yearlyInterestRate" value="{$aropolicy[yearlyInterestRate]}"/> %</td>
        </tr>
        <tr>
            <td>{$lang->defaultcommissioncharged}</td>
            <td><input type="number" step="any" min="0" name="aropolicy[commissionCharged]" id="aropolicy_commissionCharged" value="{$aropolicy[commissionCharged]}"/> %</td>
        </tr>
        <tr>
            <td>{$lang->default} {$lang->riskratio}</td>
            <td><input type="number" step="any" min="0" name="aropolicy[riskRatio]" id="aropolicy_riskRatio" value="{$aropolicy[riskRatio]}"/> %</td>
        </tr>
        <tr>
            <td>{$lang->riskratiodiffcurrcp}</td>
            <td><input type="number" step="any" min="0" name="aropolicy[riskRatioDiffCurrCP]" id="aropolicy_riskRatioDiffCurrCP" value="{$aropolicy[riskRatioDiffCurrCP]}"/> %</td>
        </tr>
        <tr>
            <td width="40%">{$lang->riskratiomonthlyincrease}</td>
            <td><input type="number" step="any" min="0" name="aropolicy[riskRatioIncreaseDiffCurrCN]" id="aropolicy_riskRatioMonthlyIncreaseDiffCurrCN" value="{$aropolicy[riskRatioIncreaseDiffCurrCN]}"/> % {$lang->each}
                <input type="number" step="any" min="0" name="aropolicy[riskRatioDays]" id="aropolicy_riskRatioDays" value="{$aropolicy[riskRatioDays]}"/> {$lang->days}</td>
        </tr>
        <tr>
            <td>{$lang->riskratiosamecurrcn}</td>
            <td><input type="number" step="any" min="0" name="aropolicy[riskRatioSameCurrCN]" id="aropolicy_riskRatioSameCurrCN" value="{$aropolicy[riskRatioSameCurrCN]}"/> %</td>
        </tr>
        <tr>
            <td>{$lang->default} {$lang->intermediary}</td><td>{$intermediary_list}</td>
        </tr>
        <tr>
            <td>{$lang->default} {$lang->incoterms}</td><td>{$incoterms_list}</td>
        </tr>
        <tr>
            <td>{$lang->default} {$lang->paymentterms}</td><td>{$paymentterms_list}</td>
        </tr>
        <tr>
            <td>{$lang->default} {$lang->acceptablepaymentterm}</td>
            <td><input type="number" step="any" min="0" name="aropolicy[defaultAcceptableMargin]" id="aropolicy_defaultAcceptableMargin" value="{$aropolicy[defaultAcceptableMargin]}"/></td>

        </tr>
        <tr>
            <td>{$lang->default} {$lang->currency}</td><td>{$currencies_list}</td>
        </tr>
        {$audittrail}
        <tr>
            <td>
                <input name="aropolicy[isActive]" id="aropolicy_isActive" type="checkbox" value="1" {$checked[isActive]}> {$lang->isactive}</td>
            </td>
        </tr>

        <tr>
            <td colspan="3" align="left" style="{$display[save]}">
                <input type="submit" value="{$lang->savecaps}" id="perform_aro/managepolicies_Button" class="button"/>
                <input type="reset" value="{$lang->reset}" class="button"/>
                <a class="button" href="{$core->settings['rootdir']}/index.php?module=aro/managepolicies&id={$aropolicy[apid]}&referrer=clone" value="Clone" target='_blank' style="{$display['clone']};padding-top:5px;color:white">
                    Clone</a>
                <div id="perform_aro/managepolicies_Results"></div>
            </td>
        </tr>
    </table>
</form>
