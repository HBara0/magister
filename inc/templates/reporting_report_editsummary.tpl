<hr />
<form name="perform_reporting/preview_Form" id="perform_reporting/preview_Form" action="#">
    <input type="hidden" value="do_savesummary" name="action"/>
    <input type="hidden" value="{$session_identifier}" name="identifier"/>
    <table style="width:100%;">
        <tr><td colspan="2" class="thead">{$lang->reportsummary}</td></tr>
        <tr><td><textarea name="summary" cols="50" rows="30" class="texteditormin" required="required"></textarea></tr>
        <tr>
            <td>  
                <input type="submit" id="perform_reporting/preview_Button" value="{$lang->save}" class="button"> <input type="reset" value="{$lang->reset}" class="button">           
                <div style="display:block;" id="perform_reporting/preview_Results"></div>   
            </td>
        </tr>    
    </table>
</form>
<hr />