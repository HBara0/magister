<tr id="contractsection_title"><td colspan="2" class="thead">{$lang->contractualinformation}</td></tr>
<tr>
    <td colspan="2">
        <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
            <table class="datatable" width="100%">
                <thead>
                    <tr>
                        <th style='width: 50%'><input class='inlinefilterfield' type='text' style="width:100%" placeholder="{$lang->countries}"/></th>
                        <th style="text-align: center;">{$lang->isexclusive}</th>
                        <th style="text-align: center;">{$lang->selectiveproducts} </th>
                    </tr>
                </thead>
                <tbody>
                    {$profilepage_contractual_rows}
                </tbody>
            </table>
        </div>
    </td>
</tr>