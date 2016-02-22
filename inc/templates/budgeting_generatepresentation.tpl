<div class="container">
    <h1>{$lang->generatepresentation}</h1>
    <form name="perform_budgeting/generatepresentation_Form" id="perform_budgeting/generatepresentation_Form" action="#" method="post">
        <div>
            {$lang->chooseanafiiliate}  {$affiliates_list}
            <input type="hidden" name="export" value="1">
        </div>
        <hr>
        <br>
        <span class="ui-state-highlight">{$lang->generatingthereportmaytakeawhile}</span><br>
        <input type="submit" value="{$lang->downloadreport}" class="button">
    </form>
</div>