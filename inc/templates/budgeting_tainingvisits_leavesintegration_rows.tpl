<tr>
    <td><input type="checkbox" name="budgetrainingvisit[leaves][{$leaves_obj->lid}]"  id="leave_{$leaves_obj->lid}" value="{$leaves_obj->lid}"/></td>
    <td>{$leaves_obj->employee}</td>
    <td>{$leavedate[$leaves_obj->lid]}</td>
    <td>{$leaves_obj->reason}</td>
    <td>{$leaves_obj->totalexpenses}</td>

</tr>