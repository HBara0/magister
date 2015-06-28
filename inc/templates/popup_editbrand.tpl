<div id="popup_createbrand" title="{$lang->createbrand}">
    <form action="#" method="post" id="perform_{$module}/{$modulefile}_Form" name="perform_{$module}/{$modulefile}_Form">
        <input type="hidden" name="action" value="do_addbrand" />
        <div>
            <div style="display:inline-block; width: 30%;"><strong>{$lang->name}</strong></div>
            <div style="display:inline-block; width: 60%;"><input name="entitybrand[name]" type="text" value="{$brandproduct[brandname]}{$entitybrand[name]}" {$disabled}/>{$ebid_hiddenfield}</div>
        </div>
        <div {$display[customer]}>
            <div style="display: inline-block; width: 30%;"><strong>{$lang->customer}</strong></div>
            <div style="display: inline-block; width: 60%;">
                <input type='text' id='allcustomertypes_{$id}_noexception_autocomplete' value="{$entitybrand[customer]}"/>
                <input type="text" size="3" id="allcustomertypes_{$id}_noexception_id_output" value="{$entitybrand[eid]}" disabled/>
                <input type='hidden' id='allcustomertypes_{$id}_noexception_id' name='entitybrand[eid]' value="{$entitybrand[eid]}" />
                <div id='searchQuickResults_customer_noexception' class='searchQuickResults' style='display:none;'></div> </div>
        </div>
        <div>
            <hr/>
            <div><input type='button' id='perform_{$module}/{$modulefile}_Button' value='{$lang->edit}' class='button'/></div>
            <div style="display:table-row;"> <div style="display:table-cell;"><div id="perform_{$module}/{$modulefile}_Results"></div></div></div>
        </div>
    </form>
</div>