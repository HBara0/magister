<div id='prof_mkd_chemsubfield' style="display: {$css[display][chemsubfield]};">
    <div style="width: 30%; display: inline-block;">{$lang->chemicalsubstance}</div>
    <div style="width: 60%; display: inline-block;">
        <input type="text" size="25" id="chemfunctionchecmical_0_autocomplete" size="100" autocomplete="off" value="{$chemsubstance->name}"/>
        <input type="hidden" id="chemfunctionchecmical_0_id" name="marketdata[cfcid]" value='{$midata->cfcid}'/>
        <div id="searchQuickResults_0" class="searchQuickResults" style="display:none;"></div>
        <a href='#createchemical_id' id='addnew_crm/marketpotentialdata_chemical'><img src='images/addnew.png' border='0' alt='{$lang->add}' /></a>
    </div>
</div>