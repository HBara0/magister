<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->managedoumentsequence}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr> {$menu}
        <td class="contentContainer"><h1>{$lang->doumentsequenceconflist}</h1>
            <form action='$_SERVER[REQUEST_URI]' method="post">
                <table class="datatable">
                    <thead>
                        <tr>
                            <th width="22%">{$lang->affiliate} <a href="{$sort_url}&amp;sortby=affid&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=affid&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="22%">{$lang->purchasetype} <a href="{$sort_url}&amp;sortby=ptid&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=ptid&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="22%">{$lang->effromdate} <a href="{$sort_url}&amp;sortby=effectiveFrom&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=effectiveFrom&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="22%;">{$lang->eftodate}<a href="{$sort_url}&amp;sortby=effectiveTo&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=effectiveTo&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th>&nbsp;</th>
                        </tr>
                        {$filters_row}
                    </thead>
                    <tbody class="datatable-striped">
                        {$documentsequenceconf_rows}
                    </tbody>

                </table>
            </form>

        </td>
    </tr>
</body>
</html>