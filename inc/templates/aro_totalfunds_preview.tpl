<a class="header" href="#"><h2>{$lang->totalfunds}</h2></a>
<div>
    <p>
    <table>
        <tbody style="width:100%;">
            <tr>
                <td colspan="4">
                    <div class="ui-state-highlight ui-corner-all hidden-print" style="padding-left: 5px; margin-bottom:10px; text-align:center; font-weight:bold;">
                        {$lang->makesureusdamounts}
                    </div>
                </td>
            </tr>

            <tr>
                <td>{$lang->ordershpinvoverdue}</td>
                <td>{$totalfunds->orderShpInvOverdue}</td>
                <td>{$lang->ordersappawaitingshp}</td>
                <td>{$totalfunds->ordersAppAwaitingShp}</td>
            </tr>
            <tr>
                <td>{$lang->ordershpinvnotdue}</td>
                <td>{$totalfunds->orderShpInvNotDue}</td>
                <td>{$lang->oderswaitingapproval}</td>
                <td>{$totalfunds->odersWaitingApproval}</td>
            </tr>
            <tr class="altrow">
                <td style="font-weight:bold;">{$lang->totalfundseng}</td>
                <td>{$totalfunds->totalFunds}</td>
            </tr>
        </tbody>

    </table>
</p>
</div>
