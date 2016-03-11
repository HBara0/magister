<div id="popup_managewidgets" title="{$lang->managewidgets}">
    <h2>{$lang->managewidgets} : {$widgettype_output}</h2>
    <form name='add_portal/dashboard_Form' id="add_portal/dashboard_Form" method="post" >
        <input type="hidden" id="action" name="action" value="managewidgets" />
        <input type="hidden" id="action" name="widget[module]" value="{$module}" />
        <input type="hidden" id="action" name="widget[inputChecksum]" value="{$inputchecksum}" />
        <input type="hidden" id="action" name="widget[sdid]" value="{$basicids[SystemDashboard::PRIMARY_KEY]}" />
        <input type="hidden" id="action" name="widget[swgiid]" value="{$widgetinstance_id}" />
        <input type="hidden" id="action" name="widget[swdgid]" value="{$basicids[SystemWidgets::PRIMARY_KEY]}" />
        {$titlefield}
        <hr>
        {$inputfields}
        <input type='button' class='button' value='{$lang->submit}' id='add_portal/dashboard_Button' />
    </form>
    <div id="add_portal/dashboard_Results" ></div>
</div>