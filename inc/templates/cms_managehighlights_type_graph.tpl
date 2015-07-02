<td colspan='4'>
    <div {$hide_graph} id='type_graph'>
        <table  width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="20%"><strong>{$lang->imgpath}</strong></td>
                <td width="35%"><input type='text' name='highlight[graph][imgPath]' value="{$highlight['imgPath']}"></td>
                <td width="15%"><strong>{$lang->title}</strong></td>
                <td width="25%"><input type='text' name='highlight[graph][graphTitle]' value="{$highlight['graphTitle']}"></td>
            </tr>
            <tr>
                <td width="20%"><strong>{$lang->link}</strong></td>
                <td width="25%"><input type='text' name='highlight[graph][targetLink]' value="{$highlight['targetLink']}"></td>
                <td width="15%"><strong>{$lang->description}</strong></td>
                <td width="30%"><textarea name='highlight[graph][description]'>{$highlight['description']}</textarea></td>
            </tr>
        </table>
    </div>
</td>
