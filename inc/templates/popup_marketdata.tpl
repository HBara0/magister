
<div id="popup_marketdata" title="{$lang->addmarketdata}">
    <script type="text/javascript">
        $(function() {
            $('input[id="mktshareperc"]').live('keyup', function() {
                if (!jQuery.isNumeric($('input[id="mktshareperc"]').val())) {
                    return;
                }
                if ($(this).val().length > 0 && $('input[id=potential]').val().length > 0) {
                    $('input[id="mktshareqty"]').val(Number($('input[id="potential"]').val()) * $(this).val() / 100);
                }
            });
            $('input[id="mktshareqty"]').live('keyup', function() {
                if ($('input[id="potential"]').val().length > 0) {
                    $('input[id="mktshareperc"]').val($(this).val() / ($('input[id="potential"]').val()) * 100);
                    //$('input[id="mktshareperc"]').trigger('keyup');
                }
            });
            /*parse end product type*/
            $("input[id='customer_0_QSearch']").live('blur', function() {
                var cid = $('input[id="customer_0_id"]').val();
                if (cid.length == 0) {
                    return;
                }
                var data = "&action=get_entityendproduct&attr=" + $(this).attr("name") + "&value=" + cid;
                sharedFunctions.requestAjax("post", "index.php?module=" + "{$module}/{$modulefile}", data, 'entitiesbrandsproducts_endproductResult', 'entitiesbrandsproducts_endproductResult', 'html');
            });
        });
    </script>
    <form name="perform_{$module}/{$modulefile}_Form" id="perform_{$module}/{$modulefile}_Form">
        <input type="hidden"  name="action" value="{$action}"/>
        <input type="hidden"  name="{$elementname}" value="{$elemtentid}"/>
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr><td> 
                    <table><tr><td>{$lang->fieldlabel} </td><td>{$field}</td></tr>
                        {$product_field}
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="100%">
                        <tr><td>{$lang->annualpotential}</td>
                            <td>{$lang->marketshare}</td>
                            <td>{$lang->marketshareqty}</td>
                        </tr>
                        <tr><td width="8%"> <input type="text"  step="any" size="12" id="potential" name="marketdata[potential]" accept="numeric"  required="required"  autocomplete="off"/></td>
                            <td width="8%"> <input type="text" step="any" size="12" id="mktshareperc" name="marketdata[mktSharePerc]" accept="numeric" required="required"  autocomplete="off"/></td>
                            <td> <input type="text" size="12" step="any" id="mktshareqty" name="marketdata[mktShareQty]" accept="numeric" required="required"  autocomplete="off"/></td>
                        </tr>
                        <tr>
                            <td>{$lang->price}</td>
                            <td> <input type="text" size="12" name="marketdata[unitPrice]" accept="numeric"  autocomplete="off"/></td>
                            <td>{$lang->usd}</td>
                        </tr>
                        <tr><td>{$lang->endproduct}</td><td><div id="entitiesbrandsproducts_endproductResult"></div> <select {$hideselect} name="marketdata[ebpid]">{$entitiesbrandsproducts_list}</select></td></tr>
                    </table></td>
            </tr>
            <tr><td>
                    <table>
                        <tr> <td>{$lang->comment}</td><td><textarea cols="40" rows="4" name="marketdata[comments]"></textarea>
                            </td></tr>
                    </table>
            </tr>
            <tr><td><hr></td></tr>
            <tr> <td> <strong>{$lang->competition}</strong></td></tr>

            <tbody id="competitor_tbody">
                <tr class="{$altrow}" id="2">
                    <td>
                        <table>
                            <input type="hidden" value="{$rowid}" name="rowid"/>
                            <tr>
                                <td> <strong>{$lang->competitor}</strong></td>
                                <td> <input type='text'id='competitorsupp_{$rowid}_QSearch' autocomplete='off' size='40px'/>
                                    <input type='hidden' id='competitorsupp_{$rowid}_id' name='marketdata[competitor][{$rowid}][eid]' value="" />
                                    <div id='searchQuickResults_competitorsupp_{$rowid}' class='searchQuickResults' style='display:none;'></div></td> </tr>
                            <tr><td>{$lang->price}</td>
                                <td> <input type="text" size="8" name="marketdata[competitor][{$rowid}][unitPrice]" accept="numeric" autocomplete="off"/></td>
                            </tr>
                            <tr><td>{$lang->product}</td>
                                <td> <input type="text"  name="marketdata[competitor][{$rowid}][pid]" id="product_{$rowid}_QSearch"  size="40" autocomplete="off"/>
                                    <input type="hidden" id="product_{$rowid}_id" name="marketdata[competitor][{$rowid}][pid]" />
                                    <div id="searchQuickResults_{$rowid}" class="searchQuickResults" style="display:none;"></div></td>
                            </tr>

                        </table>
                    </td> 
                </tr>
            </tbody>
            <tr><td>  <img id="addmore_competitor" src="{$core->settings[rootdir]}/images/add.gif" /><table><tr><td><input class="button" value="{$lang->add}" id="perform_{$module}/{$modulefile}_Button" type="submit"></td><td> <input class="button" value="{$lang->close}" id="hide_popupBox" type="button" onclick="$('#popup_marketdata').dialog('close')"></td> </tr></table></td></tr>
            <tr><td><div id="perform_{$module}/{$modulefile}_Results" type="button"></div></td></tr>
        </table>
    </form>
</div>