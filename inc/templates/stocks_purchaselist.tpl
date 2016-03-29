<h1>{$lang->purchaselisttitle}</h1>
<form method='post' action='$_SERVER[REQUEST_URI]'>
    <table class="datatable">
        <thead>
            <tr>
                <th style="width:25%;">{$lang->pid}
                    <a href="{$sort_url}&amp;sortby=products.name&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=products.name&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
                </th>
                <th style="width:18%;">{$lang->spid}
                    <a href="{$sort_url}&amp;sortby=spid&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=spid&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
                </th>
                <th style="width:16%;">{$lang->affid}
                    <a href="{$sort_url}&amp;sortby=affid&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=affid&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
                </th>
                <th style="width:15%;">{$lang->date}
                    <a href="{$sort_url}&amp;sortby=date&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=date&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
                </th>
                <th style="width:18%;">{$lang->amount}
                    <a href="{$sort_url}&amp;sortby=amount&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=amount&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
                </th>
                <th style="width:8%;">&nbsp;</th>
            </tr>
            {$filters_row}
        </thead>
    </table>
</form>
<table class="datatable">
    <thead>
        <tr class="dummytrow">
            <th style="width:25%;"></th>
            <th style="width:18%;"></th>
            <th style="width:16%;"></th>
            <th style="width:15%;"></th>
            <th style="width:18%;"></th>
            <th style="width:8%;"></th>
        </tr>
    </thead>
    <tbody>
        {$purchases_list}
    </tbody>
</table>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
