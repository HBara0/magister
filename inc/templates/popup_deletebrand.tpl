<div id="popup_deletebrand" title="{$lang->deletebrand}">
    <form id="perform_entities/managebrands_Form" name="perform_entities/managebrands_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_delete" />
        <input type="hidden" id="todelete" name="todelete" value="{$ebid}" />
        {$lang->suredelete}<br />
        <div align="center">
            <hr />
            <input type='button' id='perform_entities/managebrands_Button' value='{$lang->yes}' class='button'/>
            <input type="button" class="button" onclick="$('#popup_deletebrand').dialog('close')"value="{$lang->no}"/>
        </div>
        <div style="display:table-row">
            <div style="display:table-cell;" id="perform_entities/managebrands_Results"></div>
        </div>
    </form>
</div>