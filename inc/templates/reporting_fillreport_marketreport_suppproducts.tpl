<tr class="{$altrow}"  style="border:1px gainsboro solid;width:100%" id="{$segment[psid]}_{$sprowid}">
    <td colspan="2">
        <div style="width:100%;"><div style="display:inline-block;width:22%;">{$lang->product}</div>
            <div style="display:inline-block;width:70%;">
                <input type="hidden" value="{$segment[psid]}_{$srowid}_{$sprowid}"/>
                <input type="text" size="25" id="chemfunctionproducts_{$segment[psid]}{$srowid}{$sprowid}_autocomplete" autocomplete="off"  value="{$product_name}"/>
                <input type="hidden" id="chemfunctionproducts_{$segment[psid]}{$srowid}{$sprowid}_id" name="marketreport[{$segment[psid]}][suppliers][{$srowid}][chp][{$sprowid}][pid]" value="{$mrcompetition_product[pid]}"/>
                <div id="searchQuickResults_{$segment[psid]}{$srowid}{$sprowid}" class="searchQuickResults" style="display:none;"></div>
                <input type="hidden" name="marketreport[{$segment[psid]}][suppliers][{$srowid}][chp][{$sprowid}][inputChecksum]" value="{$inputchecksum[product]}"/>
                <br />
            </div>
        </div>
        <div style="display:inline-block;width:20%;"></div>
        <div style="display:inline-block;width:70%;"><a onclick="$('#prof_mkd_chemsubfield_{$segment[psid]}{$srowid}{$sprowid}').toggle();">or use chemical substance instead.</a></div>
    </div>

    <div id='prof_mkd_chemsubfield_{$segment[psid]}{$srowid}{$sprowid}' style="display: {$css[display][chemsubfield]};">
        <div style="display:inline-block;width:22%;"> {$lang->chemicalsubstance}</div>
        <div style="display:inline-block;width:70%;">
            <div>
                <input type="text" size="25" id="chemfunctionchecmical_{$segment[psid]}{$srowid}{$sprowid}_autocomplete" size="100" autocomplete="off" value="{$chemicalsubstance_name}"/>
                <input type="hidden" id="chemfunctionchecmical_{$segment[psid]}{$srowid}{$sprowid}_id" name="marketreport[{$segment[psid]}][suppliers][{$srowid}][chp][{$sprowid}][csid]" value="{$mrcompetition_product[csid]}"/>
                <div id="searchQuickResults_{$segment[psid]}{$srowid}{$sprowid}" class="searchQuickResults" style="display:none;"></div>
            </div>
        </div>
    </div>
</td>
</tr>