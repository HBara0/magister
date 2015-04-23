<tr>
    <td>
        <table style="width:100%;">
            <thead>
                <tr>
                    <td class="subtitle">{$lang->developmentprojects}</td>
                </tr>
            </thead>
            <tbody id="customers_tbody">
                {$markerreport_customer_row}
            </tbody>
        </table>

        <span>
            <img src="./images/add.gif"  style="cursor: pointer" id="ajaxaddmore_reporting/fillreport_customers"  alt="{$lang->addmorecustomers}"> {$lang->addmorecustomers}
            <input type="hidden" id="numrows_customers" value="{$crowid}">
        </span>
    </td>
</tr>