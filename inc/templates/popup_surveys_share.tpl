<div id="popup_sharesurvey" title="{$lang->sharewith}">
    <form name="perform_surveys/list_Form" id="perform_surveys/{$file}_Form" method="post">
        <input type="hidden" name="sid" id="sid" value="{$sid}" />
        <input type="hidden" name="identifier" id="identifier" value="{$identifier}" />
        <input type="hidden" value="do_share" name="action" id="action" />
        <div style="width:100%; height:250px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
            <table class="datatable" width="100%">
                <thead>
                <th></th>
                <th><input class='inlinefilterfield' type='text' style="width: 95%" placeholder="{$lang->employee}"/></th>
                <th><input class='inlinefilterfield' type='text' style="width: 95%" placeholder="{$lang->affiliate}"/></th>
                <th><input class='inlinefilterfield' type='text' style="width: 95%" placeholder="{$lang->position}"/></th>
                </thead>
                <tbody>
                    {$sharewith_rows}
                </tbody>
            </table>
        </div>
        <div style="display:table; border-collapse:collapse; width:100%;">
            <div style="display:table-row;">
                <div style="display: table-cell; width:35%;">
                    <input type="submit" class="button main" value="{$lang->savecaps}" id="perform_surveys/{$file}_Button" />
                    <input type="reset" class="button" onclick="$('#popup_sharemeeting').dialog('close')"value="{$lang->cancel}"/>
                </div>
            </div>
            <div style="display:table-row; width:25%;">
                <div style="display:table-cell; width:25%;" id="perform_surveys/{$file}_Results"></div>
            </div>
        </div>
    </form>
</div>