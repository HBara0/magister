<div style="font-style:italic; text-align:right; width:100%;">{$lang->amountsinmt}</div>
<table class="reportbox">
	<tr><td colspan="6" class="thead">{$lang->quarterscomparison}</td></tr>
	<tr>
    	<td class="columnhead">Q</td><td class="columnhead" style="width: 16px;">{$previous_year}</td><td class="columnhead" style="width: 16px;">{$current_year}</td><td class="columnhead" style="width: 16px;">{$current_year} VS {$previous_year}</td><td class="columnhead" style="width: 16px;">{$current_year} {$lang->salesforecast}</td><td class="columnhead" style="width: 16px;">{$lang->achievedpercentage}</td>
    </tr>
    {$quarters_quantitiescompare_rows}
    <tr><td>&nbsp;</td><td class="totalscell">{$quarter_totals_output[$previous_year][quantity][$report[affid]]}</td><td class="totalscell">{$quarter_totals_output[$current_year][quantity][$report[affid]]}</td><td class="totalscell">{$quarters_quantitiescompare_achivementprevyear}%</td><td class="totalscell">{$quarters_quantitiescompare_achivementforecast}%</td></tr>
</table>
<br />
<table class="reportbox">
<tr>
<td colspan="15" class="thead">{$lang->quantitiesperiodunderreview}</td>
  </tr>
  <tr>
    <td class="horizontalspacer">&nbsp;</td>
    <td colspan="13" class="verticalspacer">&nbsp;</td>
    <td class="horizontalspacer">&nbsp;</td>
  </tr>
  <tr>
    <td class="horizontalspacer">&nbsp;</td>
    <td>(MT)</td>
    <td class="verticalspacer">&nbsp;</td>
    <td class="columnhead">{$lang->uptoq}{$current_quarter} {$previous_year}</td>
    <td class="horizontalspacer">&nbsp;</td>
    <td class="columnhead">{$lang->uptoq}{$current_quarter} {$current_year}</td>
	<td class="horizontalspacer">&nbsp;</td>
    <td class="columnhead">{$lang->uptoq}{$current_quarter} {$current_year}</td>
    <td class="horizontalspacer">&nbsp;</td>
    <td class="columnhead">{$previous_year}</td>
    <td class="horizontalspacer">&nbsp;</td>
    <td class="columnhead">{$current_year}</td>
    <td class="horizontalspacer">&nbsp;</td>
    <td class="columnhead">{$lang->uptoq}{$current_quarter} {$current_year}</td>
    <td class="horizontalspacer">&nbsp;</td>
  </tr>
  <tr>
    <td class="horizontalspacer">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="verticalspacer">&nbsp;</td>
    <td class="columnsubhead">{$lang->actualquantities}</td>
    <td class="horizontalspacer">&nbsp;</td>
    <td class="columnsubhead">{$lang->actualquantities}</td>
    <td class="horizontalspacer">&nbsp;</td>
	<td class="columnsubhead">{$lang->actualsoldquantities}</td>
    <td class="horizontalspacer">&nbsp;</td>
    <td class="columnsubhead">{$lang->actualquantities}</td>
    <td class="horizontalspacer">&nbsp;</td>
    <td class="columnsubhead">{$lang->quantitiesforecast}</td>
    <td class="horizontalspacer">&nbsp;</td>
    <td class="columnsubhead">{$lang->achievedpercentage}</td>
    <td class="horizontalspacer">&nbsp;</td>
  </tr>
  <tr>
    <td class="horizontalspacer">&nbsp;</td>
    <td colspan="13" class="verticalspacer">&nbsp;</td>
    <td class="horizontalspacer">&nbsp;</td>
  </tr>
  <tr>
    <td class="horizontalspacer">&nbsp;</td>
    <td>{$lang->totalquantities}</td>
    <td class="verticalspacer">&nbsp;</td>
    <td class="totalscell">{$totals_output[uptoprevquarterquantities]}</td>
    <td class="horizontalspacer">&nbsp;</td>
    <td class="totalscell">{$totals_output[uptoquarterquantities]}</td>
	<td class="horizontalspacer">&nbsp;</td>
    <td class="totalscell">{$totals_output[uptoquartersoldqty]}</td>
    <td class="horizontalspacer">&nbsp;</td>
    <td class="totalscell">{$totals_output[prevyearquantities]}</td>
    <td class="horizontalspacer">&nbsp;</td>
    <td class="totalscell">{$totals_output[quantitiesforecast]}</td>
    <td class="horizontalspacer">&nbsp;</td>
    <td class="totalscell">{$totals_output[quantitiesachievedpercentage]}%</td>
    <td class="horizontalspacer">&nbsp;</td>
  </tr>
  <tr>
    <td class="horizontalspacer">&nbsp;</td>
    <td colspan="13" class="verticalspacer">&nbsp;</td>
    <td class="horizontalspacer">&nbsp;</td>
  </tr>
	{$quantitiesforperiod}
  <tr>
    <td class="horizontalspacer">&nbsp;</td>
    <td colspan="13" class="verticalspacer">&nbsp;</td>
    <td class="horizontalspacer">&nbsp;</td>
  </tr>
</table>
<br />