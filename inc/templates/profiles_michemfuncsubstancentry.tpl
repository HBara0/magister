<tr>
    <td>
        <div>
            <div style="width: 30%; display: inline-block;">{$lang->chemicalsubstance}</div>
            <div style="width: 60%; display: inline-block;">
                <input type="text" size="25" id="chemfunctionchecmical_{$mkdchem_rowid}_autocomplete" size="100" autocomplete="off" value="{$chemsubstance->name}"/>
                <input type="hidden" id="chemfunctionchecmical_{$mkdchem_rowid}_id" name="marketdata[cfcid][]" value='{$midata->cfcid}'/>
                <div id="searchQuickResults_{$mkdchem_rowid}" class="searchQuickResults" style="display:none;"></div>
                <a href='#createchemical_id' id='addnew_crm/marketpotentialdata_chemical'><img src='images/addnew.png' border='0' alt='{$lang->add}' /></a>
            </div>
        </div>
    </td>
</tr>
