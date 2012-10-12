<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->listofleaves}</title>
{$headerinc}
<script language="javascript">
$(function() 
{
	$('#moderationtools').change(function() 
	{
		if(sharedFunctions.checkSession() == false) 
		{
			return;	
		}
		
		if($(this).val().length > 0) 
		{
			var formData = $("form[id='moderation_attendance/listleaves_Form']").serialize();
			var url = "index.php?module=attendance/listleaves&action=do_moderation";
			
			sharedFunctions.requestAjax("post", url, formData, "moderation_attendance/listleaves_Results", "moderation_attendance/listleaves_Results");
		}
	});
});
</script>
</head>

<body>
{$header}
<tr>
{$menu}
<td class="contentContainer">
<h3>{$lang->listofleaves}</h3>
<form action="#" method="post" id="moderation_attendance/listleaves_Form" name="moderation_attendance/listleaves_Form" style="margin-bottom: 0px;">
<table class="datatable">
<thead>
<tr>
<th>{$lang->employeename} <a href="{$sort_url}&amp;sortby=employeename&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=employeename&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
<th>{$lang->daterequested} <a href="{$sort_url}&amp;sortby=daterequested&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=daterequested&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
<th>{$lang->fromdate} <a href="{$sort_url}&amp;sortby=fromdate&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=fromdate&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
<th>{$lang->todate} <a href="{$sort_url}&amp;sortby=till&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=till&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
<th>{$lang->leavetype} <a href="{$sort_url}&amp;sortby=type&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=type&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
<th>&nbsp;</th>
<th>&nbsp;</th>
</tr>
</thead>
<tbody>
    {$requestslist}
</tbody>
</table>
</form>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
<div style="text-align: right; width:50%; float:right; margin-top:0px;" class="smalltext">
<a href="index.php?module=attendance/listleaves">{$lang->all}</a> | {$yoursonly_filter} <a href="index.php?module=attendance/listleaves&amp;filterby=isapproved&amp;filtervalue=0">{$lang->unapprovedonly}</a> | <a href="index.php?module=attendance/listleaves&amp;filterby=isapproved&amp;filtervalue=1">{$lang->approvedonly}</a>
</div>
</td>
</tr>
{$footer}
</body>
</html>