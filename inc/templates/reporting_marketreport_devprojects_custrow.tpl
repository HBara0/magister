<tr style="border:1px gainsboro solid;" id="{$segment[psid]}{$crowid}"><td>
        <table style="width:100%;">
            <thead>
                <tr class="thead">
                    <td colspan="2">{$lang->customer}</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width:15%;" class="subtitle">{$lang->customer}</td>
                    <td style="width:85%;">
                        <input type='text' id='customer_{$segment[psid]}{$crowid}_autocomplete' name="marketreport[{$segment[psid]}][customers][{$crowid}][customerName]" value="{$mrdevprojectcustomer[customerName]}" autocomplete='off'/>
                        <input type='hidden' size='3' id='customer_{$segment[psid]}{$crowid}_id_output' disabled='disabled' value="{$mrdevprojectcustomer[cid]}"/>
                        <input type='hidden' value="{$mrdevprojectcustomer[cid]}" id='customer_{$segment[psid]}{$crowid}_id' name="marketreport[{$segment[psid]}][customers][{$crowid}][cid]" />
                        <div id='searchQuickResults_customer_{$segment[psid]}{$crowid}' class='searchQuickResults' style='display:none;'></div>
                        <input type="hidden" name="marketreport[{$segment[psid]}][customers][{$crowid}][inputChecksum]" value="{$inputchecksum[customer]}"/>
                        <a href="index.php?module=contents/addentities&amp;type=customer" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a>

                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table style="width:100%;">
                            <tbody id="customerproducts_{$segment[psid]}_{$crowid}_tbody">
                                {$customer_product_row}
                                {$customer_products}
                            </tbody>
                        </table>
                        <span>
                            <input type="hidden" name="ajaxaddmoredata[segmentid]" id="ajaxaddmoredata_segmentid"  value="{$segment[psid]}"/>
                            <input type="hidden" name="ajaxaddmoredata[crowid]" id="ajaxaddmoredata_segmentid"  value="{$crowid}"/>
                            <img src="./images/add.gif"  style="cursor:pointer" id="ajaxaddmore_reporting/fillreport_customerproducts_{$segment[psid]}_{$crowid}"  alt="{$lang->addmoreproducts}"> {$lang->addmoreproducts}
                            <input type="hidden" id="numrows_customerproducts_{$segment[psid]}_{$crowid}" value="{$cprowid}">
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</td>
</tr>