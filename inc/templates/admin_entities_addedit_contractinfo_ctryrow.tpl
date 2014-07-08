<tr>
    <td width="30%"><input name="coveredcountry[{$country->coid}][coid]" type="checkbox"{$checked[coid]} value="{$country->coid}"><input type="hidden" name="coveredcountry[{$country->coid}][eccid]" value="{$contracted_countries[$country->coid]->eccid}"/> {$country->displayname}</td>
    <td width="5%"><input name="coveredcountry[{$country->coid}][isExclusive]" id="coveredcountry_{$country->coid}_isExclusive" type="checkbox"{$checked[isExclusive]} value="1"></td>
    <td width="5%">
        <select name="coveredcountry[{$country->coid}][exclusivity]" onchange="$('#coveredcountry_{$country->coid}_isExclusive').prop('checked', true);">
            <option value="" selected="selected"></option>
            <option value="contractual"{$selected[exclusivity][contractual]}>{$lang->contractual}</option>
            <option value="LOI"{$selected[exclusivity][LOI]}>{$lang->LOI}</option>
            <option value="verbal"{$selected[exclusivity][verbal]}>{$lang->verbal}</option>
        </select>
    </td>
    <td width="1%"><input name="coveredcountry[{$country->coid}][isAgent]" type="checkbox"{$checked[isAgent]} value="1"></td>
    <td width="1%"><input name="coveredcountry[{$country->coid}][isDistributor]" type="checkbox"{$checked[isDistributor]} value="1"></td>
    <td width="1%"><input name="coveredcountry[{$country->coid}][selectiveProducts]" type="checkbox"{$checked[selectiveProducts]} value="1"></td>
<tr>