<a class="header" href="#"><h2>{$lang->totalfunds}</h2></a>
<div>
    <p>
    <table>
        <tbody style="width:100%;">
            <tr>
                <td>{$lang->ordershpinvoverdue}</td>
                <td><input type="number" step="1" name="totalfunds[orderShpInvOverdue]" id="totalfunds_orderShpInvOverdue" value="{$totalfunds->orderShpInvOverdue}" style="width:100px;"/></td>
                <td>{$lang->ordersappawaitingshp}</td>
                <td><input type="number"  step="1" name="totalfunds[ordersAppAwaitingShp]" id="totalfunds_ordersAppAwaitingShp" value="{$totalfunds->ordersAppAwaitingShp}" style="width:100px;"/></td>
            </tr>
            <tr>
                <td>{$lang->ordershpinvnotdue}</td>
                <td><input type="number"  step="1" name="totalfunds[orderShpInvNotDue]" id="totalfunds_orderShpInvNotDue" value="{$totalfunds->orderShpInvNotDue}" style="width:100px;"/></td>
                <td>{$lang->oderswaitingapproval}</td>
                <td><input type="number"  step="1" name="totalfunds[odersWaitingApproval]" id="totalfunds_odersWaitingApproval" value="{$totalfunds->odersWaitingApproval}" style="width:100px;"/></td>
            </tr>
            <tr class="altrow">
                <td>{$lang->totalfundseng}</td>
                <td><input type="number"  step="1" name="totalfunds[totalFunds]" id="totalfunds_total" value="{$totalfunds->totalFunds}" style="width:100px;"/></td>
            </tr>
        </tbody>

    </table>
</p>
</div>
