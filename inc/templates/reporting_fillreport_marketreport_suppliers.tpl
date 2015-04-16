<tr>
    <td>
        <table style="width:100%;">
            <thead>
                <tr>
                    <td class="subtitle">{$lang->competitiongeneralcomment}</td>
                </tr>
            </thead>
            <tbody id="suppliers_{$segment[psid]}_tbody">
                {$markerreport_segment_suppliers_row}
            </tbody>
            <tfoot>
                <tr class="thead">
                    <td colspan="2">{$lang->unspecifiedsupplier}</td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <div style="width:100%;">
                                <table style="width:100%;">
                                    <tbody id="unspecifiedsupplierproducts_{$segment[psid]}_0_tbody">
                                        <tr id="test">
                                            <td style="width:30%;">
                                                <input type="checkbox" value="1" name='marketreport[{$segment[psid]}][suppliers][0][unspecifiedsupp]' {$checked[unspecifiedsupp]}/>
                                                <small>{$lang->unspecifiedsupplier}</small>
                                                <input type="hidden" name="marketreport[{$segment[psid]}][suppliers][0][inputChecksum]" value="{$inputchecksum[unspecifiedsupp]}"/>
                                            </td>
                                            <td tyle="width:65%;">
                                                <input type="text" size="25" id="chemfunctionchecmical_{$segment[psid]}00_autocomplete" size="100" autocomplete="off" value="{$unspecified_chemname}" placeholder="pick chemical substance"/>
                                                <input type="hidden" id="chemfunctionchecmical_{$segment[psid]}00_id" name="marketreport[{$segment[psid]}][suppliers][0][chp][0][csid]" value="{$unspecified_id}"/>
                                                <div id="searchQuickResults_{$segment[psid]}00" class="searchQuickResults" style="display:none;"></div>
                                                <input type="hidden" name="marketreport[{$segment[psid]}][suppliers][0][chp][0][inputChecksum]" value="{$inputchecksum[unspecifiedsuppcs]}"/>
                                            </td>
                                        </tr>
                                        {$unspecifiedsupplierproducts}
                                    </tbody>
                                </table>
                            </div>
                            <span>
                                <input type="hidden" name="ajaxaddmoredata[segmentid]" id="ajaxaddmoredata_segmentid"  value="{$segment[psid]}"/>
                                <input type="hidden" name="ajaxaddmoredata[srowid]" id="ajaxaddmoredata_segmentid"  value="0"/>
                                <img src="./images/add.gif"  style="cursor:pointer" id="ajaxaddmore_reporting/fillreport_unspecifiedsupplierproducts_{$segment[psid]}_0"  alt="{$lang->addmoreproducts}"> {$lang->addmorechem}
                                <input type="hidden" id="numrows_unspecifiedsupplierproducts_{$segment[psid]}_0" value="0">
                            </span>


                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>

        <span>
            <input type="hidden" name="ajaxaddmoredata[segmentid]" id="ajaxaddmoredata_segmentid_{$segment[psid]}"  value="{$segment[psid]}"/>
            <img src="./images/add.gif"  style="cursor: pointer" id="ajaxaddmore_reporting/fillreport_suppliers_{$segment[psid]}"  alt="{$lang->addmoresuppliers}"> {$lang->addmoresuppliers}
            <input type="hidden" id="numrows_suppliers_{$segment[psid]}" value="{$srowid}">
        </span>
    </td>
</tr>