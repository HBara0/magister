<tr style="border:1px gainsboro solid;" id="{$segment[psid]}_{$srowid}"><td>
        <table style="width:100%;">
            <thead>
                <tr class="thead">
                    <td colspan="2">{$lang->supplier}</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width:30%;">{$lang->supplier} <small>{$lang->compsuppliercomment}</small></td>
                    <td style="width:70%;">
                        <input type='text'id='competitorsupp_{$segment[psid]}{$srowid}_QSearch' autocomplete='off'  value="{$supplier_name}" size="25"/>
                        <input type='hidden' id='competitorsupp_{$segment[psid]}{$srowid}_id' name='marketreport[{$segment[psid]}][suppliers][{$srowid}][sid]' value="{$competitionsupplier['sid']}" />
                        <div id='searchQuickResults_competitorsupp_{$segment[psid]}{$srowid}' class='searchQuickResults' style='display:none;'></div>
                        <input type="hidden" name="marketreport[{$segment[psid]}][suppliers][{$srowid}][inputChecksum]" value="{$inputchecksum[supplier]}"/>
                        <a href="index.php?module=contents/addentities&amp;type=supplier" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a>

                    </td>
                </tr>
                <tr>
                    <td>
                        <a style="padding-top:-10px;" {$display[product]} style="color:#91b64f;" onclick="$('#competitorsupp_{$segment[psid]}{$srowid}_QSearch').toggle();
                                $('#marketreport_{$segment['psid']}_suppliers_{$srowid}_origin').toggle();"> Select Origin </a>
                    </td>
                    <td><span id="marketreport_{$segment['psid']}_suppliers_{$srowid}_origin" style="display: {$css[display][origin]};">{$countries_selectlist}</span>
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