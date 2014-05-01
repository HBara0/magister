<form action="index.php?module=reporting/fillmreport&amp;action=process&amp;identifier={$core->input[identifier]}" method="post">
    <input type="hidden" name="referrer" value="fill">
    <input type="hidden" name="processtype" value="finalize">
    <div align="center"><input type="button" value="{$lang->prev}" class="button" onClick="goToURL('index.php?module=reporting/fillmreport&identifier={$core->input[identifier]}');"> <input type="submit" value="{$lang->finalize}" class="button"></div>
</form>