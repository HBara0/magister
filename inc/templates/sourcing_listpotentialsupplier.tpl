<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->listsurveys}</title>
{$headerinc}
<link href="{$core->settings[rootdir]}/css/rateit.css" rel="stylesheet" type="text/css">
<link href="{$core->settings[rootdir]}/css/rml.css" rel="stylesheet" type="text/css">
<style type="text/css">
span.listitem:hover { border-bottom: #CCCCCC solid thin; }
</style>
<script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>

<script>
{$header_ratingjs}
</script>

</head>
<body>
{$header}
<tr>
{$menu}
<td class="contentContainer">
<h3>{$lang->listpotentialsupplier}</h3>
<form  action='$_SERVER[REQUEST_URI]' method="post">
<table class="datatable" width="100%">
	<thead>
		<tr>
        <th width="19%">{$lang->companyname} <a href="{$sort_url}&amp;sortby=companyName&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=companyName&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
        <th width="19%">{$lang->type}<a href="{$sort_url}&amp;sortby=type&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=type&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
        <th width="19%">{$lang->segments}</th>
        <th width="19%">{$lang->country} <a href="{$sort_url}&amp;sortby=country&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=country&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
        <th width="19%">{$lang->opportunity} <a href="{$sort_url}&amp;sortby=businessPotential&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=businessPotential&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>

     <th width="1%">&nbsp;</th>	       
        </tr>
		{$filters_row}
	</thead>
    <tbody>
  
	{$sourcing_listpotentialsupplier_rows}
    </tbody>
</table>  
  </form>




<div style="width:40%; float:left; margin-top:0px;" class="smalltext">
<form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: 
  <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
</form></div>

</td>
</tr>
{$footer}
</body>
</html>


