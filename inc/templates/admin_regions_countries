<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->listavailablecountries}</title>
{$headerinc}
</head>
<body>
{$header}
<tr>
{$menu}
<td class="contentContainer">
<h3>{$lang->listavailablecountries}</h3>
<table class="datatable">
<thead>
<tr>
<th>{$lang->id}</th><th>{$lang->name}</th><th>{$lang->affiliate}</th>
</tr>
</thead>
<tbody>
{$countries_list}
</tbody>
</table>
<hr />
<h3>{$lang->addcountry}</h3>
<form id="add_regions/countries_Form" name="add_regions/countries_Form" action="#" method="post">
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<td width="10%"><strong>{$lang->name}</strong></td>
<td width="90%"><input type="text" id="name" name="name" /> <input type="text" id="acronym" name="acronym" size="5" maxlength="5" /></td>
</tr>
<tr>
<td width="10%">{$lang->affiliate}</td>
<td width="90%">{$affiliates_list}</td>
</tr>
<tr>
<td colspan="2"><input type="button" id="add_regions/countries_Button" value="{$lang->add}" /><input type="reset" value="{$lang->reset}" />
<div id="add_regions/countries_Results"></div>
</td>
</tr>
</table>
</form>
</td>
  </tr>
{$footer}
</body>
</html>