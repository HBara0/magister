<div id="popup_createbrand" title="{$lang->createbrand}">
    <form action="#" method="post" id="add_{$module}/{$modulefile}_Form" name="add_{$module}/{$modulefile}_Form">
        <input type="hidden" name="action" value="do_addbrand" />
        <div>
            <div style="display:inline-block; width: 30%;"><strong>{$lang->name}</strong></div>
            <div style="display:inline-block; width: 60%;"><input name="entitybrand[title]" type="text"/></div>
        </div>
        <div>
            <div style="display: inline-block; width: 30%;"><strong>{$lang->customer}</strong></div>
            <div style="display: inline-block; width: 60%;">
                <input type='text' id='customer_noexception_autocomplete'/>
                <input type="text" size="3" id="customer_noexception_id_output" value="" disabled/>
                <input type='hidden' id='customer_noexception_id' name='entitybrand[eid]' value="" />
                <div id='searchQuickResults_customer_noexception' class='searchQuickResults' style='display:none;'></div> </div>
        </div>
        <div>
            <hr />
        </div>
        <div>
            <div style="display:inline-block; width: 30%;">{$lang->endproducttypes}</div>
            <div style="display:inline-block; width: 60%;">
                <select name="entitybrand[endproducttypes][]" multiple="multiple" size="10" id='popup_createbrand_endproducttypes'>{$endproducttypes_list}</select>
            </div>
        </div>
        <div>
            <div style="display:inline-block; width: 30%;"></div>
            <div style="display:inline-block; width: 60%;"><input type="checkbox" value="1" onclick="$('#popup_createbrand_endproducttypes').toggle()" name="entitybrand[isGeneral]"> or consider the brand with unspecified/unknown end-product type</div>
        </div>
        <div>
            <div><input type='button' id='add_{$module}/{$modulefile}_Button' value='{$lang->savecaps}' class='button'/></div>
            <div style="display:table-row;"> <div style="display:table-cell;"><div id="add_{$module}/{$modulefile}_Results"></div></div></div>
        </div>
    </form>
</div>