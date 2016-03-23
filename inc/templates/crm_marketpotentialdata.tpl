
<script src="{$core->settings[rootdir]}/js/profiles_marketintelligence.min.js" type="text/javascript"></script>
<h1>{$lang->potentialmarketdata}</h1>
<div style="float: right">{$addmarketdata_link}</div>
<table class="datatable_basic table table-bordered row-border hover order-column" data-totalcolumns="14,15,16" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>{$lang->affiliate}</th>
            <th>{$lang->customer}</th>
            <th>{$lang->country}</th>
            <th>{$lang->product}</th>
            <th>{$lang->supplier}</th>
            <th>{$lang->chemicalsubs}</th>
            <th>{$lang->basicingredients}</th>
            <th>{$lang->functionalproperty}</th>
            <th>{$lang->application}</th>
            <th>{$lang->segment}</th>
            <th>{$lang->brand}</th>
            <th>{$lang->brandendproduct}</th>
            <th>{$lang->endproducttype}</th>
            <th>{$lang->characteristic}</th>
            <th>{$lang->potentialqty}</th>
            <th>{$lang->marketshare}</th>
            <th>{$lang->price}</th>
            <th>{$lang->date}</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>{$lang->affiliate}</th>
            <th>{$lang->customer}</th>
            <th>{$lang->country}</th>
            <th>{$lang->product}</th>
            <th>{$lang->supplier}</th>
            <th>{$lang->chemicalsubs}</th>
            <th>{$lang->basicingredients}</th>
            <th>{$lang->functionalproperty}</th>
            <th>{$lang->application}</th>
            <th>{$lang->segment}</th>
            <th>{$lang->brand}</th>
            <th>{$lang->brandendproduct}</th>
            <th>{$lang->endproducttype}</th>
            <th>{$lang->characteristic}</th>
            <th>{$lang->potentialqty}</th>
            <th>{$lang->marketshare}</th>
            <th>{$lang->price}</th>
            <th>{$lang->date}</th>
            <th>&nbsp;</th>
        </tr>
    </tfoot>
    <tbody>
        {$marketpotdata_list}
    </tbody>
</table>

{$popup_createbrand}
{$popup_marketdata}
