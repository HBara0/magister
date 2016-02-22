<h1>{$lang->currentholidays}</h1>
<table class="datatable">
    <thead>
        <tr>
            <th style="width:30%;">{$lang->name}
                <a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
            </th>
            <th style="width:25%;">{$lang->month}
                <a href="{$sort_url}&amp;sortby=month&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=month&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
            </th>
            <th style="width:25%;">{$lang->day}
                <a href="{$sort_url}&amp;sortby=day&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=day&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
            </th>
            <th style="width:20%;">{$lang->days}
                <a href="{$sort_url}&amp;sortby=numDays&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=numDays&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
            </th>
        </tr>
    </thead>
    <tbody>
        {$holidays_list}
    </tbody>
</table>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
