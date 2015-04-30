<tr>
    <td>
        <table style="width:100%;">
            <thead>
                <tr>
                    <td class="subtitle">{$lang->developmentprojects}</td>
                </tr>
            </thead>
            <tbody id="customers_{$segment[psid]}_{$crowid}_tbody">
                {$markerreport_customer_row}
            </tbody>
        </table>

        <span>
            <input type="hidden" name="ajaxaddmoredata[segmentid]" id="ajaxaddmoredata_segmentid"  value="{$segment[psid]}"/>
            <img src="./images/add.gif"  style="cursor: pointer" id="ajaxaddmore_reporting/fillreport_customers_{$segment[psid]}_{$crowid}"  alt="{$lang->addmorecustomers}"> {$lang->addmorecustomers}
            <input type="hidden" id="numrows_customers_{$segment[psid]}_{$crowid}" value="{$crowid}">
        </span>
    </td>
</tr>