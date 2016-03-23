<div id="popup_managedashboard" title="{$lang->managedashboard}">
    <form name="perform_portal/dashboard_Form" id="perform_portal/dashboard_Form">
        <input type='hidden' name='action' value='manage_dashboard'>
        <input type="hidden" value="{$dashboard[sdid]}" name="dashboard[sdid]">
        <input type="hidden" value="{$dashboard[inputChecksum]}" name="dashboard[inputChecksum]">
        <div class="form-group">
            <label for="dashboardtitle_{$dashboard[sdid]}">{$lang->title}</label>
            <input type="text" id="dashboardtitle_{$dashboard[sdid]}" class="form-control" value="{$dashboard[title]}" name="dashboard[title]">
        </div>
        <div class="form-group">
            <label id="columncount_{$dashboard[sdid]}">{$lang->columncount}</label>
            <input type="number" id="columncount_{$dashboard[sdid]}" min="1" max="3" value="{$dashboard[columnCount]}" name="dashboard[columnCount]">
        </div>
        <div><input type='button' id='perform_portal/dashboard_Button' value='{$lang->submit}' class='button'/></div>
        <div style="display:table-row;">
            <div style="display:table-cell;">
                <div id="perform_portal/dashboard_Results"> </div>
            </div>
        </div>
    </form>
</div>
