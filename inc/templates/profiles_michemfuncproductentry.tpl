
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
            <div style="width: 30%; display: inline-block; vertical-align: top; margin-bottom: 10px;">{$lang->product}</div><div style="width: 60%; display: inline-block;">
                <input type="text" size="25" name="marketdata[cfpid]" id="chemfunctionproducts_1_autocomplete" size="100" autocomplete="off"  value="{$product->name}"/>
                <input type="hidden" id="chemfunctionproducts_1_id" name="marketdata[cfpid]" value="{$midata->cfpid}"/>
                <input type="hidden" value="1" id="userproducts" name="userproducts" />
                <div id="searchQuickResults_1" class="searchQuickResults" style="display:none;"></div>
                <br />
            </div>
        </div>

