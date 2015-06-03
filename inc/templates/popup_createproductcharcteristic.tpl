<div id="popup_createproductcharcteristic">
    <form action="#" method="post" id="add_products/characteristicslist_Form" name="add_products/characteristicslist_Form">
        <input type="hidden" name="action" value="do_addcharacteristic" />
        <table>
            <tr><td><div>
                        <div style="display:inline-block; width: 15%;"><strong>{$lang->title}</strong></div>
                </td><td>
                    <div style="display:inline-block; width: 75%;">
                        <input name="characteristic[title]" type="text" required="required" value="{$chars[title]}"/>
                        <input name="characteristic[pcid]" type="hidden" value="{$chars[pcid]}">
                    </div>
                </td></tr>
            <tr><td colspan="2"><hr></td></tr>
            <tr><td><div style="display:inline-block; width: 15%;"><strong>{$lang->charvalues}</strong></div></td><td>
                    <div>
                        <table>
                            <tbody id="charvalues_{$pcid}_tbody">
                                {$valchar_output}
                            </tbody>
                    </div>
            <tfoot>
                <tr>
                    <td>
                        <span>
                            <div>
                                <img src="{$core->settings['rootdir']}/images/add.gif" style="cursor: pointer" id="ajaxaddmore_products/characteristicslist_charvalues_{$pcid}" alt="{$lang->add}">{$lang->add}
                                <input name="numrows_values" type="hidden" id="numrows_charvalues" value="{$valcharrowid}">
                                <input type="hidden" name="moduletype_values" id="moduletype_charvalues_{$pcid}" value="manage" >
                            </div>
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
        </td></tr>
        <tr><td>
                <div>
                    <hr />
                    <div><input type='button' id='add_products/characteristicslist_Button' value='{$lang->savecaps}' class='button'/></div>
                    <div style="display:table-row;"> <div style="display:table-cell;"><div id="add_products/characteristicslist_Results"></div></div></div>
                </div>
            </td>
        </tr>
    </form>
</table>
</div>