<a class="header" href="#"><h2>{$lang->totalfunds}</h2></a>
<div>
    <p>
    <table>
        <thead>
            <tr>
                <td colspan="8">
                    <div class="ui-state-highlight ui-corner-all hidden-print" style="padding-left: 5px; margin-bottom:10px; text-align:center; font-weight:bold;">
                        All Amounts are in USD
                    </div>
                </td>
            </tr>
        </thead>
        <tbody class="datatable" style="width:70%">
            <tr><td></td><td></td>
                <td class="border_right">Begining of last month</td>
                <td class="border_right">3 months ago</td>
                <td class="border_right">6 months ago</td>
                <td class="border_right">9 months ago</td>
                <td class="border_right"> A year ago</td>
                <td class="border_right">Trend</td>
            </tr>
            <tr class="altrow">
                <td class="border_right">{$lang->ordershpinvoverdue}</td>
                <td class="border_right" style=";text-align: right">{$totalfunds->orderShpInvOverdue}</td>
                {$fundsengaged_evolution_row[orderShpInvOverdue]}
            </tr>
            <tr>
                <td class="border_right">{$lang->ordersappawaitingshp}</td>
                <td class="border_right" style=";text-align: right">{$totalfunds->ordersAppAwaitingShp}</td>
                {$fundsengaged_evolution_row[ordersAppAwaitingShp]}
            </tr>
            <tr class="altrow">
                <td class="border_right">{$lang->ordershpinvnotdue}</td>
                <td class="border_right" style="text-align: right">{$totalfunds->orderShpInvNotDue}</td>
                {$fundsengaged_evolution_row[orderShpInvNotDue]}
            </tr>
            <tr>
                <td class="border_right">{$lang->oderswaitingapproval}</td>
                <td class="border_right" style="text-align: right">{$totalfunds->odersWaitingApproval}</td>
                {$fundsengaged_evolution_row[odersWaitingApproval]}
            </tr>
            <tr class="altrow">
                <td style="font-weight:bold;" class="border_righ">{$lang->totalfundseng}</td>
                <td class="border_right" style="text-align: right">{$totalfunds->totalFunds}</td>
                {$fundsengaged_evolution_row[totalFunds]}
            </tr>
    </table>
</p>
</div>
