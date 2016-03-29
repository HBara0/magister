<h1>{$lang->importqdata}</h1>
<form name="perform_reporting/importqdata_Form" id="perform_reporting/importqdata_Form" action="#" method="post">
    <input type='hidden' value="do_import" name="action">
    {$lang->quarter} <select id="quarter" name="quarter">
        <option value="1"{$selected[1]}>1</option>
        <option value="2"{$selected[2]}>2</option>
        <option value="3"{$selected[3]}>3</option>
        <option value="4"{$selected[4]}>4</option>
    </select>
    &nbsp;{$lang->year} <input type="text" size="4" maxlength="4" id="year" name="year" value="{$quarter[year]}" required="required"/>
    <div>{$affid_field}</div>
    <div>
        <select name="foreignSystem" id="foreignSystem"><option value="1">Outsys</option><option value="2">Sage Accpac</option><option value="3">Openbravo</option><option value="4">SYSPRO</option><option value="5">Iran ERP</option><option value="6">Sage 1000</option><option value="7">MS Excel</option><option value="8">Sage Evolution</option><option value="9">Sage 100</option><option value="10">Pakistan ERP</option></select>
    </div>
    <div>
        <input type="checkbox" value="dry" name="runtype" checked="checked"> Dry run<br />
        <input type="checkbox" value="addonly" name="operation" checked="checked"> Add only<br />
    </div>
    <input type="submit" id="perform_reporting/importqdata_Button" value="{$lang->import}" class="button" />
</form>
<div id="perform_reporting/importqdata_Results"></div>
