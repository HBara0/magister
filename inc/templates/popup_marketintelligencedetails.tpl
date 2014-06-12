<div id="popup_mktintldetails" title="{$lang->detlmrktbox}">
    <table width="100%" class="datatable">
        <tr>
            <td><strong>{$lang->customer}</strong></td>
            <td>{$mkintentry_customer->companyName}</td>
        </tr>
        <tr>
            <td><strong>{$lang->brand}</strong></td>
            <td>{$mkintentry_brand->name}</td>
        </tr>
        <tr>
            <td><strong>{$lang->endproduct}</strong></td>
            <td>{$mkintentry_endproducttype->title}</td>
        </tr>
        <tr>
            <td><strong>{$lang->annualpotential}</strong></td>
            <td>{$mkintentry->potential}</td>
        </tr>
        <tr>
            <td><strong>{$lang->marketshare}</strong></td>
            <td>{$mkintentry->mktSharePerc}</td>
        </tr>
        <tr>
            <td><strong>{$lang->marketshareqty}</strong></td>
            <td>{$mkintentry->mktShareQty}</td>
        </tr>
        <tr>
            <td><strong>{$lang->price}</strong></td>
            <td>{$mkintentry->unitPrice}</td>
        </tr>
        <tr>
            <td><strong>{$lang->comment}</strong></td>
            <td><div style="width:300px; overflow:auto; height:80px; line-height:20px;">{$mkintentry->comments}</div></td>
        </tr>
    </table>
    {$marketintelligencedetail_competitors}
</div>