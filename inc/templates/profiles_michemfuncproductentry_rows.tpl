
<div id="radio_buttons" style="display:{$css[display][radiobuttons]};">
    <br/>
    <radiogroup>
        <input type="radio" id="product_radiobtn" onclick="$('#prof_mkd_product').show();
                $('#basicingredients_1_autocomplete').empty();
                $('#chemfunctionchecmical_0_autocomplete').empty();
                $('#prof_mkd_chemsubfield').hide();
                $('#prof_mkd_basicingredients').hide();
                $('#chem_radiobtn').attr('checked', false);
                $('#basicing_radiobtn').attr('checked', false);">{$lang->product}


        <input type="radio" id="chem_radiobtn" onclick = "$('#prof_mkd_chemsubfield').show();
                $('#chemfunctionproducts_1_autocomplete').empty();
                $('#basicingredients_1_autocomplete').empty();
                $('#prof_mkd_product').hide();
                $('#prof_mkd_basicingredients').hide();
                $('#product_radiobtn').attr('checked', false);
                $('#basicing_radiobtn').attr('checked', false);">{$lang->chemicalsubs}

        <input type="radio" id="basicing_radiobtn" onclick="$('#prof_mkd_basicingredients').show();
                $('#chemfunctionproducts_1_autocomplete').empty();
                $('#chemfunctionchecmical_0_autocomplete').empty();
                $('#prof_mkd_product').hide();
                $('#prof_mkd_chemsubfield').hide();
                $('#product_radiobtn').attr('checked', false);
                $('#chem_radiobtn').attr('checked', false)">{$lang->basicingredients}
        </div>
        <!--<a href="#popup_profilesmarketdata" onclick="$('#prof_mkd_product').show();
                $('#prof_mkd_chemsubfield').hide();
                $('#prof_mkd_basicingredients').hide()" style="color: #91b64f;text-decoration:underline" onmouseover="font - size:14;">
            product </a><br/>
     <a href = "#popup_profilesmarketdata" onclick = "$('#prof_mkd_chemsubfield').show();
                 $('#prof_mkd_product').hide();
                 $('#prof_mkd_basicingredients').hide()" style="color: #91b64f;text-decoration:underline"> chemical substance </a>
         <br/>
        <a style="color: #91b64f;text-decoration:underline" href="#popup_profilesmarketdata" onclick="$('#prof_mkd_basicingredients').show();
                $('#prof_mkd_product').hide();
                $('#prof_mkd_chemsubfield').hide();" >{$lang->usebasicingredient}</a>-->
        </div>
        <br/>

        <div id="prof_mkd_product" style="display:{$css[display][product]};">
            <table  width="100%">
                <tr id="profmkdproduct_{$mkdprod_rowid}">
                    <td style="border-bottom:2px solid #CCC; margin-bottom: 5px;">
                        <table width="100%">
                            {$profiles_minproductentry_rows}
                            <tbody id="profmkdproduct_tbody"  class="{$altrow_class}">
                                {$profiles_minproductentry_row}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">
                                        <img src="./images/add.gif" id="ajaxaddmore_crm/marketpotentialdata_profmkdproduct" alt="{$lang->add}">
                                        <input id="numrows_profmkdproduct_{$mkdprod_rowid}" name="numrows_profmkdproduct{$mkdprod_rowid}" type="hidden" value="{$mkdprod_rowid}">
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
            </table>
        </div>