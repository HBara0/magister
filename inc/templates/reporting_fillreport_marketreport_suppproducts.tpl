<tr class="{$altrow}"  style="border:1px gainsboro solid;width:100%" id="{$segment[psid]}_{$sprowid}">
    <td colspan="2">
        <div id='prof_mkd_chemsubfield_{$segment[psid]}{$srowid}{$sprowid}' style="display:inline-block;">
            <div {$display[chemsubstance]}>
                <div style="display:inline-block;width:30%;"> {$lang->chemicalsubstance}</div>
                <div style="display:inline-block;width:65%;">
                    <div>
                        <input type="text" size="25" id="chemicalproducts_{$segment[psid]}{$srowid}{$sprowid}_autocomplete" size="100" autocomplete="off" value="{$chemicalsubstance_name}"/>
                        <input type="hidden" id="chemicalproducts_{$segment[psid]}{$srowid}{$sprowid}_id" name="marketreport[{$segment[psid]}][suppliers][{$srowid}][chp][{$sprowid}][csid]" value="{$mrcompetition_product[csid]}"/>
                        <div id="searchQuickResults_{$segment[psid]}{$srowid}{$sprowid}" class="searchQuickResults" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div style="display:inline-block;width:65%;padding-top:5px;{$display_orprodlink}"><img src="{$core->settings[rootdir]}/images/clickhere.png" /><a style="padding-top:-10px;" {$display[product]} style="color:#91b64f;" onclick="
                if($('#prof_mkd_chemsubfield_{$segment[psid]}{$srowid}{$sprowid}').is(':visible')) {
                    $('#prof_mkd_chemsubfield_{$segment[psid]}{$srowid}{$sprowid}').hide();
                    $('#prof_mkd_prodfield_parentdiv_{$segment[psid]}{$srowid}{$sprowid}').show();
                    $('#prof_mkd_prodfield_{$segment[psid]}{$srowid}{$sprowid}').show();
                    $('#product_{$segment[psid]}{$srowid}{$sprowid}_autocomplete').val('');
                    $('#product_{$segment[psid]}{$srowid}{$sprowid}_id').val('');
                }
                else {
                    $('#prof_mkd_prodfield_{$segment[psid]}{$srowid}{$sprowid}').hide();
                    $('#chemicalproducts_{$segment[psid]}{$srowid}{$sprowid}_id').val('');
                    $('#prof_mkd_prodfield_parentdiv_{$segment[psid]}{$srowid}{$sprowid}').hide();
                    $('#chemicalproducts_{$segment[psid]}{$srowid}{$sprowid}_autocomplete').val('');
                    $('#prof_mkd_chemsubfield_{$segment[psid]}{$srowid}{$sprowid}').show();


                }">or use product instead</a></div>
        <div {$display[product]} id='prof_mkd_prodfield_parentdiv_{$segment[psid]}{$srowid}{$sprowid}'><div style="display:inline-block;width:30%;" >{$lang->product} <small>{$lang->ifavailable}</small></div>
            <div id='prof_mkd_prodfield_{$segment[psid]}{$srowid}{$sprowid}' style="display:block;width:65%;">
                <input type="hidden" value="{$segment[psid]}_{$srowid}_{$sprowid}"/>
                <input type="text" size="25" id="product_{$segment[psid]}{$srowid}{$sprowid}_autocomplete" autocomplete="off"  value="{$product_name}"/>
                <input type="hidden" id="product_{$segment[psid]}{$srowid}{$sprowid}_id" name="marketreport[{$segment[psid]}][suppliers][{$srowid}][chp][{$sprowid}][pid]" value="{$mrcompetition_product[pid]}"/>
                <div id="searchQuickResults_{$segment[psid]}{$srowid}{$sprowid}" class="searchQuickResults" style="display:none;"></div>
                <input type="hidden" name="marketreport[{$segment[psid]}][suppliers][{$srowid}][chp][{$sprowid}][inputChecksum]" value="{$inputchecksum[product]}"/>
                <br />
            </div>
        </div>

        <div style="width:100%;">
            <br/>
            {$lang->productcomment}
            <div><textarea cols="40" name="marketreport[{$segment[psid]}][suppliers][{$srowid}][chp][{$sprowid}][howCanWeBeatThem]">{$mrcompetition_product[howCanWeBeatThem]}</textarea></div>
        </div>

    </td>