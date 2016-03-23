<h1>{$lang->holidayslist}</h1>
<div style="display:inline-block; width: 45%; margin:0;">{$affid_field}</div>
<div style="display:inline-block; width: 50%; margin:0; text-align:right; float:right;"><div id="perform_hr/holidayslist_Results"><form action="#" method="post" id="perform_hr/holidayslist_Form" name="perform_hr/holidayslist_Form"><input type="hidden" value="{$affid}" name="affidtoinform"><input name="action" value="sendholidays" type="hidden" /><input value="{$lang->sendcurrentholidays}" type="button" id="perform_hr/holidayslist_Button" name="perform_hr/holidayslist_Button" class="button"/></form></div></div>
<form method='post' action='$_SERVER[REQUEST_URI]'>
    <table class="datatable">
        <thead>
            <tr>
                <th style="width:25%;">{$lang->name}
                    <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
                </th>
                <th style="width:18%;">{$lang->month}
                    <a href="{$sort_url}&amp;sortby=month&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=month&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
                </th>
                <th style="width:16%;">{$lang->day}
                    <a href="{$sort_url}&amp;sortby=day&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=day&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
                </th>
                <th style="width:15%;">{$lang->days}
                    <a href="{$sort_url}&amp;sortby=numDays&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=numDays&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
                </th>
                <th style="width:18%;">{$lang->year}
                </th>
                <th style="width:8%;">&nbsp;</th>
            </tr>
            {$filters_row}
        </thead>
    </table>
</form>
<table class="datatable">
    <thead><tr class="dummytrow"><th style="width:25%;"></th><th style="width:18%;"></th><th style="width:16%;"></th><th style="width:15%;"></th><th style="width:18%;"></th><th style="width:8%;"></th></tr></thead>
    <tbody>
        {$holidays_list}
    </tbody>
</table>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
