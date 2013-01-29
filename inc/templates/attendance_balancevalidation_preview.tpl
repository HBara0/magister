<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->validatebalances}</title>
</head>
{$headerinc}
</head>
<body>
{$header}
<tr> {$menu}
	<td class="contentContainer"><h3>{$lang->validatebalances}</h3>
		<table width="100%" class="datatable">
		{$tableheader}
		{$tablerows}
		</table>
		<hr />
		<form action="index.php?module=attendance/balancesvalidations&amp;action=fixbalances" method="post">
			<input type="hidden" value="{$identifier}" name="identifier" id="identifier">
            <fieldset>
            <legend class="subtitle">{$lang->whattofix}</legend>
            			<div style="width:10%; display:inline-block;">{$lang->fixdaystaken}</div>
			<div style="width:80%; padding:3px; display:inline-block;">
				<input type="radio" value="1" name="fixdaysTaken">
				{$lang->yes} 
				<input type="radio" value="0" name="fixdaysTaken" checked>
				{$lang->no}</div>
			<div style="width:10%; display:inline-block;">{$lang->fixremprevyear}</div>
			<div style="width:80%; padding:3px; display:inline-block;">
				<input type="radio" value="1" name="fixremainPrevYear">
				{$lang->yes}
				<input type="radio" value="0" name="fixremainPrevYear" checked >
				{$lang->no}</div>
			<div>
				<input type="submit" class="button" value="{$lang->fix}">
			</div>
            </fieldset>	

		</form>
	</td>
</tr>
{$footer}
</body>
</html>