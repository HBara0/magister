<tr>
    <td class="subtitle" style="font-size: 18px;">{$segment[title]}<input type="hidden" name="marketreport[{$segment[psid]}][segmenttitle]" value="{$segment[title]}"><br /><span  style="text-align: right; color:#999999;" class="smalltext"><input name="marketreport[{$segment[psid]}][exclude]" id="marketreport[{$segment[psid]}][exclude]" type="checkbox" {$ischecked[$segment[psid]]} value="1" title="{$lang->marketreport_exclude_tip}"> Exclude this segment</span></td>
</tr>
<tr><td>{$criteriaandstars}</td></tr>
<tr><td><strong>{$lang->marktrend}</strong> <small>({$lang->marktrend_note})</small></td></tr>
<tr style="vertical-align: top;">
    <td>
        <textarea class="txteditadv" cols="55" rows="5" id="markTrendCompetition{$segment[psid]}" name="marketreport[{$segment[psid]}][markTrendCompetition]">{$marketreport[$segment[psid]][markTrendCompetition]}</textarea><p style="color: #CCCCCC;"><strong><em>{$lang->prevqnotes}</em></strong><br />{$last_report[$segment[psid]][markTrendCompetition]}</p>
    </td>
</tr>
<tr><td><strong>{$lang->summarysituation}</strong> <small>({$lang->summarysituation_note})</td></tr>
<tr style="vertical-align: top;">
    <td><textarea class="txteditadv" cols="55" rows="5" id="quarterlyHighlights{$segment[psid]}" name="marketreport[{$segment[psid]}][quarterlyHighlights]">{$marketreport[$segment[psid]][quarterlyHighlights]}</textarea><p style="color: #CCCCCC;"><strong><em>{$lang->prevqnotes}</em></strong><br />{$last_report[$segment[psid]][quarterlyHighlights]}</p></td>
</tr>
<tr><td><strong>{$lang->issues}</strong> <small>({$lang->issues_note})</small></td></tr>
<tr style="vertical-align: top;">
    <td><textarea class="txteditadv" cols="55" rows="5" id="issues{$segment[psid]}" name="marketreport[{$segment[psid]}][issues]">{$marketreport[$segment[psid]][issues]}</textarea><p style="color: #CCCCCC;"><strong><em>{$lang->prevqnotes}</em></strong><br />{$last_report[$segment[psid]][issues]}</p></td>
</tr>
<tr><td>&nbsp;</td></tr>