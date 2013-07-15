<tr class="{$rowclass}" id="asset_{$assigneduser[auid]}" title="{$lang->clicktomanage}"> 
    <td><a href="./users.php?action=profile&amp;uid={$assigneduser[uid]}"  rel="{$assigneduser[asid]}" target="_blank">{$employee[displayName]}</a></td>
    <td>{$assigneduser[asset]}</td>
    <td>{$assigneduser[fromDate_output]}</td>
     <td>{$assigneduser[toDate_output]}</td>
     <td> {$control_icons} </td>
      <td> <a href="#{$assigneduser[auid]}" style="display:none;" id="edituser_{$assigneduser[auid]}_assets/listuser_loadpopupbyid" rel="edit_{$assigneduser[auid]}"><img src='{$core->settings[rootdir]}/images/icons/edit.gif' border='0' title="{$lang->edit}"/></a> </td>
</tr>                                                                           