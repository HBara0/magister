<tr class='{$rowclass}'>
    <td><a  href="#"id='mainmenu_{$menulist[cmsmid]}' style='cursor:pointer;'  rel='{$menulist[cmsmid]}' title='{$menulist[title]}'>{$menulist[title]}...</a></td>
    <td>{$menulist[description]}</td>
    <td>{$menulist[dateCreated_output]}</td>
    <td>{$menulist[lang]}</td>

    <td></td>
    <td>{$menulist_editicon}</td>
</tr>
<tr  id='{$menulist[cmsmid]}'>
    <td colspan="5" id='item_result_{$menulist[cmsmid]}'></td>
</tr>