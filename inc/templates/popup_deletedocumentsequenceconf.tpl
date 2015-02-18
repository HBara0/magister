<div id="popup_deletedocumentsequenceconf" title="{$lang->deletearodocseqcong}">
    <form id="perform_aro/documentssequeneconflist_Form" name="perform_aro/documentssequeneconflist_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_deletedocumentsequenceconf" />
        <input type="hidden" id="todelete" name="todelete" value="{$core->input[id]}" />
        <strong>{$lang->confirmdeletedocseqconf}</strong>
        <hr />
        <div align="center"><input type='button' id='perform_aro/documentssequeneconflist_Button' value='{$lang->yes}' class='button'/></div>
    </form>
    <div id="perform_aro/documentssequeneconflist_Results"></div>
</div>