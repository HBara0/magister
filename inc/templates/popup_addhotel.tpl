<div id="popup_addhotel" title="{$lang->addotherhotel}"  style="height: 200px;">
    <form action="#" method="post" id="add_otherhotel_{$core->input[module]}_Form" name="add_otherhotel_{$core->input[module]}_Form">
        <input type="hidden" name="action" value="do_add_otherhotel" />
        <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td width="40%"><strong>{$lang->hotel}</strong></td><td><input type='text' required="required"   name="otherhotel[name]"  tabindex="1"/></td>
            </tr>
            <tr>
                <td><strong>{$lang->city}</strong></td>

                <Td><input type="text" autocomplete="off" tabindex="1"  id="cities_cache_{$sequence}_autocomplete"    value="{$segmentobj_destcityname}" required="required"/></Td>
            <input type='hidden' id='cities_cache_{$sequence}_id'   name="otherhotel[city]" value="{$destcityid}"/>
            </td>

            </tr>
            <tr>
                <td><strong>{$lang->country}</strong></td>

                <Td><input type="text"  disabled="disabled" autocomplete="off" tabindex="1"  id="countries_{$sequence}_cache_autocomplete"    value="{$segdescity_country}" required="required"/></Td>
                <td>
                    <input type='hidden' id='countries_{$sequence}_cache_id'   name="otherhotel[country]" value="{$segdescity_obj_coid }"/>
                </td>

            </tr>


            <td colspan="2" align="left">
                <hr />
                <input type='submit' id='add_otherhotel_{$core->input[module]}_Button' value='{$lang->savecaps}' class='button'/>
                <div id="add_otherhotel_{$core->input[module]}_Results"></div>
            </td>
            </tr>
        </table>
    </form>
</div>