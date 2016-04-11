<div id="popup_createbrand" title="{$lang->createbrand}" style="z-index: 2000">
    <form action="#" method="post" id="add_{$module}/{$modulefile}_Form" name="add_{$module}/{$modulefile}_Form">
        <input type="hidden" name="action" value="do_addbrand" />
        <div>
            <div style="display:inline-block; width: 30%;"><strong>{$lang->name}</strong></div>
            <div style="display:inline-block; width: 60%;"><input name="entitybrand[name]" type="text" value="{$brandproduct[brandname]}" {$disabled}/>{$ebid_hiddenfield}</div>
        </div>
        <div {$display[customer]}>
            <div style="display: inline-block; width: 30%;"><strong>{$lang->customer}</strong></div>
            <div style="display: inline-block; width: 60%;">
                <input type='text' id='allcustomertypes_noexception_autocomplete'/>
                <input type="text" size="3" id="allcustomertypes_noexception_id_output" disabled/>
                <input type='hidden' id='allcustomertypes_noexception_id' name='entitybrand[eid]' />
                <div id='searchQuickResults_customer_noexception' class='searchQuickResults' style='display:none;'></div> </div>
        </div>
        <div id='popupcreatebrand_endproducttypes'>
            <div><br/><strong>{$lang->endproducttypes}</strong></div>
            <div style="width:100%; height:150px; overflow:auto; vertical-align:top; margin-bottom: 10px;">
                <table class="datatable" width="100%">
                    <thead>
                        <tr>
                            <th width="100%"><input type="checkbox" id='producttypefilter_checkall'><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->endproducttypes}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
                        </tr>
                    </thead>
                    <tbody>
                        {$endproducttypes_list}
                    </tbody>
                </table>

            </div>
            <div>{$lang->charasteristiclist} {$characteristics_list}</div>
            <!--  <div>
                  <select name="entitybrand[endproducttypes][]" multiple="multiple" size="10" id='popup_createbrand_endproducttypes'>{$endproducttypes_list}</select>
              </div>-->
        </div>
        <div><input type="checkbox" value="1" onchange="$('#popupcreatebrand_endproducttypes').toggle();" name="entitybrand[isGeneral]"> {$lang->considerbrandunspecified}</div>
        <div>
            <hr/>
            <div><input type='button' id='add_{$module}/{$modulefile}_Button' value='{$lang->savecaps}' class='button'/></div>
            <div style="display:table-row;"> <div style="display:table-cell;"><div id="add_{$module}/{$modulefile}_Results"></div></div></div>
        </div>
    </form>
</div>