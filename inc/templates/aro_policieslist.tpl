<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->aropolicieslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr> {$menu}
        <td class="contentContainer"><h1>{$lang->aropolicieslist}</h1>
            <form action='$_SERVER[REQUEST_URI]' method="post">
                <table class="datatable">
                    <thead>
                        <tr>
                            <th width="20%">{$lang->affiliate} <a href="{$sort_url}&amp;sortby=affid&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=affid&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="20%">{$lang->country} <a href="{$sort_url}&amp;sortby=coid&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=coid&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="20%">{$lang->orderpurchasetype} <a href="{$sort_url}&amp;sortby=purchaseType&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=purchaseType&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="16%">{$lang->effromdate} <a href="{$sort_url}&amp;sortby=effectiveFrom&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=effectiveFrom&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="16%;">{$lang->eftodate}<a href="{$sort_url}&amp;sortby=effectiveTo&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=effectiveTo&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="8%">{$lang->isactive}</th>
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

        </td>
    </tr>
</body>
</html>