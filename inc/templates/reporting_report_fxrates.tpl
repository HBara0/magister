<page style="page-break-before:always;">
<a name="currenciesoverview">     
<table style="width:100%;">
<tr>
<td class="logo" style="width:100%;">&nbsp;</td>
</tr>
<tr>
    <td style="width:100%; text-align:left;">
    	<table class="reportbox" style="width: 100%;">
        	<tr><td width="25%" colspan="{$fxratespage_tablecolspan}" class="cathead">{$lang->currenciesfxrate}</td></tr>
                <tr><td colspan="{$fxratespage_tablecolspan}" class="thead">{$lang->quarterfxaverage} - Q{$report[quarter]}/{$report[year]}</td></tr>
                {$fxratespage_tablehead}
    		{$fx_rates_entries}
                <tr><td colspan="{$fxratespage_tablecolspan}" class="thead">EUR USD Monthly Average</td></tr>
                {$fx_rates_chart}
                {$fx_usdrates_chart}
            <tr><td width="25%" colspan="{$fxratespage_tablecolspan}" class="altrow" style="font-style:italic;">{$lang->currenciesfxratenote}</td></tr>
        </table>
    </td>
</tr>
</table>

</page>