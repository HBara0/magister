<div id="popup_deletefacility" title="{$lang->deletefacility}">
    <form action="#" method="post" id="perform_facilitymgmt/list_Form" name="perform_facilitymgmt/list_Form">
        <input type="hidden" name="action" value="deletefacility" />
        <input type="hidden" name="todelete" value="{$id}" />
        <div>{$lang->suredelete}</div>
        <input type='button' id='perform_facilitymgmt/list_Button' value='{$lang->yes}' class='button'/>
        <div id="perform_facilitymgmt/list_Results"></div>
    </form>
</div>