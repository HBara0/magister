<a class="header" href="#"><h2>{$lang->totalfunds}</h2></a>
<div>
    <p>
    <table>
        <thead>
            <tr>
                <td colspan="2">
                    <div class="ui-state-highlight ui-corner-all hidden-print" style="padding-left: 5px; margin-bottom:10px; text-align:center; font-weight:bold;">
                        {$lang->makesureusdamounts}
                    </div>
                </td>
            </tr>
        </thead>
        <tbody class="datatable">
            <tr class="altrow">
                <td class="border_right" style="width:50%">{$lang->ordershpinvoverdue}</td>
                <td class="border_right" style="width:25%;text-align: right">{$totalfunds->orderShpInvOverdue}</td>
            </tr>
            <tr>
                <td class="border_right">{$lang->ordersappawaitingshp}</td>
                <td class="border_right" style="width:25%;text-align: right">{$totalfunds->ordersAppAwaitingShp}</td>
            </tr>
            <tr class="altrow">
                <td class="border_right">{$lang->ordershpinvnotdue}</td>
                <td class="border_right" style="width:25%;text-align: right">{$totalfunds->orderShpInvNotDue}</td>
            </tr>
            <tr>
                <td class="border_right">{$lang->oderswaitingapproval}</td>
                <td class="border_right" style="width:25%;text-align: right">{$totalfunds->odersWaitingApproval}</td>
            </tr>
            <tr class="altrow">
                <td style="font-weight:bold;" class="border_righ">{$lang->totalfundseng}</td>
                <td class="border_right" style="width:25%;text-align: right">{$totalfunds->totalFunds}</td>
            </tr>
        </tbody>

    </table>
</p>
</div>
