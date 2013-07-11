<tr id="asset_{$asset[asid]}">
    <td>{$asset[title]}</td>
    <td>{$asset[affiliate]}</td>
    <td>{$asset[description]}</td>
    <td>{$asset[title]}</td>
    <td>{$asset[status]}</td>
    <td> <a href="#{$asset[asid]}" style="display:none;" id="deleteasset_{$asset[asid]}_assets/listassets_loadpopupbyid" rel="delete_{$asset[asid]}"><img src="{$core->settings[rootdir]}/images/invalid.gif" alt="{$lang->delete}" border="0"></a> </td>
    <td> <a href="#{$asset[asid]}" style="display:none;" id="editasset_{$asset[asid]}_assets/listassets_loadpopupbyid" rel="edit_{$asset[asid]}"><img src='{$core->settings[rootdir]}/images/icons/edit.gif' border='0' title="{$lang->edit}"/></a> </td>
</tr>