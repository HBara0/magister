<div id="popup_cloneendproducttype" title="{$lang->cloneendproducttype}">
    <form action="#" method="post" id="perform_products/types_Form" name="perform_products/types_Form">
        <input type="hidden" name="action" value="clone_endproducttype" />
        <input type="hidden" name="endproduct[idtoclone]" value="{$eptid}" />
        <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td>{$lang->newproducttype}</td>
            </tr>
            <tr>
                <td width="40%"><strong>{$lang->title}</strong></td>
                <td><input type="text" name="endproduct[title]" value="{$endprod['title']}" tabindex="1"/></td>
            </tr>
            <tr>
                <td><strong>{$lang->parent}</strong></td><td>
                    <input type="text" size="25" id="endproductypes_{$eptid}_autocomplete" size="100" autocomplete="off" />
                    <input type="hidden" id="endproductypes_{$eptid}_id" name="endproduct[parentid]"/>
                    <div id="searchQuickResults_{$eptid}" class="searchQuickResults" style="display:none;"></div>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="left">
                    <hr />
                    <input type='button' id='perform_products/types_Button' value='{$lang->clone}' class='button'/>
                    <div id="perform_products/types_Results"></div>
                </td>
            </tr>
        </table>

    </form>
</div>