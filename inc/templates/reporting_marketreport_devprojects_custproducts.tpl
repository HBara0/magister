<tr class="{$altrow}"  style="border:1px gainsboro solid;width:100%" id="{$cprowid}">
    <td>
        <table style="width:100%;">
            <tbody  style="width:100%;">


                <tr>
                    <td>{$lang->productname}</td> <td>{$lang->potentialqty} </td> <td>{$lang->percofsuccess} </td>
                </tr>
                <tr>
                    <td>
                        <div id='prof_mkd_prodfield_{$crowid}{$cprowid}' style="display:inline-block;width:65%;margin-top:15px;">
                            <input type="text" size="25" id="chemfunctionproducts_{$crowid}{$cprowid}_autocomplete" autocomplete="off"  value="{$product_name}"/>
                            <input type="hidden" id="chemfunctionproducts_{$crowid}{$cprowid}_id" name="marketreport[customers][{$crowid}][products][{$sprowid}][pid]" value="{}"/>
                            <div id="searchQuickResults_{$crowid}{$cprowid}" class="searchQuickResults" style="display:none;"></div>
                            <input type="hidden" name="marketreport[customers][{$crowid}][products][{$cprowid}][inputChecksum]" value="{$inputchecksum[product]}"/>

                            <br />
                        </div>
                    </td>
                    <td>
                        <input type="number" step="any" name="marketreport[customers][{$crowid}][products][{$cprowid}][potentialQty]" value=""/>
                    </td>
                    <td>
                        <input type="number" step="any" name="marketreport[customers][{$crowid}][products][{$cprowid}][successPerc]" value=""/>
                    </td>
                </tr>
                <tr>
                    <td>{$lang->who}</td> <td>{$lang->what} </td> <td>{$lang->when}</td>
                </tr>
                <tr>
                    <td>
                        <textarea name="marketreport[customers][{$crowid}][products][{$cprowid}][when]" cols="18" rows="3"></textarea>
                    </td>
                    <td>
                        <textarea name="marketreport[customers][{$crowid}][products][{$cprowid}][what]" cols="18" rows="3"></textarea>
                    </td>
                    <td>
                        <textarea name="marketreport[customers][{$crowid}][products][{$cprowid}][where]" cols="18" rows="3"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>