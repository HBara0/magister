<tr id="{$meeting[mtid]}" class="trowtools" >
    <td><a href=index.php?module=meetings/viewmeeting&referrer=list&mtid={$meeting['mtid']}>{$meeting[title]}</a><span style="padding:8px;">{$meeting_sharedwithyou}</span></td>

    <td>{$meeting[description]} </td>
    <td>{$meeting[fromDate_output]}</td>
    <td>{$meeting[toDate_output]}</td>
    <td>{$meeting[Location]} </td>
    <td id="setmom_{$meeting[mtid]}_tools">
        <div style="display:none; width:60px;">
            {$row_tools}
        </div>
    </td>
</tr>