<html>
<head>
<title>{$core->settings[systemtitle]} | {$report[title]}</title>
{$headerinc}
<link href="report.css" rel="stylesheet" type="text/css" />
<script src="{$core->settings[rootdir]}/js/fillreport.js" type="text/javascript"></script>
</head>

<body>
{$header}
<tr>
{$menu}
<td class="contentContainer">
<form id="save_report_reporting/fillreport_Button" name="save_report_reporting/fillreport_Button" action="#" method="post">
<input type="hidden" name="reportdata" value="{$reportrawdata}">
</form>
<div align="center">
{$reports}
</div>
<div align="right">{$tools}</div>
</td>
</tr>
{$footer}
</body>
</html>