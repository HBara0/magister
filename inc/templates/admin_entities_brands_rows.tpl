<tr class="{$altrow_class}">
    <td><a href="index.php?module=entities/managebrandendproducts&amp;id={$entitybrands[ebid]}">{$entitybrands[name]}</a></td>
    <td>{$entitybrands[supplier]}</td>
    <td id="tools">
        <a href="#{$entitybrands[ebid]}" id="deletebrand_{$entitybrands[ebid]}_entities/managebrands_loadpopupbyid" rel="{$entitybrands[ebid]}"><img src="{$core->settings[rootdir]}/images/invalid.gif" alt="{$lang->delete}" border="0"></a>
        <a href="#{$entitybrands[ebid]}" id="editbrand_{$entitybrands[ebid]}_entities/managebrands_loadpopupbyid" rel="{$entitybrands[ebid]}"><img src='{$core->settings[rootdir]}/images/icons/edit.gif' border='0' title="{$lang->edit}"/></a>
    </td>
</tr>