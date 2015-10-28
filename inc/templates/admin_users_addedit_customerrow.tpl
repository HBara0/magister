<tr class="{$rowclass}">
    <td width="1%"><input name="cid[{$cid}]" type="checkbox"{$checked} value="{$cid}" id="customerfilter_check_{$cid}"></td>
    <td width="40%"><a href="../index.php?module=profiles/entityprofile&eid={$cid}" title="{$value}" target="_blank">{$value}</a>
    <td>{$affiliatedcustomers[$cid]}</td>
    <td>{$customerssegments[$cid]}</td>
    <td width="1%"><a href="index.php?module=entities/edit&eid={$cid}" title="{$lang->editcustomer}" target="_blank"><img src="../images/edit.gif" border=0 alt="{$value}"/></a></td>
</tr>