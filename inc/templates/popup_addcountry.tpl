<div id="popup_addcountry" title="{$lang->addcountry}">
    <form action="#" method="post" id="perform_{$core->input['module']}_Form" name="perform_{$core->input['module']}_Form">
        <input type="hidden" name="action" value="do_add_countries" />
        <input type="hidden" value="c" name="type">
        <input type="hidden" value="{$country['coid']}" name="country[coid]">
        <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td width="40%"><strong>{$lang->name}</strong></td><td><input type="text" id="countryName" name="country[name]" value="{$country['name']}" tabindex="1"/></td>
            </tr>
            <tr>
                <td><strong>{$lang->acronym}</strong></td><td><input type="text" maxlength="2" id="acronym" name="country[acronym]" value="{$country['acronym']}" tabindex="1"/></td>
            </tr>
            <tr>
                <td><strong>{$lang->affiliate}</strong></td><td>{$affiliates_list}</td>
            </tr>
            <td colspan="2" align="left">
                <hr />
                <input type='button' id='perform_{$core->input['module']}_Button' value='{$lang->savecaps}' class='button'/>
                <div id="perform_{$core->input['module']}_Results"></div>
            </td>
            </tr>
        </table>
    </form>
</div>