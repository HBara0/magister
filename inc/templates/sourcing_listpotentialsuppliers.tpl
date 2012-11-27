<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->listpotentialsupplier}</title></head>
{$headerinc}
<link href="{$core->settings[rootdir]}/css/rateit.css" rel="stylesheet" type="text/css" />
<script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
<script>
{$header_ratingjs}
</script>
</head><body>
{$header}
<tr> {$menu}
	<td class="contentContainer"><h3>{$lang->listpotentialsupplier}</h3>
		<form action='$_SERVER[REQUEST_URI]' method="post">
			<table class="datatable" width="100%">
				<thead>
					<tr>
						<th width="19%">{$lang->companyname} <a href="{$sort_url}&amp;sortby=companyName&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=companyName&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
						<th width="19%">{$lang->type}<a href="{$sort_url}&amp;sortby=type&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=type&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
						<th width="19%">{$lang->segments}</th>
						<th width="19%">{$lang->country} <a href="{$sort_url}&amp;sortby=country&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=country&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
						<th width="19%">{$lang->opportunity} <a href="{$sort_url}&amp;sortby=businessPotential&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=businessPotential&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
						<th width="1%">&nbsp;</th>
					</tr></thead>
				{$filters_row}
				</thead>
				
				<tbody>
				
				{$sourcing_listpotentialsupplier_rows}
				</tbody>
				
			</table>
		</form>
		<div style="width:40%; float:left; margin-top:0px;">
			<form method='post' action='$_SERVER[REQUEST_URI]'>
				{$lang->perlist}:
				<input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
			</form>
		</div>
		<div style="width:40%; float:right;">
			{$lang->chemicalname}
			<select name="filters[chemicalsubstance][]"  multiple="multiple">
				{$chemicals_selectlist_otps}		
			</select>
		</div></td>
</tr>
{$footer}
<div id="popup_requestchemical"  title="{$lang->requestchemical}" >
	<form name='perform_sourcing/listpotentialsupplier_Form' id='perform_sourcing/listpotentialsupplier_Form' method="post">
		<input type="hidden" id="action" name="action" value="do_requestchemical" />
		<div style="display:table;">
			<div style="display:table-row">
				<div style="display:table-cell; width:130px; vertical-align:middle;">{$lang->chemicalname}</div>
				<div style="display:table-cell">
					<select name="request[product]">
						{$chemicals_selectlist_otps}
					</select>
				</div>
			</div>
			<div style="display:table-row;">
				<div style="display:table-cell;width:130px; vertical-align:middle;">{$lang->requestdescription}</div>
				<div style="display:table-cell; margin-top:5px;">
					<textarea name="request[requestDescription]" cols="40" rows="5"></textarea>
				</div>
			</div>
			<hr />
			<div style="display:table-row">
				<div style="display:table-cell">
					<input type="button" id="perform_sourcing/listpotentialsupplier_Button" class="button" value="{$lang->add}"/>
					<input type="reset" class="button" value="{$lang->reset}" />
				</div>
			</div>
		</div>
	</form>
	<div id="perform_sourcing/listpotentialsupplier_Results"></div>
</div>
</body>
</html>