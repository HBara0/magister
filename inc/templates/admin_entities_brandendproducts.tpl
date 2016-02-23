<h1>{$lang->managebrandsendproducts}</h1>
<table class="datatable">
    <div style="float:right;" class="subtitle"> <a href="#" id="showpopup_createbrand" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->createbrandendproduct}</a></div>
    <thead>
        <tr>
            <th style="width:30%;">{$lang->endproduct}</th>
            <th style="width:30%;">{$lang->description}</th>
            <th style="width:30%;">{$lang->characteristic}</th>
            <th style="width:30%;">{$lang->brand}</th>
            <th style="width:10%;"></th>
        </tr>
        <tr>
            {$filters_row}
        </tr>
    </thead>
    <tbody>
        {$brandproducts_list}
    </tbody>
</table>

{$popup_createbrand}
