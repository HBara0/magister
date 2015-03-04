<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->aroorderrequest}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr> {$menu}
        <td class="contentContainer"><h1>{$lang->aroorderrequest}</h1>
            <form action='$_SERVER[REQUEST_URI]' method="post">
                <table class="datatable">
                    <thead>
                        <tr>
                            <th width="18%">{$lang->affiliate} <a href="{$sort_url}&amp;sortby=affid&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=affid&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="18%">{$lang->orderpurchasetype} <a href="{$sort_url}&amp;sortby=orderType&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=orderType&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="18%">{$lang->orderreference} <a href="{$sort_url}&amp;sortby=orderReference&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=orderReference&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="18%">{$lang->buyingcurr} <a href="{$sort_url}&amp;sortby=currency&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=currency&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="18%">{$lang->createdon} <a href="{$sort_url}&amp;sortby=currency&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=currency&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                                    {$tools}
                            <th>&nbsp;</th>
                        </tr>
                        {$filters_row}
                    </thead>
                    <tbody class="datatable-striped">
                        {$aroorderrequest_rows}
                    </tbody>

                </table>
            </form>

        </td>
    </tr>
</body>
</html>