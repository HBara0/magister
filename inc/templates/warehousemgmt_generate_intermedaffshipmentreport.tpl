<h1>{$lang->intermedaffshipment}</h1>
<div style="margin-left: 5px;">
    <form name="perform_warehousemgmt/intermedaffshipment_Form" id="perform_warehousemgmt/intermedaffshipment_Form"  action="#" method="post">
        <div><div style="width:150px;display:inline-block;"><strong>{$lang->intermediaryaffiliate}</strong></div>{$intermedaffiliates_list}</div>
        <br/>
        <div><div style="width:150px;display:inline-block;"><strong>{$lang->buyingaffiliate}</strong></div>{$buyingaffiliates_list}</div>

        <br/>
        <div style="display:block;">
            <div style="display:inline-block; padding: 8px; margin:8px;">
                <input type="submit" id="perform_warehousemgmt/intermedaffshipment_Button" value="{$lang->generate}" class="button"/>
            </div>

        </div>
    </form>
    <div style="display:block;">
        <div id="perform_warehousemgmt/intermedaffshipment_Results"></div>
    </div>
</div>