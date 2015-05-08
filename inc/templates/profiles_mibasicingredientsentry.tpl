<tr>
    <td>
        <div>
            <div style="width: 30%; display: inline-block; vertical-align: top; margin-bottom: 10px;">{$lang->basicingredients}</div>
            <div style="width: 60%; display: inline-block;">
                <input type="text" size="25" id="basicingredients_{$mkdbing_rowid}_autocomplete" size="100" autocomplete="off"  value="{$basicingredient}"/>
                <input type="hidden" id="basicingredients_{$mkdbing_rowid}_id" name="marketdata[biid][]" value="{$midata->biid}"/>
                <div id="searchQuickResults_{$mkdbing_rowid}" class="searchQuickResults" style="display:none;"></div>
                <br />
            </div>
        </div>
    </td>
</tr>