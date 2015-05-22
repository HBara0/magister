<div style="width: 30%; display: inline-block;">{$lang->customer}</div>
<div style="width: 60%; display: inline-block;">
    <input type="text" required="required" name="eid" id="allcustomertypes_{$customer_rowid}_autocomplete" value="" autocomplete="off" onfocus="clearendroduct()" /><a href="index.php?module=contents/addentities&type=customer" target="_blank" title="{$lang->add}"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0"></a>
    <input type="hidden"  id="allcustomertypes_{$customer_rowid}_id" name="marketdata[cid]" />
    <div id="searchQuickResults_{$customer_rowid}" class="searchQuickResults" style="display:none;"></div>
</div>

<script>
    function clearendroduct() {
        $('#entbrandsproducts_0_autocomplete').val("");
        $('#entbrandsproducts_0_id').val('0');
    }
</script>