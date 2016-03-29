<h1>{$lang->createreports}</h1>
<form name="perform_reporting/createreports_Form" id="perform_reporting/createreports_Form" action="#" method="post">
    {$lang->quarter} <select id="quarter" name="quarter">
        <option value="1"{$selected[1]}>1</option>
        <option value="2"{$selected[2]}>2</option>
        <option value="3"{$selected[3]}>3</option>
        <option value="4"{$selected[4]}>4</option>
    </select>
    &nbsp;{$lang->year} <input type="text" size="4" maxlength="4" id="year" name="year" value="{$quarter[year]}"/> &nbsp;<input type="button" id="getReports" value="{$lang->getreports}" />
    <p>
        <select id="reports" name="reports[]" size="10" multiple>
            {$reports_list}
        </select>
    </p>
    <br />
    <input type="button" id="perform_reporting/createreports_Button" value="{$lang->create}" class="button" />
</form>
<div id="perform_reporting/createreports_Results"></div>
