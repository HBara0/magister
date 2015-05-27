<div id="popup_editendprod" title="{$lang->editendprod}">
    <table width="100%" class="datatable">
        <tr>
            <td width="30%">{$lang->title}</td>
            <td><input type='text' name="endproduct[title]" value="{$endprod['title']}"></td>
        </tr>
        <tr>
            <td><strong>{$lang->parent}</strong></td>
            <td>
                <input type="text" size="25" id="endproductypes_$endprod['eptid']_autocomplete" size="100" autocomplete="off" vale='{$parent['title']}' />
                <input type="hidden" id="endproductypes_$endprod['eptid']_id" name="endproduct[parent]" value='{$parent['eptid']}'/>
                <div id="searchQuickResults_$endprod['eptid']" class="searchQuickResults" style="display:none;"></div>

            </td>
        </tr>

    </table>
</div>