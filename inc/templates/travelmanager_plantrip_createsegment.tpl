<div style="display:inline-block; width:70%;">
    <table>
        <tr>
            <td width="18%">{$lang->fromdate}*</td>
            <td><input type="text" id="pickDate_from_{$sequence}"  disabled="disabled"   autocomplete="off" tabindex="1" value="{$segment[$sequence][fromDate_output]}" required="required"/>
                <input type="hidden" name="segment[{$sequence}][fromDate]" id="altpickDate_from_{$sequence}"  value="{$segment[$sequence][fromDate_formatted]}" /></td>
            </td>

            <td>{$lang->todate}*</td>
            <td><input type="text" id="pickDate_to_{$sequence}"    autocomplete="off" tabindex="1" value="{$segment[$sequence][toDate_output]}" required="required" />
                <input type="hidden" name="segment[{$sequence}][toDate]" id="altpickDate_to_{$sequence}" value="{$segment[$sequence][toDate_formatted]}"/></td>
        <input type="hidden" name="leaveDate" id="leaveDate_to_{$sequence}" value="{$leave[$sequence][toDate]}"/></td>
       <!-- <td> <span id="numdays_{$sequence}">{$segment[$sequence][numberdays]}</span></td>-->
        </tr>
        <tr>  <td>{$lang->origincity}*</td>
            <td><input type="text"  disabled="disabled" autocomplete="off" tabindex="1"  id="cities_{$sequence}_cache_autocomplete"    value="{$segment[$sequence][origincity][name]}" required="required"/>
                <input type='hidden' id='cities_{$sequence}_cache_id'   name="segment[{$sequence}][originCity]" value="{$segment[$sequence][origincity][ciid]}"/>
            </td>

            <td style="text-align:left;">{$lang->destinationcity}*</td>
            <td><input type="text" {$disabled}  id="destinationcity_{$sequence}_cache_autocomplete" autocomplete="off" tabindex="1" value="{$segment[$sequence][destinationcity][name]}" required="required"/>
                <input type="hidden" id="coid"  value="{$segment[countryleave]}" name="coid"/>
                <input type='hidden' id='destinationcity_{$sequence}_cache_id'  name="segment[{$sequence}][destinationCity]" value="{$segment[$sequence][destinationcity][ciid]}"/>
                <input type='hidden' id='destinationcity_{$sequence}_cache_id_output' name="segment[{$sequence}][destinationCity]" value="{$segment[$sequence][destinationcity][ciid]}" disabled/>

            </td>
            <td></td>
        </tr>
        <tr><td>{$lang->purpose}</td><td>{$segment_purposlist}</td></tr>
        <tr><td>{$lang->specifyentityaff}</td><td><input type="checkbox" id="specifyaffent_{$sequence}_check"  value="{$sequence}" {$checked['specifyentcheck']}/></td></tr>
        <tr id="specifyaffent_{$sequence}_block" style="display: none">
            <td>{$lang->affiliates}</td><td>{$affilate_list}</td>
            <td>{$lang->entities}</td>
            <td><input type="text"  id="allentities_{$sequence}_cache_autocomplete" autocomplete="off" tabindex="1" value="{$segment[$sequence][entity][name]}" required="required"/>
                <input type='hidden' id='allentities_{$sequence}_cache_id'  name="segment[{$sequence}][eid]" value="{$segment[$sequence][entity][eid]}"/>
                <input type='hidden' id='allentities_{$sequence}_cache_id_output' name="segment[{$sequence}][eid]" value="{$segment[$sequence][entity][eid]}" disabled/>

            </td>
        </tr>
        <tr><td>{$lang->considerleisuretourism}</td><td><input type="checkbox" name="segment[{$sequence}][isNoneBusiness]" value="1" {$checked['isNoneBusiness']}/></td></tr>
        <tr><td>{$lang->reason}</td><td><textarea name="segment[{$sequence}][reason]"  cols="30" rows="3" required="required">{$segment[$sequence][reason_output]}</textarea><br/></td></tr>
        <tr><td>{$lang->transp}</td>
            <td>{$lang->oneway}<input type="checkbox" value="1" name="segment[{$sequence}][oneway]" id="oneway_lookuptransps_{$sequence}"/>
                {$lang->roundtrip}<input type="checkbox" value="1" name="segment[{$sequence}][roundtrip]" id="roundtrip_lookuptransps_{$sequence}"/><br/><br/>
                <input type="button" class="button" value="{$lang->lookuptransps}" id="lookuptransps_{$sequence}"/>
            </td>
        </tr>
    </table>
    <div id="content_detailsloader_{$sequence}"></div>
    <div id="content_details_{$sequence}">
        {$plansegmentscontent_output}
    </div>
    <div style="display:inline-block;width:25%; margin-left:50px;vertical-align: top;" id="segment_city_{$sequence}">
        <div id="segment_city_loader_{$sequence}"></div>
        {$cityprofile_output}
        {$citybriefings_output}
    </div>
</div>

