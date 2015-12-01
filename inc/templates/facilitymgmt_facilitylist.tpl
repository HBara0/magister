<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->facilities}</title>
        {$headerinc}
    </head>
    <body>
        {$header}

    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->facilities}</h3>
            <form action='index.php?module=facilityngnt/list' method="post">
                <div style="float:right;" class="subtitle"> <a target="_blank" href="{$core->settings[rootdir]}/index.php?module=facilitymgmt/managefacility" ><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->create}</a></div>
                <table class="datatable" width="100%">
                    <thead>
                        <tr>
                            <th width="22%">{$lang->name} <a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th width="22%">{$lang->aff} <a href="{$sort_url}&amp;sortby=affid&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=affid&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th width="22%">{$lang->type} <a href="{$sort_url}&amp;sortby=type&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=type&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th width="22%">{$lang->within} <a href="{$sort_url}&amp;sortby=within&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=within&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th width="7%">{$lang->isactive}</th>
                            <th width="5%"></th>
                        </tr>
                        <!-- {$filters_row}-->
                    </thead>
                    <tbody>
                        {$facilities_rows}
                    </tbody>
                </table>
            </form>

        </td>
    </tr>

    {$footer}
</body>

</html>