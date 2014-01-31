<div id="popup_profilesmarketdata" title="{$lang->addmarketdata}">
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
            $("input[id='customer_1_QSearch']").live('blur', function() {
                var cid = $('input[id="customer_1_id"]').val();
                if (cid.length == 0) {
                    return;
                }
                var data = "&action=get_entityendproduct&attr=" + $(this).attr("name") + "&value=" + cid;
                sharedFunctions.requestAjax("post", "index.php?module=" + "{$module}/{$modulefile}", data, 'entitiesbrandsproducts_endproductResult', 'entitiesbrandsproducts_endproductResult', 'html');
            });
        });
    </script>
    <form name="perform_{$module}/{$modulefile}_Form" id="perform_{$module}/{$modulefile}_Form">
        <input type="hidden" name="action" value="{$action}"/>
        <input type="hidden" name="{$elementname}" value="{$elemtentid}"/>
        {$profiles_entityprofile_micustomerentry}
        {$profiles_michemfuncproductentry}
        <div style="width: 30%; display: inline-block;">{$lang->annualpotential}</div><div style="width: 60%; display: inline-block;"><input type="number" step="any" size="12" id="potential" name="marketdata[potential]" accept="numeric"  required="required"  autocomplete="off"/></div>
        <div style="width: 30%; display: inline-block;">{$lang->marketshare}</div><div style="width: 60%; display: inline-block;"><input type="number" step="any" size="12" id="mktshareperc" name="marketdata[mktSharePerc]" accept="numeric" required="required"  autocomplete="off"/></div>
        <div style="width: 30%; display: inline-block;">{$lang->marketshareqty}</div><div style="width: 60%; display: inline-block;"><input type="number" size="12" step="any" id="mktshareqty" name="marketdata[mktShareQty]" accept="numeric" required="required"  autocomplete="off"/></div>
        <div style="width: 30%; display: inline-block;">{$lang->price}</div><div style="width: 60%; display: inline-block;"><input type="number" size="12" name="marketdata[unitPrice]" accept="numeric"  autocomplete="off"/> {$lang->usd} {$lang->cif}</div>
        <div style="width: 30%; display: inline-block;">{$lang->endproduct}</div><div style="width: 60%; display: inline-block;"><div id="entitiesbrandsproducts_endproductResult"></div> <select {$hideselect} name="marketdata[ebpid]">{$entitiesbrandsproducts_list}</select></div>

        <div>{$lang->comment}</div>
        <div><textarea cols="60" rows="5" name="marketdata[comments]"></textarea></div>
        <div style="width: 50%; padding:5px;display: inline-block;" class="subtitle"> <a href="createbrand" id="showpopup_createbrand" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->createbrand}</a></div>
        <hr />
        <table cellpadding="0" cellspacing="0" width="100%"> 
            <tr> <td> <strong>{$lang->competition}</strong></td></tr></table>
        <table class="datatable" width="100%">
            <tbody id="competitor_tbody">

                <tr id="2">
                    <td>
     
                        <div style="width:60%; display:block;"> 
                            <div style="width:40%; display: inline-block;"> {$lang->competitortrader}</div>
                            <div style="width:40%; display: inline-block;"><input type='text'id='competitortradersupp_{$rowid}_QSearch' autocomplete='off' size='40px'/>
                                <input type='hidden' id='competitortradersupp_{$rowid}_id' name='marketdata[competitor][{$rowid}][trader]' value="" />
                                <div id='searchQuickResults_competitortradersupp_{$rowid}' class='searchQuickResults' style='display:none;'></div></div>
                        </div>

                        <div style="width:60%; display:block;"> 
                            <div style="width:40%; display: inline-block;"> {$lang->competitorprod}</div> 
                            <div style="width:40%; display: inline-block;">  <input type='text'id='competitorproducersupp_{$rowid}_QSearch' autocomplete='off' size='40px'/>
                                <input type='hidden' id='competitorproducersupp_{$rowid}_id' name='marketdata[competitor][{$rowid}][producer]' value="" />
                                <div id='searchQuickResults_competitorproducersupp_{$rowid}' class='searchQuickResults' style='display:none;'></div> </div>
                        </div>
                        <div style="width:100%; display:block;"> 
                            <div style="width:24%; display: inline-block;"> {$lang->price}</div> <div style="width:15%; display: inline-block;"> <input type="text" size="8" name="marketdata[competitor][{$rowid}][unitPrice]" accept="numeric" autocomplete="off"/></div>
                            <div style="width:33.3%; display: inline-block;">{$lang->usd}</div> 
                        </div>
                        <div style="width:60%; display:block;"> 
                            <div style="width:40%; display: inline-block;"> {$lang->product}</div>
                            <div style="width:40%; display: inline-block;"> <input type="text"  name="marketdata[competitor][{$rowid}][pid]" id="product_{$rowid}_QSearch"  size="40" autocomplete="off"/>
                                <input type="hidden" id="product_{$rowid}_id" name="marketdata[competitor][{$rowid}][pid]" />
                                <div id="searchQuickResults_{$rowid}" class="searchQuickResults" style="display:none;"></div></div> 
                        </div> 

                    </td>
                </tr>

            </tbody>
        </table>

        <tr><td><img id="addmore_competitor" src="{$core->settings[rootdir]}/images/add.gif" /><table><tr><td><input class="button" value="{$lang->add}" id="perform_{$module}/{$modulefile}_Button" type="submit"></td><td> <input class="button" value="{$lang->close}" id="hide_popupBox" type="button" onclick="$('#popup_profilesmarketdata').dialog('close')"></td> </tr></table></td></tr>
        <tr><td><div id="perform_{$module}/{$modulefile}_Results" type="button"></div></td></tr>
        </table>
    </form>
</div>
