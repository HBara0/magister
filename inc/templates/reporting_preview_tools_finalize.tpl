<div id="save_report_reporting/fillreport_Results" style='float: left; width: 90%; vertical-align:middle; border-right: 1px solid #91b64f; height: 40px; padding: 5px; color:#666666; '>
    &rsaquo;&rsaquo; {$lang->finalizewilllock}
    {$warnnegative}
</div>
<div style='float: right; width: 8%;'>
    <form id='save_report_reporting/fillreport_Form' name='save_report_reporting/fillreport_Form' action='#' method='post'>
        <input type="hidden" value="{$report[rid]}" name="rid"/>
        <input type="hidden" id="transfill" name="transfill" value="{$transfill}">
        <input type="hidden" name="identifier" value="{$session_identifier}" />
        <input type="hidden" name="savetype" value="{$reporting_preview_tools_finalize_type}" /> <input type='button' id="showpopup_finalizereportconfirmation"  value='{$lang->finalize}' class='button' />
        <div id="popup_finalizereportconfirmation" title="{$lang->suretofinalizetitle}">{$missing_employees_notification} {$warnnegative}<br>{$reporting_preview_tools_finalize_button}</div>
    </form>
</div>