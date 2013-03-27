<table class="reportbox">
<tr><td colspan="14" class="thead">{$lang->activityby} {$lang->$aggregate_type} - <em>{$lang->$category} ({$categories_uom[$category]})</em></td></tr>
  <tr>
    <td  width="140px" rowspan="2" class="columnhead">&nbsp;</td>
 
    <td colspan="4" align="center" class="columnhead">{$report_years[before_2years]}</td>
    <td colspan="4" align="center" class="columnhead altrow">{$report_years[before_1year]}</td>
    <td colspan="4" align="center" class="altrow2 columnhead">{$report_years[current_year]}</td>
  </tr>
  <tr>
        <td width="28px" class="columnsubhead">{$lang->q1}</td>
        <td width="28px" class="columnsubhead">{$lang->q2}</td>
        <td width="28px" class="columnsubhead">{$lang->q3}</td>
        <td width="28px" class="columnsubhead">{$lang->q4}</td>
        <td width="28px" class="columnsubhead altrow">{$lang->q1}</td>
        <td width="28px" class="columnsubhead altrow">{$lang->q2}</td>
        <td width="28px" class="columnsubhead altrow">{$lang->q3}</td>
        <td width="28px" class="columnsubhead altrow">{$lang->q4}</td>
        <td width="28px" class="columnsubhead altrow2">{$lang->q1}</td>
        <td width="28px" class="columnsubhead altrow2">{$lang->q2}</td>
        <td width="28px" class="columnsubhead altrow2">{$lang->q3}</td>
        <td width="28px" class="columnsubhead altrow2">{$lang->q4}</td>
  </tr>
  {$reporting_report_newoverviewbox_row[$aggregate_type][$category]}
  <tr>
      <td colspan="13" class="columnhead" style="height:2px;"></td>
  </tr>
<tr>
   <td class="mainbox_itemnamecell" style="font-weight:bold;">{$lang->total}</td>
   <td class="mainbox_totalcell">{$boxes_totals[mainbox][$aggregate_type][$category][actual][$report_years[before_2years]][1]}</td>
   <td class="mainbox_totalcell">{$boxes_totals[mainbox][$aggregate_type][$category][actual][$report_years[before_2years]][2]}</td>
   <td class="mainbox_totalcell">{$boxes_totals[mainbox][$aggregate_type][$category][actual][$report_years[before_2years]][3]}</td>
   <td class="mainbox_totalcell">{$boxes_totals[mainbox][$aggregate_type][$category][actual][$report_years[before_2years]][4]}</td>
   <td class="altrow mainbox_totalcell">{$boxes_totals[mainbox][$aggregate_type][$category][actual][$report_years[before_1year]][1]}</td>
   <td class="altrow mainbox_totalcell">{$boxes_totals[mainbox][$aggregate_type][$category][actual][$report_years[before_1year]][2]}</td>
   <td class="altrow mainbox_totalcell">{$boxes_totals[mainbox][$aggregate_type][$category][actual][$report_years[before_1year]][3]}</td>
   <td class="altrow mainbox_totalcell">{$boxes_totals[mainbox][$aggregate_type][$category][actual][$report_years[before_1year]][4]}</td>
   <td class="altrow2 mainbox_totalcell">{$boxes_totals[mainbox][$aggregate_type][$category][actual][$report_years[current_year]][1]}</td>
   <td class="altrow2 mainbox_totalcell">{$boxes_totals[mainbox][$aggregate_type][$category][actual][$report_years[current_year]][2]}</td>
   <td class="altrow2 mainbox_totalcell">{$boxes_totals[mainbox][$aggregate_type][$category][actual][$report_years[current_year]][3]}</td>
   <td class="altrow2 mainbox_totalcell">{$boxes_totals[mainbox][$aggregate_type][$category][actual][$report_years[current_year]][4]}</td>
 </tr>
</table>
<br />