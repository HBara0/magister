<div id="popup_profilesmarketdata" title="{$lang->addmarketdata}" style="z-index: 2000">
    <form name="perform_{$module}/{$modulefile}_Form" id="perform_{$module}/{$modulefile}_Form">
        <input type="hidden" name="action" value="{$action}"/>
        <input type="hidden" name="{$elementname}" value="{$elemtentid}"/>
        <input type="hidden" name="{$elementname}" value="{$elemtentid}"/>
        <input type="hidden" name="marketdata[visitreportdate]" value="{$visitreport->date}"/>
        <input type="hidden" name="marketdata[vridentifier]" value="{$visitreport->identifier}"/>
        <div>
            {$profiles_entityprofile_micustomerentry}
            <div style="width: 30%; display: inline-block;">{$lang->endproductbrand}</div>
            <div style="width: 60%; display: inline-block;"><input onFocus="getustomerid()" type="text" size="25" id="entbrandsproducts_{$brandprod_rowid}_autocomplete" size="100" autocomplete="off" value="{$brandname}"/> | <a style="cursor: pointer" id="showpopup_createbrand" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0" title="{$lang->createbrand}"></a>
                <input type="hidden" id="entbrandsproducts_{$brandprod_rowid}_id" name="marketdata[ebpid]" value='{$midata->ebpid}'/>
                <input type="hidden" id="entbrandsproducts_{$brandprod_rowid}_cid" name="cid" value='{$midata->cid}'/>
                <div id="searchQuickResults_{$brandprod_rowid}" class="searchQuickResults" style="display:none;"></div>
            </div>
            {$profiles_minproductentry}
            {$profiles_michemfuncproductentry}
            {$profiles_mibasicingredientsentry}
            <div style="width: 30%; display: inline-block;">{$lang->annualpotential}</div><div style="width: 60%; display: inline-block;"><input type="number" step="any" size="12" id="potential" name="marketdata[potential]" accept="numeric"  required="required" autocomplete="off" min="0" value="{$midata->potential}{$midata_potential}"/></div>
            <div style="width: 30%; display: inline-block;">{$lang->marketshareperc}</div><div style="width: 60%; display: inline-block;"><input type="number" step="any" size="12" id="mktshareperc" name="marketdata[mktSharePerc]" accept="numeric" required="required" autocomplete="off" min="0" value="{$midata->mktSharePerc}{$midata_mktSharePerc}"/></div>
            <div style="width: 30%; display: inline-block;">{$lang->marketshareqty}</div><div style="width: 60%; display: inline-block;"><input type="number" size="12" step="any" id="mktshareqty" name="marketdata[mktShareQty]" accept="numeric" required="required" autocomplete="off" min="0" value="{$midata->mktShareQty}{$midata_mktShareQty}"/></div>
            <div style="width: 30%; display: inline-block;">{$lang->price}</div><div style="width: 60%; display: inline-block;"><input type="number" step="any" size="12" name="marketdata[unitPrice]" accept="numeric" autocomplete="off" min="0" value="{$midata->unitPrice}{$midata_unitPrice}"/> USD/KG {$lang->cif}</div>
            <div style="width: 30%; display: inline-block;">{$profiles_mincustomervisit_title}</div>
            <div style="width: 30%; display: inline-block;">
                {$profiles_mincustomervisit}
            </div>
            <div>{$lang->comment}</div>
            <div><textarea cols="60" rows="5" name="marketdata[comments]">{$midata->comments}</textarea></div>
                {$comments}
            <hr />
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr><td><strong>{$lang->competition}</strong></td></tr>
                <tr>
                    <td>
                        <table class="datatable" width="100%">
                            <tbody id="competitor{$mimorerowsid}_tbody">
                                <tr id="2">
                                    <td>
                                        <div style="width:100%; display:block;padding:5px">
                                            <div style="width:45%; display: inline-block;">{$lang->competitortrader}</div>
                                            <div style="width:45%; display: inline-block;"><input type='text'id='competitortradersupp_{$rowid}_QSearch' autocomplete='off'  value="{$competitor[trader]}"/>
                                                <input type='hidden' id='competitortradersupp_{$rowid}_id' name='marketdata[competitor][{$rowid}][trader]' value="{$mrktcompetitor_obj->trader}" /> <a href="index.php?module=contents/addentities&type=supplier" target="_blank" title="{$lang->add}"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0"></a>
                                                <div id='searchQuickResults_competitortradersupp_{$rowid}' class='searchQuickResults' style='display:none;'></div></div>
                                        </div>
                                        <div style="width:100%; display:block;padding:5px">
                                            <div style="width:45%; display: inline-block;">{$lang->competitorprod}</div>
                                            <div style="width:45%; display: inline-block;"><input type='text'id='competitorproducersupp_{$rowid}_QSearch' value="{$competitor[producer]}" autocomplete='off'/> <a href="index.php?module=contents/addentities&type=supplier" target="_blank" title="{$lang->add}"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0"></a>
                                                <input type='hidden' id='competitorproducersupp_{$rowid}_id' name='marketdata[competitor][{$rowid}][producer]'  value="{$mrktcompetitor_obj->producer}" />
                                                <div id='searchQuickResults_competitorproducersupp_{$rowid}' class='searchQuickResults' style='display:none;'></div> </div>
                                        </div>
                                        <div style="width:100%; display:block;padding:5px">
                                            <div style="width:45%; display: inline-block;">{$lang->price}</div>
                                            <div style="width:45%; display: inline-block;"><input type="text" size="8" name="marketdata[competitor][{$rowid}][unitPrice]"  value="{$competitor[uniprice]}" accept="numeric" autocomplete="off" min="0"/> {$incoterms_list} USD/KG</div>
                                        </div>
                                        <div style="width:100%; display:block;padding:5px">
                                            <div style="width:45%; display: inline-block;">{$lang->product}</div>
                                            <div style="width:45%; display: inline-block;"><input type="text" name="marketdata[competitor][{$rowid}][pid]" id="product_{$rowid}_QSearch"  value="{$competitor[product]}" autocomplete="off"/>
                                                <input type="hidden" id="product_{$rowid}_id"  value="{$competitor[pid]}" name="marketdata[competitor][{$rowid}][pid]" />
                                                <div id="searchQuickResults_{$rowid}" class="searchQuickResults" style="display:none;"></div></div>
                                        </div>
                                        <div style="width:100%; display:block;padding:5px">
                                            <div style="width:45%; display: inline-block;">{$lang->packaging}</div>
                                            <div style="width:45%; display: inline-block;">{$packaging_list}</div>
                                        </div>

                                        <div style="width:100%; display:block;padding:5px">
                                            <div style="width:45%; display: inline-block;">{$lang->saletype}</div>
                                            <div style="width:45%; display: inline-block;">{$saletype_list}</div>
                                        </div>
                                        <div style="width:100%; display:block;padding:5px">
                                            <div style="width:45%; display: inline-block;">{$lang->sampleacquired}</div>
                                            <div style="width:45%; display: inline-block;">{$samplacquire}</div>
                                        </div>
                                    </td>
                                </tr>
                                {$competitors_rows}
                            </tbody>
                            <tfoot>
                                <tr><td><img id="addmore_competitor{$mimorerowsid}" src="{$core->settings[rootdir]}/images/add.gif" /></td></tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
            </table>
            <div>
                <input class="button" value="{$lang->add}" id="perform_{$module}/{$modulefile}_Button" type="submit">
                <div id="perform_{$module}/{$modulefile}_Results"></div>
            </div>
    </form>
</div>


<script>
    function getustomerid() {
        var cid = $('#allcustomertypes_{$customer_rowid}_id').val();
        if(typeof cid != 'undefined') {
            $('#entbrandsproducts_{$brandprod_rowid}_cid').val(cid);
        }
    }
</script>