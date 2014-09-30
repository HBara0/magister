<tr><td colspan="6"><hr /></td></tr>
<tr>
    <td style="font-weight:bold;">{$lang->financegeneralexpenses}</td>
    <td><input name="financialbudget[finGenAdmExpAmtApty]" id="finGenAdm_actualPrevTwoYears" accept="numeric" style="width:100%;" type="number" step="any" required="required" value="{$financialbudget->finGenAdmExpAmtApty}" min="0" max="{$total[actualPrevTwoYears]}"></td>
    <td><input name="financialbudget[finGenAdmExpAmtBpy]" id="finGenAdm_budgetPrevYear" accept="numeric" style="width:100%;" type="number" step="any" required="required" value="{$financialbudget->finGenAdmExpAmtBpy}" min="0" max="{$total[budgetPrevYear]}"></td>
    <td><input name="financialbudget[finGenAdmExpAmtYpy]" id="finGenAdm_yefPrevYear" accept="numeric" style="width:100%;" type="number" step="any" required="required" value="{$financialbudget->finGenAdmExpAmtYpy}" min="0" max="{$total[yefPrevYear]}"></td>
    <td><input name="financialbudget[finGenAdmExpAmtCurrent]" id="finGenAdm_budgetCurrent" accept="numeric" style="width:100%;" type="number" step="any" required="required" value="{$financialbudget->finGenAdmExpAmtCurrent}" min="0" max="{$total[budgetCurrent]}"></td>
    <td>&nbsp;</td>
</tr>
<tr>
    <td style="font-weight:bold;">{$lang->proportionfgaexpenses}</td>
    <td><div id="propfin_actualPrevTwoYears"></div></td>
    <td><div id="propfin_budgetPrevYear"></div></td>
    <td><div id="propfin_yefPrevYear"></div></td>
    <td><div id="propfin_budgetCurrent"></div></td>
    <td></td>
</tr>
<tr>
    <td style="font-weight:bold;">{$lang->commercialexpenses}</td>
    <td><div id="comexpenses_actualPrevTwoYears"></div></td>
    <td><div id="comexpenses_budgetPrevYear"></div></td>
    <td><div id="comexpenses_yefPrevYear"></div></td>
    <td><div id="comexpenses_budgetCurrent"></div></td>
    <td></td>
</tr>
<tr>
    <td style="font-weight:bold;">{$lang->proportioncommercialexpenses}</td>
    <td><div id="propcomexpenses_actualPrevTwoYears"></div></td>
    <td><div id="propcomexpenses_budgetPrevYear"></div></td>
    <td ><div id="propcomexpenses_yefPrevYear"></div></td>
    <td><div id="propcomexpenses_budgetCurrent"></div></td>
    <td></td>
</tr>
</table>