<page style="page-break-before:always;">
<a name="contributors"></a>
<table style="width:100%;">
<tr>
<td class="logo" style="width:100%;">&nbsp;</td>
</tr>
<tr>
    <td style="width:100%; text-align:left;">
    	<table class="reportbox" style="width: 100%;">
        	<tr><td colspan="2" class="cathead">{$lang->reportcontributorsoverview}</td></tr>
            <tr><td colspan="2" class="cathead" style="color:#FFFFFF; padding-top:10px; padding-bottom:5px; font-size: 14px; font-weight: bold;">{$lang->auditedby}: {$report[auditors][employeeName]} (<a href="mailto:{$report[auditors][email]}" style="color:#FFFFFF;">{$report[auditors][email]}</a>)</td></tr>
    		{$authors_overview_entries}
        </table>
    </td>
</tr>
</table>
</page>