<a class="header" href="#"><h2 id="aro_currentstock">{$lang->currentstock}</h2></a>
<div>
    <p>
    <table>
        <thead>
            {$extraheader_row}
            <tr style="vertical-align: top;{$bold}">
                <td class="border_right" rowspan="2" valign="top" align="center" style="width:150px;">{$lang->product}</td>
                <td class="border_right" rowspan="2" valign="top" align="center" style="width:100px;">{$lang->packaging}</td>
                <td class="border_right" rowspan="2" valign="top" align="center" style="width:100px;">{$lang->quantity}</td>
                <td class="border_right" rowspan="2" valign="top" align="center" style="width:100px;">{$lang->stockvalue}</td>
                <td class="border_right" rowspan="2" valign="top" align="center" style="width:150px;">{$lang->dateofstockentry}</td>
                <td class="border_right" rowspan="2" valign="top" align="center" style="width:100px;">{$lang->expirydate}</td>
                <td class="border_right" rowspan="2" valign="top" align="center" style="width:150px;">{$lang->estdateofsale} </td>
                {$extraheader_fields}
        </thead>

        <tbody id="currentstockrow_{$csrowid}_tbody" style="width:100%;" class="{$datatable}">
            {$currentstock_rows}
        </tbody>
        <tfoot>
            <tr><td valign="top">
                    <input type="hidden" name="numrows_currentstockrow{$csrowid}"  id="numrows_currentstockrow_{$csrowid}" value="{$csrowid}">
                    <img src="./images/add.gif" id="ajaxaddmore_aro/managearodouments_currentstockrow_{$csrowid}" alt="{$lang->add}" style="display:none;">
                </td>
            </tr>
        </tfoot>

    </table>
</p>
</div>