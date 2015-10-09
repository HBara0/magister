<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listavailableentities}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->listavailableentities}</h1>
            <form method='post' action='$_SERVER[REQUEST_URI]'>
                <table class="datatable">
                    <thead>
                        <tr>
                            <th>{$lang->id}</th><th>{$lang->companyname} <a href="{$sort_url}&amp;sortby=entityname&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=entityname&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th><th style="width:50%;">{$lang->affiliate}</th><th>{$lang->country} <a href="{$sort_url}&amp;sortby=cname&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=cname&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th><th>&nbsp;</th>
                        </tr>
                        <tr>
                            {$filters_row}
                        </tr>
                    </thead>
                    <tbody>
                        {$entities_list}
                    </tbody>
                </table>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>