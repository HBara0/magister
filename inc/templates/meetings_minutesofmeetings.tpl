<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->setmof}</title>
        {$headerinc}
        <script type="text/javascript">
            $(document).ready(function() {
                $('.texteditor').redactor({
                    buttons: ['html', '|', 'formatting', '|', 'bold', 'italic', 'deleted', '|',
                        'unorderedlist', 'orderedlist', 'outdent', 'indent', '|',
                        'table', '|', 'alignment', '|', 'horizontalrule'],
                    fullpage: true
                });
            });
        </script>
    </head>
    <style>
        .redactor_editor {
            width:800px;
        }
    </style>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->setmof}</h3>
            <form name="perform_meetings/minutesmeeting_Form" id="perform_meetings/minutesmeeting_Form" method="post">
                <input type="hidden" name="mof[momid]" id="momid" value="{$mof[momid]}" />
                <input type="hidden" value="do_{$action}" name="action" id="action" />
                <div>{$lang->meeting}: {$meeting_list}</div>
                <div class="subtitle" style="margin-top: 10px;">{$lang->discussiondetails}</div>
                <div><textarea class="texteditor" id="meetingdetails" name="mof[meetingDetails]" cols="90" rows="25">{$mof[meetingDetails]}</textarea></div>
                <div class="subtitle" style="margin-top: 10px;">{$lang->followup}</div>
                <div><textarea name="mof[followup]" id="followup" class="texteditor" cols="90" rows="25">{$mof[followup]}</textarea></div>
                <div>
                    <hr />
                    <input type="submit" class="button" value="{$lang->savecaps}" id="perform_meetings/minutesmeeting_Button" />
                </div>
            </form>
            <div style="display:table-row">
                <div style="display:table-cell;"id="perform_meetings/minutesmeeting_Results"></div>
            </div>
        </td>
    </tr>
</body>
</html>