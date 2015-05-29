<tr id="{$rowid}">
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