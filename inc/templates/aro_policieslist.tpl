<div class="container"><h1>{$lang->listpolicies}</h1>
    <form action='$_SERVER[REQUEST_URI]' method="post">
        <table class="datatable">
            <thead>
                <tr>
                    <th width="22%">{$lang->affiliate} <a href="{$sort_url}&amp;sortby=affid&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=affid&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                    <th width="22%">{$lang->orderpurchasetype} <a href="{$sort_url}&amp;sortby=purchaseType&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=purchaseType&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                    <th width="22%">{$lang->effromdate} <a href="{$sort_url}&amp;sortby=effectiveFrom&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=effectiveFrom&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                    <th width="22%;">{$lang->eftodate}<a href="{$sort_url}&amp;sortby=effectiveTo&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=effectiveTo&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                    <th width="7%">{$lang->isactive}</th>
                        {$tools}
                    <th>&nbsp;</th>
                </tr>
                {$filters_row}
            </thead>
            <tbody class="datatable-striped">
                {$aropolicies_rows}
            </tbody>

        </table>
    </form>
</div>