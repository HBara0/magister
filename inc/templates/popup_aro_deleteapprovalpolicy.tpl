<div id="popup_deleteapprovalpolcicies" title="{$lang->deletepolicy}">
    <form id="perform_aro/approvalchainspolicieslist_Form" name="perform_aro/approvalchainspolicieslist_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_deletepolicy" />
        <input type="hidden" id="todelelete" name="todelelete" value="{$core->input[id]}" />
        <strong>{$lang->sureredelete}</strong>
        <p><em>{$lang->surerevokeleavenote}</em></p>
        <hr />
        <div align="center"><input type='button' id='perform_aro/approvalchainspolicieslist_Button' value='{$lang->yes}' class='button'/></div>
    </form>
    <div id="perform_aro/approvalchainspolicieslist_Results"></div>
</div>