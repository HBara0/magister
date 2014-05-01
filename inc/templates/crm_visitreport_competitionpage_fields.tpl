<table cellpadding="0" cellspacing="0" width="100%">
    <tr><td colspan="2" class="thead">{$competition[comments][$val][suppliername]}</td></tr>
    <tr>

        <td width="30%">Competition Information</td>
        <td><textarea cols="50" rows="3" name="comments[$val][competitionInfo]" id="competitionInfo_{$val}" class="texteditormin">{$competition[comments][$val][competitionInfo]}</textarea></td>
    </tr>
    <tr>
        <td>Rumors on competitors / sources</td>
        <td><textarea cols="50" rows="3" name="comments[$val][rumorsCompetitors]" id="rumorsCompetitors_{$val}" class="texteditormin">{$competition[comments][$val][rumorsCompetitors]}</textarea></td>
    </tr>

    <tr>
        <td>Rumors on Orkila / sources</td>
        <td><textarea cols="50" rows="3" name="comments[$val][ownRumors]" id="ownRumors_{$val}" class="texteditormin">{$competition[comments][$val][ownRumors]}</textarea></td>
    </tr>
</table>