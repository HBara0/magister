<tr style="border:1px gainsboro solid;" id="{$crowid}"><td>
        <table style="width:100%;">
            <thead>
                <tr class="thead">
                    <td colspan="2">{$lang->customer}</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width:15%;">{$lang->customer}</td>
                    <td style="width:85%;">
                        <input type='text' id='customer_{$crowid}_autocomplete' name="marketreport[customers][{$crowid}][customerName]" value="{}" autocomplete='off'/>
                        <input type='text' size='3' id='customer_{$crowid}_id_output' disabled='disabled' value=""/>
                        <input type='hidden' value="{$customer[cid]}" id='customer_{$crowid}_id' name="marketreport[customers][{$crowid}][cid]" />
                        <div id='searchQuickResults_customer_{$crowid}' class='searchQuickResults' style='display:none;'></div>
                        <input type="hidden" name="marketreport[{$crowid}][inputChecksum]" value="{$inputchecksum[customer]}"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table style="width:100%;">
                            <tbody id="customerproducts_{$crowid}_tbody">
                                {$customer_product_row}
                                {$customer_products}
                            </tbody>
                        </table>
                        <span>
                            <input type="hidden" name="ajaxaddmoredata[crowid]" id="ajaxaddmoredata_segmentid"  value="{$crowid}"/>
                            <img src="./images/add.gif"  style="cursor:pointer" id="ajaxaddmore_reporting/fillreport_customerproducts_{$crowid}"  alt="{$lang->addmoreproducts}"> {$lang->addmoreproducts}
                            <input type="hidden" id="numrows_customerproducts_{$crowid}" value="{$cprowid}">
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</td>
</tr>