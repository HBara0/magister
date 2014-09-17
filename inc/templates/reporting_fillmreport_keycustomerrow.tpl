<tr id="{$rowid}">
    <td valign="top" width="15%" style="border-right: 1px solid #E8E8E8;" class="altrow2">{$lang->customername}</td>
    <td valign="top"><input type='text' id='customer_{$rowid}_autocomplete' name='keycustomers[$rowid][customername]' value="{$report_data[keycustomers][$rowid][customername]}" autocomplete="off"/>
        <input type="text" size="3" id="customer_{$rowid}_id_output" value="{$report_data[keycustomers][$rowid][cid]}" disabled/>
        <input type='hidden' id='customer_{$rowid}_id' name='keycustomers[$rowid][cid]' value="{$report_data[keycustomers][$rowid][cid]}"/>
        <a href="index.php?module=contents/addentities&amp;type=customer" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a>
        <div id='searchQuickResults_customer_{$rowid}' class='searchQuickResults' style='display:none;'></div>
        <p>
        <div style="float:left; width: 30%">{$lang->currentstatus}</div><div style="float:left; width: 50%;"><textarea id="customer_status_{$rowid}" name="keycustomers[$rowid][status]" cols="30" rows="2">{$report_data[keycustomers][$rowid][status]}</textarea></div>
        <div style="float:left; width: 30%">{$lang->changes}</div><div style="float:left; width: 50%;"><textarea id="customer_changes_{$rowid}" name="keycustomers[$rowid][changes]" cols="30" rows="2">{$report_data[keycustomers][$rowid][changes]}</textarea></div>
        <div style="float:left; width: 30%">{$lang->risksopportunities}</div><div style="float:left; width: 50%;"><textarea id="customer_risksOpportunities_{$rowid}" name="keycustomers[$rowid][risksOpportunities]" cols="30" rows="2">{$report_data[keycustomers][$rowid][risksOpportunities]}</textarea></div>
    </p>
</td>
</tr>