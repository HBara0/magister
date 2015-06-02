<div id="popup_createproductcharcteristic" title="{$title}">
    <form action="#" method="post" id="add_products/characteristicslist_Form" name="add_products/characteristicslist_Form">
        <input type="hidden" name="action" value="do_addcharacteristic" />
        <div>
            <div style="display:inline-block; width: 15%;"><strong>{$lang->title}</strong></div>
            <div style="display:inline-block; width: 75%;">
                <input name="characteristic[title]" type="text" required="required" value="{$chars[title]}"/>
            </div>
        </div>
        <div>
            <hr />
            <div><input type='button' id='add_products/characteristicslist_Button' value='{$lang->savecaps}' class='button'/></div>
            <div style="display:table-row;"> <div style="display:table-cell;"><div id="add_products/characteristicslist_Results"></div></div></div>
        </div>
    </form>
</div>