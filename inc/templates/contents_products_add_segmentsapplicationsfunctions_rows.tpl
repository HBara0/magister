<tr class='{$rowclass}'>
    <td width="1%"> <input name="applicationfunction[safid][]" type="checkbox"{$defaultfunctionchecked[$segmentapp_data[segappfuncs][safid]]}  value="{$segmentapp_data[segappfuncs][safid]}"> </td>
    <td width="16.33%"> {$segmentapp_data[chemicalfunction][title]}</td>
    <td width="3%">   <input type="radio" name="defaultFunction" {$defaultfunctionchecked[$cfpid]}   value="{$segmentapp_data[segappfuncs][safid]}"/></td>
    <td width="16.33%">{$segmentapp_data[application]}</td>
    <td width="16.33%">{$segmentapp_data[segment]}</td>
<tr>