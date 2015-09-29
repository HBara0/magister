<tr  class="trowtools">
    <td width="40%">{$paymentterm->get_displayname()}</td>
    <td width="40%">{$paymentterm->overduePaymentDays}</td>
    <td width="15%">{$paymentterm->nextBusinessDayicon}</td>
    <td id="edit_{$paymentterm->ptid}_tools" width="5%">
        {$edit_link}
    </td>
</tr>