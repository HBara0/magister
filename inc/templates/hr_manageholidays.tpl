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
{$header}
</head>
<body>
<tr>
{$menu}
<td class="contentContainer">
<h3>{$pagetitle}</h3>
<form name='change_hr/manageholidays_Form' id="change_hr/manageholidays_Form" method="post"  >
<input type="hidden" id="action" name="action" value="{$action}" />
{$affid_field}
{$hid_field}
<table width="100%" class="datatable">
	<thead>
		<tr>
			<th colspan="2">{$lang->name}</th>
            <th width="47" >{$lang->year}</th>
            
            <th width="47" >{$lang->days}</th>
              
             <th width="90">{$lang->month}</th>
             <th width="266" >{$lang->day}</th>
	</tr>
	</thead>
	<tbody>
		<tr class="altrow">
                    <td width="144" align="center"><input type='text' name="title" id="title" required="required" value="{$holiday[title]}"/></td>
                    <td width="97" align="center">{$lang->once} <input type='checkbox' name='isOnce' id='isOnce' value="1"{$checkedboxes[isOnce]}></td>
                   
                    <td align="center"><input type='text' name="year" id="year" maxlength="4" size="4" accept="numeric" value="{$holiday[year]}"{$year_disabled}/></td>
                    <td align="center"><input type='text' name="numDays" id="numDays" size="3" maxlength="3"  required="required"  value="{$holiday[numDays]}"   accept="numeric"/></td>
                    
                    <td align="center">{$months_list}</td>
                            <td align="center">{$days_list}</td>
            
		  </tr>
		<tr>
		  <td colspan="6" align="left">
           <table width="100%" border="0">
  <tr class="thead">
    <td colspan="4"> {$lang->validityperiod}</td>
    </tr>
  <tr>
    <th>{$lang->from}</th>
    <th>{$lang->fromtime}</th>
    <th>{$lang->to}</th>
    <th>{$lang->totime}</th>
  </tr>
  <tr  class="altrow">
    <td><input type="text" id="pickDate_holidayfromdate"  name="validFrom" autocomplete="off" ttabindexabindex="2" value="{$holiday[validFromOuptut]}"  /></td>
    <td><input type="time" name="fromTime" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" title="{$lang->hoursfrom}" value="{$holiday[fromTime]}" placeholder="08:00"> </td>
    <td><input type="text" id="pickDate_holidaytodate" name="validTo" autocomplete="off" tabindex="2"value="{$holiday[validToOutput]}" /></td>
    <td><input type="time" name="toTime" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})"  title="{$lang->hoursto}" value="{$holiday[toTime]}"placeholder="24:00" ></td>
  </tr>
</table></td>
		  </tr>
 
        
	
      
        
	</tbody>
    

    
		<tr>
			<td colspan="8">
            	<hr />
                <span class="subtitle">{$lang->exceptfollowingemployees}</span>
           		<div id="exceptionsemployees_list">{$exceptionsemployees_list}</div>
            </td>
		</tr>
		<tr>
			<td colspan="8"><hr /><input type='submit' class='button' value='{$lang->savecaps}' id='change_hr/manageholidays_Button' /> </td>
		</tr>
</table>
</form>
<div id="change_hr/manageholidays_Results"></div>
</td>
</tr>
{$footer}
</body>
</html>