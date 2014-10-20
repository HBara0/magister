<tr><td colspan="6"><hr /></td></tr>
<tr>
    <td style="font-weight:bold;">{$lang->financegeneralexpenses}</td>
    <td><input name="financialbudget[finGenAdmExpAmtApty]" id="finGenAdm_actualPrevTwoYears" accept="numeric" style="width:100%;" type="number" step="any" required="required" value="{$financialbudgetdata[actualPrevTwoYears]}" min="0" max="{$total[actualPrevTwoYears]}"></td>
    <td><input name="financialbudget[finGenAdmExpAmtBpy]" id="finGenAdm_budgetPrevYear" accept="numeric" style="width:100%;" type="number" step="any" required="required" value="{$financialbudgetdata[budgetPrevYear]}" min="0" max="{$total[budgetPrevYear]}"></td>
    <td><input name="financialbudget[finGenAdmExpAmtYpy]" id="finGenAdm_yefPrevYear" accept="numeric" style="width:100%;" type="number" step="any" required="required" value="{$financialbudgetdata[yefPrevYear]}" min="0" max="{$total[yefPrevYear]}"></td>
    <td><input name="financialbudget[finGenAdmExpAmtCurrent]" id="finGenAdm_budgetCurrent" accept="numeric" style="width:100%;" type="number" step="any" required="required" value="{$financialbudgetdata[budgetCurrent]}" min="0" max="{$total[budgetCurrent]}"></td>
    <td>&nbsp;</td>
</tr>
<tr>
    <td style="font-weight:bold;">{$lang->proportionfgaexpenses}</td>
    <td><div id="propfin_actualPrevTwoYears">{$propfin[actualPrevTwoYears]}</div></td>
    <td><div id="propfin_budgetPrevYear">{$propfin[budgetPrevYear]}</div></td>
    <td><div id="propfin_yefPrevYear">{$propfin[yefPrevYear]}</div></td>
    <td><div id="propfin_budgetCurrent">{$propfin[budgetCurrent]}</div></td>
    <td></td>
</tr>
<tr>
    <td style="font-weight:bold;">{$lang->commercialexpenses}</td>
    <td><div id="comexpenses_actualPrevTwoYears">{$comexpenses[actualPrevTwoYears]}</div></td>
    <td><div id="comexpenses_budgetPrevYear">{$comexpenses[budgetPrevYear]}</div></td>
    <td><div id="comexpenses_yefPrevYear">{$comexpenses[yefPrevYear]}</div></td>
    <td><div id="comexpenses_budgetCurrent">{$comexpenses[budgetCurrent]}</div></td>
    <td></td>
</tr>
<tr>
    <td style="font-weight:bold;">{$lang->proportioncommercialexpenses}</td>
    <td><div id="propcomexpenses_actualPrevTwoYears">{$propcomexpenses[actualPrevTwoYears]}</div></td>
    <td><div id="propcomexpenses_budgetPrevYear">{$propcomexpenses[budgetPrevYear]}</div></td>
    <td ><div id="propcomexpenses_yefPrevYear">{$propcomexpenses[yefPrevYear]}</div></td>
    <td><div id="propcomexpenses_budgetCurrent">{$propcomexpenses[budgetCurrent]}</div></td>
    <td></td>
</tr>