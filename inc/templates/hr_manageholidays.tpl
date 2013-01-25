<html>
<head>
<title>{$core->settings[systemtitle]} | {$pagetitle}</title>
{$headerinc}
<script language="javascript" type="text/javascript">
	$(function() {
		$("#affid").change(function() {
			if(sharedFunctions.checkSession() == false) {
				return;	
			}
			
			sharedFunctions.requestAjax("post", "index.php?module=hr/manageholidays&action=get_affiliateemployees", "affid=" + $(this).val(), 'exceptionsemployees_list', 'exceptionsemployees_list', true);
		});
			
		$("input[id='isOnce']").change(function() {
			if($(this).is(":checked")) {
				$("#year").removeAttr("disabled");
			}
			else
			{
				$("#year").attr("disabled", "true");
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
<h3>{$pagetitle}</h3>
<form name='change_hr/manageholidays_Form' id="change_hr/manageholidays_Form" method="post">
<input type="hidden" id="action" name="action" value="{$action}" />
{$affid_field}
{$hid_field}
<table width="100%" class="datatable">
	<thead>
		<tr>
			<th style="width:30%;">{$lang->name}</th>
			<th style="width:18%;">{$lang->month}</th>
			<th style="width:18%;">{$lang->day}</th>
			<th style="width:17%;">{$lang->year}</th>
			<th style="width:17%;">{$lang->days}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
            <td align="center"><input type='text' name="title" id="title" required="required" value="{$holiday[title]}"/> <input type='checkbox' name='isOnce' id='isOnce' value="1"{$checkedboxes[isOnce]} />{$lang->once}</td>
			<td align="center">{$months_list}</td>
			<td align="center">{$days_list}</td>
			<td align="center"><input type='text' name="year" id="year" maxlength="4" size="4" accept="numeric" value="{$holiday[year]}"{$year_disabled}/></td>
			<td align="center"><input type='text' name="numDays" id="numDays" size="3" maxlength="3" required="required" value="{$holiday[numDays]}" accept="numeric" /></td>
		</tr>
	</tbody>
		<tr>
			<td colspan="5">
            	<hr />
                <span class="subtitle">{$lang->exceptfollowingemployees}</span>
           		<div id="exceptionsemployees_list">{$exceptionsemployees_list}</div>
            </td>
		</tr>
		<tr>
			<td colspan="5"><hr /><input type='submit' class='button' value='{$lang->savecaps}' id='change_hr/manageholidays_Button' /> </td>
		</tr>
</table>
</form>
<div id="change_hr/manageholidays_Results"></div>
</td>
</tr>
{$footer}
</body>
</html>