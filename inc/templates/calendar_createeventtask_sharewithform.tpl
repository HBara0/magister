<div id="sharetask" class="subtitle" style="cursor:pointer;" onClick="$('#calendar_task_share').toggle();">{$lang->sharewith}...</div>
<div id="calendar_task_share" style="display:none;">
    <form name="perform_sharetask_calendar/eventstasks_Form" id="perform_sharetask_calendar/eventstasks_Form" method="post">
        <input type="hidden" id="action" name="action" value="share_task" />
        <input type="hidden" id="id" name="id" value="{$task_details[ctid]}" />
        {$sharewith_section}
        <input type="button" id='perform_sharetask_calendar/eventstasks_Button' value='{$lang->savecaps}' class="button">
        <div id="perform_sharetask_calendar/eventstasks_Results"></div>
    </form>
</div>