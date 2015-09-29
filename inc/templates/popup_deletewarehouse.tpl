<div id="popup_deletewarehouse" title="{$lang->confirmdeletewarehouse}">
    <form id="perform_contents/listwarehouses_Form" name="perform_contents/listwarehouses_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_deletewarehouse" />
        <input type="hidden" id="todelete" name="todelete" value="{$core->input[id]}" />
        <strong>{$lang->confirmdeletewarehouse}</strong>
        <hr />
        <div align="center"><input type='button' id='perform_contents/listwarehouses_Button' value='{$lang->yes}' class='button'/></div>
    </form>
    <div id="perform_contents/listwarehouses_Results"></div>
</div>