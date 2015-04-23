<tr class="{$altrow}"  style="border:1px gainsboro solid;width:100%" id="{$cprowid}">
    <td>
        <table style="width:100%;">
            <tbody  style="width:100%;">


                <tr>
                    <td>{$lang->productname}</td> <td>{$lang->potentialqty} </td> <td>{$lang->percofsuccess} </td>
                </tr>
                <tr>
                    <td>
                        <input type='text' name="marketreport[customers][{$crowid}][products][{$cprowid}][productname]" id="product_{$crowid}{$cprowid}_autocomplete" value="{$project[productname]}" autocomplete='off' />
                        <input type="hidden" value="{$project[pid]}" id="product_{$crowid}{$cprowid}_id" name="marketreport[customers][{$crowid}][products][{$cprowid}][pid]" />
                        <div id="searchQuickResults_{$crowid}{$cprowid}" class="searchQuickResults" style="display:none;"></div>
                        <input type="hidden" name="marketreport[customers][{$crowid}][products][{$cprowid}][inputChecksum]" value="{$inputchecksum[custproduct]}"/>
                        <br />
                    </td>
                    <td>
                        <input type="number" step="any" name="marketreport[customers][{$crowid}][products][{$cprowid}][potentialQty]" value="{$project[potentialQty]}"/>
                    </td>
                    <td>
                        <input type="number" step="any" name="marketreport[customers][{$crowid}][products][{$cprowid}][successPerc]" value="{$project[successPerc]}"/>
                    </td>
                </tr>
                <tr><td colspan="3" class="subtitle">{$lang->followup}</td></tr>
                <tr>
                    <td>{$lang->who}</td> <td>{$lang->what} </td> <td>{$lang->when}</td>
                </tr>
                <tr>
                    <td>
                        <textarea name="marketreport[customers][{$crowid}][products][{$cprowid}][who]" cols="18" rows="3">{$project[who]}</textarea>
                    </td>
                    <td>
                        <textarea name="marketreport[customers][{$crowid}][products][{$cprowid}][what]" cols="18" rows="3">{$project[what]}</textarea>
                    </td>
                    <td>
                        <textarea name="marketreport[customers][{$crowid}][products][{$cprowid}][when]" cols="18" rows="3">{$project[whenn]}</textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>