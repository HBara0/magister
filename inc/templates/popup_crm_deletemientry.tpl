<div id="popup_deletemientry" title="{$lang->deleteentry}">
    <form id="perform_crm/marketpotentialdata_Form" name="perform_crm/marketpotentialdata_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_delete" />
        <input type="hidden" id="todelete" name="todelete" value="{$id}" />
        <strong>{$lang->suredeletemientry}</strong>
        <div align="center">
            <hr />
            <input type='button' id='perform_crm/marketpotentialdata_Button' value='{$lang->yes}' class='button'/>
        </div>
        <div style="display:table-row">
            <div style="display:table-cell;" id="perform_crm/marketpotentialdata_Results"></div>
        </div>
    </form>
</div>