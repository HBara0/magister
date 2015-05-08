<tr>
    <td>
        <div>
            <div style="width: 30%; display: inline-block; vertical-align: top; margin-bottom: 10px;">{$lang->product}</div><div style="width: 60%; display: inline-block;">
                <input type="text" size="25" id="chemfunctionproducts_{$mkdprod_rowid}_autocomplete" size="100" autocomplete="off"  value="{$product->name}"/>
                <input type="hidden" id="chemfunctionproducts_{$mkdprod_rowid}_id" name="marketdata[cfpid][]" value="{$midata->cfpid}"/>
                <input type="hidden" value="1" id="userproducts" name="userproducts" />
                <div id="searchQuickResults_{$mkdprod_rowid}" class="searchQuickResults" style="display:none;"></div>
                <br />
            </div>
        </div>

    </td>
</tr>
