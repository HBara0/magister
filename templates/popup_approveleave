<div id="popup_approveleave" title="{$lang->approveleave}">
    <form action="index.php?module=attendance/listleaves" target="approveleave_processFrame" method="post">
    <input type="hidden" name="action" value="perform_approveleave" />
    <input type="hidden" id="torevoke" name="toapprove" value="{$core->input[id]}" />
    <strong>{$lang->sureapproveleave}</strong>
    <p><em>{$lang->sureapproveleavenote}</em></p>
    <hr />
    <div align="center"><input type='submit' value='{$lang->yes}' class='button' onClick="$('#approveleave_Result').show();"/></div>
    </form>
    <div id="approveleave_Result"></div>
    <iframe id='approveleave_processFrame' name='approveleave_processFrame' src='#' style="display:none; margin:0px;"></iframe>
</div>