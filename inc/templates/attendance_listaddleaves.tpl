<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->addleaveslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->addleaveslist}</h3>
            <table class="datatable">
                <thead>
                    <tr>
                        <th style="width:15%;">{$lang->employeename} <a href="{$sort_url}&amp;sortby=fullname&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=fullname&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                        <th style="width:15%;">{$lang->days} <a href="{$sort_url}&amp;sortby=days&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=days&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                        <th style="width:15%;">{$lang->date} <a href="{$sort_url}&amp;sortby=date&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=date&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                        <th style="width:25%;">{$lang->justification}</th>
                        <th style="width:15%;">{$lang->daterequested} <a href="{$sort_url}&amp;sortby=requestedOn&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=requestedOn&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                        <th style="width:15%;">{$lang->dateapproved} <a href="{$sort_url}&amp;sortby=approvedOn&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=approvedOn&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                    </tr>
                </thead>
                <tbody>
                    {$addleaves_list}
                </tbody>
            </table>
            <div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='5' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
            <div style="width:50%; float:right; margin-top:0px; text-align:right;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>
                    <input type="hidden" name="filterby" id="filterby" value="displayName"> <input type="text" name="filtervalue" id="filtervalue"> <input type="submit" class="button" value="{$lang->filter}"></form></div>
        </td>
    </tr>
    {$footer}
</body>
</html>