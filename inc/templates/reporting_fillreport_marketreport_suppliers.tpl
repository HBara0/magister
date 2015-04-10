<tr>
    <td>
        <table style="width:100%;">
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
                            <div style="display:inline-block;width:22%;"><input type="checkbox" value="1" name='marketreport[{$segment[psid]}][suppliers][0][unspecifiedsupp]'/> <small>{$lang->unspecifiedsupplier}</small>
                                <input type="hidden" name="marketreport[{$segment[psid]}][suppliers][0][inputChecksum]" value="{$inputchecksum[unspecifiedsupp]}"/>
                            </div>
                            <div style="display:inline-block;width:70%;">
                                <div>
                                    <input type="text" size="25" id="chemfunctionchecmical_{$segment[psid]}00_autocomplete" size="100" autocomplete="off" value="" placeholder="pick chemical substance"/>
                                    <input type="hidden" id="chemfunctionchecmical_{$segment[psid]}00_id" name="marketreport[{$segment[psid]}][suppliers][0][chp][0][csid]" value=''/>
                                    <div id="searchQuickResults_{$segment[psid]}00" class="searchQuickResults" style="display:none;"></div>
                                    <input type="hidden" name="marketreport[{$segment[psid]}][suppliers][0][chp][0][inputChecksum]" value="{$inputchecksum[unspecifiedsuppcs]}"/>

                                </div>
                            </div>
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