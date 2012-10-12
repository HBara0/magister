<table class="reporttable" width="100%">
  <tr>
    <td rowspan="3" class="logo">&nbsp;</td>
    <td colspan="2"><h3>{$lang->monthlymarketreport}</h3></td>
  </tr>
  <tr>
    <td style="font-weight:bold; width:20%;">{$lang->monthunderreview}</td>
    <td style="font-weight:bold;" class="red_text">{$report_data[month]} {$report_data[year]}</td>
  </tr>
  <tr>
    <td style="font-weight:bold;"><span class="green_text">Orkila</span> {$lang->affiliate}</td>
    <td>{$report_data[affiliatename]}</td>
  </tr>
  <tr>
    <td style="font-weight:bold; text-align:center;">{$lang->supplier}</td>
    <td colspan="2" class="highlight_textbox">{$report_data[suppliername]}</td>
  </tr>
</table>
<br />

<table class="reportbox">
<tr>
    <td colspan="2" class="thead">{$lang->overallstatus}</td>
</tr>
{$overallstatus_rows}
<tr>
	<td class="altrow2" style="width: 20%; border-bottom: 1px solid #F2F2F2; padding: 5px;">{$lang->personalconsiderations}</td>
    <td class="border_left" style="width: 80%; border-bottom: 1px solid #F2F2F2;">{$report_data[considerations]}</td>
</tr>
</table>
<br />

<table class="reportbox">
<tr>
    <td colspan="3" class="thead">{$lang->keycustomers}</td>
</tr>
{$keycustomers_rows}
</table>
<br />

{$visits}

<hr />
<div align="left">
    <span class="subtitle">{$lang->accomplishmentsplans}s</span>
    <p>
        <span style="font-weight:bold;">{$lang->accomplishmentsreportingperiod}:</span><br />
        <span style="padding-left:10px;">{$report_data[accomplishments]}</span>
    </p>
    
     <p>
        <span style="font-weight:bold;">{$lang->actionstoimplement}:</span><br />
        <span style="padding-left:10px;">{$report_data[actions]}</span>
    </p>
</div>
<hr />
{$contributors}