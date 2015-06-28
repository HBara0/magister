<tr class="{$altrow}"  style="border:1px gainsboro solid;width:100%" id="{$cprowid}">
    <td>
        <table style="width:100%;">
            <tbody  style="width:100%;">


                <tr>
                    <td>{$lang->productname}</td> <td>{$lang->potentialqty} </td> <td>{$lang->percofsuccess} </td>
                </tr>
                <tr>
                    <td>
                        <input type='text' id="product_{$segment[psid]}{$crowid}{$cprowid}_cust_autocomplete" value="{$customerproduct[productName]}" autocomplete='off' />
                        <input type="hidden" value="{$customerproduct[pid]}" id="product_{$segment[psid]}{$crowid}{$cprowid}_cust_id" name="marketreport[{$segment[psid]}][customers][{$crowid}][products][{$cprowid}][pid]" />
                        <div id="searchQuickResults_product_{$segment[psid]}{$crowid}{$cprowid}_cust" class="searchQuickResults" style="display:none;"></div>
                        <input type="hidden" name="marketreport[{$segment[psid]}][customers][{$crowid}][products][{$cprowid}][inputChecksum]" value="{$inputchecksum[custproduct]}"/>
                        <br />
                    </td>
                    <td>
                        <input type="number" step="any" name="marketreport[{$segment[psid]}][customers][{$crowid}][products][{$cprowid}][potentialQty]" value="{$customerproduct[potentialQty]}"/>
                    </td>
                    <td>
                        <input type="number" step="any" name="marketreport[{$segment[psid]}][customers][{$crowid}][products][{$cprowid}][successPerc]" value="{$customerproduct[successPerc]}"/>
                    </td>
                </tr>
                <tr><td colspan="3" class="subtitle">{$lang->followup}</td></tr>
                <tr>
                    <td>{$lang->who}</td> <td>{$lang->what} </td> <td>{$lang->when}</td>
                </tr>
                <tr>
                    <td>
                        <textarea name="marketreport[{$segment[psid]}][customers][{$crowid}][products][{$cprowid}][who]" cols="18" rows="3">{$customerproduct[who]}</textarea>
                    </td>
                    <td>
                        <textarea name="marketreport[{$segment[psid]}][customers][{$crowid}][products][{$cprowid}][what]" cols="18" rows="3">{$customerproduct[what]}</textarea>
                    </td>
                    <td>


                        <input type="text" id="pickDate_to_{$segment[psid]}{$crowid}{$cprowid}" autocomplete="off" tabindex="2" value="{$customerproduct[when_formatted]}"/>
                        <input type="hidden" name="marketreport[{$segment[psid]}][customers][{$crowid}][products][{$cprowid}][whenn]" id="altpickDate_to_{$segment[psid]}{$crowid}{$cprowid}" value="{$customerproduct[when_output]}"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>