<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->employeeslist}</title>
{$headerinc}
</head>

<body>
{$header}
<tr>
<td class="menuContainer" align="left">
<ul id="mainmenu">
	<li><span><a href="users.php?action=profile">{$lang->viewyourprofile}</a></span></li>
    <li><span><a href="users.php?action=profile&amp;do=edit">{$lang->manageyouraccount}</a></span></li>
</ul>
</td>
<td class="contentContainer">
<h3>{$lang->employeeslist}</h3>
<table class="datatable">
<thead>
<tr>
<th>{$lang->fullname} <a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
<th>{$lang->displayname} <a href="{$sort_url}&amp;sortby=displayname&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=displayname&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
<th>{$lang->mainaffiliate} <a href="{$sort_url}&amp;sortby=mainaffiliate&&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=mainaffiliate&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
<th>{$lang->position}</th>
<th>{$lang->reportsto} <a href="{$sort_url}&amp;sortby=supervisor&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=supervisor&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
<th width="6%">&nbsp;</th>
</tr>
</thead>
<tbody>
    {$usersrows}    
</tbody>
</table>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
<div style="width:50%; float:right; margin-top:0px; text-align:right;" class="smalltext"><form method='post' action='users.php?action=userslist'><select id="filterby" name="filterby">
<option value="displayname">{$lang->displayname}</option>
<option value="firstname">{$lang->fullname}</option>
</select> <input type="text" name="filtervalue" id="filtervalue"> <input type="submit" class="button" value="{$lang->filter}"></form></div>
</td>
</tr>
{$footer}
</body>
</html>