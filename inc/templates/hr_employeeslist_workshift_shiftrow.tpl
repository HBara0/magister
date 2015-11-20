<tr id='{$rowid}'>
    <td>{$workshifts_list}</td>
    <td>
        <input type='text' id='pickDate_shift{$rowid}_from' autocomplete='off' value='{$fromDate[$rowid][output]}' />
        <input type='hidden' name='fromDate[{$rowid}]' id='altpickDate_shift{$rowid}_from' value='{$fromDate[$rowid][formatted]}'/>
    </td>
    <td>
        <input type='text' id='pickDate_shift{$rowid}_to' autocomplete='off' value='{$toDate[$rowid][output]}' />
        <input type='hidden' name='toDate[{$rowid}]' id='altpickDate_shift{$rowid}_to' value='{$toDate[$rowid][formatted]}' />
    </td>
</tr>