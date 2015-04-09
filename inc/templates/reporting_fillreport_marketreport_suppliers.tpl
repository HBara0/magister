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
                            <div style="display:inline-block;width:25%;"><input type="checkbox" value="1" name='marketreport[{$segment[psid]}][suppliers][0][sid][0]'/> <small>{$lang->unspecifiedsupplier}</small></div>
                            <div style="display:inline-block;width:70%;">
                                <div>
                                    <input type="text" size="25" id="chemfunctionchecmical_{$segment[psid]}0{$sprowid}_autocomplete" size="100" autocomplete="off" value="" placeholder="pick chemical substance"/>
                                    <input type="hidden" id="chemfunctionchecmical_{$segment[psid]}0{$sprowid}_id" name="marketreport[{$segment[psid]}][suppliers][0][csid][0]" value=''/>
                                    <div id="searchQuickResults_{$segment[psid]}0{$sprowid}" class="searchQuickResults" style="display:none;"></div>
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