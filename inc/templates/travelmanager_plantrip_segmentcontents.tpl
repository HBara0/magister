<div style="display:block;" >
    <div style="margin:50px;text-align: center" >
        <img src="./images/chevron-down.png" align="middle" style=""/>
    </div>
    <h2 class="subtitle" id="transpsetionheader_{$sequence}">Possible Transportations</h2>
    <div class="ui-state-highlight ui-corner-all" style='padding:8px;margin-bottom: 10px;'>{$lang->transppolicy}</div>
    <div class="ui-state-highlight ui-corner-all" style='margin-bottom: 10px;font-weight: bold;' id="transpmethod_{$sequence}">{$lang->makesuretochooseandclick}</div>
    <div style="display: block">{$lang->oneway}<input type="radio" value="2" name="segment[{$sequence}][transp]" id="transp_lookuptransps_{$sequence}"/>
        {$lang->roundtrip}<input type="radio" value="1" name="segment[{$sequence}][transp]" id="transp_lookuptransps_{$sequence}"/><br/><br/>
        <input type="button" class="button" value="{$lang->lookuptransps}" id="lookuptransps_{$sequence}"/>
    </div>
    <!--able width="100%" cellspacing="0" cellpadding="0" style="margin-left: 8px;" class="datatable">
          <tbody id="othertranspcat_{$sequence}_tbody">-->
    <div id="content_suggestedtransploader_{$sequence}"></div>
    <div id="content_suggestedtransp_{$sequence}">{$transsegments_output[suggested]}</div>

    <input type="checkbox" id="checkbox_show_othertransps_{$sequence}" style="margin-left:12px;" {$checked[othertranspssection]}/>{$lang->other} {$lang->transportations}
    <div id="show_othertransps_{$sequence}" style="{$display[othertranspssection]}">
        <div>{$transsegments_output[othertransp]}</div>
        <table width="100%" class="datatable" style="margin-top:-20px;">
            <tbody id="othertranspcat_{$sequence}_tbody">
                {$rows}
            </tbody>
        </table>

        <div {$transp_dispnone}> <img src="./images/add.gif" style="cursor: pointer" id="ajaxaddmore_travelmanager/plantrip_othertranspcat_{$sequence}"  alt="{$lang->add}"> Add another transportation
            <input type="hidden" name="ajaxaddmoredata[destcity]" id="ajaxaddmoredata_destcity" value="{$destcityid}"/>
            <input name="numrows_othertranspcat_{$sequence}" type="hidden" id="numrows_othertranspcat_{$sequence}" value="{$rowid}">
        </div>
    </div>
</div>
<hr>
<div style="margin:50px;text-align: center" >
    <img src="./images/chevron-down.png" align="middle" style=""/>
</div>
<h2 class="subtitle" style="padding:8px;width:40%;" id="accomsectionheader_{$sequence}">{$lang->accomodations}</h2>
<div class="ui-state-highlight ui-corner-all" style='padding:8px;margin-bottom: 8px;'>{$lang->hotelpolicy}</div>
<input type="checkbox" id="noAccomodation_{$sequence}" name="segment[{$sequence}][noAccomodation]" {$checkedaccomodation} value="1" /> {$lang->noaccomodation}

<div style="display:block;" id="segment_hotels_{$sequence}">
   <!-- <div class="subtitle">{}Approved Hotels</div>-->

    {$hotelssegments_output}
</div>
<div style="display:block;" id="other_hotels_{$sequence}">
  <!-- <div class="subtitle">{}Approved Hotels</div>-->
    {$otherhotels_output}
</div>
<div style="margin:50px;text-align: center" >
    <img src="./images/chevron-down.png" align="middle" style=""/>
</div>
<div style="display:block; width: 100%;" id="segment_expenses_{$sequence}">
    <input name="sequence" type="hidden" id="sequence" value="{$sequence}">
    <h2 class="subtitle" style="padding:8px;width:40%;" id="addexpensessetionheader_{$sequence}">{$lang->addexp}</h2>
    <div class="ui-state-highlight ui-corner-all" style='padding:8px;margin-bottom: 8px;'>{$lang->foodpolicies}<br/>{$lang->addexpensespolicies}</div>
    <table width="100%" border="1" cellspacing="0" cellpadding="0" style="margin-left: 8px;" class="datatable">
        <tbody id="expenses_{$sequence}_tbody">
            {$segments_expenses_output}

        </tbody>
    </table>
    <span> <img src="./images/add.gif"  style="cursor: pointer" id="ajaxaddmore_travelmanager/plantrip_expenses_{$sequence}"  alt="{$lang->add}"> {$lang->addexpenses}
        <input name="numrows_expenses_{$sequence}" type="hidden" id="numrows_expenses_{$sequence}" value="{$rowid}">
        <input type="hidden" name="ajaxaddmoredata[destcity]" id="ajaxaddmoredata_destcity" value="{$destcityid}"/>

    </span>

</div>
<div style="margin:50px;text-align: center" >
    <img src="./images/chevron-down.png" align="middle" style=""/>
</div>
<div style="display:block; width: 100%; margin-top: 10px;" id="segment_finances_{$sequence}">
    <input name="sequence" type="hidden" id="sequence" value="{$sequence}">
    <h2 class="subtitle" style="padding:8px;width:100%;" id="financesetionheader_{$sequence}">{$lang->finance} ({$lang->anticipatedamount})</h2>
    <table width="100%" cellspacing="0" cellpadding="0" style="margin-left: 8px;" class="datatable">
        <tr id="finance_{$sequence}_suggestionrow" style="display:block">
            <td><p id="finance_{$sequence}_suggestion"></p></td>
        </tr>
        <tbody id="finances_{$sequence}_tbody">
            {$finance_output}
        </tbody>
    </table>
    <span>
        <div>
            <img src="./images/add.gif" style="cursor: pointer" id="ajaxaddmore_travelmanager/plantrip_finances_{$sequence}" alt="{$lang->add}"> Add Amount
            <input name="numrows_finances_{$sequence}" type="hidden" id="numrows_finances_{$sequence}" value="{$frowid}">
        </div>
    </span>
</div>