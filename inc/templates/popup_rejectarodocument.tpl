<div id="popup_rejectarodocument" title="{$lang->rejectarodocument}">
    <form id="perform_aro/managearodouments_Form" name="perform_aro/managearodouments_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_rejectarodocument" />
        <input type="hidden" id="toreject" name="toreject" value="{$core->input[id]}" />
        {$lang->reasontorejectaro}
        <textarea name="rejectionmessage[message]" rows="3" cols="50"></textarea>
        <br/>
        <div align="center"><input type='button' id='perform_aro/managearodouments_Button' value='{$lang->reject}' class='button'/></div>
    </form>
    <div id="perform_aro/managearodouments_Results"></div>
</div>