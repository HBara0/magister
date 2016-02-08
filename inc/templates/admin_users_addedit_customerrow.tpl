<tr class="{$rowclass}">
    <td><input name="cid[{$cid}]" type="checkbox"{$checked} value="{$cid}" id="customerfilter_check_{$cid}"></td>
    <td><a href="../index.php?module=profiles/entityprofile&eid={$cid}" title="{$value}" target="_blank">{$value}</a>
    <td>{$affiliatedcustomers[$cid]}</td>
    <td>{$customerssegments[$cid]}</td>
    <td ><a href="index.php?module=entities/edit&eid={$cid}" title="{$lang->editcustomer}" target="_blank"><img src="../images/edit.gif" border=0 alt="{$value}"/></a></td>
</tr>