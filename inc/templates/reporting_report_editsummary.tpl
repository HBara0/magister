<hr />
<form name="preview_Form" id="preview_Form" action="index.php?module=reporting/preview&referrer=direct&amp;action=do_savesummary" method="post">
    <input type="hidden" value="{$report_summary[rpsid]}" name="rpsid"/>
    <input type="hidden" value="{$session_identifier}" name="identifier"/>
    <table style="width:100%;">
        <tr><td colspan="2" class="thead">{$lang->reportsummary}</td></tr>
        <tr><td><textarea name="summary" cols="50" rows="30" class="texteditormin" required="required">{$report_summary[summary]}</textarea></td></tr>
        <tr>
            <td>  
                <input type="submit" value="{$lang->save}" class="button"> <input type="reset" value="{$lang->reset}" class="button">           
                <div style="display:block;" id="perform_reporting/preview_Results"></div>   
            </td>
        </tr>    
    </table>
</form>
<hr /> 