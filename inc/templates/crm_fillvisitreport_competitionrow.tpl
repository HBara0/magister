<tr id="{$rownumber}">
    <td><input type="text" name="competitorName_{$rownumber}" id="competitorName_{$rownumber}" value='{$competitiondetails[$rownumber][competitorName]}'/></td>
    <td><input type='text' id='product_{$rownumber}_autocomplete' autocomplete="off" value="{$competitiondetails[$rownumber][productname]}"/><input type='hidden' id='product_{$rownumber}_id' name='pid_{$rownumber}' value="{$competitiondetails[$rownumber][pid]}" /><div id='searchQuickResults_product_{$rownumber}' class='searchQuickResults' style='display:none;'></div></td>
    <td><select name="aggressionLevel_{$rownumber}" id="aggressionLevel_{$rownumber}">
            <option value="1"{$competitiondetails_aggressionLevel_selected[$rownumber][1]}>{$lang->extremeaggression}</option>
            <option value="2"{$competitiondetails_aggressionLevel_selected[$rownumber][2]}>{$lang->highaggression}</option>
            <option value="3"{$competitiondetails_aggressionLevel_selected[$rownumber][3]}>{$lang->mildaggression}</option>
        </select>
    </td>
    <td><input type="text" name="recentPrice_{$rownumber}" id="recentPrice_{$rownumber}" accept="numeric" size="4" value="{$competitiondetails[$rownumber][recentPrice]}"/></td>
    <td><input type="text" name="ourRecentPrice_{$rownumber}" id="ourRecentPrice_{$rownumber}" accept="numeric" size="4" value="{$competitiondetails[$rownumber][ourRecentPrice]}"/></td>
    <td><select name="supplyStatus_{$rownumber}" id="supplyStatus_{$rownumber}">
            <option value="1"{$competitiondetails_supplyStatus_selected[$rownumber][1]}>{$lang->regular}</option>
            <option value="2"{$competitiondetails_supplyStatus_selected[$rownumber][2]}>{$lang->onspotbasis}</option>
            <option value="3"{$competitiondetails_supplyStatus_selected[$rownumber][3]}>{$lang->usedto}</option>
            <option value="4"{$competitiondetails_supplyStatus_selected[$rownumber][4]}>{$lang->never}</option>
        </select>
    </td>
    <td><select name="availabilityIssues_{$rownumber}" id="availabilityIssues_{$rownumber}">
            <option value="1"{$competitiondetails_availabilityIssues_selected[$rownumber][1]}>{$lang->available}</option>
            <option value="2"{$competitiondetails_availabilityIssues_selected[$rownumber][2]}>{$lang->underspotshortage}</option>
            <option value="3"{$competitiondetails_availabilityIssues_selected[$rownumber][3]}>{$lang->usuallyundershortage}</option>
        </select>
    </td>
</tr> 