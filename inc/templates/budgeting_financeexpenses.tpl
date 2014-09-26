<table class="datatable" style="width:85%;">
    <tr class="" style="width:100%;">
        <td style="width:30%;font-weight:bold;">{$lang->financegeneralexpenses}</td>
        <td style="width:15%"><input name="financialbudget[finGenAdmExpAmtApty]" id="finGenAdm_actualPrevTwoYears" accept="numeric" style="width:100%;" type="number" required="required" value="{$financialbudget->finGenAdmExpAmtApty}" min="0" max="{$total[actualPrevTwoYears]}"></td>
        <td style="width:15%"><input name="financialbudget[finGenAdmExpAmtBpy]" id="finGenAdm_budgetPrevYear" accept="numeric" style="width:100%;" type="number" required="required" value="{$financialbudget->finGenAdmExpAmtBpy}" min="0" max="{$total[budgetPrevYear]}"></td>
        <td style="width:15%"><input name="financialbudget[finGenAdmExpAmtYpy]" id="finGenAdm_yefPrevYear" accept="numeric" style="width:100%;" type="number" required="required" value="{$financialbudget->finGenAdmExpAmtYpy}" min="0" max="{$total[yefPrevYear]}"></td>
        <td style="width:15%"><input name="financialbudget[finGenAdmExpAmtCurrent]" id="finGenAdm_budgetCurrent"  accept="numeric" style="width:100%;" type="number" required="required" value="{$financialbudget->finGenAdmExpAmtCurrent}" min="0" max="{$total[budgetCurrent]}"></td>
    </tr>
    <tr class="" style="width:100%;">
        <td style="width:30%;font-weight:bold;">{$lang->proportionfgaexpenses}</td>
        <td style="width:15%"><div id="propfin_actualPrevTwoYears"></div></td>
        <td style="width:15%"><div id="propfin_budgetPrevYear"></div></td>
        <td style="width:15%"><div id="propfin_yefPrevYear"></div></td>
        <td style="width:15%"><div id="propfin_budgetCurrent"></div></td>
    </tr>
    <tr class="" style="width:100%;">
        <td style="width:30%;font-weight:bold;">{$lang->commercialexpenses}</td>
        <td style="width:15%"><div id="comexpenses_actualPrevTwoYears"></div></td>
        <td style="width:15%"><div id="comexpenses_budgetPrevYear"></div></td>
        <td style="width:15%"><div id="comexpenses_yefPrevYear"></div></td>
        <td style="width:15%"><div id="comexpenses_budgetCurrent"></div></td>
    </tr>
    <tr class="" style="width:100%;">
        <td style="width:30%;font-weight:bold;">{$lang->proportioncommercialexpenses}</td>
        <td style="width:15%"><div id="propcomexpenses_actualPrevTwoYears"></div></td>
        <td style="width:15%"><div id="propcomexpenses_budgetPrevYear"></div></td>
        <td style="width:15%"><div id="propcomexpenses_yefPrevYear"></div></td>
        <td style="width:15%"><div id="propcomexpenses_budgetCurrent"></div></td>
    </tr>

</table>