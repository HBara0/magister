<div id="popup_managewidgets" title="{$lang->managewidgets}">
    <h2>{$lang->managewidgets} : {$widgettype_output}</h2>
    <form name='add_portal/dashboard_Form' id="add_portal/dashboard_Form" method="post" >
        <input type="hidden"  name="action" value="managewidgets" />
        <input type="hidden"  name="widget[module]" value="{$module}" />
        <input type="hidden"  name="widget[inputChecksum]" value="{$inputchecksum}" />
        <input type="hidden"  name="widget[sdid]" value="{$basicids[sdid]}" />
        <input type="hidden"  name="widget[swgiid]" value="{$widgetinstance_id}"/>
        <input type="hidden"  name="widget[swdgid]" value="{$basicids[swdgid]}"/>
        {$titlefield}
        <hr>
        {$inputfields}
        <input type='button' class='button' value='{$lang->submit}' id='add_portal/dashboard_Button' />
    </form>
    <div id="add_portal/dashboard_Results" ></div>
</div>