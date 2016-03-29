<h1>{$lang->employeeslist}</h1>
{$affid_field}
<form method='post' action='$_SERVER[REQUEST_URI]'>
    <table class="datatable">
        <thead>
            <tr>
                <th style="width:35%;">{$lang->employee}
                    <a href="{$sort_url}&amp;sortby=fullname&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=fullname&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a>
                </th>
                <th style="width:25%;">{$lang->joindate}
                    <a href="{$sort_url}&amp;sortby=joindate&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=joindate&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a>
                </th>
                <th style="width:30%;">{$lang->position}</th>
                <th style="width:10%;">&nbsp;</th>
            </tr>
            {$filters_row}
        </thead>
    </table>
</form>
<table class="datatable">
    <thead><tr class="dummytrow"><th style="width:35%;"></th><th style="width:25%;"></th><th style="width:30%;"></th><th style="width:10%;"></th></tr></thead>
    <tbody>
        {$users_list}
    </tbody>
</table>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
