<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->addadditionalbalance}</title>
	{$headerinc}
</head>

<body>
{$header}
<tr>
{$menu}
<td class="contentContainer">
<h3>{$lang->addadditionalbalance}</h3>
<form name='add_attendance/addadditionalleaves_Form' id="add_attendance/addadditionalleaves_Form" method="post">
<input type="hidden" id="action" name="action" value="do_addadditionalleaves" />
	<table width="100%">
		<tr>
			<td style="width:25%;">{$lang->employeename}</td>
			<td style="width:25%;">{$lang->additionaldays}</td>
			<td style="width:25%;">{$lang->date}</td>
			<td style="width:25%;">{$lang->justification}</td>
		</tr>
		<tr>
			<td>{$users_list}</td>
			<td style="vertical-align:top"><input type="text" id='numDays' name="numDays"  size="5" accept="numeric" tabindex="2"/></td>
			<td style="vertical-align:top"><input type='text' id='pickDate_date' autocomplete='off' tabindex="3"/><br />
		  	<input type='hidden' name='date' id='altpickDate_date' /><input type='checkbox' name='correspondToDate' id='correspondToDate' value="1">{$lang->correspondtoperiod}</td>
			<td style="vertical-align:top"><input type="text" id='remark' name="remark" size="40" tabindex="4"/></td>
	  </tr>
	  
		<tr>
			<td colspan="4"><hr/><input type='button' class='button' value='{$lang->savecaps}' id='add_attendance/addadditionalleaves_Button' /></td>
		</tr>
	</table>
</form>
<div id="add_attendance/addadditionalleaves_Results"></div>
</td>
</tr>
{$footer}
</body>
</html>