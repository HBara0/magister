<h1>{$lang->optimizedatabase}</h1>
<p>{$lang->selectoptimizetables}:</p>
<form id="perform_maintenance/optimizedb_Form" name="perform_maintenance/optimizedb_Form" action="#" method="post">
    {$tables_list}
    <p><input type="button" id="perform_maintenance/optimizedb_Button" value="{$lang->optimize}" /> <input type="reset" value="{$lang->reset}" /></p>
</form>
<div id="perform_maintenance/optimizedb_Results"></div>
