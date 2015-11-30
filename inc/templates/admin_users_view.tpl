<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->manageusers}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->manageusers}</h1>
            <form method='post' action='$_SERVER[REQUEST_URI]'>
                <table class="datatable">
                    <thead>
                        <tr>
                            <th>#</th><th>{$lang->username} <a href="{$sort_url}&amp;sortby=username&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=username&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th><th>{$lang->email}  <a href="{$sort_url}&amp;sortby=email&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=email&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th><th>{$lang->usergroup}  <a href="{$sort_url}&amp;sortby=gid&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=gid&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th><th>{$lang->affiliates}</th><th>{$lang->lastvisit}  <a href="{$sort_url}&amp;sortby=lastVisit&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=lastVisit&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th><th></th>
                        </tr>
                        <tr>
                            {$filters_row}
                        </tr>
                    </thead>
                    <tbody>
                        {$userslist}
                    </tbody>
                </table>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>