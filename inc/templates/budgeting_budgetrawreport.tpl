<page>
    <div style="page-break-before:always;"></div>
    <table class="datatable" border="0" cellpadding="1" cellspacing="1" width="100%" id="tabletoexport">
        <tbody>
            <tr class="thead">
                <th style="vertical-align:central; padding:2px;  border-bottom: dashed 1px #CCCCCC;display:none" align="center" class="border_left">line id</th>
                <th style="vertical-align:central; padding:2px;  border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->manager}</th>
                <th style="vertical-align:central; padding:2px;border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->customer}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->affiliate}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->country}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->supplier}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->saletype}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->segment}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->product}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->quantity}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->uom}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->unitPrice}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->amount}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->income}</th>
                    {$loalincome_header}
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->s1perc}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->s2perc}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->intercompany}</th>
                <th style="vertical-align:central; padding:2px; border-bottom: dashed 1px #CCCCCC;" align="center" class="border_left">{$lang->purchasingentity}</th>

            </tr>
        </tbody>
        {$budget_report_row}
        {$totals_row}
    </table>
</page>