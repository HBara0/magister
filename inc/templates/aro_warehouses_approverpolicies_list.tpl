<h1>{$lang->approvalchainspolicieslist}</h1>
<form action='$_SERVER[REQUEST_URI]' method="post">
    <table class="datatable">
        <thead>
            <tr>
                <th style="width:25%">{$lang->affiliate} </th>
                <th style="width:22%">{$lang->effromdate} <a href="{$sort_url}&amp;sortby=effectiveFrom&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=effectiveFrom&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th style="width:22%;">{$lang->eftodate}<a href="{$sort_url}&amp;sortby=effectiveTo&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=effectiveTo&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th style="width:25%">{$lang->purchasetype} </th>
                <th>&nbsp;</th>
            </tr>
            {$filters_row}
        </thead>
        <tbody class="datatable-striped">
            {$policies_approverpolicieslistrow}
        </tbody>

    </table>
</form>