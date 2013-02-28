
<form  name="perform_reporting/preview_Form"   id="perform_reporting/preview_Form" action="#" >

<input type="hidden" value="do_savesummary" name="action"/>
<input type="hidden" value="{$reports_id}" name="reportids"/>
<table style="width:100%;">

<tr>
    <td style="width:100%; text-align:left;">
    	<table class="reportbox" style="width: 100%;">
        	<tr><td colspan="2" class="cathead">{$lang->reporteditsummary}</td></tr>
            <tr><td colspan="2" class="cathead" style="color:#FFFFFF; padding-top:10px; padding-bottom:5px;"></td></tr>
	<tr><td><textarea name="summary" cols="50" rows="10" class="texteditormin"></textarea>
</td></tr>
        </table>
    </td>
</tr>



<tr><td>

   <div style="display:block;" id="perform_reporting/preview_Results"></div> 
   <hr />
	<input type="submit" id="perform_reporting/preview_Button" value="{$lang->save}" class="button"> <input type="reset" value="$lang->reset" class="button">
            
        
</td></tr>
 
</table>

</form>