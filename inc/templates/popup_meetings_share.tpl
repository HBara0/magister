<div id="popup_sharemeeting" title="{$lang->sharewith}">
    <form name="perform_meetings/list_Form" id="perform_meetings/list_Form" method="post">
        <input type="hidden" name="mtid" id="mtid" value="{$mtid}" />
        <input type="hidden" value="do_share" name="action" id="action" />
        <div style="width:100%; height:250px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
            <table class="datatable" width="100%">
                {$sharewith_rows} 
            </table>
        </div> 
        <div style="display:table; border-collapse:collapse; width:100%;">
            <div style="display:table-row;">
                <div style="display: table-cell; width:35%;">
                    <input type="submit" class="button" value="{$lang->savecaps}" id="perform_meetings/list_Button" />
                    <input type="reset" class="button" onclick="$('#popup_sharemeeting').dialog('close')"value="{$lang->cancel}"/>
                </div>
            </div>
            <div style="display:table-row; width:25%;">
                <div style="display:table-cell; width:25%;" id="perform_meetings/list_Results"></div>
            </div>
        </div>
    </form>
</div>