<tr id="{{$asset[asid]}}">
    <td>{$asset[title]}</td>
    <td>{$asset[affiliate]}</td>
    <td>{$asset[description]}</td>
    <td>{$asset[type]}</td>
    <td>{$asset[status]}</td>
    <td><a href='index.php?module=assets/manageassets&amp;type=edit&amp;id={$asset[asid]}'><img src='{$core->settings[rootdir]}/images/icons/edit.gif' border='0' title="{$lang->edit}"/></a></td>
</tr>