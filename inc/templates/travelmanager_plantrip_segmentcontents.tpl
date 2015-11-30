<div style="display:block;" >
    <!--<div style="text-align: right;display: inline-block;width: 55%;vertical-align: top;margin-top: 50px;margin-bottom: 50px;">
         <div id="save_section_{$sequence}1">
             <input type="hidden" id="save_section_{$sequence}_1_input" value="0" name="segment[{$sequence}][savesection][section1]"/>
             <a id="save_section_{$sequence}_1" class="tm_sections">Save and proceed</a>
         </div>
     </div>
    -->
    <div style="display: inline-block;width:50%;vertical-align: top;margin-bottom: 50px;margin-top: 100px;">
        <input type="hidden" id="save_section_{$sequence}_1_input" value="0" name="segment[{$sequence}][savesection][section1]"/>
        <a id="save_section_{$sequence}_1" style="width:30%;float:right;padding:10px;text-align: center;background-color:#690;font-weight:bold;font-size:15px;">Save & Proceed
            <img src="{$core->settings['rootdir']}/images/chevron-down.png"/>
        </a>
    </div>
    <div style="display: inline-block;width: 30%;vertical-align: top;margin-top: 100px;margin-left: 75px;" id="sectionresults_1"></div>
    <div id="perform_travelmanager/plantrip_Results_1"></div>
    <h2 class="subtitle" id="transpsetionheader_{$sequence}">Transportations</h2>
    <div class="ui-state-highlight ui-corner-all" style='padding:8px;margin-bottom: 10px;'>{$lang->transppolicy}</div>
    <div style="display: block;border:1px solid #FCEFA1; padding:15px;">
        <h2 class="subtitle">{$lang->lookuptransps}</h2>
        <div style='margin-bottom: 10px;font-weight: bold;' id="transpmethod_{$sequence}">
            {$lang->makesuretochooseandclick}</div>
        {$lang->roundtrip}<input type="radio" value="1" name="segment[{$sequence}][transp]" id="transp_lookuptransps_{$sequence}"/>
        {$lang->oneway}<input type="radio" value="2" name="segment[{$sequence}][transp]" id="transp_lookuptransps_{$sequence}"/><br/><br/>
       <!-- <input type="button" class="button" value="{$lang->lookuptransps}" id="lookuptransps_{$sequence}"/>-->

        <!--able width="100%" cellspacing="0" cellpadding="0" style="margin-left: 8px;" class="datatable">
              <tbody id="othertranspcat_{$sequence}_tbody">-->
        <div id="content_suggestedtransploader_{$sequence}"></div>
        <div id="content_suggestedtransp_{$sequence}">{$transsegments_output[suggested]}</div>
    </div>
    <div style="display: block;border:1px solid #FCEFA1; padding:15px;margin-top:10px;">
        <h2 class="subtitle">{$lang->selectothertransportations}</h2>
        <div class = "ui-state-highlight ui-corner-all" style = "padding: 6px; font-weight: bold;margin-top:10px;">{$lang->availableflightsnoticemessage}</div>
        <input type="checkbox" id="checkbox_show_othertransps_{$sequence}" style="margin-left:12px;margin-top:15px;margin-bottom:15px;" {$checked[othertranspssection]}/>{$lang->other} {$lang->transportations}
        <div id="show_othertransps_{$sequence}" style="{$display['othertranspssection']}">
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
</div>

<div style="display: inline-block;width:50%;vertical-align: top;margin-bottom: 50px;margin-top: 100px;">
    <input type="hidden" id="save_section_{$sequence}_2_input" value="0" name="segment[{$sequence}][savesection][section2]"/>
    <a id="save_section_{$sequence}_2" style="width:30%;float:right;padding:10px;text-align: center;background-color:#690;font-weight:bold;font-size:15px;">Save & Proceed
        <img src="{$core->settings['rootdir']}/images/chevron-down.png"/>
    </a>
</div>
<div style="display: inline-block;width: 30%;vertical-align: top;margin-top: 100px;margin-left: 75px;" id="sectionresults_2"></div>

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
<div>
    <div style="display: inline-block;width:50%;vertical-align: top;margin-bottom: 50px;margin-top: 100px;">
        <input type="hidden" id="save_section_{$sequence}_3_input" value="0" name="segment[{$sequence}][savesection][section3]"/>
        <a id="save_section_{$sequence}_3" style="width:30%;float:right;padding:10px;text-align: center;background-color:#690;font-weight:bold;font-size:15px;">Save & Proceed
            <img src="{$core->settings['rootdir']}/images/chevron-down.png"/>
        </a>
    </div>
    <div style="display: inline-block;width: 30%;vertical-align: top;margin-top: 100px;margin-left: 75px;" id="sectionresults_3"></div>
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
<div style="display: inline-block;width:50%;vertical-align: top;margin-bottom: 50px;margin-top: 100px;">
    <input type="hidden" id="save_section_{$sequence}_4_input" value="0" name="segment[{$sequence}][savesection][section4]"/>
    <a id="save_section_{$sequence}_4" style="width:30%;float:right;padding:10px;text-align: center;background-color:#690;font-weight:bold;font-size:15px;">Save & Proceed
        <img src="{$core->settings['rootdir']}/images/chevron-down.png"/>
    </a>
</div>
<div style="display: inline-block;width: 30%;vertical-align: top;margin-top: 100px;margin-left: 75px;" id="sectionresults_4"></div>

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


    <div style="display: inline-block;width:50%;vertical-align: top;margin-bottom: 50px;margin-top: 100px;">
        <input type="hidden" id="save_section_{$sequence}_5_input" value="0" name="segment[{$sequence}][savesection][section5]"/>
        <a id="save_section_{$sequence}_5" style="width:30%;float:right;padding:10px;text-align: center;background-color:#690;font-weight:bold;font-size:15px;">Save
            <img src="{$core->settings['rootdir']}/images/chevron-down.png"/>
        </a>
    </div>
    <div style="display: inline-block;width: 30%;vertical-align: top;margin-top: 100px;margin-left: 75px;" id="sectionresults_5"></div>

</div>