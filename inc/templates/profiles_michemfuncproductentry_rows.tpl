
<div id="radio_buttons" style="display:{$css[display][radiobuttons]};">
    <br/>
    <script>
        function clearchemicalsubstancefields() {
            $('#chem_radiobtn').attr('checked', false);
            $('#prof_mkd_chemsubfield').hide();
            $('input[id^="chemfunctionchecmical_"][id$=_autocomplete]').attr('value', '');
            $('input[id^="chemfunctionchecmical_"][id$=_id]').attr('value', '0');
        }
        function clearbasicingredientsfields() {
            $('#basicing_radiobtn').attr('checked', false);
            $('#prof_mkd_basicingredients').hide();
            $('input[id^="basicingredients_"][id$=_autocomplete]').attr('value', '');
            $('input[id^="basicingredients_"][id$=_id]').attr('value', '0');
        }
        function clearproductsfields() {
            $('#product_radiobtn').attr('checked', false);
            $('#prof_mkd_product').hide();
            $('input[id^="chemfunctionproducts_"][id$=_autocomplete]').attr('value', '');
            $('input[id^="chemfunctionproducts_"][id$=_id]').attr('value', '0');
        }
    </script>
    <radiogroup>
        <input type="radio" id="product_radiobtn" onclick="$('#prof_mkd_product').show();
                clearchemicalsubstancefields();
                clearbasicingredientsfields();">{$lang->product}
        <input type="radio" id="chem_radiobtn" onclick = "$('#prof_mkd_chemsubfield').show();
                clearproductsfields();
                clearbasicingredientsfields();">
        {$lang->chemicalsubs}

        <input type="radio" id="basicing_radiobtn" onclick="$('#prof_mkd_basicingredients').show();
                clearproductsfields();
                clearchemicalsubstancefields();">{$lang->basicingredients}
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
                                        <img src="./images/add.gif" id="ajaxaddmore_{$module}/{$modulefile}_profmkdproduct" alt="{$lang->add}">
                                        <input id="numrows_profmkdproduct_{$mkdprod_rowid}" name="numrows_profmkdproduct{$mkdprod_rowid}" type="hidden" value="{$mkdprod_rowid}">
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
            </table>
        </div>