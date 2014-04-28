<tr>
    <td colspan="2" class="subtitle">{$segment[title]}<input type="hidden" name="marketreport[{$segment[psid]}][segmenttitle]" value="{$segment[title]}"></td><td style="text-align: right; color:#999999;" class="smalltext">Exclude this segment <input name="marketreport[{$segment[psid]}][exclude]" id="marketreport[{$segment[psid]}][exclude]"   type="checkbox" {$ischecked[$segment[psid]]} value="1" title="{$lang->marketreport_exclude_tip}"></td>
</tr>
<tr><td colspan="3"><strong>{$lang->marktrendcompetition}</strong></td></tr>
<tr>
    <td>

        <textarea class="texteditor" cols="55" rows="5" id="markTrendCompetition{$segment[psid]}" name="marketreport[{$segment[psid]}][markTrendCompetition]" >{$marketreport[$segment[psid]][markTrendCompetition]}</textarea></td>
    <td width="1%"><input type="button" style="height:55px; width:15px;" value="&lt;" id="markTrendCompetition{$segment[psid]}_swap"/></td>
    <td><textarea   cols="55" rows="5" id="markTrendCompetition{$segment[psid]}_last" name="markTrendCompetition_last[{$segment[psid]}]" disabled="disabled" class="disabled">{$last_report[$segment[psid]][markTrendCompetition]}</textarea></td>
</tr>
<tr><td colspan="3"><strong>{$lang->quarterlyhighlights}</strong></td></tr>
<tr>
    <td><textarea class="texteditor" cols="55" rows="5" id="quarterlyHighlights{$segment[psid]}" name="marketreport[{$segment[psid]}][quarterlyHighlights]">{$marketreport[$segment[psid]][quarterlyHighlights]}</textarea></td>
    <td width="1%"><input type="button" style="height:55px; width:15px;" value="&lt;" id="quarterlyHighlights{$segment[psid]}_swap"/></td>
    <td><textarea cols="55" rows="5" id="quarterlyHighlights{$segment[psid]}_last" name="quarterlyHighlights_last[{$segment[psid]}]" disabled="disabled" class="disabled">{$last_report[$segment[psid]][quarterlyHighlights]}</textarea></td>
</tr>
<tr><td colspan="3"><strong>{$lang->devprojectsnewop}</strong></td></tr>
<tr>
    <td><textarea class="texteditor" cols="55" rows="5" id="devProjectsNewOp{$segment[psid]}" name="marketreport[{$segment[psid]}][devProjectsNewOp]">{$marketreport[$segment[psid]][devProjectsNewOp]}</textarea></td>
    <td width="1%"><input type="button" style="height:55px; width:15px;" value="&lt;" id="devProjectsNewOp{$segment[psid]}_swap"/></td>
    <td><textarea cols="55" rows="5" id="devProjectsNewOp{$segment[psid]}_last" name="devProjectsNewOp_last[{$segment[psid]}]" disabled="disabled" class="disabled">{$last_report[$segment[psid]][devProjectsNewOp]}</textarea></td>
</tr>
<tr><td colspan="3"><strong>{$lang->issues}</strong></td></tr>
<tr>
    <td><textarea class="texteditor" cols="55" rows="5" id="issues{$segment[psid]}" name="marketreport[{$segment[psid]}][issues]">{$marketreport[$segment[psid]][issues]}</textarea></td>
    <td width="1%"><input type="button" style="height:55px; width:15px;" value="&lt;" id="issues{$segment[psid]}_swap"/></td>
    <td><textarea cols="55" rows="5" id="issues{$segment[psid]}_last" name="issues_last[{$segment[psid]}]" disabled="disabled" class="disabled">{$last_report[$segment[psid]][issues]}</textarea></td>
</tr>
<tr><td colspan="3"><strong>{$lang->actionplan}</strong></td></tr>
<tr>
    <td><textarea class="texteditor" cols="55" rows="5" id="actionPlan{$segment[psid]}" name="marketreport[{$segment[psid]}][actionPlan]">{$marketreport[$segment[psid]][actionPlan]}</textarea></td>
    <td width="1%"><input type="button" style="height:55px; width:15px;" value="&lt;" id="actionPlan{$segment[psid]}_swap"/></td>
    <td><textarea cols="55" rows="5" id="actionPlan{$segment[psid]}_last" name="actionPlan_last[{$segment[psid]}]" disabled="disabled" class="disabled">{$last_report[$segment[psid]][actionPlan]}</textarea></td>
</tr>
<tr><td colspan="3"><strong>{$lang->remarks}</strong></td></tr>
<tr>
    <td><input type="text" id="remarks{$segment[psid]}" name="marketreport[{$segment[psid]}][remarks]" size="55" value="{$marketreport[$segment[psid]][remarks]}"/></td>
    <td width="1%"><input type="button" style="height:55px; width:15px;" value="&lt;" id="remarks{$segment[psid]}_swap"/></td>
    <td><input type="text" id="remarks{$segment[psid]}_last" name="remarks_last[{$segment[psid]}]" size="55" value="{$last_report[$segment[psid]][remarks]}" disabled="disabled" class="disabled" /></td>
</tr>