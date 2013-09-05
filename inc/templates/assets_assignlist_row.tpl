<tr class="{$rowclass} trowtools" id="assetuser_{$assigneduser[auid]}"> 
    <td><a href="./users.php?action=profile&amp;uid={$assigneduser[uid]}" rel="{$assigneduser[asid]}" target="_blank">{$employee[displayName]}</a></td>
    <td>{$assigneduser[asset]}</td>
    <td>{$assigneduser[fromDate_output]}</td>
    <td>{$assigneduser[toDate_output]}</td>
    <td id="assetuser_{$assigneduser[auid]}_tools">
        <div style="display:none;">
            {$tools}
            <a href="#{$assigneduser[auid]}" id="edituser_{$assigneduser[auid]}_assets/listusers_loadpopupbyid" rel="edit_{$assigneduser[auid]}"><img src='{$core->settings[rootdir]}/images/icons/edit.gif' border='0' title="{$lang->edit}"/></a>
        </div>
    </td>
</tr>