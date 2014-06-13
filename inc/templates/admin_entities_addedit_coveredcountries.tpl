<tr>
    <td width="1%"><input name="coveredcountry[{$country->coid}][coid]" type="checkbox"{$checked[coid]} value="{$country->coid}">
    <td  width="20%"> <input type="text"  name="coveredcountry[{$country->coid}][eccid]" value="{$contracted_countries[$country->coid]->eccid}"/>{$countryname}</td>
    <td width="1%"><input name="coveredcountry[{$country->coid}][isExclusive]" type="checkbox" {$checked[isExclusive]} value="1"></td>
    <td width="1%"><input name="coveredcountry[{$country->coid}][selectiveProducts]" type="checkbox"{$checked[selectiveProducts]} value="1"></td>
<tr>