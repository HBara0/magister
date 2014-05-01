<div id="popup_revokeleave" title="{$lang->surerevokeleave}">
    <form id="perform_attendance/listleaves_Form" name="perform_attendance/listleaves_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_revokeleave" />
        <input type="hidden" id="torevoke" name="torevoke" value="{$core->input[id]}" />
        <strong>{$lang->surerevokeleave}</strong>
        <p><em>{$lang->surerevokeleavenote}</em></p>
        <hr />
        <div align="center"><input type='button' id='perform_attendance/listleaves_Button' value='{$lang->yes}' class='button'/></div>
    </form>
    <div id="perform_attendance/listleaves_Results"></div>
</div>