<div id="popup_deletearopolicy" title="{$lang->confirmdeletearopolicy}">
    <form id="perform_aro/listpolicies_Form" name="perform_aro/listpolicies_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_deletearopolicy" />
        <input type="hidden" id="todelete" name="todelete" value="{$core->input[id]}" />
        <strong>{$lang->confirmdeletearopolicy}</strong>
        <hr />
        <div align="center"><input type='button' id='perform_aro/listpolicies_Button' value='{$lang->yes}' class='button'/></div>
    </form>
    <div id="perform_aro/listpolicies_Results"></div>
</div>