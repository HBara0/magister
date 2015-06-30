<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listavailableapplication}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->listavailableapplication}</h1>
            <div style="float:right;" class="subtitle"><a href="#" id="showpopup_creteapplication" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->create}</a></div>
            <table class="datatable">
                <thead>
                    <tr>
                        <th>{$lang->name} <a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                        <th>{$lang->title} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                        <th>{$lang->segment}  <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                        <th>{$lang->desc} </th>
                    </tr>
                </thead>
                <tbody>
                    {$productsapplications_list}
                </tbody>
                <tr>
                    <td colspan="4">
                        <div style="width:40%; float:left; margin-top:0px;">
                            <form method='post' action='$_SERVER[REQUEST_URI]'>
                                {$lang->perlist}:
                                <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
                            </form>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    {$footer}
    {$dialog_managerapplication}
    {$popup_admin_deleteapplications}
</body>
</html>