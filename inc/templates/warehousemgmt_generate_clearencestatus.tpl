<h1>{$lang->clearancestatus}</h1>
<div style="margin-left: 5px;">
    <form name="perform_warehousemgmt/clearancestatus_Form" id="perform_warehousemgmt/clearancestatus_Form"  action="#" method="post">
        <div style="vertical-align:top;"><div style="width:100px;display:inline-block;"><strong>{$lang->affiliate}</strong></div>{$affiliates_list}</div>
        <br/>
        <div style="display:block;">
            <div style="display:inline-block; padding: 8px; margin:8px;">
                <input type="submit" id="perform_warehousemgmt/clearancestatus_Button" value="{$lang->generate}" class="button"/>
            </div>

        </div>
    </form>
    <div style="display:block;">
        <div id="perform_warehousemgmt/clearancestatus_Results"></div>
    </div>
</div>