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
                <input type='text' id='customer_0_QSearch'/>
                <input type="text" size="3" id="customer_0_id_output" value="" disabled/>
                <input type='hidden' id='customer_0_id' name='entitybrand[eid]' value="" />
                <div id='searchQuickResults_0' class='searchQuickResults' style='display:none;'></div> </div>
        </div>
        <div>
            <div style="display:inline-block; width: 30%;">{$lang->endproducttypes} </div>
            <div style="display:inline-block; width: 60%;">
                <select name="entitybrand[endproducttypes][]">{$endproducttypes_list}</select>
            </div>
        </div>
        <div>
            <div><input type='button' id='add_{$module}/{$modulefile}_Button' value='{$lang->savecaps}' class='button'/></div>
            <div style="display:table-row;"> <div style="display:table-cell;"> <div id="add_{$module}/{$modulefile}_Results"></div></div></div>
        </div>
    </form>
</div>