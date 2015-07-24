<div style="display:block;" >
    <hr style="width: 100%;height:7px"/>
    <div class="ui-state-highlight ui-corner-all">{$lang->makesuretochooseandclick}</div>
    <h2 class="subtitle">Possible Transportations</h2>
    <div style="display: block">{$lang->oneway}<input type="radio" value="2" name="segment[{$sequence}][transp]" id="transp_lookuptransps_{$sequence}"/>
        {$lang->roundtrip}<input type="radio" value="1" name="segment[{$sequence}][transp]" id="transp_lookuptransps_{$sequence}"/><br/><br/>
        <input type="button" class="button" value="{$lang->lookuptransps}" id="lookuptransps_{$sequence}"/>
    </div>
    <!--able width="100%" cellspacing="0" cellpadding="0" style="margin-left: 8px;" class="datatable">
          <tbody id="othertranspcat_{$sequence}_tbody">-->
    <div id="content_suggestedtransploader_{$sequence}"></div>
    <div id="content_suggestedtransp_{$sequence}">{$transsegments_output[suggested]}</div>
    <hr>
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
<hr style="width: 100%;height:7px"/>
<h2 class="subtitle" style="padding:8px;width:40%;">{$lang->accomodations}</h2>
<input type="checkbox" id="noAccomodation_{$sequence}" name="segment[{$sequence}][noAccomodation]" {$checkedaccomodation} value="1" /> {$lang->noaccomodation}

<div style="display:block;" id="segment_hotels_{$sequence}">
   <!-- <div class="subtitle">{}Approved Hotels</div>-->

    {$hotelssegments_output}
</div>
<div style="display:block;" id="other_hotels_{$sequence}">
  <!-- <div class="subtitle">{}Approved Hotels</div>-->
    {$otherhotels_output}
</div>
<hr style="width: 100%;height:7px"/>
<div style="display:block; width: 100%;" id="segment_expenses_{$sequence}">
    <input name="sequence" type="hidden" id="sequence" value="{$sequence}">
    <h2 class="subtitle" style="padding:8px;width:40%;">{$lang->addexp}</h2>
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
<hr style="width: 100%;height:7px"/>
<div style="display:block; width: 100%; margin-top: 10px;" id="segment_finances_{$sequence}">
    <input name="sequence" type="hidden" id="sequence" value="{$sequence}">
    <h2 class="subtitle" style="padding:8px;width:100%;">{$lang->finance} ({$lang->anticipatedamount})</h2>
    <table width="100%"cellspacing="0" cellpadding="0" style="margin-left: 8px;" class="datatable">
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
