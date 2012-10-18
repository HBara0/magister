<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->holidayslist}</title>
{$headerinc}
</head>
<body>
{$header}
<tr>
{$menu}
<td class="contentContainer">
<h3>{$lang->holidayslist}</h3>
<table class="datatable">
	<thead>
    	<tr>
        	<th colspan="3" align="left">{$affid_field}</th>
            <th colspan="3" style="text-align:right;"><div id="perform_hr/holidayslist_Results"><form action="#" method="post" id="perform_hr/holidayslist_Form" name="perform_hr/holidayslist_Form"><input type="hidden" value="{$affid}" name="affidtoinform"><input name="action" value="sendholidays" type="hidden" /><input value="{$lang->sendcurrentholidays}" type="button" id="perform_hr/holidayslist_Button" name="perform_hr/holidayslist_Button" class="button"/></form></div></th>
        </tr>
		<tr>
			<th style="width:25%;">{$lang->name}
				<a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a> 
			</th>
			<th style="width:18%;">{$lang->month}
				<a href="{$sort_url}&amp;sortby=month&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=month&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
			</th>
			<th style="width:16%;">{$lang->day}
				<a href="{$sort_url}&amp;sortby=day&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=day&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
			</th>
			<th style="width:20%;">{$lang->days}
				<a href="{$sort_url}&amp;sortby=numDays&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=numDays&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a>
			</th>
			<th style="width:18%;">{$lang->year}
			</th>
			<th style="width:3%;">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		{$holidays_list}
	</tbody>
</table>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
<div style="width:50%; float:right; margin-top:0px; text-align:right;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>
 <input type="text" name="filtervalue" id="filtervalue"> <input type="submit" class="button" value="{$lang->filter}"></form></div>
</td>
</tr>
{$footer}
</body>
</html>