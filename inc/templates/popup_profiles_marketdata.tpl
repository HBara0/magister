<div id="popup_profilesmarketdata" title="{$lang->addmarketdata}">
    <form name="perform_{$module}/{$modulefile}_Form" id="perform_{$module}/{$modulefile}_Form">
        <input type="hidden" name="action" value="{$action}"/>
        <input type="hidden" name="{$elementname}" value="{$elemtentid}"/>
        <input type="hidden" name="{$elementname}" value="{$elemtentid}"/>
        <input type="hidden" name="marketdata[visitreportdate]" value="{$visitreport->date}"/>
        <input type="hidden" name="marketdata[vridentifier]" value="{$visitreport->identifier}"/>
        <div>
            {$profiles_entityprofile_micustomerentry}
            {$profiles_minproductentry}
            {$profiles_michemfuncproductentry}
            <div style="width: 30%; display: inline-block;">{$lang->annualpotential}</div><div style="width: 60%; display: inline-block;"><input type="number" step="any" size="12" id="potential" name="marketdata[potential]" accept="numeric"  required="required" autocomplete="off" min="0" value="{$midata->potential}"/></div>
            <div style="width: 30%; display: inline-block;">{$lang->marketshareperc}</div><div style="width: 60%; display: inline-block;"><input type="number" step="any" size="12" id="mktshareperc" name="marketdata[mktSharePerc]" accept="numeric" required="required" autocomplete="off" min="0" value="{$midata->mktSharePerc}"/></div>
            <div style="width: 30%; display: inline-block;">{$lang->marketshareqty}</div><div style="width: 60%; display: inline-block;"><input type="number" size="12" step="any" id="mktshareqty" name="marketdata[mktShareQty]" accept="numeric" required="required" autocomplete="off" min="0" value="{$midata->mktShareQty}"/></div>
            <div style="width: 30%; display: inline-block;">{$lang->price}</div><div style="width: 60%; display: inline-block;"><input type="number" step="any" size="12" name="marketdata[unitPrice]" accept="numeric" autocomplete="off" min="0" value="{$midata->unitPrice}"/> USD/KG {$lang->cif}</div>
            <div style="width: 30%; display: inline-block;">{$lang->endproduct}</div>
            <div style="width: 60%; display: inline-block;"><!--<select {$hideselect} name="marketdata[ebpid]">{$entitiesbrandsproducts_list}</select>--> <span id="entitiesbrandsproducts_endproductResult">{$entitiesbrandsproducts_list}</span> | <a id="showpopup_createbrand" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->createbrand}</a></div>
            <div style="width: 30%; display: inline-block;"> {$profiles_mincustomervisit_title}</div>
            <div style="width: 30%; display: inline-block;">
                {$profiles_mincustomervisit}
            </div>
            <div>{$lang->comment}</div>
            <div><textarea cols="60" rows="5" name="marketdata[comments]">{$midata->comments}</textarea></div>
            <hr />
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr><td><strong>{$lang->competition}</strong></td></tr>
                <tr>
                    <td>
                        <table class="datatable" width="100%">
                            <tbody id="competitor_tbody">
                                <tr id="2">
                                    <td>
                                        <div style="width:100%; display:block;">
                                            <div style="width:45%; display: inline-block;">{$lang->competitortrader}</div>
                                            <div style="width:45%; display: inline-block;"><input type='text'id='competitortradersupp_{$rowid}_autocomplete' autocomplete='off' />
                                                <input type='hidden' id='competitortradersupp_{$rowid}_id' name='marketdata[competitor][{$rowid}][trader]' value="" /> <a href="index.php?module=contents/addentities&type=supplier" target="_blank" title="{$lang->add}"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0"></a>
                                                <div id='searchQuickResults_competitortradersupp_{$rowid}' class='searchQuickResults' style='display:none;'></div></div>
                                        </div>
                                        <div style="width:100%; display:block;">
                                            <div style="width:45%; display: inline-block;">{$lang->competitorprod}</div>
                                            <div style="width:45%; display: inline-block;"><input type='text'id='competitorproducersupp_{$rowid}_autocomplete' autocomplete='off'/> <a href="index.php?module=contents/addentities&type=supplier" target="_blank" title="{$lang->add}"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0"></a>
                                                <input type='hidden' id='competitorproducersupp_{$rowid}_id' name='marketdata[competitor][{$rowid}][producer]' value="" />
                                                <div id='searchQuickResults_competitorproducersupp_{$rowid}' class='searchQuickResults' style='display:none;'></div> </div>
                                        </div>
                                        <div style="width:100%; display:block;">
                                            <div style="width:45%; display: inline-block;">{$lang->price}</div><div style="width:45%; display: inline-block;"><input type="text" size="8" name="marketdata[competitor][{$rowid}][unitPrice]" accept="numeric" autocomplete="off" min="0"/> USD/KG</div>
                                        </div>
                                        <div style="width:100%; display:block;">
                                            <div style="width:45%; display: inline-block;">{$lang->product}</div>
                                            <div style="width:45%; display: inline-block;"><input type="text" name="marketdata[competitor][{$rowid}][pid]" id="product_{$rowid}_autocomplete"  autocomplete="off"/>
                                                <input type="hidden" id="product_{$rowid}_id" name="marketdata[competitor][{$rowid}][pid]" />
                                                <div id="searchQuickResults_{$rowid}" class="searchQuickResults" style="display:none;"></div></div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr><td><img id="addmore_competitor" src="{$core->settings[rootdir]}/images/add.gif" /></td></tr>
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
