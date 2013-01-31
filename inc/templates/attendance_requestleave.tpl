<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->requestleave}</title>
{$headerinc}
<script type="text/javascript">
	$(function() {
		$("#uid, #type").change(function() {
			if(sharedFunctions.checkSession() == false) {
				return;	
			}
			
			sharedFunctions.requestAjax("post", "index.php?module=attendance/requestleave&action=getaffiliates", "uid=" + $('#uid').val() + "&ltid=" + $('#type').val(), 'to_inform_fields', 'to_inform_fields', true);
		});
		
		$("#type, #pickDate_to").live('change', function() {
			if(sharedFunctions.checkSession() == false) {
				return;	
			}
			
			if(($("#altpickDate_from").val() != '') && $("#altpickDate_to").val() != '') {
				sharedFunctions.requestAjax("post", "index.php?module=attendance/requestleave&action=getleavetime", "ltid=" + $('#type').val() + "&uid=" + $("#uid").val()+ "&fromDate=" + $("#altpickDate_from").val() + "&toDate=" + $("#altpickDate_to").val(), 'leavetime_details', 'leavetime_details', true);
			}
			else
			{			
				sharedFunctions.requestAjax("post", "index.php?module=attendance/requestleave&action=getleavetime", "ltid=" + $('#type').val() + "&uid=" + $("#uid").val(), 'leavetime_details', 'leavetime_details', true);
			}
			
			sharedFunctions.requestAjax("post", "index.php?module=attendance/requestleave&action=getadditionalfields", "ltid=" + $('#type').val() + "&fromDate=" + $("#altpickDate_from").val() + "&toDate=" + $("#altpickDate_to").val() + "&uid=" + $("#uid").val(), 'additionalfields_output', 'additionalfields_output', true);
		});
	});
</script>
</head>
<body>
{$header}
<tr>
{$menu}
    <td class="contentContainer">
    <h3>{$lang->$action}</h3>
    <form name="perform_attendance/{$action}_Form" id="perform_attendance/{$action}_Form" action="#" method="post">
    {$lidfield}
    {$uidfield}
	<table width="100%" cellpadding="0" cellspacing="0">
		{$requestonbehalf_field}
        <tr>
            <td width="18%">{$lang->fromdate}</td>
        	<td><input type="text" id="pickDate_from" autocomplete="off" tabindex="1" value="{$leave[fromDate_output]}" required="required"/><input type="hidden" name="fromDate" id="altpickDate_from" value="{$leave[fromDate_formatted]}" /></td>
        </tr>
        <tr>
            <td>{$lang->todate}</td>
            <td><input type="text" id="pickDate_to" autocomplete="off" tabindex="2" value="{$leave[toDate_output]}" required="required" /><input type="hidden" name="toDate" id="altpickDate_to" value="{$leave[toDate_formatted]}"/></td>
        </tr>
        <tr><td>&nbsp;</td><td style="font-style:italic;"><span id="leavetime_details">{$lang->betweenhours}</span></td></tr>
        <tr>
        	<td>{$lang->leavetype}</td>
            <td>{$leavetypes_list}&nbsp;<span id="additionalfields_output">{$additional_fields_output}</span></td>
        </tr>
        <tr>
        	<td>{$lang->leavereason}</td>
            <td><textarea cols="50" rows="5" name="reason" id="reason">{$leave[reason]}</textarea></td>
        </tr>
        <tr><td colspan="2"><hr /><span class="subtitle">{$lang->contactwhileabsent}</span></td></tr>
        <tr>
        	<td>{$lang->leaveaddress}</td>
            <td>
            	<textarea cols="50" rows="2" name="addressWhileAbsent" id="addressWhileAbsent">{$leave[addressWhileAbsent]}</textarea>
            </td>
         </tr>
         <tr>
         	<td>{$lang->phonenumber}</td>
            <td>
				<input type="text" id="telephone_intcode" name="telephone[intcode]" size="3" maxlength="3" accept="numeric"  value="{$telephone[intcode]}" /> <input type="text" id="telephone_areacode" name="telephone[areacode]" size='4' maxlength="4" accept="numeric"  value="{$telephone[areacode]}" /> <input type="text" id="telephone_number" name="telephone[number]" accept="numeric"  value="{$telephone[number]}" />
            </td>
        </tr>
        <tr>
            <td>{$lang->contactpersonleave}</td>
            <td><input type='text' id='user_1_QSearch' value="{$leave[contactPersonName]}"/><input type="text" size="3" id="user_1_id_output" value="{$leave[contactPerson]}" disabled/><input type='hidden' id='user_1_id' name='contactPerson' value="{$leave[contactPerson]}" /><div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div></td>
        </tr>
        <tr>
            <td>{$lang->limitedemailaccess}</td>
            <td>{$limitedemail_radiobutton}</td>
        </tr>
        <tr><td colspan="2"><hr /></td></tr>
        <tr>
            <td colspan="2">        
                <fieldset class="altrow">
                    <legend><strong>{$lang->informfollowing}:</strong></legend>
                    <div id='to_inform_fields'>{$to_inform}</div>
                </fieldset>
            </td>
        </tr>
        <tr><td colspan="2"><hr /></td></tr>
    </table>
    <input type="submit" id="perform_attendance/{$action}_Button" value="{$lang->$action}" class="button" />
    </form>
    <div id="perform_attendance/{$action}_Results"></div>
    </td>
</tr>
{$footer}
</body>
</html>