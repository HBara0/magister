<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listasset}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr> {$menu}
        <td class="contentContainer"><h1>{$lang->warehousespolicieslist}</h1>
            <form action='$_SERVER[REQUEST_URI]' method="post">
                <table class="datatable">
                    <thead>
                        <tr>
                            <th style="width:15%">{$lang->warehouse} <a href="{$sort_url}&amp;sortby=warehouse&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=warehouse&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th style="width:25%">{$lang->effromdate} <a href="{$sort_url}&amp;sortby=effromdate&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=effromdate&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th style="width:15%;">{$lang->eftodate}<a href="{$sort_url}&amp;sortby=eftodate&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=eftodate&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                                    {$tools}
                            <th>&nbsp;</th>
                        </tr>
                        {$filters_row}
                    </thead>
                    <tbody class="datatable-striped">
                        {$policies_listrow}
                    </tbody>

                </table>
            </form>

        </td>
    </tr>
</body>
</html>