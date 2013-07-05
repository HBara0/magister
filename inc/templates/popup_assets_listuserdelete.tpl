
<div id="popup_deleteuser" title="{$lang->deleteuserassets}">
    <form id="perform_assets/listuser_Form" name="perform_assets/listuser_Form" action="#" method="post">
     <input type="hidden" name="action" value="perform_delete" />
    <input type="hidden" id="todelete" name="todelete" value="{$core->input[id]}" />
    
    <p>{$lang->deleteuserassets}  <strong>{$employee[displayName]}</strong></p>
       <div align="center"><input type='button' id='perform_assets/listuser_Button' value='{$lang->yes}' class='button'/></div>
    </form>
</div>