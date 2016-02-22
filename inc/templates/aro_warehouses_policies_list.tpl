<div class="container"><h1>{$lang->warehousespolicieslist}</h1>
    <form action='$_SERVER[REQUEST_URI]' method="post">
        <table class="datatable">
            <thead>
                <tr>
                    <th style="width:33%">{$lang->warehouse} <a href="{$sort_url}&amp;sortby=warehouse&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=warehouse&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                    <th style="width:30%">{$lang->effromdate} <a href="{$sort_url}&amp;sortby=effectiveFrom&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=effectiveFrom&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                    <th style="width:30%;">{$lang->eftodate}<a href="{$sort_url}&amp;sortby=effectiveTo&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=effectiveTo&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            {$tools}

                </tr>
                {$filters_row}
            </thead>
            <tbody class="datatable-striped">
                {$policies_listrow}
            </tbody>

        </table>
    </form>
</div>