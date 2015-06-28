<div id="popup_deletebrandendproduct" title="{$lang->deletebrandendproduct}">
    <form id="perform_entities/managebrandendproducts_Form" name="perform_entities/managebrandendproducts_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_delete" />
        <input type="hidden" id="todelete" name="todelete" value="{$core->input[id]}" />
        {$lang->suredelete}<br />
        <div align="center">
            <hr />
            <input type='button' id='perform_entities/managebrandendproducts_Button' value='{$lang->yes}' class='button'/>
            <input type="button" class="button" onclick="$('#popup_deletebrandendproduct').dialog('close')"value="{$lang->no}"/>
        </div>
        <div style="display:table-row">
            <div style="display:table-cell;" id="perform_entities/managebrandendproducts_Results"></div>
        </div>
    </form>
</div>