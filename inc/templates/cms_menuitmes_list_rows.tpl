<tr class='{$rowclass}'>
    <td><a  href="#"id='mainmenu_{$menulist[cmsmid]}' style='cursor:pointer;'  rel='{$menulist[cmsmid]}' title='{$menulist[title]}'>{$menulist[title]}...</a></td>
    <td>{$menulist[description]}</td>
    <td>{$menulist[dateCreated_output]}</td>
    <td>{$menulist[lang]}</td>

    <td><a href='index.php?module=cms/managemenu&type=addmenuitem&amp;id={$menulist[cmsmid]}' target='_blank'  title='{$lang->addmenuitem}'><img src='./images/add.gif' border='0'/></a></td>
    <td>{$menulist_editicon}</td>
</tr>
<tr  id='{$menulist[cmsmid]}'>
    <td id='item_result_{$menulist[cmsmid]}'></td>
</tr>