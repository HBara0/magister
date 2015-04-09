<tr style="border:1px gainsboro solid;" id="{$segment[psid]}_{$srowid}"><td>
        <table style="width:100%;">
            <thead>
                <tr class="thead">
                    <td colspan="2">Supplier</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{$lang->supplier}</td>
                    <td>
                        <input type='text'id='competitorsupp_{$segment[psid]}{$srowid}_QSearch' autocomplete='off'  value=""/>
                        <input type='hidden' id='competitorsupp_{$segment[psid]}{$srowid}_id' name='marketreport[{$segment[psid]}][suppliers][{$srowid}][sid]' value="" />
                        <div id='searchQuickResults_competitorsupp_{$segment[psid]}{$srowid}' class='searchQuickResults' style='display:none;'></div
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table style="width:100%;">
                            <tbody id="supplierproducts_{$segment[psid]}_{$srowid}_tbody">
                                {$product_row}
                                {$supplierproducts}
                            </tbody>
                        </table>
                        <span>
                            <input type="hidden" name="ajaxaddmoredata[segmentid]" id="ajaxaddmoredata_segmentid"  value="{$segment[psid]}"/>
                            <input type="hidden" name="ajaxaddmoredata[srowid]" id="ajaxaddmoredata_segmentid"  value="{$srowid}"/>
                            <img src="./images/add.gif"  style="cursor:pointer" id="ajaxaddmore_reporting/fillreport_supplierproducts_{$segment[psid]}_{$srowid}"  alt="{$lang->addmoreproducts}"> {$lang->addmoreproducts}
                            <input type="hidden" id="numrows_supplierproducts_{$segment[psid]}_{$srowid}" value="{$sprowid}">
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</td></tr>