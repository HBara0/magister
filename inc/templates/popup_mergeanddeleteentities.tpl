<script>
    $(document).ready(function() {
        $('input[type="radio"]').click(function() {
            if($(this).attr('id') == 'perform_mergeanddeleteentities') {
                $("div[id='mergeanddelete']").show();
            }
            else {
                $("div[id='mergeanddelete']").hide();
            }
            $("input[name='action']").val($(this).attr('id'));
        });
    });
</script>
<div id="popup_mergeanddeleteentities" title="{$lang->suredeleteentity}">
    <input type="radio" name="group1" id="perform_mergeanddeleteentities" value="perform_mergeanddeleteentities">{$lang->mergeentity}
    <input type="radio" name="group1" id="perform_deleteentity" value="perform_deleteentity">{$lang->deleteentity}

    <form id="perform_entities/{$filename}_Form" name="perform_entities/{$filename}_Form" action="#" method="post">
        <div id="mergeanddelete" style="display:none;margin-top:10px;">
            <input type="hidden" name="action" value="perform_mergeanddeleteentities" />
            <input type="hidden" id="todelete" name="todelete" value="{$core->input[id]}" />
            {$lang->mergeentity}
            <input type='text' name="{$entitytype}_1_autocomplete" id="{$entitytype}_1_autocomplete" autocomplete='off'/>
            <input type='text' size='3' name='{$entitytype}_1_id_output' id='{$entitytype}_1_id_output' disabled='disabled'/>
            <input type='hidden' id='{$entitytype}_1_id' name='mergeeid'/>
            <div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div>
        </div>
        <hr/>
        <div align="center"><input type='button' id='perform_entities/{$filename}_Button' value='{$lang->yes}' class='button'/></div>
    </form>
    <div id="perform_entities/{$filename}_Results"></div>
</div>