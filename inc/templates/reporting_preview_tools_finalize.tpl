<div id="save_report_reporting/fillreport_Results" style='float: left; width: 80%; vertical-align:middle; border-right: 1px solid #91b64f; height: 40px; padding: 5px; color:#666666; '>
    &rsaquo;&rsaquo; {$lang->finalizewilllock}
</div>
<div style='float: right; width: 17%;'>
    <form id='save_report_reporting/fillreport_Form' name='save_report_reporting/fillreport_Form' action='#' method='post'>
    	<input type="hidden" name="identifier" value="{$session_identifier}" />
        <input type="hidden" name="savetype" value="{$reporting_preview_tools_finalize_type}" />
        <input type="button" value="{$lang->prevcaps}" class="button" onClick="goToURL('index.php?module=reporting/fillreport&amp;stage=marketreport&amp;identifier={$identifier}');"/> <input type='button' id="showpopup_finalizereportconfirmation"  value='{$lang->finalize}' class='button' />
        <div id="popup_finalizereportconfirmation" title="{$lang->suretofinalizetitle}">{$missing_employees_notification}{$reporting_preview_tools_finalize_button}</div>
    </form>
</div>