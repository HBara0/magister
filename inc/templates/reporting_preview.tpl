<link href="./css/report.css" rel="stylesheet" type="text/css" />
<script src="{$core->settings[rootdir]}/js/fillreport.js" type="text/javascript"></script>
<form id="save_report_reporting/fillreport_Button" name="save_report_reporting/fillreport_Button" action="#" method="post">
    <input type="hidden" name="reportdata" value="{$reportrawdata}">
</form>
<div align="center">
    {$reports}
</div>
<div align="center">{$reportingeditsummary}</div>
<div align="right">{$tools}</div>
