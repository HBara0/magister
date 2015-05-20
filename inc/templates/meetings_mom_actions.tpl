<table style="width:100%;">
    <thead>
        <tr><td class="thead" colspan="3">{$lang->specificactions}</td></tr>
    <thead>
    <tbody id="meetingsactions_{$arowid}_tbody" class="datatable">
        {$actions_rows}
    </tbody>
    <tfoot>
        <tr>
            <td>
                <input name="numrows_meetingsactions{$arowid}" type="hidden" id="numrows_meetingsactions_{$arowid}" value="{$arowid}">
                <img src="./images/add.gif" id="ajaxaddmore_meetings/minutesmeeting_meetingsactions_{$arowid}" alt="{$lang->add}">
            </td>
        </tr>
    </tfoot>
</table>