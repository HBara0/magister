<tr>
    <td>{$lang->representative} <input type="hidden" name="meeting[attendees][rpid][matids]" value="{$repmatids}"/></td>
    <td>
        <input type="text" id="tokeninput_representative_input" name="meeting[attendees][rpid][ids]" />
        {$repinput}
    </td>
    <td> <a href='#representative_{$reprowid}_id' id='addnew_meetings/create_representative'><img src='images/addnew.png' border='0' alt='{$lang->add}'/></a>
    </td>
</tr>