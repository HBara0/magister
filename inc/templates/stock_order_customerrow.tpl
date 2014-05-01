<tr id='{$customer_rowid}' class="{$altrow_class}">
    <td>
        <div style="widows:50%; display:inline-block;">
            <input type='text' id="customer_{$customer_rowid}_QSearch" autocomplete='off' value="{$customer[companyName]}" name='customers[$customer_rowid][companyName]'/><div id="searchQuickResults_customer_{$customer_rowid}" class='searchQuickResults' style='display:none;'></div>
            <input type='text' size='3' id="customer_{$customer_rowid}_id_output" disabled="disabled" value="{$customer[eid]}"/></div>
        <input type='hidden' id="customer_{$customer_rowid}_id" name="customers[$customer_rowid][cid]" value="{$customer[eid]}"/></div>
</div>
<div style="widows:40%; display:inline-block; float:right;"> 
    <input type='text' name="customers[$customer_rowid][paymentTermsDays]" id="customerpayments_{$customer_rowid}" tabindex='16' size='7' accept="numeric" value="{$customer[paymentTermsDays]}" autocomplete='off' />
    &nbsp;
    {$customer_payment_terms_from} <div name="customerpayments_{$customer_rowid}_Loading" id="customerpayments_{$customer_rowid}_Loading"></div>
</div>
<br />
<table width="100%">
    <thead>
        <tr class="altrow2"><th>{$lang->product}</th><th>{$lang->firstorderquantity}</th><td>{$lang->firstorderdate}</td><td>{$lang->numsubsequentorders}</td><td>{$lang->quantitysubsequentorders}</td><td>{$lang->intervalsubsequentorders}</td><td>{$lang->expectedquantity}</td></tr>
    </thead>
    <tbody id="customerproducts_{$customer_rowid}_tbody">
        {$customer[productsoutput]}
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><img src="./images/add.gif" id="addmore_customerproducts_{$customer_rowid}" alt="{$lang->add}"></td>
        </tr>
    </tfoot>
</table>
</td>
</tr>