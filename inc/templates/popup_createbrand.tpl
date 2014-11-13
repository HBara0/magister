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
                <input type='text' id='allcustomertypes_noexception_autocomplete'/>
                <input type="text" size="3" id="allcustomertypes_noexception_id_output" value="" disabled/>
                <input type='hidden' id='allcustomertypes_noexception_id' name='entitybrand[eid]' value="" />
                <div id='searchQuickResults_customer_noexception' class='searchQuickResults' style='display:none;'></div> </div>
        </div>
        <div>
            <div>
                <br /><strong>{$lang->endproducttypes}</strong></div>
            <div>
                <select name="entitybrand[endproducttypes][]" multiple="multiple" size="10" id='popup_createbrand_endproducttypes'>{$endproducttypes_list}</select>
            </div>
            <div><input type="checkbox" value="1" onclick="$('#popup_createbrand_endproducttypes').toggle()" name="entitybrand[isGeneral]"> {$lang->considerbrandunspecified}</div>
        </div>
        <div>
            <hr />
            <div><input type='button' id='add_{$module}/{$modulefile}_Button' value='{$lang->savecaps}' class='button'/></div>
            <div style="display:table-row;"> <div style="display:table-cell;"><div id="add_{$module}/{$modulefile}_Results"></div></div></div>
        </div>
    </form>
</div>