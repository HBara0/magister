<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->warehouseslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}

    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->warehouseslist}</h3>
            <form action='index.php?module=cms/eventlist' method="post">
                <table class="datatable" width="100%">
                    <thead>
                        <tr>
                            <th width="22.5%">{$lang->affiliate} <a href="{$sort_url}&amp;sortby=affid&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=affid&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th width="22.5%">{$lang->name} <a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th width="22.5%">{$lang->city} <a href="{$sort_url}&amp;sortby=ciid&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=ciid&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th width="22.5%">{$lang->country} <a href="{$sort_url}&amp;sortby=coid&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=coid&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th width="7%">{$lang->isactive}</th>
                            <th width="3%"></th>
                        </tr>
                        {$filters_row}
                    </thead>
                </table>
                <table class="datatable" width="100%">

                    <tbody>
                        {$warehouse_rows}
                    </tbody>
                </table>
            </form>
            <div style="width:40%; float:left; margin-top:20px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
                </form></div>

        </td>
    </tr>

    {$footer}
</body>

</html>