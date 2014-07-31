<table cellpadding="0" cellspacing="0" width="100%" style="vertical-align:top;">
    <tr><td colspan="2" class="thead">{$visitdetails[comments][$val][suppliername]}<input type="hidden" value="{$visitdetails[comments][$val][suppliername]}" name="comments[$val][suppliername]"></td></tr>
    <tr>
        <td width="30%">{$lang->productsdiscussed}</td>
        <td><textarea cols="50" rows="3" name="comments[$val][productsDiscussed]" id="productsDiscussed_{$val}" class="texteditormin">{$visitdetails[comments][$val][productsDiscussed]}</textarea></td>
    </tr>
    <tr>
        <td width="30%">{$lang->offersmade}</td>
        <td><textarea cols="50" rows="3" name="comments[$val][offersMade]" id="offersMade_{$val}" class="texteditormin">{$visitdetails[comments][$val][offersMade]}</textarea></td>
    </tr>
    <tr>
        <td width="30%">{$lang->newprojectcustomer}</td>
        <td><textarea cols="50" rows="3" name="comments[$val][newProjCustomer]" id="newProjCustomer_{$val}" class="texteditormin">{$visitdetails[comments][$val][newProjCustomer]}</textarea></td>
    </tr>
    <!--<tr>
        <td>{$lang->opportunitiescustomer}</td>
        <td><textarea cols="50" rows="3" name="comments[$val][opportunitiesCustomer]" id="opportunitiesCustomer_{$val}" class="texteditormin">{$visitdetails[comments][$val][opportunitiesCustomer]}</textarea></td>
    </tr>-->
    <tr>
        <td>{$lang->conclusions}</td>
        <td><textarea cols="50" rows="3" name="comments[$val][conclusions]" id="conclusions_{$val}" class="texteditormin">{$visitdetails[comments][$val][conclusions]}</textarea></td>
    </tr>
    <tr>
        <td>{$lang->followup}</td>
        <td><textarea cols="50" rows="3" name="comments[$val][followUp]" id="followUp_{$val}" class="texteditormin">{$visitdetails[comments][$val][followUp]}</textarea></td>
    </tr>
    <tr>
        <td>{$lang->markastask}</td>
        <td>
            <input name="comments[$val][markTask]" id="markTask_{$val}" type="checkbox" value="1" onClick="$('#taskdate_{$val}').toggle()" title="{$lang->marktask_tip}">
            <span id="taskdate_{$val}" style="display:none;">
                <input type="text" value="{$marktask_date_output}" autocomplete="off" id="pickDate_task{$val}" />
                <input type="hidden" value="{$marktask_date}" id="altpickDate_task{$val}" name="comments[$val][taskDate]"/>
            </span>
        </td>
    </tr>
</table>