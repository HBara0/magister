<tr id="asset_{$asset[asid]}" class="{$rowclass}{$notactive}">
    <td>{$asset[tag]}</td>
    <td>{$asset[title]}</td>
    <td>{$asset[affiliate]}</td>
    <td>{$asset[type]}</td>
    <td>{$asset[status_output]}</td>
    <td>{$asset[createdOn_output]}</td>
    <td id="asset_{$asset[asid]}_tools">
        <div style="display:none;">
            <a href="#{$asset[asid]}" id="deleteasset_{$asset[asid]}_assets/listassets_loadpopupbyid" rel="{$asset[asid]}"><img src="{$core->settings[rootdir]}/images/invalid.gif" alt="{$lang->delete}" border="0"></a>
            <a href="#{$asset[asid]}" id="editasset_{$asset[asid]}_assets/listassets_loadpopupbyid" rel="{$asset[asid]}"><img src='{$core->settings[rootdir]}/images/icons/edit.gif' border='0' title="{$lang->edit}"/></a>
        </div>
    </td>
</tr>