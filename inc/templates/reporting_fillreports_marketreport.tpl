<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->marketreport} - Q{$report_meta[quarter]} {$report_meta[year]} / {$report_meta[supplier]} - {$report_meta[affiliate]}</title>
{$headerinc}
<script src="{$core->settings[rootdir]}/js/fillreport.js" type="text/javascript"></script>
</head>
<body>
{$header}
<tr>
{$menu}
<td class="contentContainer">
<h3>{$lang->marketreport}<br /><div style="font-style:italic; font-size:12px; color:#888;">Q{$report_meta[quarter]} {$report_meta[year]} / {$report_meta[supplier]} - {$report_meta[affiliate]}</div></h3>
<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p><strong>Notice:</strong> If you don't have anything to fill under a specific segment, please use the "<em>Exclude this segment</em>" checkbox on the top right side of each section.</p><p><p><strong>Reminder:</strong> Don't forget you can use OCODE to style your text <strong>[b]bold[/b]</strong>, <em>[i]italic[/i]</em>, and <span style="text-decoration:underline;">[u]underline[/u]</span>.</p></div>
<form id="save_marketreport_reporting/fillreport_Form" name="save_marketreport_reporting/fillreport_Form" action="index.php?module=reporting/preview" method="post">
<input type="hidden" name="rid" value="{$core->input[rid]}">
<input type="hidden" id="identifier" name="identifier" value="{$core->input[identifier]}">
<table width="100%" class="datatable">
	{$markerreport_fields}
    <tr>
    	<td colspan="2"><div id="save_marketreport_reporting/fillreport_Results"></div></td>
    	<td style="text-align:right !important;">
        	<input type="button" value="{$lang->prevcaps}" class="button" onClick="goToURL('index.php?module=reporting/fillreport&amp;stage=keycustomers&amp;identifier={$core->input[identifier]}');" /> <input type="button" id="save_marketreport_reporting/fillreport_Button" value="{$lang->savecaps}" class="button"/> <input type="submit" value="{$lang->nextcaps}" class="button"/> <br />
        	<div style="text-align:right; font-style:italic;"><input type="checkbox" name="isDone" id="isDone" value="1" title="{$lang->isdone_tip}"> {$lang->finishedmypart}</div>
        </td>
    </tr>
</table>
</form>
</td>
</tr>
{$footer}
</body>
</html>