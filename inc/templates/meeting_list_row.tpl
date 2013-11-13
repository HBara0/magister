<tr id="{$mid}" class="trowtools" >
    <td><a href=index.php?module=meetings/viewmeeting&referrer=list&mtid={$meeting['mtid']}>{$meeting[title]}</a></td>
    <td>{$meeting[description]}</td>
    <td>{$meeting[fromDate_output]}</td>
    <td>{$meeting[toDate_output]}</td> 
    <td>{$meeting[Location]}</td>
    <td> 

    <td id="setmof_{$mid}_tools">
        <div style="display:none;">
            {$edit_tool}
            {$setmeeting_tools}
        </div>
    </td>




</tr>