<tr id="{$fieldrow_id}">
    <td><div style="width:150px"><input type="text" name="field[{$swsid}][{$field['inputChecksum']}][name]" value="{$field['name']}"></div></td>
    <td><div style="width:150px"><input type="text" name="field[{$swsid}][{$field['inputChecksum']}][dbColumn]" value="{$field['dbColumn']}"></div></td>
    <td><div style="width:150px"><input type="checkbox" {$field_isdisplayed_check} name="field[{$swsid}][{$field['inputChecksum']}][isDisplayed]" value="1"></div></td>
    <td><div style="width:150px"><input type="checkbox" {$field_isreadonly_check} name="field[{$swsid}][{$field['inputChecksum']}][isReadOnly]" value="1"></div></td>
    <td><div style="width:150px"><input type="number" name="field[{$swsid}][{$field['inputChecksum']}][sequence]" value="{$field['sequence']}"></div></td>
    <td><div style="width:150px">{$field_fieldtype_list}</div></td>
    <td><div style="width:150px"><div id="field[{$swsid}][{$field['inputChecksum']}][srliid]" style="{$showfieldtype_reflists[$field['inputChecksum']]}">{$field_fieldtypelist_list}</div>
            <input type="hidden" name="field[{$swsid}][{$field['inputChecksum']}][inputChecksum]" value="{$field['inputChecksum']}">
            <input type="hidden" name="field[{$swsid}][{$field['inputChecksum']}][swsid]" value="{$field['swsid']}">
            <input type="hidden" name="field[{$swsid}][{$field['inputChecksum']}][swstid]" value="{$field['swstid']}"></div></td>
    <td><div style="width:150px"><input type="text" name="field[{$swsid}][{$field['inputChecksum']}][length]" value="{$field['length']}"></div></td>
    <td><div style="width:150px"><textarea name="field[{$swsid}][{$field['inputChecksum']}][displayLogic]">{$field['displayLogic']}</textarea></div></td>
    <td><div style="width:150px"><textarea name="field[{$swsid}][{$field['inputChecksum']}][description]">{$field['description']}</textarea></div></td>
    <td><div style="width:150px"><textarea name="field[{$swsid}][{$field['inputChecksum']}][comment]">{$field['comment']}</textarea></div></td>
    <td><div style="width:150px"><textarea name="field[{$swsid}][{$field['inputChecksum']}][onChangeFunction]"></textarea></div></td>
    <td><div style="width:150px">{$field_allowedfiletypes_list}</div></td>
</tr>