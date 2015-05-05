<div id="prof_mkd_product" style="display:block;">
    <div style="width: 30%; display: inline-block; vertical-align: top; margin-bottom: 10px;">{$lang->product}</div><div style="width: 60%; display: inline-block;">
        <input type="text" size="25" name="marketdata[cfpid]" id="chemfunctionproducts_1_autocomplete" size="100" autocomplete="off"  value="{$product->name}"/>
        <input type="hidden" id="chemfunctionproducts_1_id" name="marketdata[cfpid]" value="{$midata->cfpid}"/>
        <input type="hidden" value="1" id="userproducts" name="userproducts" />
        <div id="searchQuickResults_1" class="searchQuickResults" style="display:none;"></div>
        <br />
    </div>
</div>
<div>{$lang->select}
    <a href="#popup_profilesmarketdata" onclick="$('#prof_mkd_product').show();
            $('#prof_mkd_chemsubfield').hide();
            $('#prof_mkd_basicingredients').hide()">
        product </a><br/> Or a
    <a href = "#popup_profilesmarketdata" onclick = "$('#prof_mkd_chemsubfield').show();
            $('#prof_mkd_product').hide();
            $('#prof_mkd_basicingredients').hide()"> chemical substance </a>
    <br/>
    <a href="#popup_profilesmarketdata" onclick="$('#prof_mkd_basicingredients').show();
            $('#prof_mkd_product').hide();
            $('#prof_mkd_chemsubfield').hide();">{$lang->usebasicingredient}</a></div>
