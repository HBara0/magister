<a class="header" href="#"><h2>{$lang->totalfunds}</h2></a>
<div>
    <p>
    <table class="datatable">
        <tbody style="width:100%;">
            <tr>
                <td colspan="4">
                    <div class="ui-state-highlight ui-corner-all hidden-print" style="padding-left: 5px; margin-bottom:10px; text-align:center; font-weight:bold;">
                        {$lang->makesureusdamounts}
                    </div>
                </td>
            </tr>

            <tr>
                <td class="border_right">{$lang->ordershpinvoverdue}</td>
                <td class="border_right">{$totalfunds->orderShpInvOverdue}</td>
                <td class="border_right">{$lang->ordersappawaitingshp}</td>
                <td class="border_right">{$totalfunds->ordersAppAwaitingShp}</td>
            </tr>
            <tr>
                <td class="border_right">{$lang->ordershpinvnotdue}</td>
                <td class="border_right">{$totalfunds->orderShpInvNotDue}</td>
                <td class="border_right">{$lang->oderswaitingapproval}</td>
                <td class="border_right">{$totalfunds->odersWaitingApproval}</td>
            </tr>
            <tr class="altrow">
                <td style="font-weight:bold;" class="border_righ">{$lang->totalfundseng}</td>
                <td class="border_right">{$totalfunds->totalFunds}</td>
            </tr>
        </tbody>

    </table>
</p>
</div>
