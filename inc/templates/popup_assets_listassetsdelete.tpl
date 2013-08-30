<div id="popup_deleteasset" title="{$lang->deleteasset}">
    <form id="perform_assets/listassets_Form" name="perform_assets/listassets_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_delete" />
        <input type="hidden" id="todelete" name="todelete" value="{$core->input[id]}" />
        {$lang->deleteassetcontent}<strong> {$asset[title]}?<br /></strong>
        <div align="center">
            <hr />
            <input type='button' id='perform_assets/listassets_Button' value='{$lang->yes}' class='button'/>
            <input type="button" class="button" onclick="$('#popup_deleteasset').dialog('close')"value="{$lang->no}"/>
        </div>
        <div style="display:table-row">
            <div style="display:table-cell;" id="perform_assets/listassets_Results"></div>
        </div>
    </form>
</div>