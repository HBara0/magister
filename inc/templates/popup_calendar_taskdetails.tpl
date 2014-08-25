
<div id="popup_taskdetails" title="{$lang->taskdetails}">
    <strong>{$task_details[subject]}</strong><br />
    <span style="font-style:italic">
        {$task_details[assignedTo_output]}
        {$lang->duedate}: {$task_details[dueDate_output]}<br />
        {$task_details[timeDone_output]}
        {$lang->priority}: {$task_details[priority_output]}<br />
       	<form name="perform_updatepercentage_calendar/eventstasks_Form" id="perform_updatepercentage_calendar/eventstasks_Form" method="post">
            {$lang->completed}
            <input type="hidden" id="ctid" name="ctid" value="{$task_details[ctid]}" />
            <select name="percCompleted" id="percCompleted">
                <option value="0"{$selected[percCompleted][0]}>0%</option>
                <option value="25"{$selected[percCompleted][25]}>25%</option>
                <option value="50"{$selected[percCompleted][50]}>50%</option>
                <option value="75"{$selected[percCompleted][75]}>75%</option>
                <option value="100"{$selected[percCompleted][100]}>100%</option>
            </select>
            <span id="perform_updatepercentage_calendar/eventstasks_Results"></span>
        </form>
    </span>
    <p style="font-style:italic">{$task_details[description]}</p>
    <hr />
    <div id="sharetask" class="subtitle" style="cursor:pointer;" onClick="$('#calendar_task_share').toggle();">{$lang->sharewith}...</div>
    <div id="calendar_task_share" style="display:none;">
        <form name="perform_sharetask_calendar/eventstasks_Form" id="perform_sharetask_calendar/eventstasks_Form" method="post">
            <input type="hidden" id="action" name="action" value="share_task" />
            <input type="hidden" id="id" name="id" value="{$task_details[ctid]}" />
            {$task_sharewith}
            <input type="button" id='perform_sharetask_calendar/eventstasks_Button' value='{$lang->savecaps}' class="button">
            <div id="perform_sharetask_calendar/eventstasks_Results"></div>
        </form>
    </div>
    <div id="shownotes" class="subtitle" style="cursor:pointer;" onClick="$('#calendar_task_notessection').toggle();">{$notes_count} {$lang->notes}...</div><br>
    <div id="calendar_task_notessection" style="display:none;">
        <form name="perform_savenote_calendar/eventstasks_Form" id="perform_savenote_calendar/eventstasks_Form" method="post">
            <input type="hidden" id="action" name="action" value="save_tasknote" />
            <input type="hidden" id="id" name="id" value="{$task_details[ctid]}" />
            <textarea name="note" id="note" cols="55" rows="2" class="texteditormin"></textarea><br />
            <input type="button" id='perform_savenote_calendar/eventstasks_Button' value='{$lang->savecaps}' class="button">
        </form>
        <div id="perform_savenote_calendar/eventstasks_Results"></div>
        <div id="calendar_task_notes">
            {$task_notes_output}
        </div>
    </div>
    <script src="{$core->settings[rootdir]}/js/redactor.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function() {
            $("#percCompleted").live('change', function() {
                if(sharedFunctions.checkSession() == false) {
                    return;
                }

                sharedFunctions.requestAjax("post", "index.php?module=calendar/eventstasks&action=update_task", "ctid=" + $("#ctid").val() + "&percCompleted=" + $(this).val(), '', '', 'script');
            });
            $('.texteditormin').redactor({
                air: true,
                airButtons: ['bold', 'italic', 'deleted', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'alignleft', 'aligncenter', 'alignright', 'justify'],
                allowedTags: ["br", "p", "b", "i", "del", "strike", "blockquote", "cite", "small", "ul", "ol", "li", "dl", "dt", "dd", "sup", "sub", "pre", "strong", "em"]
            });
            $('.redactor_air').css('z-index', ($('.ui-dialog').css('z-index') + 1));
        });
    </script>
</div>