<div id="popup_deletearodocument" title="{$lang->sureredeletearodocument}">
    <form id="perform_aro/managearodouments_Form" name="perform_aro/managearodouments_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_deletearodocument" />
        <input type="hidden" id="todelete" name="todelete" value="{$core->input[id]}" />
        <strong>{$lang->sureredeletearodocument}</strong>
        <hr />
        <div align="center"><input type='button' id='perform_aro/managearodouments_Button' value='{$lang->yes}' class='button'/></div>
    </form>
    <div id="perform_aro/managearodouments_Results"></div>
</div>