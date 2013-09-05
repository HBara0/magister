<div id="popup_deleteuser" title="{$lang->deleteuserassets}">
    <form id="perform_assets/listuser_Form" name="perform_assets/listuser_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_delete" />
        <input type="hidden" id="todelete" name="todelete" value="{$core->input[id]}" />
        <p>{$lang->deleteuserassets}</p>
        <div align="center">
            <hr />
            <input type='button' id='perform_assets/listuser_Button' value='{$lang->yes}' class='button'/>
            <input type="button" class="button" onclick="$('#popup_deleteuser').dialog('close')" value="{$lang->no}"/>
        </div>
        <div style="display:table-row">
            <div style="display:table-cell;" id="perform_assets/listuser_Results"></div>
        </div>
    </form>
</div>