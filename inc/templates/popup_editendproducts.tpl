<div id="popup_editendproducts" title="{$lang->editendprod}">
    <form action="#" method="post" id="add_products/types_Form" name="add_products/types_Form">
        <input type="hidden" name="action" value="edit_endproduct" />
        <input type="hidden" name="endproduct[eptid]" value="{$endprod['eptid']}" />
        <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td width="40%"><strong>{$lang->title}</strong></td><td><input type="text" name="endproduct[title]" value="{$endprod['title']}" tabindex="1"/></td>
            </tr>
            <tr>
                <td><strong>{$lang->parent}</strong></td><td>
                    <input type="text" size="25" id="endproductypes_{$endprod['eptid']}_autocomplete" size="100" autocomplete="off" value='{$parent['title']}' />
                    <input type="hidden" id="endproductypes_{$endprod['eptid']}_id" name="endproduct[parent]" value='{$parent['eptid']}'/>
                    <div id="searchQuickResults_{$endprod['eptid']}" class="searchQuickResults" style="display:none;"></div>
                </td>
            </tr>
            <tr>
                <td><strong>{$lang->applications}</strong></td><td><select name="endproduct[segapplications]" {$disabled} id="productypes_{$endprod['eptid']}_segapplications">{$applications_list}</select></td>
            </tr>
            <tr>
                <td colspan="2" align="left">
                    <hr />
                    <input type='button' id='add_products/types_Button' value='{$lang->savecaps}' class='button'/>
                    <div id="add_products/types_Results"></div>
                </td>
            </tr>
        </table>
    </form>
</div>