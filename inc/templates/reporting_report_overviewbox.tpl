<page>
    <table style="width:100%;">
        <tr>
            <td class="logo" style="width:100%;">&nbsp;</td>
        </tr>
        <tr>
            <td style="font-style:italic; text-align:right; width:100%;">{$lang->amountsincurrency}</td>
        </tr>
        <tr>
            <td style="width:100%; text-align:center;">
                <table class="reportbox" style="width: 100%;">
                    <thead>
                        <tr><td colspan="5" class="thead"style="width: 50%;">&nbsp;</td><td colspan="2" class="thead" style="width: 25%;">{$lang->differencecurrency}</td><td colspan="2" class="thead" style="width: 25%;">{$lang->differencepercentage}</td></tr>
                        <tr><td colspan="9" class="verticalspacer">&nbsp;</td></tr>
                        <tr>
                            <td width="15%">&nbsp;</td>
                            <td class="columnhead" width="10%">{$lang->uptoq}{$current_quarter}<br />{$previous_year}</td>
                            <td class="columnhead" width="10%">{$lang->uptoq}{$current_quarter}<br />{$current_year}</td>
                            <td class="columnhead" width="10%">{$previous_year}</td>
                            <td class="columnhead" width="10%">{$current_year}</td>
                            <td class="columnhead" width="10%">{$lang->uptoq}{$current_quarter}<br />{$current_year}</td>
                            <td class="columnhead" width="10%">{$current_year}</td>
                            <td class="columnhead" width="10%">{$lang->uptoq}{$current_quarter}<br />{$current_year}</td>
                            <td class="columnhead" width="10%">{$current_year}</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td class="columnsubhead">{$lang->actualvalues}</td>
                            <td class="columnsubhead">{$lang->actualvalues}</td>
                            <td class="columnsubhead">{$lang->actualvalues}</td>
                            <td class="columnsubhead">{$lang->forecastedvalues}</td>
                            <td class="columnsubhead">{$lang->actualvalues}</td>
                            <td class="columnsubhead">{$lang->forecastedvalues}</td>
                            <td class="columnsubhead">{$lang->actualvalues}</td>
                            <td class="columnsubhead">{$lang->forecastedvalues}</td>
                        </tr>
                        <tr><td colspan="9" class="verticalspacer">&nbsp;</td></tr>
                    </thead>
                    <tbody>
                        {$affiliatestotals_list}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>&nbsp;</td>
                            <td class="totalscell">{$totalvalues_output[totaluptoprevquarteryear]}</td>
                            <td class="totalscell">{$totalvalues_output[totaluptocurrentquarter]}</td>
                            <td class="totalscell">{$totalvalues_output[totalprevyear]}</td>
                            <td class="totalscell">{$totalvalues_output[totalyearforecast]}</td>
                            <td class="totalscell">{$totalvalues_output[actualdiffcurrency]}</td>
                            <td class="totalscell">{$totalvalues_output[forecastdiffcurrency]}</td>
                            <td class="totalscell">{$totalvalues_output[actualdiffpercentage]}%</td>
                            <td class="totalscell">{$totalvalues_output[forecastdiffpercentage]}%</td>
                        </tr>
                        <tr><td colspan="9" class="verticalspacer">&nbsp;</td></tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>
    <div style="text-align:left;">{$piechart_section}</div>
    <br />
    <p style="font-style:italic; text-align:right; margin-bottom: 0px; padding:0px;">{$lang->amountsincurrency}</p>
    <table class="reportbox">
        <tr><td colspan="6" class="thead">{$lang->quarterscomparison}</td></tr>
        <tr>
            <td class="columnhead">Q</td><td class="columnhead" style="width: 16px;">{$previous_year}</td><td class="columnhead" style="width: 16px;">{$current_year}</td><td class="columnhead" style="width: 16px;">{$current_year} VS {$previous_year}</td><td class="columnhead" style="width: 16px;">{$current_year} {$lang->salesforecast}</td><td class="columnhead" style="width: 16px;">{$lang->achievedpercentage}</td>
        </tr>
        {$overview_quarters_salescompare_rows}
        <tr><td>&nbsp;</td><td class="totalscell">{$totalvalues_output[quartersturnover][$previous_year]}</td><td class="totalscell">{$totalvalues_output[quartersturnover][$current_year]}</td><td class="totalscell">{$quarters_salescompare_achivementprevyear}%</td><td class="totalscell">{$quarters_salescompare_achivementforecast}%</td></tr>
    </table>
    <br />
    <div style="text-align:left; padding-left: 10px;">{$quarters_comparision_charts}</div>
    <br />
    <p style="text-align:left; font-style:italic;">{$lang->reportoverviewnote}</p>
    <hr />
    <br />
</page>