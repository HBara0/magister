<!--   <ul id="mainmenu">
               <li><span><a href="users.php?action=profile">{$lang->viewyourprofile}</a></span></li>
               <li><span><a href="users.php?action=profile&amp;do=edit">{$lang->manageyouraccount}</a></span></li>
           </ul>-->
<h1>{$lang->employeeslist}</h1>
<form method='post' action='$_SERVER[REQUEST_URI]'>
    <table class="datatable">
        <thead>
            <tr>
                <th style="width:22%;">{$lang->fullname} <a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                <th style="width:16%;">{$lang->displayname} <a href="{$sort_url}&amp;sortby=displayname&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=displayname&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                <th style="width:14%;">{$lang->mainaffiliate} <a href="{$sort_url}&amp;sortby=mainaffiliate&&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=mainaffiliate&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                <th style="width:28%;">{$lang->position}</th>
                <th style="width:14%;">{$lang->reportsto} <a href="{$sort_url}&amp;sortby=reportsToName&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=reportsToName&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                <th style="width:6%;"><a href="{$change_view_url}"><img src="./images/icons/{$change_view_icon}" alt="{$lang->changeview}" border="0"/></a>
                    <a style="cursor:pointer;" href="{$core->settings['rootdir']}/users.php?action=downloadallempvcard"><img src="./images/download_vcard.png" title="{$lang->downloademployeescards}" alt="{$lang->downloademployeescards}" /></a>
                </th>
            </tr>
            {$filters_row}
        </thead>
    </table>
</form>
<table class="datatable">
    <thead><tr class="dummytrow"><th style="width:22%;"></th><th style="width:14%;"></th><th style="width:16%;"></th><th style="width:28%;"></th><th style="width:14%;"></th><th style="width:6%;"></th></tr></thead>
    <tbody>
        {$usersrows}
    </tbody>
</table>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
