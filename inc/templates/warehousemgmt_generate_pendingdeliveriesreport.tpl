<h1>{$lang->summaryofpendingdeliveries}</h1>
<div style="margin-left: 5px;">
    <form name="perform_warehousemgmt/pendingdeliveries_Form" id="perform_warehousemgmt/pendingdeliveries_Form"  action="#" method="post">
        <div style="vertical-align:top;"><div style="width:100px;display:inline-block;"><strong>{$lang->affiliate}</strong></div>{$affiliates_list}</div>
        <br/>
        <div style="display:block;">
            <div style="display:inline-block; padding: 8px; margin:8px;">
                <input type="submit" id="perform_warehousemgmt/pendingdeliveries_Button" value="{$lang->generate}" class="button"/>
            </div>

        </div>
    </form>
    <div style="display:block;">
        <div id="perform_warehousemgmt/pendingdeliveries_Results"></div>
    </div>
</div>