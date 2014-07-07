<tr>
    <td width="20%"><input name="coveredcountry[{$country->coid}][coid]" type="checkbox"{$checked[coid]} value="{$country->coid}"><input type="hidden" name="coveredcountry[{$country->coid}][eccid]" value="{$contracted_countries[$country->coid]->eccid}"/> {$country->displayname}
        <span style="display: inline-block; padding: 6px;"> </span>
        <span style="display: inline-block;padding: 6px;">  </span>
    </td>
    <td width="1%"><input name="coveredcountry[{$country->coid}][isExclusive]" type="checkbox"{$checked[isExclusive]} value="1"></td>
    <td width="1%"><select  name="coveredcountry[{$country->coid}][Exclusivity]" >
            <option value="" selected="selected"> {$lang->selectoption}</option>
            <option value="contractual">{$lang->contractual}</option>
            <option value="LOI ">{$lang->LOI}</option>
            <option value="verbal">{$lang->verbal}</option>
        </select>

    </td>
    <td width="1%"><input name="coveredcountry[{$country->coid}][selectiveProducts]" type="checkbox"{$checked[selectiveProducts]} value="1"></td>
    <td width="1%"><input name="coveredcountry[{$country->coid}][Agent]" type="checkbox"{$checked[Agent]} value="1"></td>
    <td width="1%"><input name="coveredcountry[{$country->coid}][Distributor]" type="checkbox"{$checked[Distributor]} value="1"></td>
<tr>