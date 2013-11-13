<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listmenu}</title>
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
        .redactor_editor{
            width:800px;
            height:150px;
        }
    </style>
    <body>
        {$header}
    <tr>

        {$menu}
        <td class="contentContainer">
            <h3>{$lang->setmof}</h3>
            <form name="perform_meetings/minutesmeeting_Form" id="perform_meetings/minutesmeeting_Form"  method="post">
                <input type="hidden" name="auid" id="mtid" value="{$core->input[id]}" />
                <input type="hidden" value="do_{$action}" name="action" id="action" />

                <div style="display:table; width:60%;">
                    <div style="display:table-row; padding: 8px;">
                        <div style="display:table-cell;padding:15px; ">{$lang->meetings}</div>
                        <div style="display:table-cell;padding:8px;">  {$meeting_list} </div>
                    </div>

                    <div style="display:table-row;">{$lang->meetingdesc}
                        <div style="display:table-cell;padding:10px;"> <textarea  class="texteditor" id="meetingdetails"   name="mof[meetingDetails]"  cols="90" rows="25">{$mof[meetingDetails]}</textarea></div>
                    </div>

                    <div style="display:table-row; padding: 8px;">
                        <div style="display:table-cell;padding:8px;">{$lang->followup}</div>
                        <div style="display:table-cell;padding:8px;">  <textarea name="mof[followup]" id="followup" class="texteditor"  cols="90" rows="25">{$mof[followup]}</textarea></div>
                    </div>

                    <div style="display:table-row;">
                        <div style="display: table-cell;padding:8px; width:10px;">
                            <input type="submit" class="button" value="{$lang->savecaps}" id="perform_meetings/minutesmeeting_Button" />  </div>
  
                    </div>
            </form>

            <div style="display:table-row">
                <div style="display:table-cell;"id="perform_meetings/minutesmeeting_Results"></div>
            </div>
        </td>
    </tr>
</body>
</html>