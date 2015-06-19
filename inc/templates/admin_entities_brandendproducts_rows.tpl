<tr class="{$altrow_class}">
    <td>{$brandproduct[endproductname]}</td>
    <td>{$brandproduct[characteristic]}</td>
    <td>{$brandproduct[brand]}</td>
    <td id="tools">
        <a href="#{$entitybrandproduct[ebpid]}" id="deletebrandendproduct_{$entitybrandproduct[ebpid]}_entities/managebrandendproducts_loadpopupbyid" rel="{$entitybrandproduct[ebid]}"><img src="{$core->settings[rootdir]}/images/invalid.gif" alt="{$lang->delete}" border="0"></a>
        <a href="#{$entitybrandproduct[ebpid]}" id="editbrandendproduct_{$entitybrandproduct[ebpid]}_entities/managebrandendproducts_loadpopupbyid" rel="{$entitybrandproduct[ebpid]}"><img src='{$core->settings[rootdir]}/images/icons/edit.gif' border='0' title="{$lang->edit}"/></a>
    </td>
</tr>