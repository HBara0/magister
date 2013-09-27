<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listchemcialsrequests}</title>
        {$headerinc}
    </head><body>
        {$header}
    <tr> {$menu}
        <td class="contentContainer"><h3>{$lang->listchemcialsrequests}</h3>
            <form  action='$_SERVER[REQUEST_URI]' method="post">
                <table class="datatable">
                    <thead>
                        <tr>
                            <th>{$lang->user} <a href="{$sort_url}&amp;sortby=displayName&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=displayName&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th>{$lang->chemicalname} <a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th>{$lang->requestdescription}</th>
                            <th>{$lang->application} <a href="{$sort_url}&amp;sortby=application&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=application&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th>{$lang->origin}</th>
                            <th>{$lang->time} <a href="{$sort_url}&amp;sortby=timeRequested&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=timeRequested&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$chemcialsrequests_rows}
                    </tbody>

                </table>
            </form>
            <div style="width:40%; float:left; margin-top:0px;">
                <form method='post' action='$_SERVER[REQUEST_URI]'>
                    {$lang->perlist}:
                    <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
                </form>
            </div></td>
    </tr>
    {$footer}
</body>
</html>