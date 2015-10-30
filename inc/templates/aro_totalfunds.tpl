<a class="header" href="#"><h2>{$lang->totalfunds}</h2></a>
<div>
    <p>
    <table>
        <tbody style="width:100%;">
            <tr>
                <td colspan="4">
                    <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px; text-align:center; font-weight:bold;">
                        {$lang->makesureusdamounts}
                    </div>
                </td>
            </tr>

            <tr>
                <td>{$lang->ordershpinvoverdue}</td>
                <td><input type="number" step="any" name="totalfunds[orderShpInvOverdue]" id="totalfunds_orderShpInvOverdue" value="{$totalfunds->orderShpInvOverdue}" style="width:100px;"/></td>
                <td>{$lang->ordersappawaitingshp}</td>
                <td><input type="number"  step="any" name="totalfunds[ordersAppAwaitingShp]" id="totalfunds_ordersAppAwaitingShp" value="{$totalfunds->ordersAppAwaitingShp}" style="width:100px;"/></td>
            </tr>
            <tr>
                <td>{$lang->ordershpinvnotdue}</td>
                <td><input type="number"  step="any" name="totalfunds[orderShpInvNotDue]" id="totalfunds_orderShpInvNotDue" value="{$totalfunds->orderShpInvNotDue}" style="width:100px;"/></td>
                <td>{$lang->oderswaitingapproval}</td>
                <td><input type="number"  step="any" name="totalfunds[odersWaitingApproval]" id="totalfunds_odersWaitingApproval" value="{$totalfunds->odersWaitingApproval}" style="width:100px;"/></td>
            </tr>
            <tr class="altrow">
                <td style="font-weight:bold;">{$lang->totalfundseng}</td>
                <td><input type="number"  step="any" name="totalfunds[totalFunds]" id="totalfunds_total" value="{$totalfunds->totalFunds}" style="width:100px;" class="automaticallyfilled-noneditable"/></td>
            </tr>
        </tbody>

    </table>
</p>
</div>
